<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\CloudSearch;

class RepositoryError extends \Google\Model
{
  /**
   * Unknown error.
   */
  public const TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Unknown or unreachable host.
   */
  public const TYPE_NETWORK_ERROR = 'NETWORK_ERROR';
  /**
   * DNS problem, such as the DNS server is not responding.
   */
  public const TYPE_DNS_ERROR = 'DNS_ERROR';
  /**
   * Cannot connect to the repository server.
   */
  public const TYPE_CONNECTION_ERROR = 'CONNECTION_ERROR';
  /**
   * Failed authentication due to incorrect credentials.
   */
  public const TYPE_AUTHENTICATION_ERROR = 'AUTHENTICATION_ERROR';
  /**
   * Service account is not authorized for the repository.
   */
  public const TYPE_AUTHORIZATION_ERROR = 'AUTHORIZATION_ERROR';
  /**
   * Repository server error.
   */
  public const TYPE_SERVER_ERROR = 'SERVER_ERROR';
  /**
   * Quota exceeded.
   */
  public const TYPE_QUOTA_EXCEEDED = 'QUOTA_EXCEEDED';
  /**
   * Server temporarily unavailable.
   */
  public const TYPE_SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';
  /**
   * Client-related error, such as an invalid request from the connector to the
   * repository server.
   */
  public const TYPE_CLIENT_ERROR = 'CLIENT_ERROR';
  /**
   * Message that describes the error. The maximum allowable length of the
   * message is 8192 characters.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Error codes. Matches the definition of HTTP status codes.
   *
   * @var int
   */
  public $httpStatusCode;
  /**
   * The type of error.
   *
   * @var string
   */
  public $type;

  /**
   * Message that describes the error. The maximum allowable length of the
   * message is 8192 characters.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Error codes. Matches the definition of HTTP status codes.
   *
   * @param int $httpStatusCode
   */
  public function setHttpStatusCode($httpStatusCode)
  {
    $this->httpStatusCode = $httpStatusCode;
  }
  /**
   * @return int
   */
  public function getHttpStatusCode()
  {
    return $this->httpStatusCode;
  }
  /**
   * The type of error.
   *
   * Accepted values: UNKNOWN, NETWORK_ERROR, DNS_ERROR, CONNECTION_ERROR,
   * AUTHENTICATION_ERROR, AUTHORIZATION_ERROR, SERVER_ERROR, QUOTA_EXCEEDED,
   * SERVICE_UNAVAILABLE, CLIENT_ERROR
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RepositoryError::class, 'Google_Service_CloudSearch_RepositoryError');
