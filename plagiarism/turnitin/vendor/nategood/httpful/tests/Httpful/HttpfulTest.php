<?php
/**
 * Port over the original tests into a more traditional PHPUnit
 * format.  Still need to hook into a lightweight HTTP server to
 * better test some things (e.g. obscure cURL settings).  I've moved
 * the old tests and node.js server to the tests/.legacy directory.
 *
 * @author Nate Good <me@nategood.com>
 */
namespace Httpful\Test;

require(dirname(dirname(dirname(__FILE__))) . '/bootstrap.php');
\Httpful\Bootstrap::init();

use Httpful\Httpful;
use Httpful\Request;
use Httpful\Mime;
use Httpful\Http;
use Httpful\Response;
use Httpful\Handlers\JsonHandler;

define('TEST_SERVER', WEB_SERVER_HOST . ':' . WEB_SERVER_PORT);

class HttpfulTest extends \PHPUnit\Framework\TestCase
{
    const TEST_SERVER = TEST_SERVER;
    const TEST_URL = 'http://127.0.0.1:8008';
    const TEST_URL_400 = 'http://127.0.0.1:8008/400';

    const SAMPLE_JSON_HEADER =
"HTTP/1.1 200 OK
Content-Type: application/json
Connection: keep-alive
Transfer-Encoding: chunked\r\n";
    const SAMPLE_JSON_HEADER_LOWERCASE =
        "HTTP/2 200 
date: Tue, 07 Jan 2020 09:11:21 GMT
content-type: application/json
content-length: 513
access-control-allow-origin: *
access-control-allow-methods: GET, POST, PUT, PATCH, DELETE
access-control-allow-headers: Authorization, Content-Type, Accept-Encoding, Cache-Control, DNT
cache-control: private, must-revalidate\r\n";
    const SAMPLE_JSON_RESPONSE = '{"key":"value","object":{"key":"value"},"array":[1,2,3,4]}';
    const SAMPLE_CSV_HEADER =
"HTTP/1.1 200 OK
Content-Type: text/csv
Connection: keep-alive
Transfer-Encoding: chunked\r\n";
    const SAMPLE_CSV_RESPONSE =
"Key1,Key2
Value1,Value2
\"40.0\",\"Forty\"";
    const SAMPLE_XML_RESPONSE = '<stdClass><arrayProp><array><k1><myClass><intProp>2</intProp></myClass></k1></array></arrayProp><stringProp>a string</stringProp><boolProp>TRUE</boolProp></stdClass>';
    const SAMPLE_XML_HEADER =
"HTTP/1.1 200 OK
Content-Type: application/xml
Connection: keep-alive
Transfer-Encoding: chunked\r\n";
    const SAMPLE_VENDOR_HEADER =
"HTTP/1.1 200 OK
Content-Type: application/vnd.nategood.message+xml
Connection: keep-alive
Transfer-Encoding: chunked\r\n";
    const SAMPLE_VENDOR_TYPE = "application/vnd.nategood.message+xml";
    const SAMPLE_MULTI_HEADER =
"HTTP/1.1 200 OK
Content-Type: application/json
Connection: keep-alive
Transfer-Encoding: chunked
X-My-Header:Value1
X-My-Header:Value2\r\n";

    function testInit()
    {
      $r = Request::init();
      // Did we get a 'Request' object?
      $this->assertEquals('Httpful\Request', get_class($r));
    }

    function testDetermineLength()
    {
      $r = Request::init();
      $this->assertEquals(1, $r->_determineLength('A'));
      $this->assertEquals(2, $r->_determineLength('À'));
      $this->assertEquals(2, $r->_determineLength('Ab'));
      $this->assertEquals(3, $r->_determineLength('Àb'));
      $this->assertEquals(6, $r->_determineLength('世界'));
    }

    function testMethods()
    {
      $valid_methods = array('get', 'post', 'delete', 'put', 'options', 'head');
      $url = 'http://example.com/';
      foreach ($valid_methods as $method) {
        $r = call_user_func(array('Httpful\Request', $method), $url);
        $this->assertEquals('Httpful\Request', get_class($r));
        $this->assertEquals(strtoupper($method), $r->method);
      }
    }

    function testDefaults()
    {
        // Our current defaults are as follows
        $r = Request::init();
        $this->assertEquals(Http::GET, $r->method);
        $this->assertFalse($r->strict_ssl);
    }

