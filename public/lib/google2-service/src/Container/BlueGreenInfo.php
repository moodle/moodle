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

class BlueGreenInfo extends \Google\Collection
{
  /**
   * Unspecified phase.
   */
  public const PHASE_PHASE_UNSPECIFIED = 'PHASE_UNSPECIFIED';
  /**
   * blue-green upgrade has been initiated.
   */
  public const PHASE_UPDATE_STARTED = 'UPDATE_STARTED';
  /**
   * Start creating green pool nodes.
   */
  public const PHASE_CREATING_GREEN_POOL = 'CREATING_GREEN_POOL';
  /**
   * Start cordoning blue pool nodes.
   */
  public const PHASE_CORDONING_BLUE_POOL = 'CORDONING_BLUE_POOL';
  /**
   * Start draining blue pool nodes.
   */
  public const PHASE_DRAINING_BLUE_POOL = 'DRAINING_BLUE_POOL';
  /**
   * Start soaking time after draining entire blue pool.
   */
  public const PHASE_NODE_POOL_SOAKING = 'NODE_POOL_SOAKING';
  /**
   * Start deleting blue nodes.
   */
  public const PHASE_DELETING_BLUE_POOL = 'DELETING_BLUE_POOL';
  /**
   * Rollback has been initiated.
   */
  public const PHASE_ROLLBACK_STARTED = 'ROLLBACK_STARTED';
  protected $collection_key = 'greenInstanceGroupUrls';
  /**
   * The resource URLs of the [managed instance groups] (/compute/docs/instance-
   * groups/creating-groups-of-managed-instances) associated with blue pool.
   *
   * @var string[]
   */
  public $blueInstanceGroupUrls;
  /**
   * Time to start deleting blue pool to complete blue-green upgrade, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @var string
   */
  public $bluePoolDeletionStartTime;
  /**
   * The resource URLs of the [managed instance groups] (/compute/docs/instance-
   * groups/creating-groups-of-managed-instances) associated with green pool.
   *
   * @var string[]
   */
  public $greenInstanceGroupUrls;
  /**
   * Version of green pool.
   *
   * @var string
   */
  public $greenPoolVersion;
  /**
   * Current blue-green upgrade phase.
   *
   * @var string
   */
  public $phase;

  /**
   * The resource URLs of the [managed instance groups] (/compute/docs/instance-
   * groups/creating-groups-of-managed-instances) associated with blue pool.
   *
   * @param string[] $blueInstanceGroupUrls
   */
  public function setBlueInstanceGroupUrls($blueInstanceGroupUrls)
  {
    $this->blueInstanceGroupUrls = $blueInstanceGroupUrls;
  }
  /**
   * @return string[]
   */
  public function getBlueInstanceGroupUrls()
  {
    return $this->blueInstanceGroupUrls;
  }
  /**
   * Time to start deleting blue pool to complete blue-green upgrade, in
   * [RFC3339](https://www.ietf.org/rfc/rfc3339.txt) text format.
   *
   * @param string $bluePoolDeletionStartTime
   */
  public function setBluePoolDeletionStartTime($bluePoolDeletionStartTime)
  {
    $this->bluePoolDeletionStartTime = $bluePoolDeletionStartTime;
  }
  /**
   * @return string
   */
  public function getBluePoolDeletionStartTime()
  {
    return $this->bluePoolDeletionStartTime;
  }
  /**
   * The resource URLs of the [managed instance groups] (/compute/docs/instance-
   * groups/creating-groups-of-managed-instances) associated with green pool.
   *
   * @param string[] $greenInstanceGroupUrls
   */
  public function setGreenInstanceGroupUrls($greenInstanceGroupUrls)
  {
    $this->greenInstanceGroupUrls = $greenInstanceGroupUrls;
  }
  /**
   * @return string[]
   */
  public function getGreenInstanceGroupUrls()
  {
    return $this->greenInstanceGroupUrls;
  }
  /**
   * Version of green pool.
   *
   * @param string $greenPoolVersion
   */
  public function setGreenPoolVersion($greenPoolVersion)
  {
    $this->greenPoolVersion = $greenPoolVersion;
  }
  /**
   * @return string
   */
  public function getGreenPoolVersion()
  {
    return $this->greenPoolVersion;
  }
  /**
   * Current blue-green upgrade phase.
   *
   * Accepted values: PHASE_UNSPECIFIED, UPDATE_STARTED, CREATING_GREEN_POOL,
   * CORDONING_BLUE_POOL, DRAINING_BLUE_POOL, NODE_POOL_SOAKING,
   * DELETING_BLUE_POOL, ROLLBACK_STARTED
   *
   * @param self::PHASE_* $phase
   */
  public function setPhase($phase)
  {
    $this->phase = $phase;
  }
  /**
   * @return self::PHASE_*
   */
  public function getPhase()
  {
    return $this->phase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BlueGreenInfo::class, 'Google_Service_Container_BlueGreenInfo');
