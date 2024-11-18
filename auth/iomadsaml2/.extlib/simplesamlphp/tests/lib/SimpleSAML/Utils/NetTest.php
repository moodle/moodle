<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Utils\Net;

/**
 * Tests for SimpleSAML\Utils\Test.
 */
class NetTest extends TestCase
{
    /**
     * Test the function that checks for IPs belonging to a CIDR.
     *
     * @covers SimpleSAML\Utils\Net::ipCIDRcheck
     * @return void
     */
    public function testIpCIDRcheck(): void
    {
        // check CIDR w/o mask
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0', '127.0.0.1'));

        // check wrong CIDR w/ mask
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.256/24', '127.0.0.1'));

        // check wrong IP
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/24', '127.0.0'));
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/24', '127.0.0.*'));

        // check limits for standard classes
        $this->assertTrue(Net::ipCIDRcheck('127.0.0.0/24', '127.0.0.0'));
        $this->assertTrue(Net::ipCIDRcheck('127.0.0.0/24', '127.0.0.255'));
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/24', '127.0.0.256'));

        $this->assertTrue(Net::ipCIDRcheck('127.0.0.0/16', '127.0.0.0'));
        $this->assertTrue(Net::ipCIDRcheck('127.0.0.0/16', '127.0.255.255'));
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/16', '127.0.255.256'));
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/16', '127.0.256.255'));

        // check limits for non-standard classes
        $this->assertTrue(Net::ipCIDRcheck('127.0.0.0/23', '127.0.0.0'));
        $this->assertTrue(Net::ipCIDRcheck('127.0.0.0/23', '127.0.1.255'));
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/23', '127.0.1.256'));
        $this->assertFalse(Net::ipCIDRcheck('127.0.0.0/23', '127.0.2.0'));
    }


    /**
     * Test IPv6 support in SimpleSAML\Utils\Net::ipCIDRcheck.
     *
     * @covers SimpleSAML\Utils\Net::ipCIDRcheck
     * @return void
     */
    public function testIpv6CIDRcheck(): void
    {
        // check CIDR w/o mask
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::', '2001:0DB8::1'));

        // check wrong CIDR w/ mask
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/128', '2001:0DB8::1'));

        // check wrong IP
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/128', '2001:0DB8::Z'));

        // check limits for standard classes
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/128', '2001:0DB8:0000:0000:0000:0000:0000:0000'));
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/128', '2001:0DB8::0'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/128', '2001:0DB8::1'));

        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/112', '2001:0DB8::1'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/112', '2001:0DB8::1:1'));
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/112', '2001:0DB8::FFFF'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/112', '2001:0DB8::1:FFFF'));

        // check limits for non-standard classes
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/108', '2001:0DB8::1:1'));
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/108', '2001:0DB8::F:1'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/108', '2001:0DB8::FF:1'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/108', '2001:0DB8::1FF:1'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/108', '2001:0DB8::FFFF:1'));

        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/104', '2001:0DB8::1:1'));
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/104', '2001:0DB8::F:1'));
        $this->assertTrue(Net::ipCIDRcheck('2001:0DB8::/104', '2001:0DB8::FF:1'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/104', '2001:0DB8::1FF:1'));
        $this->assertFalse(Net::ipCIDRcheck('2001:0DB8::/104', '2001:0DB8::FFFF:1'));
    }
}
