<?php

namespace Tests\Conditions;

use SqlQuery\Conditions\SimpleCondition;
use Tests\TestCase;

class SimpleConditionTest extends TestCase
{
    public function testCreateSimpleCondition()
    {
        $condition = new SimpleCondition('name', '=', 'John');
        $this->assertInstanceOf(SimpleCondition::class, $condition);
    }

    public function testToString()
    {
        $condition = new SimpleCondition('age', '>', 18);
        $result = $condition->build();
        $this->assertIsString($result);
    }

    public function testGetParams()
    {
        $condition = new SimpleCondition('email', '=', 'test@example.com');
        $column = $condition->getColumn();
        $operator = $condition->getOperator();
        $value = $condition->getValue();
        
        $this->assertEquals('email', $column);
        $this->assertEquals('=', $operator);
        $this->assertEquals('test@example.com', $value);
    }

    public function testDifferentOperators()
    {
        $operators = ['=', '!=', '>', '<', '>=', '<=', 'LIKE'];
        
        foreach ($operators as $operator) {
            $condition = new SimpleCondition('column', $operator, 'value');
            $this->assertInstanceOf(SimpleCondition::class, $condition);
        }
    }

    public function testNullValue()
    {
        $condition = new SimpleCondition('deleted_at', 'IS', null);
        $this->assertInstanceOf(SimpleCondition::class, $condition);
    }
}
