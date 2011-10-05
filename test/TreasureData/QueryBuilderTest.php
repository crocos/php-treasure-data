<?php
/**
 *
 */


namespace TreasureData;


class QueryBuliderTest extends \PHPUnit_Framework_TestCase
{
    public function testGetQuery()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix'")
            ->getQuery();

        $this->assertEquals(
            "SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix'",
            $q
        );
    }

    public function testValueFormat()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v.user_agent <> 'zabbix'")
            ->getQuery();

        $this->assertEquals(
            "SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix'",
            $q
        );
    }

    public function testBind()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v.user_agent <> 'zabbix' AND v.user_id = :user_id")
            ->bind(array('user_id' => 1))
            ->getQuery();

        $this->assertTrue($qb->hasParam('user_id'));
        $this->assertFalse($qb->hasParam('user_id_hoge'));
        $this->assertEquals(
            "SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix' AND v['user_id'] = '1'",
            $q
        );

        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v.user_agent <> 'zabbix' AND v.user_id = :user_id")
            ->bind('user_id', 1)
            ->getQuery();

        $this->assertEquals(
            "SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix' AND v['user_id'] = '1'",
            $q
        );

        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v.user_agent <> 'zabbix' AND v.user_id = :user_id AND (time >= :time_from AND time <= :time_to)")
            ->bind('user_id', 1)
            ->bind('time_from', 11111111)
            ->bind('time_to', 11111111)
            ->getQuery();

        $this->assertEquals(
            "SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix' AND v['user_id'] = '1' AND (time >= '11111111' AND time <= '11111111')",
            $q
        );
    }

    /**
     *  @expectedException \RuntimeException
     */
    public function testBindNoKey()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v.user_agent <> 'zabbix' AND v.user_id = :user_id")
            ->bind(array('no_bind_key' => 1))
            ->getQuery();
    }

    /**
     */
    public function testBindIngnoreNonExistKey()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare("SELECT * FROM user_log WHERE v.user_agent <> 'zabbix' AND v.user_id = :user_id")
            ->bind(array(
                'no_bind_key' =>  1, // no placeholder but bind value
                'user_id' => 1,
            ), null, $ignore = true)
            ->getQuery();

        $this->assertEquals(
            "SELECT * FROM user_log WHERE v['user_agent'] <> 'zabbix' AND v['user_id'] = '1'",
            $q
        );
    }

    public function testFunctionMap()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare('SELECT f.date as day, count(*) FROM user_log WHERE v.user_id=:user_id GROUP BY f.date ORDER BY day')
            ->bind(array('user_id' => 1))
            ->getQuery();

        $this->assertEquals(
            "SELECT to_date(from_unixtime(cast(time as int))) as day, count(*) FROM user_log WHERE v['user_id']='1' GROUP BY to_date(from_unixtime(cast(time as int))) ORDER BY day",
            $q
        );
    }

    /**
     *  @expectedException \RuntimeException
     */
    public function testFunctionMapNoAlias()
    {
        $qb = new QueryBuilder();
        $q = $qb->prepare('SELECT f.hogehoge as day, count(*) FROM user_log WHERE v.user_id=:user_id GROUP BY f.date ORDER BY day')
            ->bind(array('user_id' => 1))
            ->getQuery();
    }

}
