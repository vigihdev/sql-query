<?php

namespace Tests;

use SqlQuery\Query;
use Tests\TestCase;

class QueryTest extends TestCase
{
    private Query $query;

    protected function setUp(): void
    {
        parent::setUp();
        $this->query = new Query();
    }

    public function testFromTable()
    {
        $result = $this->query->from('users');
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testWhereCondition()
    {
        $result = $this->query->where(['id', '=', 1]);
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testDistinct()
    {
        $result = $this->query->distinct();
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testLimit()
    {
        $result = $this->query->limit(10);
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testOffset()
    {
        $result = $this->query->offset(5);
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testOrderBy()
    {
        $result = $this->query->orderBy(['name' => 'ASC']);
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testGroupBy()
    {
        $result = $this->query->groupBy('category');
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testToSql()
    {
        $result = $this->query->from('users')->toSql();
        $this->assertIsString($result);
    }

    public function testBuildParts()
    {
        $result = $this->query->from('users')->buildParts();
        $this->assertIsArray($result);
    }
}
