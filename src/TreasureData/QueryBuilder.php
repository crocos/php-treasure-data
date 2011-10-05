<?php
/**
 *
 */

namespace TreasureData;

use TreasureData\FunctionAlias;

class QueryBuilder
{
    protected $query = null;
    protected $parsed_query = null;
    protected $bind_values = array();

    public function __construct(FunctionAlias $fa = null)
    {
        if ($fa == null) {
            $fa = new FunctionAlias();
        }
        $this->function_alias = $fa;
    }

    public function prepare($query)
    {
        $this->query = $query;
        return $this;
    }

    public function bind($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->bindValue($k, $v);
            }
        } else {
            $this->bindValue($key, $value);
        }
        return $this;
    }

    public function getValue($key)
    {
        if (array_key_exists($key, $this->bind_values)) {
            return $this->bind_values[$key];
        }
        throw new \RuntimeException("No such key in bound values '$key'");
    }

    public function hasParam($k)
    {
        $param_str = ":$k";
        if (strpos($this->query, $param_str) !== false) {
            return true;
        }
        return false;
    }

    public function getPreparedQuery()
    {
        return $this->query;
    }

    public function getParsedQuery()
    {
        if ($this->parsed_query === null) {
            throw new \RuntimeException("Able to get parsed query after getQuery() called.");
        }
        return $this->parsed_query;
    }

    public function getQuery()
    {
        if ($this->query === null) {
            throw new \RuntimeException("prepare() to set query before getQuery().");
        }

        $parsed_query = $this->query;
        $parsed_query = $this->bindFunctionAlias($parsed_query);
        $parsed_query = $this->bindValueAlias($parsed_query);

        return $this->parsed_query = $this->getBindQuery($parsed_query);
    }

    protected function getBindQuery($parsed_query)
    {
        $parsed_query = preg_replace('/:([^\s=<>,]+)/e', "'\''.\$this->getValue('\\1').'\''", $parsed_query);
        return $parsed_query;
    }

    protected function bindValueAlias($parsed_query)
    {
        $parsed_query = preg_replace('/v\.([^\s=<>,]+)/', "v['$1']", $parsed_query);
        return $parsed_query;
    }

    protected function bindFunctionAlias($parsed_query)
    {
        $parsed_query = preg_replace('/f\.([^\s=<>,]+)/e', "\$this->function_alias->get('\\1')", $parsed_query);
        return $parsed_query;
    }

    protected function bindValue($k, $v)
    {
        if (!$this->hasParam($k)) {
            throw new \RuntimeException(":$k is not set in prepared query.");
        }

        $this->bind_values[$k] = $v;
    }
}
