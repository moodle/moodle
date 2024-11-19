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

namespace core\oauth2\discovery;

use core\http_client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

/**
 * Unit tests for {@see auth_server_config_reader}.
 *
 * @coversDefaultClass \core\oauth2\discovery\auth_server_config_reader
 * @package core
 * @copyright 2023 Jake Dallimore <jrhdallimore@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_server_config_reader_test extends \advanced_testcase {

    /**
     * Test reading the config for an auth server.
     *
     * @covers ::read_configuration
     * @dataProvider config_provider
     * @param string $issuerurl the auth server issuer URL.
     * @param ResponseInterface $httpresponse a stub HTTP response.
     * @param null|string $altwellknownsuffix an alternate value for the well known suffix to use in the reader.
     * @param array $expected test expectations.
     * @return void
     */
    public function test_read_configuration(string $issuerurl, ResponseInterface $httpresponse, ?string $altwellknownsuffix = null,
            array $expected = []): void {

        $mock = new MockHandler([$httpresponse]);
        $handlerstack = HandlerStack::create($mock);
        if (!empty($expected['request'])) {
            // Request history tracking to allow asserting that request was sent as expected below (to the stub client).
            $container = [];
            $history = Middleware::history($container);
            $handlerstack->push($history);
        }

        $args = [
            new http_client(['handler' => $handlerstack]),
        ];
        if (!is_null($altwellknownsuffix)) {
            $args[] = $altwellknownsuffix;
        }

        if (!empty($expected['exception'])) {
            $this->expectException($expected['exception']);
        }
        $configreader = new auth_server_config_reader(...$args);
        $config = $configreader->read_configuration(new \moodle_url($issuerurl));

        if (!empty($expected['request'])) {
            // Verify the request goes to the correct URL (i.e. the well known suffix is correctly positioned).
            $this->assertEquals($expected['request']['url'], $container[0]['request']->getUri());
        }

        $this->assertEquals($expected['metadata'], (array) $config);
    }

    /**
     * Provider for testing read_configuration().
     *
     * @return array test data.
     */
    public static function config_provider(): array {
        return [
            'Valid, good issuer URL, good config' => [
                'issuer_url' => 'https://app.example.com',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'request' => [
                        'url' => 'https://app.example.com/.well-known/oauth-authorization-server'
                    ],
                    'metadata' => [
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ]
                ]
            ],
            'Valid, issuer URL with path component confirming well known suffix placement' => [
                'issuer_url' => 'https://app.example.com/some/path',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'request' => [
                        'url' => 'https://app.example.com/.well-known/oauth-authorization-server/some/path'
                    ],
                    'metadata' => [
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ]
                ]
            ],
            'Valid, single trailing / path only' => [
                'issuer_url' => 'https://app.example.com/',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'request' => [
                        'url' => 'https://app.example.com/.well-known/oauth-authorization-server'
                    ],
                    'metadata' => [
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ]
                ]
            ],
            'Invalid, non HTTPS issuer URL' => [
                'issuer_url' => 'http://app.example.com',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
            'Invalid, query string in issuer URL' => [
                'issuer_url' => 'https://app.example.com?test=cat',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
            'Invalid, fragment in issuer URL' => [
                'issuer_url' => 'https://app.example.com/#cat',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'exception' => \moodle_exception::class
                ]
            ],
            'Valid, port in issuer URL' => [
                'issuer_url' => 'https://app.example.com:8080/some/path',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => null,
                'expected' => [
                    'request' => [
                        'url' => 'https://app.example.com:8080/.well-known/oauth-authorization-server/some/path'
                    ],
                    'metadata' => [
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ]
                ]
            ],
            'Valid, alternate well known suffix, no path' => [
                'issuer_url' => 'https://app.example.com',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => 'openid-configuration', // An application using the openid well known, which is valid.
                'expected' => [
                    'request' => [
                        'url' => 'https://app.example.com/.well-known/openid-configuration'
                    ],
                    'metadata' => [
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ]
                ]
            ],
            'Valid, alternate well known suffix, with path' => [
                'issuer_url' => 'https://app.example.com/some/path/',
                'http_response' => new Response(
                    200,
                    ['Content-Type' => 'application/json'],
                    json_encode([
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ])
                ),
                'well_known_suffix' => 'openid-configuration', // An application using the openid well known, which is valid.
                'expected' => [
                    'request' => [
                        'url' => 'https://app.example.com/.well-known/openid-configuration/some/path/'
                    ],
                    'metadata' => [
                        "issuer" => "https://app.example.com",
                        "authorization_endpoint" => "https://app.example.com/authorize",
                        "token_endpoint" => "https://app.example.com/token",
                        "token_endpoint_auth_methods_supported" => [
                            "client_secret_basic",
                            "private_key_jwt"
                        ],
                        "token_endpoint_auth_signing_alg_values_supported" => [
                            "RS256",
                            "ES256"
                        ],
                        "userinfo_endpoint" => "https://app.example.com/userinfo",
                        "jwks_uri" => "https://app.example.com/jwks.json",
                        "registration_endpoint" => "https://app.example.com/register",
                        "scopes_supported" => [
                            "openid",
                            "profile",
                            "email",
                        ],
                        "response_types_supported" => [
                            "code",
                            "code token"
                        ],
                        "service_documentation" => "http://app.example.com/service_documentation.html",
                        "ui_locales_supported" => [
                            "en-US",
                            "en-GB",
                            "fr-FR",
                        ]
                    ]
                ]
            ],
            'Invalid, bad response' => [
                'issuer_url' => 'https://app.example.com',
                'http_response' => new Response(404),
                'well_known_suffix' => null,
                'expected' => [
                    'exception' => ClientException::class
                ]
            ]
        ];
    }
}
