<?php

declare(strict_types=1);

namespace SimpleSAML\Test\XML;

use Exception;
use PHPUnit\Framework\TestCase;
use SimpleSAML\XML\Parser;

/*
 * This file is part of the sgomezsimplesamlphp.
 *
 * (c) Sergio Gómez <sergio@uco.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class ParserTest extends TestCase
{
    private const XMLDOC = <<< XML
<?xml version="1.0" encoding="UTF-8"?>
<Root>
  <Value>Hello, World!</Value>
</Root>
XML;

    /** @var Parser */
    private $xml;


    /**
     * @return void
     */
    protected function setUp()
    {
        $this->xml = new Parser(static::XMLDOC);
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValue
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getValue(): void
    {
        $result = $this->xml->getValue('/Root/Value', true);
        $this->assertEquals(
            'Hello, World!',
            $result
        );
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValue
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getEmptyValue(): void
    {
        $result = $this->xml->getValue('/Root/Foo', false);
        $this->assertEquals(
            null,
            $result
        );
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValue
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getValueException(): void
    {
        $this->expectException(Exception::class);
        $this->xml->getValue('/Root/Foo', true);
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValueDefault
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getDefaultValue(): void
    {
        $result = $this->xml->getValueDefault('/Root/Other', 'Hello');
        $this->assertEquals(
            'Hello',
            $result
        );
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValueAlternatives
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getValueAlternatives(): void
    {
        $result = $this
            ->xml
            ->getValueAlternatives([
                '/Root/Other',
                '/Root/Value'
            ], true)
        ;

        $this->assertEquals(
            'Hello, World!',
            $result
        );
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValueAlternatives
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getEmptyValueAlternatives(): void
    {
        $result = $this
            ->xml
            ->getValueAlternatives([
                '/Root/Foo',
                '/Root/Bar'
            ], false)
        ;

        $this->assertEquals(
            null,
            $result
        );
    }


    /**
     * @covers \SimpleSAML\XML\Parser::getValueAlternatives
     * @covers \SimpleSAML\XML\Parser::__construct
     * @test
     * @return void
     */
    public function getValueAlternativesException(): void
    {
        $this->expectException(Exception::class);
        $this->xml->getValueAlternatives(
            [
                '/Root/Foo',
                '/Root/Bar'
            ],
            true
        );
    }
}
