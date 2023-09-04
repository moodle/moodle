<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Module\multiauth\Auth\Source;

use Error;
use Exception;
use SimpleSAML\Test\Utils\ClearStateTestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Module\multiauth\Auth\Source\MultiAuth;

class MultiAuthTest extends ClearStateTestCase
{
    /** @var Configuration */
    private $config;

    /** @var Configuration */
    private $sourceConfig;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->config = Configuration::loadFromArray(
            ['module.enable' => ['multiauth' => true]],
            '[ARRAY]',
            'simplesaml'
        );
        Configuration::setPreLoadedConfig($this->config, 'config.php');

        $this->sourceConfig = Configuration::loadFromArray(array(
            'example-multi' => array(
                'multiauth:MultiAuth',

                /*
                 * The available authentication sources.
                 * They must be defined in this authsources.php file.
                 */
                'sources' => array(
                    'example-saml' => array(
                        'text' => array(
                            'en' => 'Log in using a SAML SP',
                            'es' => 'Entrar usando un SP SAML',
                        ),
                        'css-class' => 'SAML',
                    ),
                    'example-admin' => array(
                        'text' => array(
                            'en' => 'Log in using the admin password',
                            'es' => 'Entrar usando la contraseña de administrador',
                        ),
                    ),
                ),
                'preselect' => 'example-saml',
            ),

            'example-saml' => array(
                'saml:SP',
                'entityId' => 'my-entity-id',
                'idp' => 'my-idp',
            ),

            'example-admin' => array(
                'core:AdminPassword',
            ),
        ));
        Configuration::setPreLoadedConfig($this->sourceConfig, 'authsources.php');
    }


    /**
     * @return void
     */
    public function testSourcesMustBePresent(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The required "sources" config option was not found');
        $sourceConfig = Configuration::loadFromArray(array(
            'example-multi' => array(
                'multiauth:MultiAuth',
            ),
        ));

        Configuration::setPreLoadedConfig($sourceConfig, 'authsources.php');

        new MultiAuth(['AuthId' => 'example-multi'], $sourceConfig->getArray('example-multi'));
    }


    /**
     * @return void
     */
    public function testPreselectMustBeValid(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The optional "preselect" config option must be present in "sources"');
        $sourceConfig = Configuration::loadFromArray(array(
            'example-multi' => array(
                'multiauth:MultiAuth',

                /*
                 * The available authentication sources.
                 * They must be defined in this authsources.php file.
                 */
                'sources' => array(
                    'example-saml' => array(
                        'text' => array(
                            'en' => 'Log in using a SAML SP',
                            'es' => 'Entrar usando un SP SAML',
                        ),
                        'css-class' => 'SAML',
                    ),
                    'example-admin' => array(
                        'text' => array(
                            'en' => 'Log in using the admin password',
                            'es' => 'Entrar usando la contraseña de administrador',
                        ),
                    ),
                ),
                'preselect' => 'other',
            ),

            'example-saml' => array(
                'saml:SP',
                'entityId' => 'my-entity-id',
                'idp' => 'my-idp',
            ),

            'example-admin' => array(
                'core:AdminPassword',
            ),
        ));

        Configuration::setPreLoadedConfig($sourceConfig, 'authsources.php');
        new MultiAuth(['AuthId' => 'example-multi'], $sourceConfig->getArray('example-multi'));
    }


    /**
     * @return void
     */
    public function testPreselectIsOptional(): void
    {
        $sourceConfig = Configuration::loadFromArray(array(
            'example-multi' => array(
                'multiauth:MultiAuth',

                /*
                 * The available authentication sources.
                 * They must be defined in this authsources.php file.
                 */
                'sources' => array(
                    'example-saml' => array(
                        'text' => array(
                            'en' => 'Log in using a SAML SP',
                            'es' => 'Entrar usando un SP SAML',
                        ),
                        'css-class' => 'SAML',
                    ),
                    'example-admin' => array(
                        'text' => array(
                            'en' => 'Log in using the admin password',
                            'es' => 'Entrar usando la contraseña de administrador',
                        ),
                    ),
                ),
            ),

            'example-saml' => array(
                'saml:SP',
                'entityId' => 'my-entity-id',
                'idp' => 'my-idp',
            ),

            'example-admin' => array(
                'core:AdminPassword',
            ),
        ));

        Configuration::setPreLoadedConfig($sourceConfig, 'authsources.php');

        $state = [];
        $source = new MultiAuth(['AuthId' => 'example-multi'], $sourceConfig->getArray('example-multi'));

        try {
            $source->authenticate($state);
        } catch (Error $e) {
        } catch (Exception $e) {
        }

        $this->assertArrayNotHasKey('multiauth:preselect', $state);
    }


    /**
     * @return void
     */
    public function testPreselectCanBeConfigured(): void
    {
        $state = [];

        $source = new MultiAuth(['AuthId' => 'example-multi'], $this->sourceConfig->getArray('example-multi'));

        try {
            $source->authenticate($state);
        } catch (Exception $e) {
        }

        $this->assertArrayHasKey('multiauth:preselect', $state);
        $this->assertEquals('example-saml', $state['multiauth:preselect']);
    }


    /**
     * @return void
     */
    public function testStatePreselectHasPriority(): void
    {
        $state = ['multiauth:preselect' => 'example-admin'];

        $source = new MultiAuth(['AuthId' => 'example-multi'], $this->sourceConfig->getArray('example-multi'));

        try {
            $source->authenticate($state);
        } catch (Exception $e) {
        }

        $this->assertArrayHasKey('multiauth:preselect', $state);
        $this->assertEquals('example-admin', $state['multiauth:preselect']);
    }
}
