<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.
//
// This file is part of BasicLTI4Moodle
//
// BasicLTI4Moodle is an IMS BasicLTI (Basic Learning Tools for Interoperability)
// consumer for Moodle 1.9 and Moodle 2.0. BasicLTI is a IMS Standard that allows web
// based learning tools to be easily integrated in LMS as native ones. The IMS BasicLTI
// specification is part of the IMS standard Common Cartridge 1.1 Sakai and other main LMS
// are already supporting or going to support BasicLTI. This project Implements the consumer
// for Moodle. Moodle is a Free Open source Learning Management System by Martin Dougiamas.
// BasicLTI4Moodle is a project iniciated and leaded by Ludo(Marc Alier) and Jordi Piguillem
// at the GESSI research group at UPC.
// SimpleLTI consumer for Moodle is an implementation of the early specification of LTI
// by Charles Severance (Dr Chuck) htp://dr-chuck.com , developed by Jordi Piguillem in a
// Google Summer of Code 2008 project co-mentored by Charles Severance and Marc Alier.
//
// BasicLTI4Moodle is copyright 2009 by Marc Alier Forment, Jordi Piguillem and Nikolas Galanis
// of the Universitat Politecnica de Catalunya http://www.upc.edu
// Contact info: Marc Alier Forment granludo @ gmail.com or marc.alier @ upc.edu.

namespace mod_lti\local\ltiopenid;

