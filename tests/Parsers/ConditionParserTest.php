<?php

namespace Tests\Parsers;

use SqlQuery\Parsers\ConditionParser;
use SqlQuery\Factories\ConditionFactory;
use Tests\TestCase;

class ConditionParserTest extends TestCase
{
    private ConditionParser $parser;

    protected function setUp(): void
    {
        parent::setUp();
        $factory = new ConditionFactory();
        $this->parser = new ConditionParser($factory);
    }

    public function testParseSimpleCondition()
    {
        $condition = ['name', '=', 'John'];
        $result = $this->parser->parse($condition);
        $this->assertNotNull($result);
    }

    public function testParseHashCondition()
    {
        $condition = ['name' => 'John', 'age' => 25];
        $result = $this->parser->parse($condition);
        $this->assertNotNull($result);
    }

    public function testParseStringCondition()
    {
        $condition = 'active';
        $result = $this->parser->parse($condition);
        $this->assertNotNull($result);
    }

    public function testParseCompositeCondition()
    {
        $condition = ['AND', ['name', '=', 'John'], ['age', '>', 18]];
        $result = $this->parser->parse($condition);
        $this->assertNotNull($result);
    }

    public function testParseInCondition()
    {
        $condition = ['status', 'IN', ['active', 'pending']];
        $result = $this->parser->parse($condition);
        $this->assertNotNull($result);
    }

    public function testParseNullCondition()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->parser->parse([]);
    }
}
