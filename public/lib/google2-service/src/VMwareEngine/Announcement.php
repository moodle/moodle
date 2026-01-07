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

namespace Google\Service\VMwareEngine;

class Announcement extends \Google\Model
{
  /**
   * The default value. This value should never be used.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Active announcement which should be visible to user.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Inactive announcement which should not be visible to user.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * Announcement which is being deleted
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * Announcement which being created
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * Optional. Activity type of the announcement There can be only one active
   * announcement for a given activity type and target resource.
   *
   * @var string
   */
  public $activityType;
  /**
   * A Cluster resource name.
   *
   * @var string
   */
  public $cluster;
  /**
   * Required. Code of the announcement. Indicates the presence of a VMware
   * Engine related announcement and corresponds to a related message in the
   * `description` field.
   *
   * @var string
   */
  public $code;
  /**
   * Output only. Creation time of this resource. It also serves as start time
   * of notification.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Description of the announcement.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. Additional structured details about this announcement.
   *
   * @var string[]
   */
  public $metadata;
  /**
   * Output only. The resource name of the announcement. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-west1-a/announcements/my-announcement-id`
   *
   * @var string
   */
  public $name;
  /**
   * A Private Cloud resource name.
   *
   * @var string
   */
  public $privateCloud;
  /**
   * Output only. State of the resource. New values may be added to this enum
   * when appropriate.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. Target Resource Type defines the type of the target for the
   * announcement
   *
   * @var string
   */
  public $targetResourceType;
  /**
   * Output only. Last update time of this resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Activity type of the announcement There can be only one active
   * announcement for a given activity type and target resource.
   *
   * @param string $activityType
   */
  public function setActivityType($activityType)
  {
    $this->activityType = $activityType;
  }
  /**
   * @return string
   */
  public function getActivityType()
  {
    return $this->activityType;
  }
  /**
   * A Cluster resource name.
   *
   * @param string $cluster
   */
  public function setCluster($cluster)
  {
    $this->cluster = $cluster;
  }
  /**
   * @return string
   */
  public function getCluster()
  {
    return $this->cluster;
  }
  /**
   * Required. Code of the announcement. Indicates the presence of a VMware
   * Engine related announcement and corresponds to a related message in the
   * `description` field.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Output only. Creation time of this resource. It also serves as start time
   * of notification.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Description of the announcement.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Output only. Additional structured details about this announcement.
   *
   * @param string[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return string[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Output only. The resource name of the announcement. Resource names are
   * schemeless URIs that follow the conventions in
   * https://cloud.google.com/apis/design/resource_names. For example:
   * `projects/my-project/locations/us-west1-a/announcements/my-announcement-id`
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
   * A Private Cloud resource name.
   *
   * @param string $privateCloud
   */
  public function setPrivateCloud($privateCloud)
  {
    $this->privateCloud = $privateCloud;
  }
  /**
   * @return string
   */
  public function getPrivateCloud()
  {
    return $this->privateCloud;
  }
  /**
   * Output only. State of the resource. New values may be added to this enum
   * when appropriate.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE, DELETING, CREATING
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. Target Resource Type defines the type of the target for the
   * announcement
   *
   * @param string $targetResourceType
   */
  public function setTargetResourceType($targetResourceType)
  {
    $this->targetResourceType = $targetResourceType;
  }
  /**
   * @return string
   */
  public function getTargetResourceType()
  {
    return $this->targetResourceType;
  }
  /**
   * Output only. Last update time of this resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Announcement::class, 'Google_Service_VMwareEngine_Announcement');
