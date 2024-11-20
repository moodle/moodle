<?php

use WhiteHat101\Crypt\APR1_MD5;

class APR1_MD5_HashTest extends PHPUnit_Framework_TestCase {

    public function testHash_WhiteHat101() {
        $this->assertEquals(
            '$apr1$HIcWIbgX$G9YqNkCVGlFAN63bClpoT/',
            APR1_MD5::hash('WhiteHat101','HIcWIbgX')
        );
    }

    public function testHash_apache() {
        $this->assertEquals(
            '$apr1$rOioh4Wh$bVD3DRwksETubcpEH90ww0',
            APR1_MD5::hash('apache','rOioh4Wh')
        );
    }

    public function testHash_ChangeMe1() {
        $this->assertEquals(
            '$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1',
            APR1_MD5::hash('ChangeMe1','PVWlTz/5')
        );
    }

    // Test some awkward inputs

    public function testHash_ChangeMe1_blankSalt() {
        $this->assertEquals(
            '$apr1$$DbHa0iITto8vNFPlkQsBX1',
            APR1_MD5::hash('ChangeMe1','')
        );
    }

    public function testHash_ChangeMe1_longSalt() {
        $this->assertEquals(
            '$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1',
            APR1_MD5::hash('ChangeMe1','PVWlTz/50123456789')
        );
    }

    public function testHash_ChangeMe1_nullSalt() {
        $hash = APR1_MD5::hash('ChangeMe1');
        $this->assertEquals(37, strlen($hash));
    }

    public function testHash__nullSalt() {
        $hash = APR1_MD5::hash('');
        $this->assertEquals(37, strlen($hash));
    }

    // a null password gets coerced into the blank string.
    // is this sensible?
    public function testHash_null_nullSalt() {
        $hash = APR1_MD5::hash(null);
        $this->assertEquals(37, strlen($hash));
    }

}
