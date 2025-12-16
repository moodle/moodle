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

class GoogleCloudApigeeV1Credential extends \Google\Collection
{
  protected $collection_key = 'scopes';
  protected $apiProductsType = GoogleCloudApigeeV1ApiProductRef::class;
  protected $apiProductsDataType = 'array';
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * Consumer key.
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
   * Time the credential will expire in milliseconds since epoch.
   *
   * @var string
   */
  public $expiresAt;
  /**
   * Time the credential was issued in milliseconds since epoch.
   *
   * @var string
   */
  public $issuedAt;
  /**
   * List of scopes to apply to the app. Specified scopes must already exist on
   * the API product that you associate with the app.
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
   * List of API products this credential can be used for.
   *
   * @param GoogleCloudApigeeV1ApiProductRef[] $apiProducts
   */
  public function setApiProducts($apiProducts)
  {
    $this->apiProducts = $apiProducts;
  }
  /**
   * @return GoogleCloudApigeeV1ApiProductRef[]
   */
  public function getApiProducts()
  {
    return $this->apiProducts;
  }
  /**
   * List of attributes associated with this credential.
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
   * Consumer key.
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
   * Time the credential will expire in milliseconds since epoch.
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
   * Time the credential was issued in milliseconds since epoch.
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
   * List of scopes to apply to the app. Specified scopes must already exist on
   * the API product that you associate with the app.
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
class_alias(GoogleCloudApigeeV1Credential::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Credential');
