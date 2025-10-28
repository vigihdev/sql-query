<?php

namespace Tests\Conditions;

use SqlQuery\Conditions\CompositeCondition;
use SqlQuery\Conditions\SimpleCondition;
use Tests\TestCase;

class CompositeConditionTest extends TestCase
{
    public function testCreateCompositeCondition()
    {
        $condition1 = new SimpleCondition('age', '>', 18);
        $condition2 = new SimpleCondition('status', '=', 'active');
        
        $composite = new CompositeCondition('AND', [$condition1, $condition2]);
        $this->assertInstanceOf(CompositeCondition::class, $composite);
    }

    public function testToString()
    {
        $condition1 = new SimpleCondition('age', '>', 18);
        $condition2 = new SimpleCondition('status', '=', 'active');
        
        $composite = new CompositeCondition('AND', [$condition1, $condition2]);
        $result = $composite->build();
        $this->assertIsString($result);
    }

    public function testGetParams()
    {
        $condition1 = new SimpleCondition('age', '>', 18);
        $condition2 = new SimpleCondition('status', '=', 'active');
        
        $composite = new CompositeCondition('AND', [$condition1, $condition2]);
        $operator = $composite->getOperator();
        $conditions = $composite->getConditions();
        
        $this->assertEquals('AND', $operator);
        $this->assertIsArray($conditions);
        $this->assertCount(2, $conditions);
    }

    public function testOrOperator()
    {
        $condition1 = new SimpleCondition('role', '=', 'admin');
        $condition2 = new SimpleCondition('role', '=', 'moderator');
        
        $composite = new CompositeCondition('OR', [$condition1, $condition2]);
        $this->assertInstanceOf(CompositeCondition::class, $composite);
    }
}
