<?php

namespace Tests\Utils;

use SqlQuery\Utils\ParseValueType;
use Tests\TestCase;

class ParseValueTypeTest extends TestCase
{
    public function testAddParameter()
    {
        $params = [];
        $result = ParseValueType::addParameter('hello', $params);
        
        $this->assertEquals('?', $result);
        $this->assertCount(1, $params);
        $this->assertEquals('hello', $params[0]);
    }

    public function testAddMultipleParameters()
    {
        $params = [];
        ParseValueType::addParameter('first', $params);
        ParseValueType::addParameter('second', $params);
        
        $this->assertCount(2, $params);
        $this->assertEquals('first', $params[0]);
        $this->assertEquals('second', $params[1]);
    }

    public function testIsSqlLiteral()
    {
        $this->assertTrue(ParseValueType::isSqlLiteral('NOW()'));
        $this->assertTrue(ParseValueType::isSqlLiteral('COUNT(*)'));
        $this->assertFalse(ParseValueType::isSqlLiteral('regular string'));
        $this->assertFalse(ParseValueType::isSqlLiteral(123));
    }

    public function testIsSqlLiteralCaseInsensitive()
    {
        $this->assertTrue(ParseValueType::isSqlLiteral('now()'));
        $this->assertTrue(ParseValueType::isSqlLiteral('count(id)'));
    }
}
