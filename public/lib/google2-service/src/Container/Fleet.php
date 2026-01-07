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

namespace Google\Service\Container;

class Fleet extends \Google\Model
{
  /**
   * The MembershipType is not set.
   */
  public const MEMBERSHIP_TYPE_MEMBERSHIP_TYPE_UNSPECIFIED = 'MEMBERSHIP_TYPE_UNSPECIFIED';
  /**
   * The membership supports only lightweight compatible features.
   */
  public const MEMBERSHIP_TYPE_LIGHTWEIGHT = 'LIGHTWEIGHT';
  /**
   * Output only. The full resource name of the registered fleet membership of
   * the cluster, in the format
   * `//gkehub.googleapis.com/projects/locations/memberships`.
   *
   * @var string
   */
  public $membership;
  /**
   * The type of the cluster's fleet membership.
   *
   * @var string
   */
  public $membershipType;
  /**
   * Output only. Whether the cluster has been registered through the fleet API.
   *
   * @var bool
   */
  public $preRegistered;
  /**
   * The Fleet host project(project ID or project number) where this cluster
   * will be registered to. This field cannot be changed after the cluster has
   * been registered.
   *
   * @var string
   */
  public $project;

  /**
   * Output only. The full resource name of the registered fleet membership of
   * the cluster, in the format
   * `//gkehub.googleapis.com/projects/locations/memberships`.
   *
   * @param string $membership
   */
  public function setMembership($membership)
  {
    $this->membership = $membership;
  }
  /**
   * @return string
   */
  public function getMembership()
  {
    return $this->membership;
  }
  /**
   * The type of the cluster's fleet membership.
   *
   * Accepted values: MEMBERSHIP_TYPE_UNSPECIFIED, LIGHTWEIGHT
   *
   * @param self::MEMBERSHIP_TYPE_* $membershipType
   */
  public function setMembershipType($membershipType)
  {
    $this->membershipType = $membershipType;
  }
  /**
   * @return self::MEMBERSHIP_TYPE_*
   */
  public function getMembershipType()
  {
    return $this->membershipType;
  }
  /**
   * Output only. Whether the cluster has been registered through the fleet API.
   *
   * @param bool $preRegistered
   */
  public function setPreRegistered($preRegistered)
  {
    $this->preRegistered = $preRegistered;
  }
  /**
   * @return bool
   */
  public function getPreRegistered()
  {
    return $this->preRegistered;
  }
  /**
   * The Fleet host project(project ID or project number) where this cluster
   * will be registered to. This field cannot be changed after the cluster has
   * been registered.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Fleet::class, 'Google_Service_Container_Fleet');
