<?php

class Collection
{
    private $array;

    public function __construct($array = [])
    {
        $this->array = $array;
    }

    public function push($item)
    {
        array_push($this->array, $item);
    }

    public function get($key)
    {
        return $this->array[$key];
    }

    public function set($key, $value)
    {
        $this->array[$key] = $value;
    }

    public function size()
    {
        return count($this->array);
    }

    public function hasKey($key)
    {
        return isset($this->array[$key]);
    }

    public function toArray()
    {
        return Collection::_toArray($this);
    }

    private static function _toArray($collection)
    {
        $ret = [];

        $collection->forEach(function ($key, $value) use (&$ret) {
            if ($value instanceof Collection) {
                $ret[$key] = Collection::_toArray($value);
            } else {
                $ret[$key] = $value;
            }
        });

        return $ret;
    }

    public function forEach(callable $c)
    {
        $i = 0;

        foreach ($this->array as $key => $value) {
            $c($key, $value, $i);

            $i++;
        }
    }

    public function find(callable $c)
    {
        $i = 0;

        foreach ($this->array as $key => $value) {
            if ($c($key, $value, $i)) {
                return $value;
            }

            $i++;
        }

        return null;
    }
}
