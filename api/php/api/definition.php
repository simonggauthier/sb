<?php

include_once(dirname(__FILE__) . '/../util.php');

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
            return strtotime($value);
        } else if ($this->hint === 'money') {
            return substr($value, 0, strlen($value) - 2) . '.' . substr($value, strlen($value) - 2);
        }

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
        $this->attributes = array();
    }

    public function tableName()
    {
        $ret = strtolower(preg_replace('/\B([A-Z])/', '_$1', $this->type));

        if (substr($ret, strlen($ret) - 1, 1) === 'y') {
            return substr($ret, 0, strlen($ret) - 1) . 'ies';
        }

        return $ret . 's';
    }

    public function getAttribute($name)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->name === $name) {
                return $attribute;
            }
        }

        return null;
    }

    public function getAttributeByColumnName($columnName)
    {
        foreach ($this->attributes as $attribute) {
            if ($attribute->columnName() === $columnName) {
                return $attribute;
            }
        }

        return null;
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
        $ret = array();
        $arr = $this->method === 'post' ? $_POST : $_GET;

        foreach ($this->parameters as $parameter) {
            if (isset($arr[$parameter->name])) {
                array_push($ret, $arr[$parameter->name]);
            }
        }

        return $ret;
    }

    public function hasParameter($name)
    {
        foreach ($this->parameters as $parameter) {
            if ($parameter->name === $name) {
                return true;
            }
        }

        return false;
    }
}

class ApiDefinition
{
    public $entities;
    public $routes;

    public function __construct($filename)
    {
        $json = json_decode(fileToString($filename), true);

        $this->entities = array();

        $jsonEntities = $json['entities'];

        foreach ($jsonEntities as $key => $value) {
            $entity = new Entity($key);
            $entity->attributes = $this->makeAttributes($value);

            array_push($this->entities, $entity);
        }

        $this->routes = array();

        $jsonRoutes = $json['routes'];

        foreach ($jsonRoutes as $key => $value) {
            $comps = explode(':', $key);

            $method = $comps[0];

            if ($method === 'entity') {
                array_push($this->routes, new Route('post', $comps[1], $this->makeParams($value), 'create' . ucwords($this->findControllerName($comps[1], $method))));

                array_push($value, 'id');
                array_push($this->routes, new Route('post', $comps[1], $this->makeParams($value), 'update' . ucwords($this->findControllerName($comps[1], $method))));

                $value = array('id', 'delete');
                array_push($this->routes, new Route('post', $comps[1], $this->makeParams($value), 'delete' . ucwords($this->findControllerName($comps[1], $method))));
            } else {
                array_push($this->routes, new Route($method, $comps[1], $this->makeParams($value), $this->findControllerName($comps[1], $method)));
            }
        }
    }

    private function findControllerName($name, $method)
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
        $ret = array();

        foreach ($attributes as $attr) {
            $comps = explode(':', $attr);

            $name = $comps[0];
            $hint = '';

            if (count($comps) > 1) {
                $hint = $comps[1];
            }

            array_push($ret, new Attribute($name, $hint));
        }

        return $ret;
    }

    private function makeParams($params)
    {
        $ret = array();

        foreach ($params as $param) {
            $comps = explode(':', $param);

            $name = $comps[0];
            $hint = '';

            if (count($comps) > 1) {
                $hint = $comps[1];
            }

            array_push($ret, new Parameter($name, $hint));
        }

        return $ret;
    }

    public function findRoute($method, $action)
    {
        $arr = $method === 'post' ? $_POST : $_GET;

        $isUpdate = isset($arr['id']) && !isset($arr['delete']);
        $isDelete = isset($arr['delete']);

        foreach ($this->routes as $route) {
            if ($route->method === $method && $route->action === $action) {
                if ($isUpdate && $route->hasParameter('id')) {
                    return $route;
                }

                if ($isDelete && $route->hasParameter('delete')) {

                    return $route;
                }

                if (!$isUpdate && !$isDelete) {
                    return $route;
                }
            }
        }

        return null;
    }

    public function getEntity($type)
    {
        foreach ($this->entities as $entity) {
            if ($entity->type === $type) {
                return $entity;
            }
        }

        return null;
    }
}
