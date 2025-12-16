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

class InstanceGroupManagersApplyUpdatesRequest extends \Google\Collection
{
  /**
   * Do not perform any action.
   */
  public const MINIMAL_ACTION_NONE = 'NONE';
  /**
   * Do not stop the instance.
   */
  public const MINIMAL_ACTION_REFRESH = 'REFRESH';
  /**
   * (Default.) Replace the instance according to the replacement method option.
   */
  public const MINIMAL_ACTION_REPLACE = 'REPLACE';
  /**
   * Stop the instance and start it again.
   */
  public const MINIMAL_ACTION_RESTART = 'RESTART';
  /**
   * Do not perform any action.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_NONE = 'NONE';
  /**
   * Do not stop the instance.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_REFRESH = 'REFRESH';
  /**
   * (Default.) Replace the instance according to the replacement method option.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_REPLACE = 'REPLACE';
  /**
   * Stop the instance and start it again.
   */
  public const MOST_DISRUPTIVE_ALLOWED_ACTION_RESTART = 'RESTART';
  protected $collection_key = 'instances';
  /**
   * Flag to update all instances instead of specified list of “instances”. If
   * the flag is set to true then the instances may not be specified in the
   * request.
   *
   * @var bool
   */
  public $allInstances;
  /**
   * The list of URLs of one or more instances for which you want to apply
   * updates. Each URL can be a full URL or a partial URL, such
   * aszones/[ZONE]/instances/[INSTANCE_NAME].
   *
   * @var string[]
   */
  public $instances;
  /**
   * The minimal action that you want to perform on each instance during the
   * update:              - REPLACE: At minimum, delete the instance and create
   * it      again.     - RESTART: Stop the instance and start it      again.
   * - REFRESH: Do not stop the instance and limit      disruption as much as
   * possible.     - NONE: Do not      disrupt the instance at all.
   *
   * By default, the minimum action is NONE. If your update requires a more
   * disruptive action than you set with this flag, the necessary action is
   * performed to execute the update.
   *
   * @var string
   */
  public $minimalAction;
  /**
   * The most disruptive action that you want to perform on each instance during
   * the update:              - REPLACE: Delete the instance and create it
   * again.      - RESTART: Stop the instance and start it again.      -
   * REFRESH: Do not stop the instance and limit disruption      as much as
   * possible.     - NONE: Do not disrupt the      instance at all.
   *
   * By default, the most disruptive allowed action is REPLACE. If your update
   * requires a more disruptive action than you set with this flag, the update
   * request will fail.
   *
   * @var string
   */
  public $mostDisruptiveAllowedAction;

  /**
   * Flag to update all instances instead of specified list of “instances”. If
   * the flag is set to true then the instances may not be specified in the
   * request.
   *
   * @param bool $allInstances
   */
  public function setAllInstances($allInstances)
  {
    $this->allInstances = $allInstances;
  }
  /**
   * @return bool
   */
  public function getAllInstances()
  {
    return $this->allInstances;
  }
  /**
   * The list of URLs of one or more instances for which you want to apply
   * updates. Each URL can be a full URL or a partial URL, such
   * aszones/[ZONE]/instances/[INSTANCE_NAME].
   *
   * @param string[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return string[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * The minimal action that you want to perform on each instance during the
   * update:              - REPLACE: At minimum, delete the instance and create
   * it      again.     - RESTART: Stop the instance and start it      again.
   * - REFRESH: Do not stop the instance and limit      disruption as much as
   * possible.     - NONE: Do not      disrupt the instance at all.
   *
   * By default, the minimum action is NONE. If your update requires a more
   * disruptive action than you set with this flag, the necessary action is
   * performed to execute the update.
   *
   * Accepted values: NONE, REFRESH, REPLACE, RESTART
   *
   * @param self::MINIMAL_ACTION_* $minimalAction
   */
  public function setMinimalAction($minimalAction)
  {
    $this->minimalAction = $minimalAction;
  }
  /**
   * @return self::MINIMAL_ACTION_*
   */
  public function getMinimalAction()
  {
    return $this->minimalAction;
  }
  /**
   * The most disruptive action that you want to perform on each instance during
   * the update:              - REPLACE: Delete the instance and create it
   * again.      - RESTART: Stop the instance and start it again.      -
   * REFRESH: Do not stop the instance and limit disruption      as much as
   * possible.     - NONE: Do not disrupt the      instance at all.
   *
   * By default, the most disruptive allowed action is REPLACE. If your update
   * requires a more disruptive action than you set with this flag, the update
   * request will fail.
   *
   * Accepted values: NONE, REFRESH, REPLACE, RESTART
   *
   * @param self::MOST_DISRUPTIVE_ALLOWED_ACTION_* $mostDisruptiveAllowedAction
   */
  public function setMostDisruptiveAllowedAction($mostDisruptiveAllowedAction)
  {
    $this->mostDisruptiveAllowedAction = $mostDisruptiveAllowedAction;
  }
  /**
   * @return self::MOST_DISRUPTIVE_ALLOWED_ACTION_*
   */
  public function getMostDisruptiveAllowedAction()
  {
    return $this->mostDisruptiveAllowedAction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceGroupManagersApplyUpdatesRequest::class, 'Google_Service_Compute_InstanceGroupManagersApplyUpdatesRequest');
