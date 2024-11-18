<?php
/**
 * Test for the consent:Consent authproc filter.
 *
 * @author Vincent Rioux <vrioux@ctech.ca>
 * @package SimpleSAMLphp
 */

namespace SimpleSAML\Test\Module\consent\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Module\consent\Auth\Process\Consent;

class ConsentTest extends TestCase
{
    /**
     * @return void
     */
    public function setUp()
    {
        $this->config = Configuration::loadFromArray(['module.enable' => ['consent' => true]], '[ARRAY]', 'simplesaml');
        Configuration::setPreLoadedConfig($this->config, 'config.php');
    }


    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param array $config  The filter configuration.
     * @param array $request  The request state.
     * @return array  The state array after processing.
     */
    private function processFilter(array $config, array $request)
    {
        $filter = new Consent($config, null);
        $filter->process($request);
        return $request;
    }


    /**
     * Test for the private checkDisable() method.
     *
     * @return void
     */
    public function testCheckDisable()
    {
        // test consent disable regex with match
        $config = [];

        // test consent disable with match on specific SP entityid
        $request = [
            'Source'     => [
                'entityid' => 'https://idp.example.org',
                'metadata-set' => 'saml20-idp-local',
                'consent.disable' => [
                    'https://valid.flatstring.example.that.does.not.match',
                ],
                'SingleSignOnService' => [
                    [
                        'Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                        'Location' => 'https://idp.example.org/saml2/idp/SSOService.php',
                    ],
                ],
            ],
            'Destination' => [
                // valid entityid equal to the last one in the consent.disable array
                'entityid' => 'https://sp.example.org/my-sp',
                'metadata-set' => 'saml20-sp-remote',
                'consent.disable' => [
                    ['type' => 'regex', 'pattern' => '/invalid/i'],
                    'https://sp.example.org/my-sp', // accept the SP that has this specific entityid
                    'https://idp.example.org',
                ],
            ],
            'UserID' => 'jdoe',
            'Attributes' => [
                'eduPersonPrincipalName' => ['jdoe@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        // the state should NOT have changed because NO consent should be necessary (match)
        $this->assertEquals($request, $result);

        // test consent disable with match on SP through regular expression
        $request = [
            'Source'     => [
                'entityid' => 'https://idp.example.org',
                'metadata-set' => 'saml20-idp-local',
                'consent.disable' => [
                    [], // invalid consent option array should be ignored
                    1234, // bad option
                    [''], // no type
                    ['type'=>'invalid'], // invalid consent option type should be ignored
                    ['type'=>'regex'], // regex consent option without pattern should be ignored
                    ['type'=>'regex', 'pattern'=>'/.*\.valid.regex\.that\.does\.not\.match.*/i'],
                    // accept any SP that has an entityid that contains the string ".example.org"
                    ['type'=>'regex', 'pattern'=>'/.*\.example\.org\/.*/i'],
                ],
                'SingleSignOnService' => [
                    [
                        'Binding'  => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
                        'Location' => 'https://idp.example.org/saml2/idp/SSOService.php',
                    ],
                ],
            ],
            'Destination' => [
                'entityid' => 'https://sp.example.org/my-sp', // sp contains the string ".example.org"
                'metadata-set' => 'saml20-sp-remote',
            ],
            'UserID' => 'jdoe',
            'Attributes' => [
                'eduPersonPrincipalName' => ['jdoe@example.com'],
            ],
        ];
        $result = $this->processFilter($config, $request);
        // the state should NOT have changed because NO consent should be necessary (match)
        $this->assertEquals($request, $result);

        // test corner cases
        $request['Source']['consent.disable'] = [
            'https://valid.flatstring.example.that.does.not.match',
            ['foo' => 'bar'],
        ];
        $request['Destination']['consent.disable'] = 1;
        $result = $this->processFilter($config, $request);
        // the state should NOT have changed because NO consent should be necessary (match)
        $this->assertEquals($request, $result);
    }


    /**
     * @return void
     */
    public function testAttributeHashIsConsistentWhenOrderOfValuesChange()
    {
        $attributes1 = [
            'attribute1' => ['val1', 'val2'],
            'attribute2' => ['val1', 'val2']
        ];
        $attributeHash1 = Consent::getAttributeHash($attributes1, true);

        $attributes2 = [
            'attribute1' => ['val1', 'val2'],
            'attribute2' => ['val2', 'val1']
        ];
        $attributeHash2 = Consent::getAttributeHash($attributes2, true);

        $this->assertEquals($attributeHash1, $attributeHash2, "Hash is not the same when the order of values changes");
    }


    /**
     * @return void
     */
    public function testAttributeHashIsConsistentWhenOrderOfAttributesChange()
    {
        $attributes1 = [
            'attribute2' => ['val1', 'val2'],
            'attribute1' => ['val1', 'val2']
        ];
        $attributeHash1 = Consent::getAttributeHash($attributes1, true);

        $attributes2 = [
            'attribute1' => ['val1', 'val2'],
            'attribute2' => ['val1', 'val2']
        ];
        $attributeHash2 = Consent::getAttributeHash($attributes2, true);

        $this->assertEquals(
            $attributeHash1,
            $attributeHash2,
            "Hash is not the same when the order of the attributs changes"
        );
    }


    /**
     * @return void
     */
    public function testAttributeHashIsConsistentWithoutValuesWhenOrderOfAttributesChange()
    {
        $attributes1 = [
            'attribute2' => ['val1', 'val2'],
            'attribute1' => ['val1', 'val2']
        ];
        $attributeHash1 = Consent::getAttributeHash($attributes1);

        $attributes2 = [
            'attribute1' => ['val1', 'val2'],
            'attribute2' => ['val1', 'val2']
        ];
        $attributeHash2 = Consent::getAttributeHash($attributes2);

        $this->assertEquals(
            $attributeHash1,
            $attributeHash2,
            "Hash is not the same when the order of the attributs changes and the values are not included"
        );
    }


    /**
     * @return void
     */
    public function testConstructorSetsInstancePrivateVars()
    {
        $reflection = new \ReflectionClass(Consent::class);

        $values = [
            'includeValues',
            'checked',
            'focus',
            'hiddenAttributes',
            'noconsentattributes',
            'showNoConsentAboutService'
        ];
        foreach ($values as $v) {
            $instanceVars[$v] = $reflection->getProperty($v);
            $instanceVars[$v]->setAccessible(true);
        }

        /* these just need to be different to the default values */
        $config = [
            'includeValues' => true,
            'checked' => true,
            'focus' => 'yes',
            'hiddenAttributes' => ['attribute1', 'attribute2'],
            'attributes.exclude' => ['attribute1', 'attribute2'],
            'showNoConsentAboutService' => false,
        ];

        ob_start();
        $testcase = $reflection->newInstance($config, null);
        ob_end_clean();

        $this->assertEquals($instanceVars['includeValues']->getValue($testcase), $config['includeValues']);
        $this->assertEquals($instanceVars['checked']->getValue($testcase), $config['checked']);
        $this->assertEquals($instanceVars['focus']->getValue($testcase), $config['focus']);
        $this->assertEquals($instanceVars['hiddenAttributes']->getValue($testcase), $config['hiddenAttributes']);
        $this->assertEquals($instanceVars['noconsentattributes']->getValue($testcase), $config['attributes.exclude']);
        $this->assertEquals(
            $instanceVars['showNoConsentAboutService']->getValue($testcase),
            $config['showNoConsentAboutService']
        );

        $deprecated = $reflection->newInstance(['noconsentattributes' => $config['attributes.exclude']], null);
        $this->assertEquals($instanceVars['noconsentattributes']->getValue($deprecated), $config['attributes.exclude']);
    }
}
