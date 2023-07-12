<?php

declare(strict_types=1);

namespace SimpleSAML\Test;

use Exception;
use SAML2\Constants;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Test\Utils\ClearStateTestCase;

/**
 * Tests for \SimpleSAML\Configuration
 */
class ConfigurationTest extends ClearStateTestCase
{
    /**
     * Test \SimpleSAML\Configuration::getVersion()
     * @return void
     */
    public function testGetVersion(): void
    {
        $c = Configuration::getOptionalConfig();
        $this->assertEquals($c->getVersion(), Configuration::VERSION);
    }


    /**
     * Test that the default instance fails to load even if we previously loaded another instance.
     * @return void
     */
    public function testLoadDefaultInstance(): void
    {
        $this->expectException(Error\CriticalConfigurationError::class);
        Configuration::loadFromArray(['key' => 'value'], '', 'dummy');
        Configuration::getInstance();
    }


    /**
     * Test that after a \SimpleSAML\Error\CriticalConfigurationError exception, a basic, self-survival configuration
     * is loaded.
     * @return void
     */
    public function testCriticalConfigurationError(): void
    {
        try {
            Configuration::getInstance();
            $this->fail('Exception expected');
        } catch (Error\CriticalConfigurationError $var) {
            // This exception is expected.
        }
        /*
         * After the above failure an emergency configuration is create to allow core SSP components to function and
         * possibly log/display the error.
         */
        $c = Configuration::getInstance();
        $this->assertNotEmpty($c->toArray());
    }


    /**
     * Test \SimpleSAML\Configuration::getValue()
     * @return void
     */
    public function testGetValue(): void
    {
        $c = Configuration::loadFromArray([
            'exists_true' => true,
            'exists_null' => null,
        ]);
        $this->assertEquals($c->getValue('missing'), null);
        $this->assertEquals($c->getValue('missing', true), true);
        $this->assertEquals($c->getValue('missing', true), true);

        $this->assertEquals($c->getValue('exists_true'), true);

        $this->assertEquals($c->getValue('exists_null'), null);
        $this->assertEquals($c->getValue('exists_null', false), null);
    }


    /**
     * Test \SimpleSAML\Configuration::getValue(), REQUIRED_OPTION flag.
     * @return void
     */
    public function testGetValueRequired(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([]);
        $c->getValue('missing', Configuration::REQUIRED_OPTION);
    }


    /**
     * Test \SimpleSAML\Configuration::hasValue()
     * @return void
     */
    public function testHasValue(): void
    {
        $c = Configuration::loadFromArray([
            'exists_true' => true,
            'exists_null' => null,
        ]);
        $this->assertEquals($c->hasValue('missing'), false);
        $this->assertEquals($c->hasValue('exists_true'), true);
        $this->assertEquals($c->hasValue('exists_null'), true);
    }


    /**
     * Test \SimpleSAML\Configuration::hasValue()
     * @return void
     */
    public function testHasValueOneOf(): void
    {
        $c = Configuration::loadFromArray([
            'exists_true' => true,
            'exists_null' => null,
        ]);
        $this->assertEquals($c->hasValueOneOf([]), false);
        $this->assertEquals($c->hasValueOneOf(['missing']), false);
        $this->assertEquals($c->hasValueOneOf(['exists_true']), true);
        $this->assertEquals($c->hasValueOneOf(['exists_null']), true);

        $this->assertEquals($c->hasValueOneOf(['missing1', 'missing2']), false);
        $this->assertEquals($c->hasValueOneOf(['exists_true', 'missing']), true);
        $this->assertEquals($c->hasValueOneOf(['missing', 'exists_true']), true);
    }


