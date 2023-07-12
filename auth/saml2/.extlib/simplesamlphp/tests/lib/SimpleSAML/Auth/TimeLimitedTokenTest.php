<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Auth;

use SimpleSAML\Auth\TimeLimitedToken;

class TimeLimitedTokenTest extends \SimpleSAML\Test\Utils\ClearStateTestCase
{
    /**
     * Test for malformed tokens.
     * @return void
     */
    public function testMalformedToken()
    {
        \SimpleSAML\Configuration::loadFromArray(['secretsalt' => 'random'], '[ARRAY]', 'simplesaml');

        $token = new TimeLimitedToken();
        $this->assertFalse($token->validate('malformed'));
        $this->assertFalse($token->validate('mal-for-med'));
        $this->assertFalse($token->validate('mal-formed'));
    }


    /**
     * Basic test to see if validation works for valid tokens.
     * @return void
     */
    public function testValidToken()
    {
        \SimpleSAML\Configuration::loadFromArray(['secretsalt' => 'random'], '[ARRAY]', 'simplesaml');

        $token = new TimeLimitedToken();
        $t = $token->generate();
        $this->assertTrue($token->validate($t));
    }


    /**
     * Test that token validation takes the verification data into account.
     * @return void
     */
    public function testValidTokenWithData()
    {
        \SimpleSAML\Configuration::loadFromArray(['secretsalt' => 'random'], '[ARRAY]', 'simplesaml');

        $tokenWithData = new TimeLimitedToken();
        $tokenWithData->addVerificationData('some more random data');
        $t = $tokenWithData->generate();
        $this->assertTrue($tokenWithData->validate($t));

        $tokenWithoutData = new TimeLimitedToken();
        $this->assertFalse($tokenWithoutData->validate($t));
    }


    /**
     * Test that expired tokens are rejected.
     * @return void
     */
    public function testExpiredToken()
    {
        \SimpleSAML\Configuration::loadFromArray(['secretsalt' => 'random'], '[ARRAY]', 'simplesaml');

        $token = new TimeLimitedToken();
        $this->assertFalse($token->validate('7-c0803e76fff1df0ceb222dee80aa1d73f35d84dd'));
    }


    /**
     * Test that a token that has been manipulated to extend its validity is rejected.
     * @return void
     */
    public function testManipulatedToken()
    {
        \SimpleSAML\Configuration::loadFromArray(['secretsalt' => 'random'], '[ARRAY]', 'simplesaml');

        $token = new TimeLimitedToken(1);
        $t = $token->generate();
        list($offset, $hash) = explode('-', $t);
        sleep(1);
        $this->assertFalse($token->validate(dechex(hexdec($offset) + 1).'-'.$hash));
    }
}
