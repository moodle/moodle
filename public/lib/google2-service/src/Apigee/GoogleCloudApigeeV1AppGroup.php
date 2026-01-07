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

class GoogleCloudApigeeV1AppGroup extends \Google\Collection
{
  protected $collection_key = 'attributes';
  /**
   * Output only. Internal identifier that cannot be edited
   *
   * @var string
   */
  public $appGroupId;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * channel identifier identifies the owner maintaing this grouping.
   *
   * @var string
   */
  public $channelId;
  /**
   * A reference to the associated storefront/marketplace.
   *
   * @var string
   */
  public $channelUri;
  /**
   * Output only. Created time as milliseconds since epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * app group name displayed in the UI
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Modified time as milliseconds since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * Immutable. Name of the AppGroup. Characters you can use in the name are
   * restricted to: A-Z0-9._\-$ %.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. the org the app group is created
   *
   * @var string
   */
  public $organization;
  /**
   * Valid values are `active` or `inactive`. Note that the status of the
   * AppGroup should be updated via UpdateAppGroupRequest by setting the action
   * as `active` or `inactive`.
   *
   * @var string
   */
  public $status;

  /**
   * Output only. Internal identifier that cannot be edited
   *
   * @param string $appGroupId
   */
  public function setAppGroupId($appGroupId)
  {
    $this->appGroupId = $appGroupId;
  }
  /**
   * @return string
   */
  public function getAppGroupId()
  {
    return $this->appGroupId;
  }
  /**
   * A list of attributes
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
   * channel identifier identifies the owner maintaing this grouping.
   *
   * @param string $channelId
   */
  public function setChannelId($channelId)
  {
    $this->channelId = $channelId;
  }
  /**
   * @return string
   */
  public function getChannelId()
  {
    return $this->channelId;
  }
  /**
   * A reference to the associated storefront/marketplace.
   *
   * @param string $channelUri
   */
  public function setChannelUri($channelUri)
  {
    $this->channelUri = $channelUri;
  }
  /**
   * @return string
   */
  public function getChannelUri()
  {
    return $this->channelUri;
  }
  /**
   * Output only. Created time as milliseconds since epoch.
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
   * app group name displayed in the UI
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Modified time as milliseconds since epoch.
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
   * Immutable. Name of the AppGroup. Characters you can use in the name are
   * restricted to: A-Z0-9._\-$ %.
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
   * Immutable. the org the app group is created
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * Valid values are `active` or `inactive`. Note that the status of the
   * AppGroup should be updated via UpdateAppGroupRequest by setting the action
   * as `active` or `inactive`.
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
class_alias(GoogleCloudApigeeV1AppGroup::class, 'Google_Service_Apigee_GoogleCloudApigeeV1AppGroup');
