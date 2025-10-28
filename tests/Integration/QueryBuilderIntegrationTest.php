<?php

namespace Tests\Integration;

use SqlQuery\Query;
use Tests\TestCase;

class QueryBuilderIntegrationTest extends TestCase
{
    public function testBasicQuery()
    {
        $query = new Query();
        
        $result = $query
            ->from('users')
            ->where(['active', '=', 1])
            ->limit(10);
            
        $this->assertInstanceOf(Query::class, $result);
        $sql = $result->toSql();
        $this->assertIsString($sql);
    }

    public function testQueryWithJoin()
    {
        $query = new Query();
        
        $result = $query
            ->from('users u')
            ->innerJoin('posts p', 'u.id', '=', 'p.user_id')
            ->where(['u.active', '=', 1]);
            
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testQueryWithGroupBy()
    {
        $query = new Query();
        
        $result = $query
            ->from('products')
            ->where(['price', '>', 100])
            ->groupBy('category');
            
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testComplexWhereConditions()
    {
        $query = new Query();
        
        $result = $query
            ->from('users')
            ->where(['name', 'LIKE', '%john%'])
            ->orWhere(['email', 'LIKE', '%john%'])
            ->where(['status', 'IN', ['active', 'pending']]);
            
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testDistinctWithLimitOffset()
    {
        $query = new Query();
        
        $result = $query
            ->distinct()
            ->from('products')
            ->where(['active', '=', 1])
            ->orderBy(['category' => 'ASC'])
            ->limit(20)
            ->offset(10);
            
        $this->assertInstanceOf(Query::class, $result);
    }

    public function testBuildPartsMethod()
    {
        $query = new Query();
        
        $query->from('users')->where(['id', '>', 0]);
        $parts = $query->buildParts();
        
        $this->assertIsArray($parts);
        $this->assertNotEmpty($parts);
    }
}
