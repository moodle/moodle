<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\Utils\ClearStateTestCase;
use SimpleSAML\SessionHandlerPHP;
use SimpleSAML\Configuration;

class SessionHandlerPHPTest extends ClearStateTestCase
{
    /** @var array */
    protected $sessionConfig = [
        'session.cookie.name' => 'SimpleSAMLSessionID',
        'session.cookie.lifetime' => 100,
        'session.cookie.path' => '/ourPath',
        'session.cookie.domain' => 'example.com',
        'session.cookie.secure' => true,
        'session.phpsession.cookiename' => 'SimpleSAML',
    ];

    /** @var array */
    protected $original;


    /**
     * @return void
     */
    protected function setUp()
    {
        $this->original = $_SERVER;
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_NAME'] = 'example.com';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = 443;
        $_SERVER['REQUEST_URI'] = '/simplesaml';
    }


    /**
     * @return void
     */
    protected function tearDown()
    {
        $_SERVER = $this->original;
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::__construct()
     * @covers SimpleSAML\SessionHandlerPHP::getSessionHandler()
     * @covers SimpleSAML\SessionHandler::getSessionHandler()
     * @return void
     */
    public function testGetSessionHandler(): void
    {
        Configuration::loadFromArray($this->sessionConfig, '[ARRAY]', 'simplesaml');
        $sh = SessionHandlerPHP::getSessionHandler();
        $this->assertInstanceOf(SessionHandlerPHP::class, $sh);
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::setCookie()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testSetCookie(): void
    {
        Configuration::loadFromArray($this->sessionConfig, '[ARRAY]', 'simplesaml');
        $sh = SessionHandlerPHP::getSessionHandler();
        $sh->setCookie('SimpleSAMLSessionID', '1');

        $headers = xdebug_get_headers();
        $this->assertContains('SimpleSAML=1;', $headers[0]);
        $this->assertRegExp('/\b[Ee]xpires=([Mm]on|[Tt]ue|[Ww]ed|[Tt]hu|[Ff]ri|[Ss]at|[Ss]un)/', $headers[0]);
        $this->assertRegExp('/\b[Pp]ath=\/ourPath(;|$)/', $headers[0]);
        $this->assertRegExp('/\b[Dd]omain=example.com(;|$)/', $headers[0]);
        $this->assertRegExp('/\b[Ss]ecure(;|$)/', $headers[0]);
        $this->assertRegExp('/\b[Hh]ttp[Oo]nly(;|$)/', $headers[0]);
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::setCookie()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testSetCookieSameSiteNone(): void
    {
        Configuration::loadFromArray(
            array_merge($this->sessionConfig, ['session.cookie.samesite' => 'None']),
            '[ARRAY]',
            'simplesaml'
        );
        $sh = SessionHandlerPHP::getSessionHandler();
        $sh->setCookie('SimpleSAMLSessionID', 'None');

        $headers = xdebug_get_headers();
        $this->assertContains('SimpleSAML=None;', $headers[0]);
        $this->assertRegExp('/\b[Ss]ame[Ss]ite=None(;|$)/', $headers[0]);
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::setCookie()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testSetCookieSameSiteLax(): void
    {
        Configuration::loadFromArray(
            array_merge($this->sessionConfig, ['session.cookie.samesite' => 'Lax']),
            '[ARRAY]',
            'simplesaml'
        );
        $sh = SessionHandlerPHP::getSessionHandler();
        $sh->setCookie('SimpleSAMLSessionID', 'Lax');

        $headers = xdebug_get_headers();
        $this->assertContains('SimpleSAML=Lax;', $headers[0]);
        $this->assertRegExp('/\b[Ss]ame[Ss]ite=Lax(;|$)/', $headers[0]);
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::setCookie()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testSetCookieSameSiteStrict(): void
    {
        Configuration::loadFromArray(
            array_merge($this->sessionConfig, ['session.cookie.samesite' => 'Strict']),
            '[ARRAY]',
            'simplesaml'
        );
        $sh = SessionHandlerPHP::getSessionHandler();
        $sh->setCookie('SimpleSAMLSessionID', 'Strict');

        $headers = xdebug_get_headers();
        $this->assertContains('SimpleSAML=Strict;', $headers[0]);
        $this->assertRegExp('/\b[Ss]ame[Ss]ite=Strict(;|$)/', $headers[0]);
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::restorePrevious()
     * @runInSeparateProcess
     * @requires extension xdebug
     * @return void
     */
    public function testRestorePrevious(): void
    {
        session_name('PHPSESSID');
        $sid = session_id();
        session_start();

        Configuration::loadFromArray($this->sessionConfig, '[ARRAY]', 'simplesaml');
        /** @var SessionHandlerPHP $sh */
        $sh = SessionHandlerPHP::getSessionHandler();
        $sh->setCookie('SimpleSAMLSessionID', 'Restore');
        $sh->restorePrevious();

        $headers = xdebug_get_headers();
        $this->assertContains('PHPSESSID=' . $sid, $headers[0]);
        $this->assertContains('SimpleSAML=Restore;', $headers[1]);
        $this->assertContains('PHPSESSID=' . $sid, $headers[2]);
        $this->assertEquals($headers[0], $headers[2]);
    }


    /**
     * @covers SimpleSAML\SessionHandlerPHP::newSessionId()
     * @return void
     */
    public function testNewSessionId(): void
    {
        Configuration::loadFromArray($this->sessionConfig, '[ARRAY]', 'simplesaml');
        $sh = SessionHandlerPHP::getSessionHandler();
        $sid = $sh->newSessionId();
        $this->assertStringMatchesFormat('%s', $sid);
    }
}