    function testShortMime()
    {
        // Valid short ones
        $this->assertEquals(Mime::JSON,  Mime::getFullMime('json'));
        $this->assertEquals(Mime::XML,   Mime::getFullMime('xml'));
        $this->assertEquals(Mime::HTML,  Mime::getFullMime('html'));
        $this->assertEquals(Mime::CSV,  Mime::getFullMime('csv'));
        $this->assertEquals(Mime::UPLOAD,  Mime::getFullMime('upload'));

        // Valid long ones
        $this->assertEquals(Mime::JSON, Mime::getFullMime(Mime::JSON));
        $this->assertEquals(Mime::XML,  Mime::getFullMime(Mime::XML));
        $this->assertEquals(Mime::HTML, Mime::getFullMime(Mime::HTML));
        $this->assertEquals(Mime::CSV, Mime::getFullMime(Mime::CSV));
        $this->assertEquals(Mime::UPLOAD, Mime::getFullMime(Mime::UPLOAD));

        // No false positives
        $this->assertNotEquals(Mime::XML,  Mime::getFullMime(Mime::HTML));
        $this->assertNotEquals(Mime::JSON, Mime::getFullMime(Mime::XML));
        $this->assertNotEquals(Mime::HTML, Mime::getFullMime(Mime::JSON));
        $this->assertNotEquals(Mime::XML, Mime::getFullMime(Mime::CSV));
    }

    function testSettingStrictSsl()
    {
        $r = Request::init()
             ->withStrictSsl();

        $this->assertTrue($r->strict_ssl);

        $r = Request::init()
             ->withoutStrictSsl();

        $this->assertFalse($r->strict_ssl);
    }

    function testSendsAndExpectsType()
    {
        $r = Request::init()
            ->sendsAndExpectsType(Mime::JSON);
        $this->assertEquals(Mime::JSON, $r->expected_type);
        $this->assertEquals(Mime::JSON, $r->content_type);

        $r = Request::init()
            ->sendsAndExpectsType('html');
        $this->assertEquals(Mime::HTML, $r->expected_type);
        $this->assertEquals(Mime::HTML, $r->content_type);

        $r = Request::init()
            ->sendsAndExpectsType('form');
        $this->assertEquals(Mime::FORM, $r->expected_type);
        $this->assertEquals(Mime::FORM, $r->content_type);

        $r = Request::init()
            ->sendsAndExpectsType('application/x-www-form-urlencoded');
        $this->assertEquals(Mime::FORM, $r->expected_type);
        $this->assertEquals(Mime::FORM, $r->content_type);

        $r = Request::init()
            ->sendsAndExpectsType(Mime::CSV);
        $this->assertEquals(Mime::CSV, $r->expected_type);
        $this->assertEquals(Mime::CSV, $r->content_type);
    }

    function testIni()
    {
        // Test setting defaults/templates

        // Create the template
        $template = Request::init()
            ->method(Http::POST)
            ->withStrictSsl()
            ->expectsType(Mime::HTML)
            ->sendsType(Mime::FORM);

        Request::ini($template);

        $r = Request::init();

        $this->assertTrue($r->strict_ssl);
        $this->assertEquals(Http::POST, $r->method);
        $this->assertEquals(Mime::HTML, $r->expected_type);
        $this->assertEquals(Mime::FORM, $r->content_type);

        // Test the default accessor as well
        $this->assertTrue(Request::d('strict_ssl'));
        $this->assertEquals(Http::POST, Request::d('method'));
        $this->assertEquals(Mime::HTML, Request::d('expected_type'));
        $this->assertEquals(Mime::FORM, Request::d('content_type'));

        Request::resetIni();
    }

    function testAccept()
    {
        $r = Request::get('http://example.com/')
            ->expectsType(Mime::JSON);

        $this->assertEquals(Mime::JSON, $r->expected_type);
        $r->_curlPrep();
        $this->assertStringContainsString('application/json', $r->raw_headers);
    }

    function testCustomAccept()
    {
        $accept = 'application/api-1.0+json';
        $r = Request::get('http://example.com/')
            ->addHeader('Accept', $accept);

        $r->_curlPrep();
        $this->assertStringContainsString($accept, $r->raw_headers);
        $this->assertEquals($accept, $r->headers['Accept']);
    }

