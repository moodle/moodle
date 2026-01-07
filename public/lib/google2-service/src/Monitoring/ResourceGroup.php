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

namespace Google\Service\Monitoring;

class ResourceGroup extends \Google\Model
{
  /**
   * Default value (not valid).
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * A group of instances from Google Cloud Platform (GCP) or Amazon Web
   * Services (AWS).
   */
  public const RESOURCE_TYPE_INSTANCE = 'INSTANCE';
  /**
   * A group of Amazon ELB load balancers.
   */
  public const RESOURCE_TYPE_AWS_ELB_LOAD_BALANCER = 'AWS_ELB_LOAD_BALANCER';
  /**
   * The group of resources being monitored. Should be only the [GROUP_ID], and
   * not the full-path projects/[PROJECT_ID_OR_NUMBER]/groups/[GROUP_ID].
   *
   * @var string
   */
  public $groupId;
  /**
   * The resource type of the group members.
   *
   * @var string
   */
  public $resourceType;

  /**
   * The group of resources being monitored. Should be only the [GROUP_ID], and
   * not the full-path projects/[PROJECT_ID_OR_NUMBER]/groups/[GROUP_ID].
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * The resource type of the group members.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, INSTANCE, AWS_ELB_LOAD_BALANCER
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceGroup::class, 'Google_Service_Monitoring_ResourceGroup');
