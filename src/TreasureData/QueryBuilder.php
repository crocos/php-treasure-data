<?php
/**
 *
 */

namespace TreasureData;

class QueryBuilder
{
    protected $query = null;
    protected $parsed_query = null;
    protected $bind_values = array();

    private $_function_map = array(
        'date'  => 'to_date(from_unixtime(cast(time as int)))',
        'month' => 'to_month(from_unixtime(cast(time as int)))',
    );

    public function __construct()
    {
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

    public function getQuery()
    {
        if ($this->query === null) {
            throw new \RuntimeException("prepare() to set query before getQuery().");
        }

        $this->parsed_query = $this->query;
        $parsed_query = $this->bindFunctionMap();
        $parsed_query = preg_replace('/v\.([^\s=<>,]+)/', "v['$1']", $parsed_query);

        $this->parsed_query = $parsed_query;
        return $this->getBindQuery();
    }

    protected function getBindQuery()
    {
        $this->parsed_query = preg_replace('/:([^\s=<>,]+)/e', "'\''.\$this->getValue('\\1').'\''", $this->parsed_query);
        return $this->parsed_query;
    }

    protected function bindFunctionMap()
    {
        $parsed_query = $this->parsed_query;
        $parsed_query = preg_replace('/f\.([^\s=<>,]+)/e', "\$this->getFunctionMap('\\1')", $parsed_query);
        return $parsed_query;
    }

    protected function getFunctionMap($func_id)
    {
        if (isset($this->_function_map[$func_id])) {
            return $this->_function_map[$func_id];
        }
        throw new \RuntimeException("Cannot use '$func_id' as any function alias.");
    }

    protected function bindValue($k, $v)
    {
        if (!$this->hasParam($k)) {
            throw new \RuntimeException(":$k is not set in prepared query.");
        }

        $this->bind_values[$k] = $v;
    }
}
