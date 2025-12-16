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

namespace Google\Service\Networkconnectivity;

class SpokeStateCount extends \Google\Model
{
  /**
   * No state information available
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The resource's create operation is in progress.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The resource is active
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource's delete operation is in progress.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resource's accept operation is in progress.
   */
  public const STATE_ACCEPTING = 'ACCEPTING';
  /**
   * The resource's reject operation is in progress.
   */
  public const STATE_REJECTING = 'REJECTING';
  /**
   * The resource's update operation is in progress.
   */
  public const STATE_UPDATING = 'UPDATING';
  /**
   * The resource is inactive.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The hub associated with this spoke resource has been deleted. This state
   * applies to spoke resources only.
   */
  public const STATE_OBSOLETE = 'OBSOLETE';
  /**
   * The resource is in an undefined state due to resource creation or deletion
   * failure. You can try to delete the resource later or contact support for
   * help.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Output only. The total number of spokes that are in this state and
   * associated with a given hub.
   *
   * @var string
   */
  public $count;
  /**
   * Output only. The state of the spokes.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. The total number of spokes that are in this state and
   * associated with a given hub.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Output only. The state of the spokes.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, ACCEPTING,
   * REJECTING, UPDATING, INACTIVE, OBSOLETE, FAILED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SpokeStateCount::class, 'Google_Service_Networkconnectivity_SpokeStateCount');
