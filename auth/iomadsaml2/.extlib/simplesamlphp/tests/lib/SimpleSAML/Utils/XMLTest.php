<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use DOMComment;
use DOMDocument;
use DOMElement;
use DOMException;
use DOMText;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Utils\XML;

/**
 * Tests for SimpleSAML\Utils\XML.
 */
class XMLTest extends TestCase
{
    private const FRAMEWORK = 'vendor/simplesamlphp/simplesamlphp-test-framework';

    /**
     * @covers \SimpleSAML\Utils\XML::isDOMNodeOfType
     * @test
     * @return void
     */
    public function testIsDomNodeOfTypeBasic(): void
    {
        $name = 'name';
        $namespace_uri = 'ns';
        $element = new DOMElement($name, 'value', $namespace_uri);

        $res = XML::isDOMNodeOfType($element, $name, $namespace_uri);

        $this->assertTrue($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isDOMNodeOfType
     * @test
     * @return void
     */
    public function testIsDomNodeOfTypeMissingNamespace(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $name = 'name';
        $namespace_uri = '@missing';
        $element = new DOMElement($name, 'value', $namespace_uri);

        XML::isDOMNodeOfType($element, $name, $namespace_uri);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isDOMNodeOfType
     * @test
     * @return void
     */
    public function testIsDomNodeOfTypeEmpty(): void
    {
        $name = 'name';
        $namespace_uri = '';
        $element = new DOMElement($name);

        $res = XML::isDOMNodeOfType($element, $name, $namespace_uri);

        $this->assertFalse($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isDOMNodeOfType
     * @test
     * @return void
     */
    public function testIsDomNodeOfTypeShortcut(): void
    {
        $name = 'name';
        $namespace_uri = 'urn:oasis:names:tc:SAML:2.0:metadata';
        $short_namespace_uri = '@md';
        $element = new DOMElement($name, 'value', $namespace_uri);

        $res = XML::isDOMNodeOfType($element, $name, $short_namespace_uri);

        $this->assertTrue($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isDOMNodeOfType
     * @test
     * @return void
     */
    public function testIsDomNodeOfTypeIncorrectName(): void
    {
        $name = 'name';
        $bad_name = 'bad name';
        $namespace_uri = 'ns';
        $element = new DOMElement($name, 'value', $namespace_uri);

        $res = XML::isDOMNodeOfType($element, $bad_name, $namespace_uri);

        $this->assertFalse($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isDOMNodeOfType
     * @test
     * @return void
     */
    public function testIsDomNodeOfTypeIncorrectNamespace(): void
    {
        $name = 'name';
        $namespace_uri = 'ns';
        $bad_namespace_uri = 'bad name';
        $element = new DOMElement($name, 'value', $namespace_uri);

        $res = XML::isDOMNodeOfType($element, $name, $bad_namespace_uri);

        $this->assertFalse($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::getDOMText
     * @test
     * @return void
     */
    public function testGetDomTextBasic(): void
    {
        $data = 'root value';
        $dom = new DOMDocument();
        $element = $dom->appendChild(new \DOMElement('root'));
        $element->appendChild(new DOMText($data));

        $res = XML::getDOMText($element);
        $expected = $data;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::getDOMText
     * @test
     * @return void
     */
    public function testGetDomTextMulti(): void
    {
        $data1 = 'root value 1';
        $data2 = 'root value 2';
        $dom = new DOMDocument();
        $element = $dom->appendChild(new DOMElement('root'));
        $element->appendChild(new DOMText($data1));
        $element->appendChild(new DOMText($data2));

        $res = XML::getDOMText($element);
        $expected = $data1 . $data2 . $data1 . $data2;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::getDOMText
     * @test
     * @return void
     */
    public function testGetDomTextIncorrectType(): void
    {
        $this->expectException(Error\Exception::class);
        $dom = new DOMDocument();
        $element = $dom->appendChild(new DOMElement('root'));
        $element->appendChild(new DOMComment(''));

        XML::getDOMText($element);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::getDOMChildren
     * @test
     * @return void
     */
    public function testGetDomChildrenBasic(): void
    {
        $name = 'name';
        $namespace_uri = 'ns';
        $dom = new DOMDocument();
        $element = new DOMElement($name, 'value', $namespace_uri);
        $dom->appendChild($element);

        $res = XML::getDOMChildren($dom, $name, $namespace_uri);
        $expected = [$element];

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::getDOMChildren
     * @test
     * @return void
     */
    public function testGetDomChildrenIncorrectType(): void
    {
        $dom = new DOMDocument();
        $text = new DOMText('text');
        $comment = new DOMComment('comment');
        $dom->appendChild($text);
        $dom->appendChild($comment);

        $res = XML::getDOMChildren($dom, 'name', 'ns');

        $this->assertEmpty($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::getDOMChildren
     * @test
     * @return void
     */
    public function testGetDomChildrenIncorrectName(): void
    {
        $name = 'name';
        $bad_name = 'bad name';
        $namespace_uri = 'ns';
        $dom = new DOMDocument();
        $element = new DOMElement($name, 'value', $namespace_uri);
        $dom->appendChild($element);

        $res = XML::getDOMChildren($dom, $bad_name, $namespace_uri);

        $this->assertEmpty($res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::formatDOMElement
     * @test
     * @return void
     */
    public function testFormatDomElementBasic(): void
    {
        $dom = new DOMDocument();
        $root = new DOMElement('root');
        $dom->appendChild($root);
        $root->appendChild(new \DOMText('text'));

        XML::formatDOMElement($root);
        $res = $dom->saveXML();
        $expected = <<<'NOWDOC'
<?xml version="1.0"?>
<root>text</root>

NOWDOC;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::formatDOMElement
     * @test
     * @return void
     */
    public function testFormatDomElementNested(): void
    {
        $dom = new DOMDocument();
        $root = new DOMElement('root');
        $nested = new DOMElement('nested');
        $dom->appendChild($root);
        $root->appendChild($nested);
        $nested->appendChild(new DOMText('text'));

        XML::formatDOMElement($root);
        $res = $dom->saveXML();
        $expected = <<<'NOWDOC'
<?xml version="1.0"?>
<root>
  <nested>text</nested>
</root>

NOWDOC;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::formatDOMElement
     * @test
     * @return void
     */
    public function testFormatDomElementIndentBase(): void
    {
        $indent_base = 'base';
        $dom = new DOMDocument();
        $root = new DOMElement('root');
        $nested = new DOMElement('nested');
        $dom->appendChild($root);
        $root->appendChild($nested);
        $nested->appendChild(new DOMText('text'));

        XML::formatDOMElement($root, $indent_base);
        $res = $dom->saveXML();
        $expected = <<<HEREDOC
<?xml version="1.0"?>
<root>
$indent_base  <nested>text</nested>
$indent_base</root>

HEREDOC;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::formatDOMElement
     * @test
     * @return void
     */
    public function testFormatDomElementTextAndChild(): void
    {
        $dom = new DOMDocument();
        $root = new DOMElement('root');
        $dom->appendChild($root);
        $root->appendChild(new DOMText('text'));
        $root->appendChild(new DOMElement('child'));

        XML::formatDOMElement($root);
        $res = $dom->saveXML();
        $expected = <<<HEREDOC
<?xml version="1.0"?>
<root>text<child/></root>

HEREDOC;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::formatXMLString
     * @test
     * @return void
     */
    public function testFormatXmlStringBasic(): void
    {
        $xml = '<root><nested>text</nested></root>';

        $res = XML::formatXMLString($xml);
        $expected = <<<'NOWDOC'
<root>
  <nested>text</nested>
</root>
NOWDOC;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::formatXMLString
     * @test
     * @return void
     */
    public function testFormatXmlStringMalformedXml(): void
    {
        $this->expectException(DOMException::class);
        $xml = '<root><nested>text';

        XML::formatXMLString($xml);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isValid
     * @test
     * @return void
     */
    public function testIsValidMalformedXml(): void
    {
        $xml = '<root><nested>text';

        $res = XML::isValid($xml, 'unused');
        $this->assertIsString($res);

        $expected = 'Failed to parse XML string for schema validation';
        $this->assertContains($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\XML::isValid
     * @return void
     */
    public function testIsValidMetadata(): void
    {
        $schema = 'saml-schema-metadata-2.0.xsd';
        $xml = file_get_contents(self::FRAMEWORK . '/metadata/xml/valid-metadata-selfsigned.xml');

        $dom = new DOMDocument('1.0');
        $dom->loadXML($xml, LIBXML_NONET);

        $res = XML::isValid($dom, $schema);
        $this->assertTrue($res === true);
    }

    /**
     * @covers \SimpleSAML\Utils\XML::checkSAMLMessage()
     * @return void
     */
    public function testCheckSAMLMessageInvalidType(): void
    {
        $this->expectException(InvalidArgumentException::class);
        XML::checkSAMLMessage('<test></test>', 'blub');
    }
}
