<?php

global $CFG;
require_once($CFG->dirroot . '/lib/password_compat/lib/password.php');

class PasswordVerifyTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        if (password_compat_not_supported()) {
            // Skip test if password_compat is not supported.
            $this->markTestSkipped('password_compat not supported');
        }
    }

    public function testFuncExists() {
        $this->assertTrue(function_exists('password_verify'));
    }

    public function testFailedType() {
        $this->assertFalse(password_verify(123, 123));
    }

    public function testSaltOnly() {
        $this->assertFalse(password_verify('foo', '$2a$07$usesomesillystringforsalt$'));
    }

    public function testInvalidPassword() {
        $this->assertFalse(password_verify('rasmusler', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi'));
    }

    public function testValidPassword() {
        $this->assertTrue(password_verify('rasmuslerdorf', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hi'));
    }

    public function testInValidHash() {
        $this->assertFalse(password_verify('rasmuslerdorf', '$2a$07$usesomesillystringfore2uDLvp1Ii2e./U9C8sBjqp8I90dH6hj'));
    }

}
