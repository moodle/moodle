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

namespace Google\Service\CloudFunctions;

class UpgradeInfo extends \Google\Model
{
  /**
   * Unspecified state. Most functions are in this upgrade state.
   */
  public const UPGRADE_STATE_UPGRADE_STATE_UNSPECIFIED = 'UPGRADE_STATE_UNSPECIFIED';
  /**
   * Functions in this state are eligible for 1st Gen upgrade.
   */
  public const UPGRADE_STATE_ELIGIBLE_FOR_2ND_GEN_UPGRADE = 'ELIGIBLE_FOR_2ND_GEN_UPGRADE';
  /**
   * Functions in this state are ineligible for 1st Gen upgrade until
   * redeployment with newer runtime.
   */
  public const UPGRADE_STATE_INELIGIBLE_FOR_UPGRADE_UNTIL_REDEPLOYMENT = 'INELIGIBLE_FOR_UPGRADE_UNTIL_REDEPLOYMENT';
  /**
   * An upgrade related operation is in progress.
   */
  public const UPGRADE_STATE_UPGRADE_OPERATION_IN_PROGRESS = 'UPGRADE_OPERATION_IN_PROGRESS';
  /**
   * SetupFunctionUpgradeConfig API was successful and a 2nd Gen function has
   * been created based on 1st Gen function instance.
   */
  public const UPGRADE_STATE_SETUP_FUNCTION_UPGRADE_CONFIG_SUCCESSFUL = 'SETUP_FUNCTION_UPGRADE_CONFIG_SUCCESSFUL';
  /**
   * SetupFunctionUpgradeConfig API was un-successful.
   */
  public const UPGRADE_STATE_SETUP_FUNCTION_UPGRADE_CONFIG_ERROR = 'SETUP_FUNCTION_UPGRADE_CONFIG_ERROR';
  /**
   * AbortFunctionUpgrade API was un-successful.
   */
  public const UPGRADE_STATE_ABORT_FUNCTION_UPGRADE_ERROR = 'ABORT_FUNCTION_UPGRADE_ERROR';
  /**
   * RedirectFunctionUpgradeTraffic API was successful and traffic is served by
   * 2nd Gen function stack.
   */
  public const UPGRADE_STATE_REDIRECT_FUNCTION_UPGRADE_TRAFFIC_SUCCESSFUL = 'REDIRECT_FUNCTION_UPGRADE_TRAFFIC_SUCCESSFUL';
  /**
   * RedirectFunctionUpgradeTraffic API was un-successful.
   */
  public const UPGRADE_STATE_REDIRECT_FUNCTION_UPGRADE_TRAFFIC_ERROR = 'REDIRECT_FUNCTION_UPGRADE_TRAFFIC_ERROR';
  /**
   * RollbackFunctionUpgradeTraffic API was un-successful.
   */
  public const UPGRADE_STATE_ROLLBACK_FUNCTION_UPGRADE_TRAFFIC_ERROR = 'ROLLBACK_FUNCTION_UPGRADE_TRAFFIC_ERROR';
  /**
   * CommitFunctionUpgrade API was un-successful and 1st gen function might have
   * broken.
   */
  public const UPGRADE_STATE_COMMIT_FUNCTION_UPGRADE_ERROR = 'COMMIT_FUNCTION_UPGRADE_ERROR';
  /**
   * CommitFunctionUpgrade API was un-successful but safe to rollback traffic or
   * abort.
   */
  public const UPGRADE_STATE_COMMIT_FUNCTION_UPGRADE_ERROR_ROLLBACK_SAFE = 'COMMIT_FUNCTION_UPGRADE_ERROR_ROLLBACK_SAFE';
  protected $buildConfigType = BuildConfig::class;
  protected $buildConfigDataType = '';
  protected $eventTriggerType = EventTrigger::class;
  protected $eventTriggerDataType = '';
  protected $serviceConfigType = ServiceConfig::class;
  protected $serviceConfigDataType = '';
  /**
   * UpgradeState of the function
   *
   * @var string
   */
  public $upgradeState;

  /**
   * Describes the Build step of the function that builds a container to prepare
   * for 2nd gen upgrade.
   *
   * @param BuildConfig $buildConfig
   */
  public function setBuildConfig(BuildConfig $buildConfig)
  {
    $this->buildConfig = $buildConfig;
  }
  /**
   * @return BuildConfig
   */
  public function getBuildConfig()
  {
    return $this->buildConfig;
  }
  /**
   * Describes the Event trigger which has been setup to prepare for 2nd gen
   * upgrade.
   *
   * @param EventTrigger $eventTrigger
   */
  public function setEventTrigger(EventTrigger $eventTrigger)
  {
    $this->eventTrigger = $eventTrigger;
  }
  /**
   * @return EventTrigger
   */
  public function getEventTrigger()
  {
    return $this->eventTrigger;
  }
  /**
   * Describes the Cloud Run service which has been setup to prepare for 2nd gen
   * upgrade.
   *
   * @param ServiceConfig $serviceConfig
   */
  public function setServiceConfig(ServiceConfig $serviceConfig)
  {
    $this->serviceConfig = $serviceConfig;
  }
  /**
   * @return ServiceConfig
   */
  public function getServiceConfig()
  {
    return $this->serviceConfig;
  }
  /**
   * UpgradeState of the function
   *
   * Accepted values: UPGRADE_STATE_UNSPECIFIED, ELIGIBLE_FOR_2ND_GEN_UPGRADE,
   * INELIGIBLE_FOR_UPGRADE_UNTIL_REDEPLOYMENT, UPGRADE_OPERATION_IN_PROGRESS,
   * SETUP_FUNCTION_UPGRADE_CONFIG_SUCCESSFUL,
   * SETUP_FUNCTION_UPGRADE_CONFIG_ERROR, ABORT_FUNCTION_UPGRADE_ERROR,
   * REDIRECT_FUNCTION_UPGRADE_TRAFFIC_SUCCESSFUL,
   * REDIRECT_FUNCTION_UPGRADE_TRAFFIC_ERROR,
   * ROLLBACK_FUNCTION_UPGRADE_TRAFFIC_ERROR, COMMIT_FUNCTION_UPGRADE_ERROR,
   * COMMIT_FUNCTION_UPGRADE_ERROR_ROLLBACK_SAFE
   *
   * @param self::UPGRADE_STATE_* $upgradeState
   */
  public function setUpgradeState($upgradeState)
  {
    $this->upgradeState = $upgradeState;
  }
  /**
   * @return self::UPGRADE_STATE_*
   */
  public function getUpgradeState()
  {
    return $this->upgradeState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeInfo::class, 'Google_Service_CloudFunctions_UpgradeInfo');
