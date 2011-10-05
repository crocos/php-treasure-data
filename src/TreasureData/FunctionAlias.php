<?php
/**
 *
 */

namespace TreasureData;

class FunctionAlias
{
    private $_function_map = array(
        'date'   => 'to_date(from_unixtime(cast(time as int)))',
        'month'  => 'month(from_unixtime(cast(time as int)))',
        'year'   => 'year(from_unixtime(cast(time as int)))',
        'day'    => 'day(from_unixtime(cast(time as int)))',
        'hour'   => 'hour(from_unixtime(cast(time as int)))',
        'minute' => 'minute(from_unixtime(cast(time as int)))',
        'second' => 'second(from_unixtime(cast(time as int)))',
        'week'   => 'weekofyear(from_unixtime(cast(time as int)))',
    );

    public function get($key)
    {
        if ($this->has($key)) {
            return $this->_function_map[$key];
        }
        throw new \RuntimeException("Cannot use '$key' as any function alias.");
    }

    public function getAliases()
    {
        return $this->_function_map;
    }

    public function has($key)
    {
        if (isset($this->_function_map[$key])) {
            return true;
        }
        return false;
    }
}
