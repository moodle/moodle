<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\saml\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Error;
use SimpleSAML\Module\saml\Auth\Process\NameIDAttribute;
use SAML2\XML\saml\NameID;
use SAML2\Constants;

/**
 * Test for the saml:NameIDAttribute filter.
 *
 * @author Eugene Venter <eugene@catalyst.net.nz>
 * @package SimpleSAMLphp
 */
class NameIDAttributeTest extends TestCase
{
    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param array $config  The filter configuration.
     * @param array $request  The request state.
     * @return array  The state array after processing.
     */
    private function processFilter(array $config, array $request): array
    {
        $filter = new NameIDAttribute($config, null);
        $filter->process($request);
        return $request;
    }


    /**
     * Test minimal configuration.
     * @return void
     */
    public function testMinimalConfig(): void
    {
        $config = [];
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';


        $nameId = new NameID();
        $nameId->setValue('eugene@oombaas');
        $nameId->setFormat(Constants::NAMEID_PERSISTENT);
        $nameId->setNameQualifier($idpId);
        $nameId->setSPNameQualifier($spId);

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
            'saml:sp:NameID' => $nameId,
        ];
        $result = $this->processFilter($config, $request);
        $this->assertEquals("{$idpId}!{$spId}!{$nameId->getValue()}", $result['Attributes']['nameid'][0]);
    }


    /**
     * Test custom attribute name.
     * @return void
     */
    public function testCustomAttributeName(): void
    {
        $attributeName = 'eugeneNameIDAttribute';
        $config = ['attribute' => $attributeName];
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';

        $nameId = new NameID();
        $nameId->setValue('eugene@oombaas');
        $nameId->setFormat(Constants::NAMEID_PERSISTENT);
        $nameId->setNameQualifier($idpId);
        $nameId->setSPNameQualifier($spId);

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
            'saml:sp:NameID' => $nameId,
        ];
        $result = $this->processFilter($config, $request);
        $this->assertTrue(isset($result['Attributes'][$attributeName]));
        $this->assertEquals("{$idpId}!{$spId}!{$nameId->getValue()}", $result['Attributes'][$attributeName][0]);
    }


    /**
     * Test custom format.
     * @return void
     */
    public function testFormat(): void
    {
        $config = ['format' => '%V!%%'];
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';

        $nameId = new NameID();
        $nameId->setValue('eugene@oombaas');
        $nameId->setFormat(Constants::NAMEID_PERSISTENT);
        $nameId->setNameQualifier($idpId);
        $nameId->setSPNameQualifier($spId);

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
            'saml:sp:NameID' => $nameId,
        ];
        $result = $this->processFilter($config, $request);
        $this->assertEquals("{$nameId->getValue()}!%", $result['Attributes']['nameid'][0]);
    }


    /**
     * Test invalid format throws an exception.
     * @return void
     */
    public function testInvalidFormatThrowsException()
    {
        $config = ['format' => '%X'];
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';

        $nameId = new NameID();
        $nameId->setValue('eugene@oombaas');

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
            'saml:sp:NameID' => $nameId,
        ];

        $this->expectException(Error\Exception::class);
        $this->expectExceptionMessage('NameIDAttribute: Invalid replacement: "%X"');

        $this->processFilter($config, $request);
    }


    /**
     * Test invalid request silently continues, leaving the state untouched
     * @return void
     */
    public function testInvalidRequestLeavesStateUntouched()
    {
        $config = ['format' => '%V!%F'];
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
        ];

        $pre = $request;
        $this->processFilter($config, $request);
        $this->assertEquals($pre, $request);
    }


    /**
     * Test custom attribute name with format.
     * @return void
     */
    public function testCustomAttributeNameAndFormat(): void
    {
        $attributeName = 'eugeneNameIDAttribute';
        $config = ['attribute' => $attributeName, 'format' => '%V'];
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';

        $nameId = new NameID();
        $nameId->setValue('eugene@oombaas');
        $nameId->setFormat(Constants::NAMEID_PERSISTENT);
        $nameId->setNameQualifier($idpId);
        $nameId->setSPNameQualifier($spId);

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
            'saml:sp:NameID' => $nameId,
        ];
        $result = $this->processFilter($config, $request);
        $this->assertTrue(isset($result['Attributes'][$attributeName]));
        $this->assertEquals("{$nameId->getValue()}", $result['Attributes'][$attributeName][0]);
    }


    /**
     * Test overriding NameID Format/NameQualifier/SPNameQualifier with defaults.
     * @return void
     */
    public function testOverrideNameID()
    {
        $spId = 'eugeneSP';
        $idpId = 'eugeneIdP';

        $nameId = new NameID();
        $nameId->setValue('eugene@oombaas');

        $request = [
            'Source'     => [
                'entityid' => $spId,
            ],
            'Destination' => [
                'entityid' => $idpId,
            ],
            'saml:sp:NameID' => $nameId,
        ];
        $this->processFilter(array(), $request);
        $this->assertEquals("{$nameId->getFormat()}", Constants::NAMEID_UNSPECIFIED);
        $this->assertEquals("{$nameId->getNameQualifier()}", $idpId);
        $this->assertEquals("{$nameId->getSPNameQualifier()}", $spId);
    }
}
