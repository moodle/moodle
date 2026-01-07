<?php
/*
 * Copyright 2024 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Auth\CredentialSource;

use Google\Auth\ExecutableHandler\ExecutableHandler;
use Google\Auth\ExecutableHandler\ExecutableResponseError;
use Google\Auth\ExternalAccountCredentialSourceInterface;
use RuntimeException;

/**
 * ExecutableSource enables the exchange of workload identity pool external credentials for
 * Google access tokens by retrieving 3rd party tokens through a user supplied executable. These
 * scripts/executables are completely independent of the Google Cloud Auth libraries. These
 * credentials plug into ADC and will call the specified executable to retrieve the 3rd party token
 * to be exchanged for a Google access token.
 *
 * To use these credentials, the GOOGLE_EXTERNAL_ACCOUNT_ALLOW_EXECUTABLES environment variable
 * must be set to '1'. This is for security reasons.
 *
 * Both OIDC and SAML are supported. The executable must adhere to a specific response format
 * defined below.
 *
 * The executable must print out the 3rd party token to STDOUT in JSON format. When an
 * output_file is specified in the credential configuration, the executable must also handle writing the
 * JSON response to this file.
 *
 * <pre>
 * OIDC response sample:
 * {
 *   "version": 1,
 *   "success": true,
 *   "token_type": "urn:ietf:params:oauth:token-type:id_token",
 *   "id_token": "HEADER.PAYLOAD.SIGNATURE",
 *   "expiration_time": 1620433341
 * }
 *
 * SAML2 response sample:
 * {
 *   "version": 1,
 *   "success": true,
 *   "token_type": "urn:ietf:params:oauth:token-type:saml2",
 *   "saml_response": "...",
 *   "expiration_time": 1620433341
 * }
 *
 * Error response sample:
 * {
 *   "version": 1,
 *   "success": false,
 *   "code": "401",
 *   "message": "Error message."
 * }
 * </pre>
 *
 * The "expiration_time" field in the JSON response is only required for successful
 * responses when an output file was specified in the credential configuration
 *
 * The auth libraries will populate certain environment variables that will be accessible by the
 * executable, such as: GOOGLE_EXTERNAL_ACCOUNT_AUDIENCE, GOOGLE_EXTERNAL_ACCOUNT_TOKEN_TYPE,
 * GOOGLE_EXTERNAL_ACCOUNT_INTERACTIVE, GOOGLE_EXTERNAL_ACCOUNT_IMPERSONATED_EMAIL, and
 * GOOGLE_EXTERNAL_ACCOUNT_OUTPUT_FILE.
 */
class ExecutableSource implements ExternalAccountCredentialSourceInterface
{
    private const GOOGLE_EXTERNAL_ACCOUNT_ALLOW_EXECUTABLES = 'GOOGLE_EXTERNAL_ACCOUNT_ALLOW_EXECUTABLES';
    private const SAML_SUBJECT_TOKEN_TYPE = 'urn:ietf:params:oauth:token-type:saml2';
    private const OIDC_SUBJECT_TOKEN_TYPE1 = 'urn:ietf:params:oauth:token-type:id_token';
    private const OIDC_SUBJECT_TOKEN_TYPE2 = 'urn:ietf:params:oauth:token-type:jwt';

    private string $command;
    private ExecutableHandler $executableHandler;
    private ?string $outputFile;

    /**
     * @param string $command    The string command to run to get the subject token.
     * @param string|null $outputFile
     */
    public function __construct(
        string $command,
        ?string $outputFile,
        ?ExecutableHandler $executableHandler = null,
    ) {
        $this->command = $command;
        $this->outputFile = $outputFile;
        $this->executableHandler = $executableHandler ?: new ExecutableHandler();
    }

    /**
     * Gets the unique key for caching
     * The format for the cache key is:
     * Command.OutputFile
     *
     * @return ?string
     */
    public function getCacheKey(): ?string
    {
        return $this->command . '.' . $this->outputFile;
    }

