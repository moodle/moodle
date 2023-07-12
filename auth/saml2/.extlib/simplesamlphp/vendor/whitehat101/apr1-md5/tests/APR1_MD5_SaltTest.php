<?php

use WhiteHat101\Crypt\APR1_MD5;

class APR1_MD5_SaltTest extends PHPUnit_Framework_TestCase {

    public function testSaltType() {
        $this->assertInternalType('string', APR1_MD5::salt());
    }

    public function testSaltPattern() {
        $this->assertRegExp('/.{8}/', APR1_MD5::salt());
    }

    public function testSaltRamdomness() {
        $this->assertNotEquals(APR1_MD5::salt(), APR1_MD5::salt());
    }

}
