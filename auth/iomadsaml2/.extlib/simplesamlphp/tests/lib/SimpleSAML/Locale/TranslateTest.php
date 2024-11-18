<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Locale;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Configuration;
use SimpleSAML\Locale\Translate;

class TranslateTest extends TestCase
{
    /**
     * Test SimpleSAML\Locale\Translate::noop().
     * @return void
     */
    public function testNoop(): void
    {
        // test default
        $c = Configuration::loadFromArray([]);
        $t = new Translate($c);
        $testString = 'Blablabla';
        $this->assertEquals($testString, $t->noop($testString));
    }


    /**
     * Test SimpleSAML\Locale\Translate::t().
     * @return void
     */
    public function testTFallback()
    {
        $c = \SimpleSAML\Configuration::loadFromArray([]);
        $t = new Translate($c);
        $testString = 'Blablabla';

        // $fallbackdefault = true
        $result = 'not translated (' . $testString . ')';
        $this->assertEquals($result, $t->t($testString));

        // $fallbackdefault = false, should be a noop
        $this->assertEquals($testString, $t->t($testString, [], false));
    }
}
