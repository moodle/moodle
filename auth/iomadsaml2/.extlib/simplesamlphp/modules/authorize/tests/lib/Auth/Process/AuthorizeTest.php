<?php
/**
 * Test for the authorize:Authorize authproc filter.
 */

namespace SimpleSAML\Module\authorize\Auth\Process;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Module\Authorize\Tests\Utils\TestableAuthorize;
use SimpleSAML\Utils\Attributes;

class AuthorizeTest extends TestCase
{
    /**
     * Helper function to run the filter with a given configuration.
     *
     * @param array $config The filter configuration.
     * @param array $request The request state.
     * @return array  The state array after processing.
     */
    private function processFilter(array $config, array $request)
    {
        $filter = new TestableAuthorize($config, null);
        $filter->process($request);
        return $request;
    }

    /**
     * Test that having a matching attribute grants access
     * @dataProvider allowScenarioProvider
     * @param array $userAttributes The attributes to test
     * @param bool $isAuthorized Should the user be authorized
     */
    public function testAllowScenarios($userAttributes, $isAuthorized)
    {
        $userAttributes = Attributes::normalizeAttributesArray($userAttributes);
        $config = [
            'uid' => [
                '/^.*@example.com$/',
                '/^(user1|user2|user3)@example.edu$/',
            ],
            'schacUserStatus' => '@urn:mace:terena.org:userStatus:example.org:service:active.*@',
        ];

        $resultState = $this->processFilter($config, ['Attributes' => $userAttributes]);

        $resultAuthorized = isset($resultState['NOT_AUTHORIZED']) ? false : true;
        $this->assertEquals($isAuthorized, $resultAuthorized);
    }

    public function allowScenarioProvider()
    {
        return [
            // Should be allowed
            [['uid' => 'anything@example.com'], true],
            [['uid' => 'user2@example.edu'], true],
            [['schacUserStatus' => 'urn:mace:terena.org:userStatus:example.org:service:active.my.service'], true],
            [
                [
                    'uid' => ['wrongValue', 'user2@example.edu', 'wrongValue2'],
                    'schacUserStatus' => 'incorrectstatus'
                ],
                true
            ],

            //Should be denied
            [['wrongAttributes' => ['abc']], false],
            [
                [
                    'uid' => [
                        'anything@example.com.wrong',
                        'wronguser@example.edu',
                        'user2@example.edu.wrong',
                        'prefixuser2@example.edu'
                    ]
                ],
                false
            ],
        ];
    }

    /**
     * Test that having a matching attribute prevents access
     * @dataProvider invertScenarioProvider
     * @param array $userAttributes The attributes to test
     * @param bool $isAuthorized Should the user be authorized
     */
    public function testInvertAllowScenarios($userAttributes, $isAuthorized)
    {
        $userAttributes = Attributes::normalizeAttributesArray($userAttributes);
        $config = [
            'deny' => true,
            'uid' => [
                '/.*@students.example.edu$/',
                '/^(stu1|stu2|stu3)@example.edu$/',
            ],
            'schacUserStatus' => '@urn:mace:terena.org:userStatus:example.org:service:blocked.*@',
        ];

        $resultState = $this->processFilter($config, ['Attributes' => $userAttributes]);

        $resultAuthorized = isset($resultState['NOT_AUTHORIZED']) ? false : true;
        $this->assertEquals($isAuthorized, $resultAuthorized);
    }

    public function invertScenarioProvider()
    {
        return [
            // Should be allowed
            [['noMatch' => 'abc'], true],
            [['uid' => 'anything@example.edu'], true],

            //Should be denied
            [['uid' => 'anything@students.example.edu'], false],
            [['uid' => 'stu3@example.edu'], false],
            [['schacUserStatus' => 'urn:mace:terena.org:userStatus:example.org:service:blocked'], false],
            // Matching any of the attributes results in denial
            [
                [
                    'uid' => ['noMatch', 'abc@students.example.edu', 'noMatch2'],
                    'schacUserStatus' => 'noMatch'
                ],
                false
            ],
        ];
    }

    /**
     * Test that having a matching attribute prevents access
     * @dataProvider noregexScenarioProvider
     * @param array $userAttributes The attributes to test
     * @param bool $isAuthorized Should the user be authorized
     */
    public function testDisableRegex($userAttributes, $isAuthorized)
    {
        $userAttributes = Attributes::normalizeAttributesArray($userAttributes);
        $config = [
            'regex' => false,
            'group' => [
                'CN=SimpleSAML Students,CN=Users,DC=example,DC=edu',
                'CN=All Teachers,OU=Staff,DC=example,DC=edu',
            ],
        ];

        $resultState = $this->processFilter($config, ['Attributes' => $userAttributes]);

        $resultAuthorized = isset($resultState['NOT_AUTHORIZED']) ? false : true;
        $this->assertEquals($isAuthorized, $resultAuthorized);
    }

    public function noregexScenarioProvider()
    {
        return [
            // Should be allowed
            [['group' => 'CN=SimpleSAML Students,CN=Users,DC=example,DC=edu'], true],

            //Should be denied
            [['wrongAttribute' => 'CN=SimpleSAML Students,CN=Users,DC=example,DC=edu'], false],
            [['group' => 'CN=wrongCN=SimpleSAML Students,CN=Users,DC=example,DC=edu'], false],
        ];
    }
}
