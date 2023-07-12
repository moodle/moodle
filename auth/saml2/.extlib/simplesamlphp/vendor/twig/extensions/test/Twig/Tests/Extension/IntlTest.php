<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class Twig_Tests_Extension_IntlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @requires extension intl
     * @requires PHP 5.5
     */
    public function testLocalizedDateFilterWithDateTimeZone()
    {
        class_exists('Twig_Extensions_Extension_Intl');
        $env = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $date = twig_localized_date_filter($env, new DateTime('2015-01-01T00:00:00', new DateTimeZone('UTC')), 'short', 'long', 'en', '+01:00');
        $this->assertEquals('1/1/15 1:00:00 AM GMT+01:00', $date);
    }

    /**
     * @requires extension intl
     * @requires PHP 5.5
     */
    public function testLocalizedDateFilterWithDateTimeZoneZ()
    {
        class_exists('Twig_Extensions_Extension_Intl');
        $env = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $date = twig_localized_date_filter($env, new DateTime('2017-11-19T00:00:00Z'), 'short', 'long', 'fr', 'Z');
        $this->assertEquals('19/11/2017 00:00:00 UTC', $date);
    }
}
