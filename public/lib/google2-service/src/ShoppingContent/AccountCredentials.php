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

namespace Google\Service\ShoppingContent;

class AccountCredentials extends \Google\Model
{
  /**
   * Unknown purpose.
   */
  public const PURPOSE_ACCOUNT_CREDENTIALS_PURPOSE_UNSPECIFIED = 'ACCOUNT_CREDENTIALS_PURPOSE_UNSPECIFIED';
  /**
   * The credentials allow Google to manage Shopify orders on behalf of the
   * merchant (deprecated).
   *
   * @deprecated
   */
  public const PURPOSE_SHOPIFY_ORDER_MANAGEMENT = 'SHOPIFY_ORDER_MANAGEMENT';
  /**
   * The credentials allow Google to manage Shopify integration on behalf of the
   * merchant.
   */
  public const PURPOSE_SHOPIFY_INTEGRATION = 'SHOPIFY_INTEGRATION';
  /**
   * An OAuth access token.
   *
   * @var string
   */
  public $accessToken;
  /**
   * The amount of time, in seconds, after which the access token is no longer
   * valid.
   *
   * @var string
   */
  public $expiresIn;
  /**
   * Indicates to Google how Google should use these OAuth tokens.
   *
   * @var string
   */
  public $purpose;

  /**
   * An OAuth access token.
   *
   * @param string $accessToken
   */
  public function setAccessToken($accessToken)
  {
    $this->accessToken = $accessToken;
  }
  /**
   * @return string
   */
  public function getAccessToken()
  {
    return $this->accessToken;
  }
  /**
   * The amount of time, in seconds, after which the access token is no longer
   * valid.
   *
   * @param string $expiresIn
   */
  public function setExpiresIn($expiresIn)
  {
    $this->expiresIn = $expiresIn;
  }
  /**
   * @return string
   */
  public function getExpiresIn()
  {
    return $this->expiresIn;
  }
  /**
   * Indicates to Google how Google should use these OAuth tokens.
   *
   * Accepted values: ACCOUNT_CREDENTIALS_PURPOSE_UNSPECIFIED,
   * SHOPIFY_ORDER_MANAGEMENT, SHOPIFY_INTEGRATION
   *
   * @param self::PURPOSE_* $purpose
   */
  public function setPurpose($purpose)
  {
    $this->purpose = $purpose;
  }
  /**
   * @return self::PURPOSE_*
   */
  public function getPurpose()
  {
    return $this->purpose;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountCredentials::class, 'Google_Service_ShoppingContent_AccountCredentials');
