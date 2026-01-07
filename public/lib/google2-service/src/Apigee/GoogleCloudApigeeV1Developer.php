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

class GoogleCloudApigeeV1Developer extends \Google\Collection
{
  protected $collection_key = 'companies';
  /**
   * Access type.
   *
   * @var string
   */
  public $accessType;
  /**
   * Developer app family.
   *
   * @var string
   */
  public $appFamily;
  /**
   * List of apps associated with the developer.
   *
   * @var string[]
   */
  public $apps;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * List of companies associated with the developer.
   *
   * @var string[]
   */
  public $companies;
  /**
   * Output only. Time at which the developer was created in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * ID of the developer. **Note**: IDs are generated internally by Apigee and
   * are not guaranteed to stay the same over time.
   *
   * @var string
   */
  public $developerId;
  /**
   * Required. Email address of the developer. This value is used to uniquely
   * identify the developer in Apigee hybrid. Note that the email address has to
   * be in lowercase only.
   *
   * @var string
   */
  public $email;
  /**
   * Required. First name of the developer.
   *
   * @var string
   */
  public $firstName;
  /**
   * Output only. Time at which the developer was last modified in milliseconds
   * since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Required. Last name of the developer.
   *
   * @var string
   */
  public $lastName;
  /**
   * Output only. Name of the Apigee organization in which the developer
   * resides.
   *
   * @var string
   */
  public $organizationName;
  /**
   * Output only. Status of the developer. Valid values are `active` and
   * `inactive`.
   *
   * @var string
   */
  public $status;
  /**
   * Required. User name of the developer. Not used by Apigee hybrid.
   *
   * @var string
   */
  public $userName;

  /**
   * Access type.
   *
   * @param string $accessType
   */
  public function setAccessType($accessType)
  {
    $this->accessType = $accessType;
  }
  /**
   * @return string
   */
  public function getAccessType()
  {
    return $this->accessType;
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
   * List of apps associated with the developer.
   *
   * @param string[] $apps
   */
  public function setApps($apps)
  {
    $this->apps = $apps;
  }
  /**
   * @return string[]
   */
  public function getApps()
  {
    return $this->apps;
  }
  /**
   * Optional. Developer attributes (name/value pairs). The custom attribute
   * limit is 18.
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
   * List of companies associated with the developer.
   *
   * @param string[] $companies
   */
  public function setCompanies($companies)
  {
    $this->companies = $companies;
  }
  /**
   * @return string[]
   */
  public function getCompanies()
  {
    return $this->companies;
  }
  /**
   * Output only. Time at which the developer was created in milliseconds since
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
   * ID of the developer. **Note**: IDs are generated internally by Apigee and
   * are not guaranteed to stay the same over time.
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
   * Required. Email address of the developer. This value is used to uniquely
   * identify the developer in Apigee hybrid. Note that the email address has to
   * be in lowercase only.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Required. First name of the developer.
   *
   * @param string $firstName
   */
  public function setFirstName($firstName)
  {
    $this->firstName = $firstName;
  }
  /**
   * @return string
   */
  public function getFirstName()
  {
    return $this->firstName;
  }
  /**
   * Output only. Time at which the developer was last modified in milliseconds
   * since epoch.
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
   * Required. Last name of the developer.
   *
   * @param string $lastName
   */
  public function setLastName($lastName)
  {
    $this->lastName = $lastName;
  }
  /**
   * @return string
   */
  public function getLastName()
  {
    return $this->lastName;
  }
  /**
   * Output only. Name of the Apigee organization in which the developer
   * resides.
   *
   * @param string $organizationName
   */
  public function setOrganizationName($organizationName)
  {
    $this->organizationName = $organizationName;
  }
  /**
   * @return string
   */
  public function getOrganizationName()
  {
    return $this->organizationName;
  }
  /**
   * Output only. Status of the developer. Valid values are `active` and
   * `inactive`.
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
  /**
   * Required. User name of the developer. Not used by Apigee hybrid.
   *
   * @param string $userName
   */
  public function setUserName($userName)
  {
    $this->userName = $userName;
  }
  /**
   * @return string
   */
  public function getUserName()
  {
    return $this->userName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Developer::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Developer');