    /**
     * @param callable|null $httpHandler unused.
     * @return string
     * @throws RuntimeException if the executable is not allowed to run.
     * @throws ExecutableResponseError if the executable response is invalid.
     */
    public function fetchSubjectToken(?callable $httpHandler = null): string
    {
        // Check if the executable is allowed to run.
        if (getenv(self::GOOGLE_EXTERNAL_ACCOUNT_ALLOW_EXECUTABLES) !== '1') {
            throw new RuntimeException(
                'Pluggable Auth executables need to be explicitly allowed to run by '
                . 'setting the GOOGLE_EXTERNAL_ACCOUNT_ALLOW_EXECUTABLES environment '
                . 'Variable to 1.'
            );
        }

        if (!$executableResponse = $this->getCachedExecutableResponse()) {
            // Run the executable.
            $exitCode = ($this->executableHandler)($this->command);
            $output = $this->executableHandler->getOutput();

            // If the exit code is not 0, throw an exception with the output as the error details
            if ($exitCode !== 0) {
                throw new ExecutableResponseError(
                    'The executable failed to run'
                    . ($output ? ' with the following error: ' . $output : '.'),
                    (string) $exitCode
                );
            }

            $executableResponse = $this->parseExecutableResponse($output);

            // Validate expiration.
            if (isset($executableResponse['expiration_time']) && time() >= $executableResponse['expiration_time']) {
                throw new ExecutableResponseError('Executable response is expired.');
            }
        }

        // Throw error when the request was unsuccessful
        if ($executableResponse['success'] === false) {
            throw new ExecutableResponseError($executableResponse['message'], (string) $executableResponse['code']);
        }

        // Return subject token field based on the token type
        return $executableResponse['token_type'] === self::SAML_SUBJECT_TOKEN_TYPE
            ? $executableResponse['saml_response']
            : $executableResponse['id_token'];
    }

    /**
     * @return array<string, mixed>|null
     */
    private function getCachedExecutableResponse(): ?array
    {
        if (
            $this->outputFile
            && file_exists($this->outputFile)
            && !empty(trim($outputFileContents = (string) file_get_contents($this->outputFile)))
        ) {
            try {
                $executableResponse = $this->parseExecutableResponse($outputFileContents);
            } catch (ExecutableResponseError $e) {
                throw new ExecutableResponseError(
                    'Error in output file: ' . $e->getMessage(),
                    'INVALID_OUTPUT_FILE'
                );
            }

            if ($executableResponse['success'] === false) {
                // If the cached token was unsuccessful, run the executable to get a new one.
                return null;
            }

            if (isset($executableResponse['expiration_time']) && time() >= $executableResponse['expiration_time']) {
                // If the cached token is expired, run the executable to get a new one.
                return null;
            }

            return $executableResponse;
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function parseExecutableResponse(string $response): array
    {
        $executableResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new ExecutableResponseError(
                'The executable returned an invalid response: ' . $response,
                'INVALID_RESPONSE'
            );
        }
        if (!array_key_exists('version', $executableResponse)) {
            throw new ExecutableResponseError('Executable response must contain a "version" field.');
        }
        if (!array_key_exists('success', $executableResponse)) {
            throw new ExecutableResponseError('Executable response must contain a "success" field.');
        }

        // Validate required fields for a successful response.
        if ($executableResponse['success']) {
            // Validate token type field.
            $tokenTypes = [self::SAML_SUBJECT_TOKEN_TYPE, self::OIDC_SUBJECT_TOKEN_TYPE1, self::OIDC_SUBJECT_TOKEN_TYPE2];
            if (!isset($executableResponse['token_type'])) {
                throw new ExecutableResponseError(
                    'Executable response must contain a "token_type" field when successful'
                );
            }
            if (!in_array($executableResponse['token_type'], $tokenTypes)) {
                throw new ExecutableResponseError(sprintf(
                    'Executable response "token_type" field must be one of %s.',
                    implode(', ', $tokenTypes)
                ));
            }

            // Validate subject token for SAML and OIDC.
            if ($executableResponse['token_type'] === self::SAML_SUBJECT_TOKEN_TYPE) {
                if (empty($executableResponse['saml_response'])) {
                    throw new ExecutableResponseError(sprintf(
                        'Executable response must contain a "saml_response" field when token_type=%s.',
                        self::SAML_SUBJECT_TOKEN_TYPE
                    ));
                }
            } elseif (empty($executableResponse['id_token'])) {
                throw new ExecutableResponseError(sprintf(
                    'Executable response must contain a "id_token" field when '
                    . 'token_type=%s.',
                    $executableResponse['token_type']
                ));
            }

            // Validate expiration exists when an output file is specified.
            if ($this->outputFile) {
                if (!isset($executableResponse['expiration_time'])) {
                    throw new ExecutableResponseError(
                        'The executable response must contain a "expiration_time" field for successful responses ' .
                        'when an output_file has been specified in the configuration.'
                    );
                }
            }
        } else {
            // Both code and message must be provided for unsuccessful responses.
            if (!array_key_exists('code', $executableResponse)) {
                throw new ExecutableResponseError('Executable response must contain a "code" field when unsuccessful.');
            }
            if (empty($executableResponse['message'])) {
                throw new ExecutableResponseError('Executable response must contain a "message" field when unsuccessful.');
            }
        }

        return $executableResponse;
    }
}
