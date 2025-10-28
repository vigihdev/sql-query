<?php

namespace Tests\Factories;

use SqlQuery\Factories\ConditionFactory;
use SqlQuery\Conditions\SimpleCondition;
use SqlQuery\Conditions\InCondition;
use SqlQuery\Conditions\LikeCondition;
use SqlQuery\Conditions\IsCondition;
use Tests\TestCase;

class ConditionFactoryTest extends TestCase
{
    private ConditionFactory $factory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->factory = new ConditionFactory();
    }

    public function testCreateSimpleCondition()
    {
        $condition = $this->factory->create('=', ['name', 'John']);
        $this->assertInstanceOf(SimpleCondition::class, $condition);
    }

    public function testCreateInCondition()
    {
        $condition = $this->factory->create('IN', ['id', [1, 2, 3]]);
        $this->assertInstanceOf(InCondition::class, $condition);
    }

    public function testCreateLikeCondition()
    {
        $condition = $this->factory->create('LIKE', ['name', '%john%']);
        $this->assertInstanceOf(LikeCondition::class, $condition);
    }

    public function testCreateIsCondition()
    {
        $condition = $this->factory->create('IS NULL', ['deleted_at']);
        $this->assertInstanceOf(IsCondition::class, $condition);
    }

    public function testSupportedOperators()
    {
        $operators = ['=', '!=', '>', '<', '>=', '<='];
        
        foreach ($operators as $operator) {
            $condition = $this->factory->create($operator, ['column', 'value']);
            $this->assertInstanceOf(SimpleCondition::class, $condition);
        }
    }

    public function testInvalidOperator()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->factory->create('INVALID_OP', ['column', 'value']);
    }
}