    /**
     * Test \SimpleSAML\Configuration::getBasePath()
     * @return void
     */
    public function testGetBasePath(): void
    {
        $c = Configuration::loadFromArray([]);
        $this->assertEquals($c->getBasePath(), '/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'simplesaml/']);
        $this->assertEquals($c->getBasePath(), '/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => '/simplesaml/']);
        $this->assertEquals($c->getBasePath(), '/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'simplesaml']);
        $this->assertEquals($c->getBasePath(), '/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => '/simplesaml']);
        $this->assertEquals($c->getBasePath(), '/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'path/to/simplesaml/']);
        $this->assertEquals($c->getBasePath(), '/path/to/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => '/path/to/simplesaml/']);
        $this->assertEquals($c->getBasePath(), '/path/to/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => '/path/to/simplesaml']);
        $this->assertEquals($c->getBasePath(), '/path/to/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'https://example.org/ssp/']);
        $this->assertEquals($c->getBasePath(), '/ssp/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'https://example.org/']);
        $this->assertEquals($c->getBasePath(), '/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'http://example.org/ssp/']);
        $this->assertEquals($c->getBasePath(), '/ssp/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'http://example.org/ssp/simplesaml']);
        $this->assertEquals($c->getBasePath(), '/ssp/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'http://example.org/ssp/simplesaml/']);
        $this->assertEquals($c->getBasePath(), '/ssp/simplesaml/');

        $c = Configuration::loadFromArray(['baseurlpath' => '']);
        $this->assertEquals($c->getBasePath(), '/');

        $c = Configuration::loadFromArray(['baseurlpath' => '/']);
        $this->assertEquals($c->getBasePath(), '/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'https://example.org:8443']);
        $this->assertEquals($c->getBasePath(), '/');

        $c = Configuration::loadFromArray(['baseurlpath' => 'https://example.org:8443/']);
        $this->assertEquals($c->getBasePath(), '/');
    }


    /**
     * Test \SimpleSAML\Configuration::resolvePath()
     * @return void
     */
    public function testResolvePath(): void
    {
        $c = Configuration::loadFromArray([
            'basedir' => '/basedir/',
        ]);

        $this->assertEquals($c->resolvePath(null), null);
        $this->assertEquals($c->resolvePath('/otherdir'), '/otherdir');
        $this->assertEquals($c->resolvePath('relativedir'), '/basedir/relativedir');

        $this->assertEquals($c->resolvePath('slash/'), '/basedir/slash');
        $this->assertEquals($c->resolvePath('slash//'), '/basedir/slash');

        $this->assertEquals($c->resolvePath('C:\\otherdir'), 'C:/otherdir');
        $this->assertEquals($c->resolvePath('C:/otherdir'), 'C:/otherdir');
    }


    /**
     * Test \SimpleSAML\Configuration::getPathValue()
     * @return void
     */
    public function testGetPathValue(): void
    {
        $c = Configuration::loadFromArray([
            'basedir' => '/basedir/',
            'path_opt' => 'path',
            'slashes_opt' => 'slashes//',
        ]);

        $this->assertEquals($c->getPathValue('missing'), null);
        $this->assertEquals($c->getPathValue('path_opt'), '/basedir/path/');
        $this->assertEquals($c->getPathValue('slashes_opt'), '/basedir/slashes/');
    }


    /**
     * Test \SimpleSAML\Configuration::getBaseDir()
     * @return void
     */
    public function testGetBaseDir(): void
    {
        $c = Configuration::loadFromArray([]);
        $this->assertEquals($c->getBaseDir(), dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR);

        $c = Configuration::loadFromArray([
            'basedir' => DIRECTORY_SEPARATOR . 'basedir',
        ]);
        $this->assertEquals($c->getBaseDir(), DIRECTORY_SEPARATOR . 'basedir' . DIRECTORY_SEPARATOR);

        $c = Configuration::loadFromArray([
            'basedir' => DIRECTORY_SEPARATOR . 'basedir' . DIRECTORY_SEPARATOR,
        ]);
        $this->assertEquals($c->getBaseDir(), DIRECTORY_SEPARATOR . 'basedir' . DIRECTORY_SEPARATOR);
    }


    /**
     * Test \SimpleSAML\Configuration::getBoolean()
     * @return void
     */
    public function testGetBoolean(): void
    {
        $c = Configuration::loadFromArray([
            'true_opt' => true,
            'false_opt' => false,
        ]);
        $this->assertEquals($c->getBoolean('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getBoolean('true_opt', '--missing--'), true);
        $this->assertEquals($c->getBoolean('false_opt', '--missing--'), false);
    }


    /**
     * Test \SimpleSAML\Configuration::getBoolean() missing option
     * @return void
     */
    public function testGetBooleanMissing(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([]);
        $c->getBoolean('missing_opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getBoolean() wrong option
     * @return void
     */
    public function testGetBooleanWrong(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'wrong' => 'true',
        ]);
        $c->getBoolean('wrong');
    }


    /**
     * Test \SimpleSAML\Configuration::getString()
     * @return void
     */
    public function testGetString(): void
    {
        $c = Configuration::loadFromArray([
            'str_opt' => 'Hello World!',
        ]);
        $this->assertEquals($c->getString('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getString('str_opt', '--missing--'), 'Hello World!');
    }


    /**
     * Test \SimpleSAML\Configuration::getString() missing option
     * @return void
     */
    public function testGetStringMissing(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([]);
        $c->getString('missing_opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getString() wrong option
     * @return void
     */
    public function testGetStringWrong(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'wrong' => false,
        ]);
        $c->getString('wrong');
    }


    /**
     * Test \SimpleSAML\Configuration::getInteger()
     * @return void
     */
    public function testGetInteger(): void
    {
        $c = Configuration::loadFromArray([
            'int_opt' => 42,
        ]);
        $this->assertEquals($c->getInteger('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getInteger('int_opt', '--missing--'), 42);
    }


    /**
     * Test \SimpleSAML\Configuration::getInteger() missing option
     * @return void
     */
    public function testGetIntegerMissing(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([]);
        $c->getInteger('missing_opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getInteger() wrong option
     * @return void
     */
    public function testGetIntegerWrong(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'wrong' => '42',
        ]);
        $c->getInteger('wrong');
    }


    /**
     * Test \SimpleSAML\Configuration::getIntegerRange()
     * @return void
     */
    public function testGetIntegerRange(): void
    {
        $c = Configuration::loadFromArray([
            'int_opt' => 42,
        ]);
        $this->assertEquals($c->getIntegerRange('missing_opt', 0, 100, '--missing--'), '--missing--');
        $this->assertEquals($c->getIntegerRange('int_opt', 0, 100), 42);
    }


    /**
     * Test \SimpleSAML\Configuration::getIntegerRange() below limit
     * @return void
     */
    public function testGetIntegerRangeBelow(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'int_opt' => 9,
        ]);
        $this->assertEquals($c->getIntegerRange('int_opt', 10, 100), 42);
    }


    /**
     * Test \SimpleSAML\Configuration::getIntegerRange() above limit
     * @return void
     */
    public function testGetIntegerRangeAbove(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'int_opt' => 101,
        ]);
        $this->assertEquals($c->getIntegerRange('int_opt', 10, 100), 42);
    }


    /**
     * Test \SimpleSAML\Configuration::getValueValidate()
     * @return void
     */
    public function testGetValueValidate(): void
    {
        $c = Configuration::loadFromArray([
            'opt' => 'b',
        ]);
        $this->assertEquals($c->getValueValidate('missing_opt', ['a', 'b', 'c'], '--missing--'), '--missing--');
        $this->assertEquals($c->getValueValidate('opt', ['a', 'b', 'c']), 'b');
    }


    /**
     * Test \SimpleSAML\Configuration::getValueValidate() wrong option
     * @return void
     */
    public function testGetValueValidateWrong(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => 'd',
        ]);
        $c->getValueValidate('opt', ['a', 'b', 'c']);
    }


    /**
     * Test \SimpleSAML\Configuration::getArray()
     * @return void
     */
    public function testGetArray(): void
    {
        $c = Configuration::loadFromArray([
            'opt' => ['a', 'b', 'c'],
        ]);
        $this->assertEquals($c->getArray('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getArray('opt'), ['a', 'b', 'c']);
    }


    /**
     * Test \SimpleSAML\Configuration::getArray() wrong option
     * @return void
     */
    public function testGetArrayWrong(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => 'not_an_array',
        ]);
        $c->getArray('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getArrayize()
     * @return void
     */
    public function testGetArrayize(): void
    {
        $c = Configuration::loadFromArray([
            'opt' => ['a', 'b', 'c'],
            'opt_int' => 42,
            'opt_str' => 'string',
        ]);
        $this->assertEquals($c->getArrayize('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getArrayize('opt'), ['a', 'b', 'c']);
        $this->assertEquals($c->getArrayize('opt_int'), [42]);
        $this->assertEquals($c->getArrayize('opt_str'), ['string']);
    }


    /**
     * Test \SimpleSAML\Configuration::getArrayizeString()
     * @return void
     */
    public function testGetArrayizeString(): void
    {
        $c = Configuration::loadFromArray([
            'opt' => ['a', 'b', 'c'],
            'opt_str' => 'string',
        ]);
        $this->assertEquals($c->getArrayizeString('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getArrayizeString('opt'), ['a', 'b', 'c']);
        $this->assertEquals($c->getArrayizeString('opt_str'), ['string']);
    }


    /**
     * Test \SimpleSAML\Configuration::getArrayizeString() option
     * with an array that contains something that isn't a string.
     * @return void
     */
    public function testGetArrayizeStringWrongValue(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => ['a', 'b', 42],
        ]);
        $c->getArrayizeString('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getConfigItem()
     * @return void
     */
    public function testGetConfigItem(): void
    {
        $c = Configuration::loadFromArray([
            'opt' => ['a' => 42],
        ]);
        $this->assertNull($c->getConfigItem('missing_opt', null));
        $opt = $c->getConfigItem('opt');
        $notOpt = $c->getConfigItem('notOpt');
        $this->assertInstanceOf(Configuration::class, $opt);
        $this->assertInstanceOf(Configuration::class, $notOpt);
        $this->assertEquals($opt->getValue('a'), 42);
    }


    /**
     * Test \SimpleSAML\Configuration::getConfigItem() wrong option
     * @return void
     */
    public function testGetConfigItemWrong(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => 'not_an_array',
        ]);
        $c->getConfigItem('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getConfigList()
     * @return void
     */
    public function testGetConfigList()
    {
        $c = Configuration::loadFromArray([
            'opts' => [
                'a' => ['opt1' => 'value1'],
                'b' => ['opt2' => 'value2'],
            ],
        ]);
        $this->assertEquals($c->getConfigList('missing_opt'), []);
        $opts = $c->getConfigList('opts');
        $this->assertInternalType('array', $opts);
        $this->assertEquals(array_keys($opts), ['a', 'b']);
        $this->assertInstanceOf(Configuration::class, $opts['a']);
        $this->assertEquals($opts['a']->getValue('opt1'), 'value1');
        $this->assertInstanceOf(Configuration::class, $opts['b']);
        $this->assertEquals($opts['b']->getValue('opt2'), 'value2');
    }


    /**
     * Test \SimpleSAML\Configuration::getConfigList() wrong option
     * @return void
     */
    public function testGetConfigListWrong()
    {
        $this->expectException(\Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => 'not_an_array',
        ]);
        $c->getConfigList('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getConfigList() with an array of wrong options.
     * @return void
     */
    public function testGetConfigListWrongArrayValues()
    {
        $this->expectException(\Exception::class);
        $c = Configuration::loadFromArray([
            'opts' => [
                'a',
                'b',
            ],
        ]);
        $c->getConfigList('opts');
    }


    /**
     * Test \SimpleSAML\Configuration::getOptions()
     * @return void
     */
    public function testGetOptions(): void
    {
        $c = Configuration::loadFromArray([
            'a' => true,
            'b' => null,
        ]);
        $this->assertEquals($c->getOptions(), ['a', 'b']);
    }


    /**
     * Test \SimpleSAML\Configuration::toArray()
     * @return void
     */
    public function testToArray(): void
    {
        $c = Configuration::loadFromArray([
            'a' => true,
            'b' => null,
        ]);
        $this->assertEquals($c->toArray(), ['a' => true, 'b' => null]);
    }


    /**
     * Test \SimpleSAML\Configuration::getDefaultEndpoint().
     *
     * Iterate over all different valid definitions of endpoints and check if the expected output is produced.
     * @return void
     */
    public function testGetDefaultEndpoint(): void
    {
        /*
         * First we run the full set of tests covering all possible configurations for indexed endpoint types,
         * basically AssertionConsumerService and ArtifactResolutionService. Since both are the same, we just run the
         * tests for AssertionConsumerService.
         */
        $acs_eps = [
            // just a string with the location
            'https://example.com/endpoint.php',
            // an array of strings with location of different endpoints
            [
                'https://www1.example.com/endpoint.php',
                'https://www2.example.com/endpoint.php',
            ],
            // define location and binding
            [
                [
                    'Location' => 'https://example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_POST,
                ],
            ],
            // define the ResponseLocation too
            [
                [
                    'Location' => 'https://example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_POST,
                    'ResponseLocation' => 'https://example.com/endpoint.php',
                ],
            ],
            // make sure indexes are NOT taken into account (they just identify endpoints)
            [
                [
                    'index' => 1,
                    'Location' => 'https://www1.example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_REDIRECT,
                ],
                [
                    'index' => 2,
                    'Location' => 'https://www2.example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_POST,
                ],
            ],
            // make sure isDefault has priority over indexes
            [
                [
                    'index' => 1,
                    'Location' => 'https://www2.example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_POST,
                ],
                [
                    'index' => 2,
                    'isDefault' => true,
                    'Location' => 'https://www1.example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_REDIRECT,
                ],
            ],
            // make sure endpoints with invalid bindings are ignored and those marked as NOT default are still used
            [
                [
                    'index' => 1,
                    'Location' => 'https://www1.example.com/endpoint.php',
                    'Binding' => 'invalid_binding',
                ],
                [
                    'index' => 2,
                    'isDefault' => false,
                    'Location' => 'https://www2.example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_POST,
                ],
            ],
        ];
        $acs_expected_eps = [
            // output should be completed with the default binding (HTTP-POST for ACS)
            [
                'Location' => 'https://example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_POST,
            ],
            // we should just get the first endpoint with the default binding
            [
                'Location' => 'https://www1.example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_POST,
            ],
            // if we specify the binding, we should get it back
            [
                'Location' => 'https://example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_POST
            ],
            // if we specify ResponseLocation, we should get it back too
            [
                'Location' => 'https://example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_POST,
                'ResponseLocation' => 'https://example.com/endpoint.php',
            ],
            // indexes must NOT be taken into account, order is the only thing that matters here
            [
                'Location' => 'https://www1.example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_REDIRECT,
                'index' => 1,
            ],
            // isDefault must have higher priority than indexes
            [
                'Location' => 'https://www1.example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_REDIRECT,
                'isDefault' => true,
                'index' => 2,
            ],
            // the first valid enpoint should be used even if it's marked as NOT default
            [
                'index' => 2,
                'isDefault' => false,
                'Location' => 'https://www2.example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_POST,
            ]
        ];

        $a = [
            'metadata-set' => 'saml20-sp-remote',
            'ArtifactResolutionService' => 'https://example.com/ars',
            'SingleSignOnService' => 'https://example.com/sso',
            'SingleLogoutService' => [
                'Location' => 'https://example.com/slo',
                'Binding' => 'valid_binding', // test unknown bindings if we don't specify a list of valid ones
            ],
        ];

        $valid_bindings = [
            Constants::BINDING_HTTP_POST,
            Constants::BINDING_HTTP_REDIRECT,
            Constants::BINDING_HOK_SSO,
            Constants::BINDING_HTTP_ARTIFACT,
            Constants::BINDING_SOAP,
        ];

        // run all general tests with AssertionConsumerService endpoint type
        foreach ($acs_eps as $i => $ep) {
            $a['AssertionConsumerService'] = $ep;
            $c = Configuration::loadFromArray($a);
            $this->assertEquals($acs_expected_eps[$i], $c->getDefaultEndpoint(
                'AssertionConsumerService',
                $valid_bindings
            ));
        }

        // now make sure SingleSignOnService, SingleLogoutService and ArtifactResolutionService works fine
        $a['metadata-set'] = 'shib13-idp-remote';
        $c = Configuration::loadFromArray($a);
        $this->assertEquals(
            [
                'Location' => 'https://example.com/sso',
                'Binding' => 'urn:mace:shibboleth:1.0:profiles:AuthnRequest',
            ],
            $c->getDefaultEndpoint('SingleSignOnService')
        );
        $a['metadata-set'] = 'saml20-idp-remote';
        $c = Configuration::loadFromArray($a);
        $this->assertEquals(
            [
                'Location' => 'https://example.com/ars',
                'Binding' => Constants::BINDING_SOAP,
            ],
            $c->getDefaultEndpoint('ArtifactResolutionService')
        );
        $this->assertEquals(
            [
                'Location' => 'https://example.com/slo',
                'Binding' => Constants::BINDING_HTTP_REDIRECT,
            ],
            $c->getDefaultEndpoint('SingleLogoutService')
        );

        // test for old shib1.3 AssertionConsumerService
        $a['metadata-set'] = 'shib13-sp-remote';
        $a['AssertionConsumerService'] = 'https://example.com/endpoint.php';
        $c = Configuration::loadFromArray($a);
        $this->assertEquals(
            [
                'Location' => 'https://example.com/endpoint.php',
                'Binding' => 'urn:oasis:names:tc:SAML:1.0:profiles:browser-post',
            ],
            $c->getDefaultEndpoint('AssertionConsumerService')
        );

        // test for no valid endpoints specified
        $a['SingleLogoutService'] = [
            [
                'Location' => 'https://example.com/endpoint.php',
                'Binding' => 'invalid_binding',
                'isDefault' => true,
            ],
        ];
        $c = Configuration::loadFromArray($a);
        try {
            $c->getDefaultEndpoint('SingleLogoutService', $valid_bindings);
            $this->fail('Failed to detect invalid endpoint binding.');
        } catch (Exception $e) {
            $this->assertEquals(
                '[ARRAY][\'SingleLogoutService\']:Could not find a supported SingleLogoutService ' . 'endpoint.',
                $e->getMessage()
            );
        }
        $a['metadata-set'] = 'foo';
        $c = Configuration::loadFromArray($a);
        try {
            $c->getDefaultEndpoint('SingleSignOnService');
            $this->fail('No valid metadata set specified.');
        } catch (Exception $e) {
            $this->assertStringStartsWith('Missing default binding for', $e->getMessage());
        }
    }


    /**
     * Test \SimpleSAML\Configuration::getEndpoints().
     * @return void
     */
    public function testGetEndpoints(): void
    {
        // test response location for old-style configurations
        $c = Configuration::loadFromArray([
            'metadata-set' => 'saml20-idp-remote',
            'SingleSignOnService' => 'https://example.com/endpoint.php',
            'SingleSignOnServiceResponse' => 'https://example.com/response.php',
        ]);
        $e = [
            [
                'Location' => 'https://example.com/endpoint.php',
                'Binding' => Constants::BINDING_HTTP_REDIRECT,
                'ResponseLocation' => 'https://example.com/response.php',
            ]
        ];
        $this->assertEquals($e, $c->getEndpoints('SingleSignOnService'));

        // test for input failures

        // define a basic configuration array
        $a = [
            'metadata-set' => 'saml20-idp-remote',
            'SingleSignOnService' => null,
        ];

        // define a set of tests
        $tests = [
            // invalid endpoint definition
            10,
            // invalid definition of endpoint inside the endpoints array
            [
                1234
            ],
            // missing location
            [
                [
                    'foo' => 'bar',
                ],
            ],
            // invalid location
            [
                [
                    'Location' => 1234,
                ]
            ],
            // missing binding
            [
                [
                    'Location' => 'https://example.com/endpoint.php',
                ],
            ],
            // invalid binding
            [
                [
                    'Location' => 'https://example.com/endpoint.php',
                    'Binding' => 1234,
                ],
            ],
            // invalid response location
            [
                [
                    'Location' => 'https://example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_REDIRECT,
                    'ResponseLocation' => 1234,
                ],
            ],
            // invalid index
            [
                [
                    'Location' => 'https://example.com/endpoint.php',
                    'Binding' => Constants::BINDING_HTTP_REDIRECT,
                    'index' => 'string',
                ],
            ],
        ];

        // define a set of exception messages to expect
        $msgs = [
            'Expected array or string.',
            'Expected a string or an array.',
            'Missing Location.',
            'Location must be a string.',
            'Missing Binding.',
            'Binding must be a string.',
            'ResponseLocation must be a string.',
            'index must be an integer.',
        ];

        // now run all the tests expecting the correct exception message
        foreach ($tests as $i => $test) {
            $a['SingleSignOnService'] = $test;
            $c = Configuration::loadFromArray($a);
            try {
                $c->getEndpoints('SingleSignOnService');
            } catch (Exception $e) {
                $this->assertStringEndsWith($msgs[$i], $e->getMessage());
            }
        }
    }


    /**
     * Test \SimpleSAML\Configuration::getLocalizedString()
     * @return void
     */
    public function testGetLocalizedString(): void
    {
        $c = Configuration::loadFromArray([
            'str_opt' => 'Hello World!',
            'str_array' => [
                'en' => 'Hello World!',
                'no' => 'Hei Verden!',
            ],
        ]);
        $this->assertEquals($c->getLocalizedString('missing_opt', '--missing--'), '--missing--');
        $this->assertEquals($c->getLocalizedString('str_opt'), ['en' => 'Hello World!']);
        $this->assertEquals($c->getLocalizedString('str_array'), ['en' => 'Hello World!', 'no' => 'Hei Verden!']);
    }


    /**
     * Test \SimpleSAML\Configuration::getLocalizedString() not array nor simple string
     * @return void
     */
    public function testGetLocalizedStringNotArray(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => 42,
        ]);
        $c->getLocalizedString('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getLocalizedString() not string key
     * @return void
     */
    public function testGetLocalizedStringNotStringKey(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => [42 => 'text'],
        ]);
        $c->getLocalizedString('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getLocalizedString() not string value
     * @return void
     */
    public function testGetLocalizedStringNotStringValue(): void
    {
        $this->expectException(Exception::class);
        $c = Configuration::loadFromArray([
            'opt' => ['en' => 42],
        ]);
        $c->getLocalizedString('opt');
    }


    /**
     * Test \SimpleSAML\Configuration::getConfig() nonexistent file
     * @return void
     */
    public function testGetConfigNonexistentFile(): void
    {
        $this->expectException(Exception::class);
        Configuration::getConfig('nonexistent-nopreload.php');
    }


    /**
     * Test \SimpleSAML\Configuration::getConfig() preloaded nonexistent file
     * @return void
     */
    public function testGetConfigNonexistentFilePreload(): void
    {
        $c = Configuration::loadFromArray([
            'key' => 'value'
        ]);
        $virtualFile = 'nonexistent-preload.php';
        Configuration::setPreLoadedConfig($c, $virtualFile);
        $nc = Configuration::getConfig($virtualFile);
        $this->assertEquals('value', $nc->getValue('key', null));
    }


    /**
     * Test that Configuration objects can be initialized from an array.
     *
     * ATTENTION: this test must be kept the last.
     * @return void
     */
    public function testLoadInstanceFromArray(): void
    {
        $c = [
            'key' => 'value'
        ];
        // test loading a custom instance
        Configuration::loadFromArray($c, '', 'dummy');
        $this->assertEquals('value', Configuration::getInstance('dummy')->getValue('key', null));

        // test loading the default instance
        Configuration::loadFromArray($c, '', 'simplesaml');
        $this->assertEquals('value', Configuration::getInstance()->getValue('key', null));
    }
}
