<?php

include 'api/collection.php';

class Database
{
    private $filename;
    private $pdo;
    private $definition;

    public function __construct($filename, $definition)
    {
        $this->filename = $filename;

        $this->pdo = new PDO('sqlite:' . $filename);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $this->definition = $definition;
    }

    public function executeScript($scriptFilename)
    {
        $db = new SQLite3($this->filename);

        $script = fileToString($scriptFilename);
        $lines = new Collection(explode(';', $script));

        $lines->forEach(function ($key, $line) use (&$db) {
            $db->exec(trim($line));
        });
    }

    private function executeQuery($query, $params)
    {
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($params->toArray());

        return $stmt;
    }

    private function fetchOne($stmt)
    {
        $ret = $stmt->fetch();

        if (!$ret) {
            return null;
        }

        return $ret;
    }

    private function makeEntitySelectQuery($entity, $crits, $select = 'SELECT * FROM')
    {
        $query = $select . ' ' . $entity->tableName();

        $crits->forEach(function ($key, $crit, $i) use ($entity, &$query) {
            $attribute = $entity->findAttribute($key);

            if ($i === 0) {
                $query = $query . ' WHERE ' . $attribute->columnName() . ' ' . $attribute->equality() . ' ?';
            } else {
                $query = $query . ' and ' . $attribute->columnName() . ' ' . $attribute->equality() . ' ?';
            }
        });

        return $query;
    }

    private function makeEntityValues($crits)
    {
        $ret = new Collection();

        $crits->forEach(function ($key, $value) use (&$ret) {
            $ret->push($value);
        });

        return $ret;
    }

    private function makeEntityResultSet($entity, $rs)
    {
        if (!$rs) {
            return null;
        }

        $ret = new Collection();
        $rs = new Collection($rs);

        $rs->forEach(function ($key, $result) use ($entity, &$ret) {
            if (!is_numeric($key)) {
                $attr = $entity->findAttributeByColumnName($key);

                $ret->set($attr->name, $attr->toValue($result));
            }
        });

        return $ret;
    }

    public function getEntity($type, $crits)
    {
        if (!$crits instanceof Collection) {
            $crits = new Collection($crits);
        }

        $entity = $this->definition->getEntity($type);

        $rs = $this->fetchOne($this->executeQuery($this->makeEntitySelectQuery($entity, $crits), $this->makeEntityValues($crits)));

        return $this->makeEntityResultSet($entity, $rs);
    }

    public function getEntities($type, $crits)
    {
        if (!$crits instanceof Collection) {
            $crits = new Collection($crits);
        }

        $entity = $this->definition->getEntity($type);

        $ret = new Collection();

        $rs = $this->executeQuery($this->makeEntitySelectQuery($entity, $crits), $this->makeEntityValues($crits));

        while ($row = $rs->fetch()) {
            $ret->push($this->makeEntityResultSet($entity, $row));
        }

        return $ret;
    }

    public function createEntity($type, &$attributes)
    {
        if (!$attributes instanceof Collection) {
            $attributes = new Collection($attributes);
        }

        $entity = $this->definition->getEntity($type);

        $query = 'insert into ' . $entity->tableName() . ' (';

        // Column list
        $entity->attributes->forEach(function ($key, $attribute, $i) use (&$query, $entity) {
            // Skip autoincrement
            if ($attribute->name === 'id' && $attribute->hint === 'number') {
                return;
            }

            $query .= $attribute->columnName();

            if ($i < $entity->attributes->size() - 1) {
                $query .= ', ';
            }
        });

        $query .= ') values (';
        $values = new Collection();

        // '? list and values
        $entity->attributes->forEach(function ($key, $attribute, $i) use (&$query, $entity, &$values, $attributes) {
            // Skip autoincrement
            if ($attribute->name === 'id' && $attribute->hint === 'number') {
                return;
            }

            if (strpos($attribute->name, 'startDate') === 0 || $attributes->get($attribute->name) === '$now') {
                $query .= 'date(\'now\')';
            } else {
                $query .= '?';

                $values->push($attribute->makeInsertValue($attributes->get($attribute->name)));
            }

            if ($i < $entity->attributes->size() - 1) {
                $query .= ', ';
            }
        });

        $query .= ')';

        $this->executeQuery($query, $values);

        if (!$attributes->hasKey('id')) {
            $attributes->set('id', $this->pdo->lastInsertId());
        }
    }

    public function updateEntity($type, $attributes)
    {
        $entity = $this->definition->getEntity($type);

        $query = 'update ' . $entity->tableName() . ' set ';
        $values = new Collection();

        $attributes->forEach(function ($key, $value, $i) use ($entity, $attributes, &$query, &$values) {
            $attribute = $entity->findAttribute($key);

            if ($attribute->name !== 'id') {
                if ($attributes->get($attribute->name) === '$now') {
                    $query .= $attribute->columnName() . ' = date(\'now\')';
                } else {
                    $query .= $attribute->columnName() . ' = ?';

                    $values->push($attribute->makeInsertValue($attributes->get($attribute->name)));
                }

                if ($i < $attributes->size() - 1) {
                    $query .= ', ';
                }
            }
        });

        $id = $entity->findAttribute('id');
        $values->push($id->makeInsertValue($attributes->get('id')));

        $query .= ' where id ' . $id->equality() . ' ?';

        $this->executeQuery($query, $values);
    }

    public function deleteEntity($type, $crits)
    {
        if (!$crits instanceof Collection) {
            $crits = new Collection($crits);
        }

        $entity = $this->definition->getEntity($type);

        $this->executeQuery($this->makeEntitySelectQuery($entity, $crits, 'DELETE FROM'), $this->makeEntityValues($crits));
    }
}