    function testUserAgent()
    {
        $r = Request::get('http://example.com/')
            ->withUserAgent('ACME/1.2.3');

        $this->assertArrayHasKey('User-Agent', $r->headers);
        $r->_curlPrep();
        $this->assertStringContainsString('User-Agent: ACME/1.2.3', $r->raw_headers);
        $this->assertStringNotContainsString('User-Agent: HttpFul/1.0', $r->raw_headers);

        $r = Request::get('http://example.com/')
            ->withUserAgent('');

        $this->assertArrayHasKey('User-Agent', $r->headers);
        $r->_curlPrep();
        $this->assertStringContainsString('User-Agent:', $r->raw_headers);
        $this->assertStringNotContainsString('User-Agent: HttpFul/1.0', $r->raw_headers);
    }

    function testAuthSetup()
    {
        $username = 'nathan';
        $password = 'opensesame';

        $r = Request::get('http://example.com/')
            ->authenticateWith($username, $password);

        $this->assertEquals($username, $r->username);
        $this->assertEquals($password, $r->password);
        $this->assertTrue($r->hasBasicAuth());
    }

    function testDigestAuthSetup()
    {
        $username = 'nathan';
        $password = 'opensesame';

        $r = Request::get('http://example.com/')
            ->authenticateWithDigest($username, $password);

        $this->assertEquals($username, $r->username);
        $this->assertEquals($password, $r->password);
        $this->assertTrue($r->hasDigestAuth());
    }

