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

class GoogleCloudApigeeV1DeveloperApp extends \Google\Collection
{
  protected $collection_key = 'scopes';
  /**
   * List of API products associated with the developer app.
   *
   * @var string[]
   */
  public $apiProducts;
  /**
   * Developer app family.
   *
   * @var string
   */
  public $appFamily;
  /**
   * ID of the developer app. This ID is not user specified but is automatically
   * generated on app creation. appId is a UUID.
   *
   * @var string
   */
  public $appId;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * Callback URL used by OAuth 2.0 authorization servers to communicate
   * authorization codes back to developer apps.
   *
   * @var string
   */
  public $callbackUrl;
  /**
   * Output only. Time the developer app was created in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $createdAt;
  protected $credentialsType = GoogleCloudApigeeV1Credential::class;
  protected $credentialsDataType = 'array';
  /**
   * ID of the developer.
   *
   * @var string
   */
  public $developerId;
  /**
   * Expiration time, in milliseconds, for the consumer key that is generated
   * for the developer app. If not set or left to the default value of `-1`, the
   * API key never expires. The expiration time can't be updated after it is
   * set.
   *
   * @var string
   */
  public $keyExpiresIn;
  /**
   * Output only. Time the developer app was modified in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Name of the developer app.
   *
   * @var string
   */
  public $name;
  /**
   * Scopes to apply to the developer app. The specified scopes must already
   * exist for the API product that you associate with the developer app.
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
   * List of API products associated with the developer app.
   *
   * @param string[] $apiProducts
   */
  public function setApiProducts($apiProducts)
  {
    $this->apiProducts = $apiProducts;
  }
  /**
   * @return string[]
   */
  public function getApiProducts()
  {
    return $this->apiProducts;
  }
  /**
   * Developer app family.
   *
   * @param string $appFamily
   */
  public function setAppFamily($appFamily)
  {
    $this->appFamily = $appFamily;
  }
  /**
   * @return string
   */
  public function getAppFamily()
  {
    return $this->appFamily;
  }
  /**
   * ID of the developer app. This ID is not user specified but is automatically
   * generated on app creation. appId is a UUID.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * List of attributes for the developer app.
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
   * Callback URL used by OAuth 2.0 authorization servers to communicate
   * authorization codes back to developer apps.
   *
   * @param string $callbackUrl
   */
  public function setCallbackUrl($callbackUrl)
  {
    $this->callbackUrl = $callbackUrl;
  }
  /**
   * @return string
   */
  public function getCallbackUrl()
  {
    return $this->callbackUrl;
  }
  /**
   * Output only. Time the developer app was created in milliseconds since
   * epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Output only. Set of credentials for the developer app consisting of the
   * consumer key/secret pairs associated with the API products.
   *
   * @param GoogleCloudApigeeV1Credential[] $credentials
   */
  public function setCredentials($credentials)
  {
    $this->credentials = $credentials;
  }
  /**
   * @return GoogleCloudApigeeV1Credential[]
   */
  public function getCredentials()
  {
    return $this->credentials;
  }
  /**
   * ID of the developer.
   *
   * @param string $developerId
   */
  public function setDeveloperId($developerId)
  {
    $this->developerId = $developerId;
  }
  /**
   * @return string
   */
  public function getDeveloperId()
  {
    return $this->developerId;
  }
  /**
   * Expiration time, in milliseconds, for the consumer key that is generated
   * for the developer app. If not set or left to the default value of `-1`, the
   * API key never expires. The expiration time can't be updated after it is
   * set.
   *
   * @param string $keyExpiresIn
   */
  public function setKeyExpiresIn($keyExpiresIn)
  {
    $this->keyExpiresIn = $keyExpiresIn;
  }
  /**
   * @return string
   */
  public function getKeyExpiresIn()
  {
    return $this->keyExpiresIn;
  }
  /**
   * Output only. Time the developer app was modified in milliseconds since
   * epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Name of the developer app.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Scopes to apply to the developer app. The specified scopes must already
   * exist for the API product that you associate with the developer app.
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
class_alias(GoogleCloudApigeeV1DeveloperApp::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DeveloperApp');
