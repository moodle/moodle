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

class GoogleCloudApigeeV1App extends \Google\Collection
{
  protected $collection_key = 'scopes';
  protected $apiProductsType = GoogleCloudApigeeV1ApiProductRef::class;
  protected $apiProductsDataType = 'array';
  /**
   * Name of the AppGroup
   *
   * @var string
   */
  public $appGroup;
  /**
   * ID of the app.
   *
   * @var string
   */
  public $appId;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * Callback URL used by OAuth 2.0 authorization servers to communicate
   * authorization codes back to apps.
   *
   * @var string
   */
  public $callbackUrl;
  /**
   * Name of the company that owns the app.
   *
   * @var string
   */
  public $companyName;
  /**
   * Output only. Unix time when the app was created.
   *
   * @var string
   */
  public $createdAt;
  protected $credentialsType = GoogleCloudApigeeV1Credential::class;
  protected $credentialsDataType = 'array';
  /**
   * Email of the developer.
   *
   * @var string
   */
  public $developerEmail;
  /**
   * ID of the developer.
   *
   * @var string
   */
  public $developerId;
  /**
   * Duration, in milliseconds, of the consumer key that will be generated for
   * the app. The default value, -1, indicates an infinite validity period. Once
   * set, the expiration can't be updated. json key: keyExpiresIn
   *
   * @var string
   */
  public $keyExpiresIn;
  /**
   * Output only. Last modified time as milliseconds since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Name of the app.
   *
   * @var string
   */
  public $name;
  /**
   * Scopes to apply to the app. The specified scope names must already exist on
   * the API product that you associate with the app.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Status of the credential.
   *
   * @var string
   */
  public $status;

  /**
   * List of API products associated with the app.
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
   * Name of the AppGroup
   *
   * @param string $appGroup
   */
  public function setAppGroup($appGroup)
  {
    $this->appGroup = $appGroup;
  }
  /**
   * @return string
   */
  public function getAppGroup()
  {
    return $this->appGroup;
  }
  /**
   * ID of the app.
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
   * List of attributes.
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
   * authorization codes back to apps.
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
   * Name of the company that owns the app.
   *
   * @param string $companyName
   */
  public function setCompanyName($companyName)
  {
    $this->companyName = $companyName;
  }
  /**
   * @return string
   */
  public function getCompanyName()
  {
    return $this->companyName;
  }
  /**
   * Output only. Unix time when the app was created.
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
   * Output only. Set of credentials for the app. Credentials are API key/secret
   * pairs associated with API products.
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
   * Email of the developer.
   *
   * @param string $developerEmail
   */
  public function setDeveloperEmail($developerEmail)
  {
    $this->developerEmail = $developerEmail;
  }
  /**
   * @return string
   */
  public function getDeveloperEmail()
  {
    return $this->developerEmail;
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
   * Duration, in milliseconds, of the consumer key that will be generated for
   * the app. The default value, -1, indicates an infinite validity period. Once
   * set, the expiration can't be updated. json key: keyExpiresIn
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
   * Output only. Last modified time as milliseconds since epoch.
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
   * Name of the app.
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
   * Scopes to apply to the app. The specified scope names must already exist on
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
   * Status of the credential.
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
class_alias(GoogleCloudApigeeV1App::class, 'Google_Service_Apigee_GoogleCloudApigeeV1App');
