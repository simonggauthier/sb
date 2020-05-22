<?php

include_once 'collection.php';
include_once 'util.php';

function handlerName($action)
{
    return $action;
}

class Attribute
{
    public $name;
    public $hint;

    public function __construct($name, $hint)
    {
        $this->name = $name;
        $this->hint = $hint;
    }

    public function columnName()
    {
        return strtolower(preg_replace('/\B([A-Z])/', '_$1', $this->name));
    }

    public function equality()
    {
        if ($this->hint === 'number') {
            return '=';
        }

        return 'like';
    }

    public function toValue($value)
    {
        if ($this->hint === 'number') {
            return intval($value);
        } else if ($this->hint === 'date') {
            return $value;
        } else if ($this->hint === 'money') {
            if (!strpos($value, '.')) {
                $value .= '.00';
            }

            return $value;
        }

        return $value;
    }

    public function makeInsertValue($value)
    {
        return $value;
    }
}

class Entity
{
    public $type;
    public $attributes;

    public function __construct($type)
    {
        $this->type = $type;
        $this->attributes = new Collection();
    }

    public function tableName()
    {
        $ret = strtolower(preg_replace('/\B([A-Z])/', '_$1', $this->type));

        if (substr($ret, strlen($ret) - 1, 1) === 'y') {
            return substr($ret, 0, strlen($ret) - 1) . 'ies';
        }

        return $ret . 's';
    }

    public function findAttribute($name)
    {
        return $this->attributes->find(function ($key, $attribute) use ($name) {
            return $attribute->name === $name;
        });
    }

    public function findAttributeByColumnName($columnName)
    {
        return $this->attributes->find(function ($key, $attribute) use ($columnName) {
            return $attribute->columnName() === $columnName;
        });
    }
}


class Parameter
{
    public $name;
    public $hint;

    public function __construct($name, $hint = '')
    {
        $this->name = $name;
        $this->hint = $hint;
    }
}

class Route
{
    public $method;
    public $action;
    public $parameters;
    public $controllerName;

    public function __construct($method, $action, $parameters, $controllerName)
    {
        $this->method = $method;
        $this->action = $action;
        $this->parameters = $parameters;
        $this->controllerName = $controllerName;
    }

    public function extractParams()
    {
        $ret = new Collection();
        $arr = new Collection($this->method === 'post' ? $_POST : $_GET);

        $this->parameters->forEach(function ($key, $parameter) use ($arr, &$ret) {
            if ($arr->hasKey($parameter->name)) {
                $ret->push($arr->get($parameter->name));
            }
        });

        return $ret;
    }

    public function hasParameter($name)
    {
        return $this->parameters->find(function ($key, $parameter) use ($name) {
            return $parameter->name === $name;
        }) != null;
    }
}

class ApiDefinition
{
    public $entities;
    public $routes;

    public function __construct($filename)
    {
        $json = json_decode(fileToString($filename), true);

        $this->entities = new Collection();

        $jsonEntities = new Collection($json['entities']);

        $jsonEntities->forEach(function ($key, $jsonEntity) {
            $jsonEntity = new Collection($jsonEntity);

            $entity = new Entity($key);
            $entity->attributes = $this->makeAttributes($jsonEntity);

            $this->entities->push($entity);
        });

        $this->routes = new Collection();

        $jsonRoutes = new Collection($json['routes']);

        $jsonRoutes->forEach(function ($key, $jsonRoute) {
            $jsonRoute = new Collection($jsonRoute);

            $comps = explode(':', $key);
            $method = $comps[0];

            if ($method === 'entity') {
                $this->routes->push(new Route('post', $comps[1], $this->makeParams($jsonRoute), 'create' . ucwords($this->makeControllerName($comps[1], $method))));

                $jsonRoute->push('id');
                $this->routes->push(new Route('post', $comps[1], $this->makeParams($jsonRoute), 'update' . ucwords($this->makeControllerName($comps[1], $method))));

                $jsonRoute = new Collection(['id', 'delete']);
                $this->routes->push(new Route('post', $comps[1], $this->makeParams($jsonRoute), 'delete' . ucwords($this->makeControllerName($comps[1], $method))));
            } else {
                $this->routes->push(new Route($method, $comps[1], $this->makeParams($jsonRoute), $this->makeControllerName($comps[1], $method)));
            }
        });
    }

    private function makeControllerName($name, $method)
    {
        $ret = str_replace('/', '', $name);
        $ret = str_replace('-', ' ', $ret);

        if (strpos($ret, ' ') >= 0) {
            $ret = substr($ret, 0, 1) . substr(ucwords($ret), 1);
        }

        $ret = str_replace(' ', '', $ret);

        if ($method === 'get') {
            $ret = 'get' . ucwords($ret);
        }

        return $ret;
    }

    private function makeAttributes($attributes)
    {
        $ret = new Collection();

        $attributes->forEach(function ($key, $attribute) use (&$ret) {
            $comps = explode(':', $attribute);

            $name = $comps[0];
            $hint = '';

            if (count($comps) > 1) {
                $hint = $comps[1];
            }

            $ret->push(new Attribute($name, $hint));
        });

        return $ret;
    }

    private function makeParams($params)
    {
        $ret = new Collection();

        $params->forEach(function ($key, $param) use (&$ret) {
            $comps = explode(':', $param);

            $name = $comps[0];
            $hint = '';

            if (count($comps) > 1) {
                $hint = $comps[1];
            }

            $ret->push(new Parameter($name, $hint));
        });

        return $ret;
    }

    public function findRoute($method, $action)
    {
        $arr = new Collection($method === 'post' ? $_POST : $_GET);

        $isUpdate = $arr->hasKey('id') && !$arr->hasKey('delete');
        $isDelete = $arr->hasKey('delete');

        return $this->routes->find(function ($key, $route) use ($isUpdate, $isDelete, $action) {
            if ($route->action !== $action) {
                return false;
            }

            if ($isUpdate && $route->hasParameter('id')) {
                return true;
            }

            if ($isDelete && $route->hasParameter('delete')) {
                return true;
            }

            if (!$isUpdate && !$isDelete) {
                return true;
            }

            return false;
        });
    }

    public function getEntity($type)
    {
        return $this->entities->find(function ($key, $entity) use ($type) {
            return $entity->type === $type;
        });
    }
}
