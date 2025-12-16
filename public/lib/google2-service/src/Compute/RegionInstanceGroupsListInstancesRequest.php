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

namespace Google\Service\Compute;

class RegionInstanceGroupsListInstancesRequest extends \Google\Model
{
  /**
   * Matches any status of the instances, running, non-running and others.
   */
  public const INSTANCE_STATE_ALL = 'ALL';
  /**
   * Instance is in RUNNING state if it is running.
   */
  public const INSTANCE_STATE_RUNNING = 'RUNNING';
  /**
   * Instances in which state should be returned. Valid options are: 'ALL',
   * 'RUNNING'. By default, it lists all instances.
   *
   * @var string
   */
  public $instanceState;
  /**
   * Name of port user is interested in. It is optional. If it is set, only
   * information about this ports will be returned. If it is not set, all the
   * named ports will be returned. Always lists all instances.
   *
   * @var string
   */
  public $portName;

  /**
   * Instances in which state should be returned. Valid options are: 'ALL',
   * 'RUNNING'. By default, it lists all instances.
   *
   * Accepted values: ALL, RUNNING
   *
   * @param self::INSTANCE_STATE_* $instanceState
   */
  public function setInstanceState($instanceState)
  {
    $this->instanceState = $instanceState;
  }
  /**
   * @return self::INSTANCE_STATE_*
   */
  public function getInstanceState()
  {
    return $this->instanceState;
  }
  /**
   * Name of port user is interested in. It is optional. If it is set, only
   * information about this ports will be returned. If it is not set, all the
   * named ports will be returned. Always lists all instances.
   *
   * @param string $portName
   */
  public function setPortName($portName)
  {
    $this->portName = $portName;
  }
  /**
   * @return string
   */
  public function getPortName()
  {
    return $this->portName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RegionInstanceGroupsListInstancesRequest::class, 'Google_Service_Compute_RegionInstanceGroupsListInstancesRequest');
