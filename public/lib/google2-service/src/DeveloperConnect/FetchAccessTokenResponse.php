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

namespace Google\Service\DeveloperConnect;

class FetchAccessTokenResponse extends \Google\Collection
{
  protected $collection_key = 'scopes';
  protected $exchangeErrorType = ExchangeError::class;
  protected $exchangeErrorDataType = '';
  /**
   * Expiration timestamp. Can be empty if unknown or non-expiring.
   *
   * @var string
   */
  public $expirationTime;
  /**
   * The scopes of the access token.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * The token content.
   *
   * @var string
   */
  public $token;

  /**
   * The error resulted from exchanging OAuth tokens from the service provider.
   *
   * @param ExchangeError $exchangeError
   */
  public function setExchangeError(ExchangeError $exchangeError)
  {
    $this->exchangeError = $exchangeError;
  }
  /**
   * @return ExchangeError
   */
  public function getExchangeError()
  {
    return $this->exchangeError;
  }
  /**
   * Expiration timestamp. Can be empty if unknown or non-expiring.
   *
   * @param string $expirationTime
   */
  public function setExpirationTime($expirationTime)
  {
    $this->expirationTime = $expirationTime;
  }
  /**
   * @return string
   */
  public function getExpirationTime()
  {
    return $this->expirationTime;
  }
  /**
   * The scopes of the access token.
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * The token content.
   *
   * @param string $token
   */
  public function setToken($token)
  {
    $this->token = $token;
  }
  /**
   * @return string
   */
  public function getToken()
  {
    return $this->token;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchAccessTokenResponse::class, 'Google_Service_DeveloperConnect_FetchAccessTokenResponse');
