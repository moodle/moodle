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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1AppGroupAppKey extends \Google\Collection
{
  protected $collection_key = 'scopes';
  protected $apiProductsType = GoogleCloudApigeeV1APIProductAssociation::class;
  protected $apiProductsDataType = 'array';
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * Immutable. Consumer key.
   *
   * @var string
   */
  public $consumerKey;
  /**
   * Secret key.
   *
   * @var string
   */
  public $consumerSecret;
  /**
   * Output only. Time the AppGroup app expires in milliseconds since epoch.
   *
   * @var string
   */
  public $expiresAt;
  /**
   * Immutable. Expiration time, in seconds, for the consumer key. If not set or
   * left to the default value of `-1`, the API key never expires. The
   * expiration time can't be updated after it is set.
   *
   * @var string
   */
  public $expiresInSeconds;
  /**
   * Output only. Time the AppGroup app was created in milliseconds since epoch.
   *
   * @var string
   */
  public $issuedAt;
  /**
   * Scopes to apply to the app. The specified scope names must already be
   * defined for the API product that you associate with the app.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Status of the credential. Valid values include `approved` or `revoked`.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. List of API products and its status for which the credential
   * can be used. **Note**: Use UpdateAppGroupAppKeyApiProductRequest API to
   * make the association after the consumer key and secret are created.
   *
   * @param GoogleCloudApigeeV1APIProductAssociation[] $apiProducts
   */
  public function setApiProducts($apiProducts)
  {
    $this->apiProducts = $apiProducts;
  }
  /**
   * @return GoogleCloudApigeeV1APIProductAssociation[]
   */
  public function getApiProducts()
  {
    return $this->apiProducts;
  }
  /**
   * List of attributes associated with the credential.
   *
   * @param GoogleCloudApigeeV1Attribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApigeeV1Attribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Immutable. Consumer key.
   *
   * @param string $consumerKey
   */
  public function setConsumerKey($consumerKey)
  {
    $this->consumerKey = $consumerKey;
  }
  /**
   * @return string
   */
  public function getConsumerKey()
  {
    return $this->consumerKey;
  }
  /**
   * Secret key.
   *
   * @param string $consumerSecret
   */
  public function setConsumerSecret($consumerSecret)
  {
    $this->consumerSecret = $consumerSecret;
  }
  /**
   * @return string
   */
  public function getConsumerSecret()
  {
    return $this->consumerSecret;
  }
  /**
   * Output only. Time the AppGroup app expires in milliseconds since epoch.
   *
   * @param string $expiresAt
   */
  public function setExpiresAt($expiresAt)
  {
    $this->expiresAt = $expiresAt;
  }
  /**
   * @return string
   */
  public function getExpiresAt()
  {
    return $this->expiresAt;
  }
  /**
   * Immutable. Expiration time, in seconds, for the consumer key. If not set or
   * left to the default value of `-1`, the API key never expires. The
   * expiration time can't be updated after it is set.
   *
   * @param string $expiresInSeconds
   */
  public function setExpiresInSeconds($expiresInSeconds)
  {
    $this->expiresInSeconds = $expiresInSeconds;
  }
  /**
   * @return string
   */
  public function getExpiresInSeconds()
  {
    return $this->expiresInSeconds;
  }
  /**
   * Output only. Time the AppGroup app was created in milliseconds since epoch.
   *
   * @param string $issuedAt
   */
  public function setIssuedAt($issuedAt)
  {
    $this->issuedAt = $issuedAt;
  }
  /**
   * @return string
   */
  public function getIssuedAt()
  {
    return $this->issuedAt;
  }
  /**
   * Scopes to apply to the app. The specified scope names must already be
   * defined for the API product that you associate with the app.
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
   * Status of the credential. Valid values include `approved` or `revoked`.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1AppGroupAppKey::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AppGroupAppKey');
