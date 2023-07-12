<?php

use WhiteHat101\Crypt\APR1_MD5;

class APR1_MD5_CheckTest extends PHPUnit_Framework_TestCase {

    public function testHash_WhiteHat101() {
        $this->assertTrue(
            APR1_MD5::check('WhiteHat101','$apr1$HIcWIbgX$G9YqNkCVGlFAN63bClpoT/')
        );
    }

    public function testHash_apache() {
        $this->assertTrue(
            APR1_MD5::check('apache','$apr1$rOioh4Wh$bVD3DRwksETubcpEH90ww0')
        );
    }

    public function testHash_ChangeMe1() {
        $this->assertTrue(
            APR1_MD5::check('ChangeMe1','$apr1$PVWlTz/5$SNkIVyogockgH65nMLn.W1')
        );
    }

}