    function testJsonResponseParse()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);

        $this->assertEquals("value", $response->body->key);
        $this->assertEquals("value", $response->body->object->key);
        $this->assertIsArray( $response->body->array);
        $this->assertEquals(1, $response->body->array[0]);
    }

    function testJsonResponseParseLowercaseHeaders()
    {
        $req = Request::init();
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER_LOWERCASE, $req);

        $this->assertEquals("value", $response->body->key);
        $this->assertEquals("value", $response->body->object->key);
        $this->assertIsArray( $response->body->array);
        $this->assertEquals(1, $response->body->array[0]);
    }

    function testXMLResponseParse()
    {
        $req = Request::init()->sendsAndExpects(Mime::XML);
        $response = new Response(self::SAMPLE_XML_RESPONSE, self::SAMPLE_XML_HEADER, $req);
        $sxe = $response->body;
        $this->assertEquals("object", gettype($sxe));
        $this->assertEquals("SimpleXMLElement", get_class($sxe));
        $bools = $sxe->xpath('/stdClass/boolProp');
        // list( , $bool ) = each($bools);
        $bool = array_shift($bools);
        $this->assertEquals("TRUE", (string) $bool);
        $ints = $sxe->xpath('/stdClass/arrayProp/array/k1/myClass/intProp');
        // list( , $int ) = each($ints);
        $int = array_shift($ints);
        $this->assertEquals("2", (string) $int);
        $strings = $sxe->xpath('/stdClass/stringProp');
        // list( , $string ) = each($strings);
        $string = array_shift($strings);
        $this->assertEquals("a string", (string) $string);
    }

    function testCsvResponseParse()
    {
        $req = Request::init()->sendsAndExpects(Mime::CSV);
        $response = new Response(self::SAMPLE_CSV_RESPONSE, self::SAMPLE_CSV_HEADER, $req);

        $this->assertEquals("Key1", $response->body[0][0]);
        $this->assertEquals("Value1", $response->body[1][0]);
        $this->assertIsString( $response->body[2][0]);
        $this->assertEquals("40.0", $response->body[2][0]);
    }

    function testParsingContentTypeCharset()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        // $response = new Response(SAMPLE_JSON_RESPONSE, "", $req);
        // // Check default content type of iso-8859-1
        $response = new Response(self::SAMPLE_JSON_RESPONSE, "HTTP/1.1 200 OK
Content-Type: text/plain; charset=utf-8\r\n", $req);
        $this->assertInstanceOf('Httpful\Response\Headers', $response->headers);
        $this->assertEquals($response->headers['Content-Type'], 'text/plain; charset=utf-8');
        $this->assertEquals($response->content_type, 'text/plain');
        $this->assertEquals($response->charset, 'utf-8');
    }

    function testParsingContentTypeUpload()
    {
        $req = Request::init();

        $req->sendsType(Mime::UPLOAD);
        // $response = new Response(SAMPLE_JSON_RESPONSE, "", $req);
        // // Check default content type of iso-8859-1
        $this->assertEquals($req->content_type, 'multipart/form-data');
    }

    function testAttach() {
        $req = Request::init();
        $testsPath = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . '..');
        $filename = $testsPath . DIRECTORY_SEPARATOR . 'test_image.jpg';
        $req->attach(array('index' => $filename));
        $payload = $req->payload['index'];
        // PHP 5.5  + will take advantage of CURLFile while previous
        // versions just use the string syntax
        if (is_string($payload)) {
            $this->assertEquals($payload, '@' . $filename . ';type=image/jpeg');
        } else {
            $this->assertInstanceOf('CURLFile', $payload);
        }

        $this->assertEquals($req->content_type, Mime::UPLOAD);
        $this->assertEquals($req->serialize_payload_method, Request::SERIALIZE_PAYLOAD_NEVER);
    }

    function testIsUpload() {
        $req = Request::init();

        $req->sendsType(Mime::UPLOAD);

        $this->assertTrue($req->isUpload());
    }

    function testEmptyResponseParse()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response("", self::SAMPLE_JSON_HEADER, $req);
        $this->assertEquals(null, $response->body);

        $reqXml = Request::init()->sendsAndExpects(Mime::XML);
        $responseXml = new Response("", self::SAMPLE_XML_HEADER, $reqXml);
        $this->assertEquals(null, $responseXml->body);
    }

    function testNoAutoParse()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON)->withoutAutoParsing();
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $this->assertIsString( $response->body);
        $req = Request::init()->sendsAndExpects(Mime::JSON)->withAutoParsing();
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $this->assertIsObject($response->body);
    }

    function testParseHeaders()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $this->assertEquals('application/json', $response->headers['Content-Type']);
    }

    function testRawHeaders()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $this->assertStringContainsString('Content-Type: application/json', $response->raw_headers);
    }

    function testHasErrors()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response('', "HTTP/1.1 100 Continue\r\n", $req);
        $this->assertFalse($response->hasErrors());
        $response = new Response('', "HTTP/1.1 200 OK\r\n", $req);
        $this->assertFalse($response->hasErrors());
        $response = new Response('', "HTTP/1.1 300 Multiple Choices\r\n", $req);
        $this->assertFalse($response->hasErrors());
        $response = new Response('', "HTTP/1.1 400 Bad Request\r\n", $req);
        $this->assertTrue($response->hasErrors());
        $response = new Response('', "HTTP/1.1 500 Internal Server Error\r\n", $req);
        $this->assertTrue($response->hasErrors());
    }

    function testWhenError() {
        $caught = false;

        try {
            Request::get('malformed:url')
                ->whenError(function($error) use(&$caught) {
                    $caught = true;
                })
                ->timeoutIn(0.1)
                ->send();
        } catch (\Httpful\Exception\ConnectionErrorException $e) {}

        $this->assertTrue($caught);
    }

    function testBeforeSend() {
        $invoked = false;
        $changed = false;
        $self = $this;

        try {
            Request::get('malformed://url')
                ->beforeSend(function($request) use(&$invoked,$self) {
                    $self->assertEquals('malformed://url', $request->uri);
                    $self->assertEquals('A payload', $request->serialized_payload);
                    $request->uri('malformed2://url');
                    $invoked = true;
                })
                ->whenError(function($error) { /* Be silent */ })
                ->body('A payload')
                ->send();
        } catch (\Httpful\Exception\ConnectionErrorException $e) {
            $this->assertTrue(strpos($e->getMessage(), 'malformed2') !== false);
            $changed = true;
        }

        $this->assertTrue($invoked);
        $this->assertTrue($changed);
    }

    function test_parseCode()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $code = $response->_parseCode("HTTP/1.1 406 Not Acceptable\r\n");
        $this->assertEquals(406, $code);
    }

    function testToString()
    {
        $req = Request::init()->sendsAndExpects(Mime::JSON);
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $this->assertEquals(self::SAMPLE_JSON_RESPONSE, (string)$response);
    }

    function test_parseHeaders()
    {
        $parse_headers = Response\Headers::fromString(self::SAMPLE_JSON_HEADER);
        $this->assertCount(3, $parse_headers);
        $this->assertEquals('application/json', $parse_headers['Content-Type']);
        $this->assertTrue(isset($parse_headers['Connection']));
    }

    function testMultiHeaders()
    {
        $req = Request::init();
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_MULTI_HEADER, $req);
        $parse_headers = $response->_parseHeaders(self::SAMPLE_MULTI_HEADER);
        $this->assertEquals('Value1,Value2', $parse_headers['X-My-Header']);
    }

    function testDetectContentType()
    {
        $req = Request::init();
        $response = new Response(self::SAMPLE_JSON_RESPONSE, self::SAMPLE_JSON_HEADER, $req);
        $this->assertEquals('application/json', $response->headers['Content-Type']);
    }

    function testMissingBodyContentType()
    {
        $body = 'A string';
        $request = Request::post(HttpfulTest::TEST_URL, $body)->_curlPrep();
        $this->assertEquals($body, $request->serialized_payload);
    }

    function testParentType()
    {
        // Parent type
        $request = Request::init()->sendsAndExpects(Mime::XML);
        $response = new Response('<xml><name>Nathan</name></xml>', self::SAMPLE_VENDOR_HEADER, $request);

        $this->assertEquals("application/xml", $response->parent_type);
        $this->assertEquals(self::SAMPLE_VENDOR_TYPE, $response->content_type);
        $this->assertTrue($response->is_mime_vendor_specific);

        // Make sure we still parsed as if it were plain old XML
        $this->assertEquals("Nathan", $response->body->name->__toString());
    }

    function testMissingContentType()
    {
        // Parent type
        $request = Request::init()->sendsAndExpects(Mime::XML);
        $response = new Response('<xml><name>Nathan</name></xml>',
"HTTP/1.1 200 OK
Connection: keep-alive
Transfer-Encoding: chunked\r\n", $request);

        $this->assertEquals("", $response->content_type);
    }

    function testCustomMimeRegistering()
    {
        // Register new mime type handler for "application/vnd.nategood.message+xml"
        Httpful::register(self::SAMPLE_VENDOR_TYPE, new DemoMimeHandler());

        $this->assertTrue(Httpful::hasParserRegistered(self::SAMPLE_VENDOR_TYPE));

        $request = Request::init();
        $response = new Response('<xml><name>Nathan</name></xml>', self::SAMPLE_VENDOR_HEADER, $request);

        $this->assertEquals(self::SAMPLE_VENDOR_TYPE, $response->content_type);
        $this->assertEquals('custom parse', $response->body);
    }

    public function testShorthandMimeDefinition()
    {
        $r = Request::init()->expects('json');
        $this->assertEquals(Mime::JSON, $r->expected_type);

        $r = Request::init()->expectsJson();
        $this->assertEquals(Mime::JSON, $r->expected_type);
    }

    public function testOverrideXmlHandler()
    {
        // Lazy test...
        $prev = \Httpful\Httpful::get(\Httpful\Mime::XML);
        $this->assertEquals($prev, new \Httpful\Handlers\XmlHandler());
        $conf = array('namespace' => 'http://example.com');
        \Httpful\Httpful::register(\Httpful\Mime::XML, new \Httpful\Handlers\XmlHandler($conf));
        $new = \Httpful\Httpful::get(\Httpful\Mime::XML);
        $this->assertNotEquals($prev, $new);
    }

    public function testHasProxyWithoutProxy()
    {
        $r = Request::get('someUrl');
        $this->assertFalse($r->hasProxy());
    }

    public function testHasProxyWithProxy()
    {
        $r = Request::get('some_other_url');
        $r->useProxy('proxy.com');
        $this->assertTrue($r->hasProxy());
    }

    public function testHasProxyWithEnvironmentProxy()
    {
        putenv('http_proxy=http://127.0.0.1:300/');
        $r = Request::get('some_other_url');
        $this->assertTrue($r->hasProxy());
    }


    public function testParseJSON()
    {
        $handler = new JsonHandler();

        $bodies = array(
            'foo',
            array(),
            array('foo', 'bar'),
            null
        );
        foreach ($bodies as $body) {
            $this->assertEquals($body, $handler->parse(json_encode($body)));
        }

        try {
            $result = $handler->parse('invalid{json');
        } catch (\Httpful\Exception\JsonParseException $e) {
            $this->assertEquals('Unable to parse response as JSON: ' . json_last_error_msg(), $e->getMessage());
            return;
        }
        $this->fail('Expected an exception to be thrown due to invalid json');
    }

    // /**
    //  * Skeleton for testing against the 5.4 baked in server
    //  */
    // public function testLocalServer()
    // {
    //     if (!defined('WITHOUT_SERVER') || (defined('WITHOUT_SERVER') && !WITHOUT_SERVER)) {
    //         // PHP test server seems to always set content type to application/octet-stream
    //         // so force parsing as JSON here
    //         Httpful::register('application/octet-stream', new \Httpful\Handlers\JsonHandler());
    //         $response = Request::get(TEST_SERVER . '/test.json')
    //             ->sendsAndExpects(MIME::JSON);
    //         $response->send();
    //         $this->assertTrue(...);
    //     }
    // }
}

class DemoMimeHandler extends \Httpful\Handlers\MimeHandlerAdapter
{
    public function parse($body)
    {
        return 'custom parse';
    }
}