/**
 * Tests for the jwks_helper class.
 *
 * @coversDefaultClass \mod_lti\local\ltiopenid\jwks_helper
 * @package    mod_lti
 * @copyright  2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class jwks_helper_test extends \basic_testcase {

    /**
     * Test the fix_jwks_alg method with a range of inputs.
     *
     * @dataProvider jwks_alg_provider
     * @covers ::fix_jwks_alg
     * @param array $jwks the JWKS key set.
     * @param string $jwt the JWT.
     * @param array $expected the expected outputs/exceptions.
     * @return void
     */
    public function test_fix_jwks_alg(array $jwks, string $jwt, array $expected): void {
        if (isset($expected['exception'])) {
            $this->expectException($expected['exception']);
        }
        $fixed = jwks_helper::fix_jwks_alg($jwks, $jwt);
        $this->assertEquals($expected['jwks'], $fixed);
    }

    /**
     * Provider for test_fix_jwks_alg.
     * @return array test data.
     */
    public static function jwks_alg_provider(): array {
        return [
            // Algs already present, so no changes to input key array.
            'All JWKS keys have algs set' => [
                'jwks' => [
                    'keys' => [
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '41',
                            'alg' => 'RS256'
                        ],
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '42',
                            'alg' => 'RS256'
                        ]
                    ]
                ],
                // RS256 JWT with kid = 42.
                'jwt' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjQyIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlRoZSBjYXQiLCJz'.
                    'bG9nYW4iOiJMb3ZlcyBpdCwgbG92ZXMgaXQsIGxvdmVzIE1vb2RsZSIsImlhdCI6MTUxNjIzOTAyMn0.EiqMEqufKJj74JevdTxXqzHvHGIcZ'.
                    'EFYhOe9sliL2FmlyiJcf7waObO2ZNwWvVZwTI4DfEGFamheMmTb6-YBODacDvH6BlQNb0H_6ye6AGl1u-3OAQj7i_SKsLuB37k6Lw5YFrwQYr'.
                    '7bjujSaQx6BL3kaqkqCdZhFjr2EYcn5-NehGHsevKqpMA-ShBovcndYkD5gfZEbXr59sgpQuJ43qO7gnGPzRbaJAEw_0_6v0r3y0pzDNfarNd'.
                    'fHfCZQbcF9T8dpHAeO4JMmuCanV8iJziI8ihVPwH-BwUJmzthyUgy8542FinHVbXo-88wu9xpbdV17VPgeGGBCpYpnVnWaA',
                'expected' => [
                    'jwks' => [
                        'keys' => [
                            [
                                'kty' => 'RSA',
                                'use' => 'sig',
                                'e' => 'AQAB',
                                'n' => '3nVf6',
                                'kid' => '41',
                                'alg' => 'RS256'
                            ],
                            [
                                'kty' => 'RSA',
                                'use' => 'sig',
                                'e' => 'AQAB',
                                'n' => '3nVf6',
                                'kid' => '42',
                                'alg' => 'RS256'
                            ]
                        ]
                    ]
                ]
            ],
            // Only the key matching the kid in the JWT header should be fixed.
            'All JWKS keys missing alg' => [
                'jwks' => [
                    'keys' => [
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '41',
                        ],
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '42',
                        ]
                    ]
                ],
                // RS256 JWT with kid = 42.
                'jwt' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjQyIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlRoZSBjYXQiLCJz'.
                    'bG9nYW4iOiJMb3ZlcyBpdCwgbG92ZXMgaXQsIGxvdmVzIE1vb2RsZSIsImlhdCI6MTUxNjIzOTAyMn0.EiqMEqufKJj74JevdTxXqzHvHGIcZ'.
                    'EFYhOe9sliL2FmlyiJcf7waObO2ZNwWvVZwTI4DfEGFamheMmTb6-YBODacDvH6BlQNb0H_6ye6AGl1u-3OAQj7i_SKsLuB37k6Lw5YFrwQYr'.
                    '7bjujSaQx6BL3kaqkqCdZhFjr2EYcn5-NehGHsevKqpMA-ShBovcndYkD5gfZEbXr59sgpQuJ43qO7gnGPzRbaJAEw_0_6v0r3y0pzDNfarNd'.
                    'fHfCZQbcF9T8dpHAeO4JMmuCanV8iJziI8ihVPwH-BwUJmzthyUgy8542FinHVbXo-88wu9xpbdV17VPgeGGBCpYpnVnWaA',
                'expected' => [
                    'jwks' => [
                        'keys' => [
                            [
                                'kty' => 'RSA',
                                'use' => 'sig',
                                'e' => 'AQAB',
                                'n' => '3nVf6',
                                'kid' => '41',
                            ],
                            [
                                'kty' => 'RSA',
                                'use' => 'sig',
                                'e' => 'AQAB',
                                'n' => '3nVf6',
                                'kid' => '42',
                                'alg' => 'RS256'
                            ]
                        ]
                    ]
                ]
            ],
            // Exception expected when JWT alg is supported but does not match the family of key in the JWK.
            'JWT kty algorithm family mismatch' => [
                'jwks' => [
                    'keys' => [
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '41',
                        ],
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '42',
                        ]
                    ]
                ],
                // ES256 JWT with kid = 42.
                'jwt' => 'eyJhbGciOiJFUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjQyIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwi'.
                    'YWRtaW4iOnRydWUsImlhdCI6MTUxNjIzOTAyMn0.dbUlZopFo7164JVLD0G4GoZOhoMYWhIXkgtlblBXT6fC3K4lJ38l3LzlEBhfRRKvJlXpe'.
                    'NNGmBg8V29jd5J33Q',
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
            // Exception expected when JWK kid field missing.
            'JWT missing kid header field' => [
                'jwks' => [
                    'keys' => [
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '41',
                            'alg' => 'RS256'
                        ],
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '42',
                            'alg' => 'RS256'
                        ]
                    ]
                ],
                // RS256 JWT with kid omitted.
                'jwt' => 'eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IkpvaG4gRG9lIiwiYWRtaW4iOnRydWU'.
                    'sImlhdCI6MTUxNjIzOTAyMn0.NHVaYe26MbtOYhSKkoKYdFVomg4i8ZJd8_-RU8VNbftc4TSMb4bXP3l3YlNWACwyXPGffz5aXHc6lty1Y2t4'.
                    'SWRqGteragsVdZufDn5BlnJl9pdR_kdVFUsra2rWKEofkZeIC4yWytE58sMIihvo9H1ScmmVwBcQP6XETqYd0aSHp1gOa9RdUPDvoXQ5oqygT'.
                    'qVtxaDr6wUFKrKItgBMzWIdNZ6y7O9E0DhEPTbE9rfBo6KTFsHAZnMg4k68CDp2woYIaXbmYTWcvbzIuHO7_37GT79XdIwkm95QJ7hYC9Riwr'.
                    'V7mesbY4PAahERJawntho0my942XheVLmGwLMBkQ',
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
            // Exception expected when JWT passes unsupported symmetrical alg.
            'JWT passes in unsupported alg' => [
                'jwks' => [
                    'keys' => [
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '41',
                        ],
                        [
                            'kty' => 'RSA',
                            'use' => 'sig',
                            'e' => 'AQAB',
                            'n' => '3nVf6',
                            'kid' => '42',
                        ]
                    ]
                ],
                // HS256 JWT with kid = 42.
                'jwt' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCIsImtpZCI6IjQyIn0.eyJzdWIiOiIxMjM0NTY3ODkwIiwibmFtZSI6IlRoZSBjYXQiLCJz'.
                    'bG9nYW4iOiJMb3ZlcyBpdCwgbG92ZXMgaXQsIGxvdmVzIE1vb2RsZSIsImlhdCI6MTUxNjIzOTAyMn0.zBM5Jw0BOig5-C1R7TD-TzH1QVmyD'.
                    'yMjbK0KGG76xIE',
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
        ];
    }
}
