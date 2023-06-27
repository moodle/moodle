<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Locale;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Locale\Language;

class LanguageTest extends TestCase
{
    /**
     * Test SimpleSAML\Locale\Language::getDefaultLanguage().
     * @return void
     */
    public function testGetDefaultLanguage(): void
    {
        // test default
        $c = Configuration::loadFromArray([]);
        $l = new Language($c);
        $this->assertEquals('en', $l->getDefaultLanguage());

        // test defaults coming from configuration
        $c = Configuration::loadFromArray([
            'language.available' => ['en', 'es', 'nn'],
            'language.default' => 'es',
        ]);
        $l = new Language($c);
        $this->assertEquals('es', $l->getDefaultLanguage());
    }


    /**
     * Test SimpleSAML\Locale\Language::getLanguageCookie().
     * @return void
     */
    public function testGetLanguageCookie(): void
    {
        // test it works when no cookie is set
        Configuration::loadFromArray([], '', 'simplesaml');
        $this->assertNull(Language::getLanguageCookie());

        // test that it works fine with defaults
        Configuration::loadFromArray([], '', 'simplesaml');
        $_COOKIE['language'] = 'en';
        $this->assertEquals('en', Language::getLanguageCookie());

        // test that it works with non-defaults
        Configuration::loadFromArray([
            'language.available' => ['en', 'es', 'nn'],
            'language.cookie.name' => 'xyz'
        ], '', 'simplesaml');
        $_COOKIE['xyz'] = 'Es'; // test values are converted to lowercase too
        $this->assertEquals('es', Language::getLanguageCookie());
    }


    /**
     * Test SimpleSAML\Locale\Language::getLanguageList().
     * @return void
     */
    public function testGetLanguageListNoConfig(): void
    {
        // test default
        $c = Configuration::loadFromArray([], '', 'simplesaml');
        $l = new Language($c);
        $l->setLanguage('en');
        $this->assertEquals(['en' => true], $l->getLanguageList());
    }


    /**
     * Test SimpleSAML\Locale\Language::getLanguageList().
     * @return void
     */
    public function testGetLanguageListCorrectConfig(): void
    {
        // test langs from from language_names
        $c = Configuration::loadFromArray([
            'language.available' => ['en', 'nn', 'es'],
        ], '', 'simplesaml');
        $l = new Language($c);
        $l->setLanguage('es');
        $this->assertEquals([
            'en' => false,
            'es' => true,
            'nn' => false,
        ], $l->getLanguageList());
    }


    /**
     * Test SimpleSAML\Locale\Language::getLanguageList().
     * @return void
     */
    public function testGetLanguageListIncorrectConfig(): void
    {
        // test non-existent langs
        $c = Configuration::loadFromArray([
            'language.available' => ['foo', 'bar'],
        ], '', 'simplesaml');
        $l = new Language($c);
        $l->setLanguage('foo');
        $this->assertEquals(['en' => true], $l->getLanguageList());
    }


    /**
     * Test SimpleSAML\Locale\Language::getLanguageParameterName().
     * @return void
     */
    public function testGetLanguageParameterName(): void
    {
        // test for default configuration
        $c = Configuration::loadFromArray([], '', 'simplesaml');
        $l = new Language($c);
        $this->assertEquals('language', $l->getLanguageParameterName());

        // test for valid configuration
        $c = Configuration::loadFromArray([
            'language.parameter.name' => 'xyz'
        ], '', 'simplesaml');
        $l = new Language($c);
        $this->assertEquals('xyz', $l->getLanguageParameterName());
    }


    /**
     * Test SimpleSAML\Locale\Language::isLanguageRTL().
     * @return void
     */
    public function testIsLanguageRTL(): void
    {
        // test defaults
        $c = Configuration::loadFromArray([], '', 'simplesaml');
        $l = new Language($c);
        $l->setLanguage('en');
        $this->assertFalse($l->isLanguageRTL());

        // test non-defaults, non-RTL
        $c = Configuration::loadFromArray([
            'language.rtl' => ['foo', 'bar'],
        ], '', 'simplesaml');
        $l = new Language($c);
        $l->setLanguage('en');
        $this->assertFalse($l->isLanguageRTL());

        // test non-defaults, RTL
        $c = Configuration::loadFromArray([
            'language.available' => ['en', 'nn', 'es'],
            'language.rtl' => ['nn', 'es'],
        ], '', 'simplesaml');
        $l = new Language($c);
        $l->setLanguage('es');
        $this->assertTrue($l->isLanguageRTL());
    }


    /**
     * Test SimpleSAML\Locale\Language::setLanguage().
     * @return void
     */
    public function testSetLanguage(): void
    {
        // test with valid configuration, no cookies set
        $c = Configuration::loadFromArray([
            'language.available' => ['en', 'nn', 'es'],
            'language.parameter.name' => 'xyz',
            'language.parameter.setcookie' => false,
        ], '', 'simplesaml');
        $_GET['xyz'] = 'Es'; // test also that lang code is transformed to lower caps
        $l = new Language($c);
        $this->assertEquals('es', $l->getLanguage());

        // test with valid configuration, no cookies, language set unavailable
        $_GET['xyz'] = 'unavailable';
        $l = new Language($c);
        $this->assertEquals('en', $l->getLanguage());
    }
}
