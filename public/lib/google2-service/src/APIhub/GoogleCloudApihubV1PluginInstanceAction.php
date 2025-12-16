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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1PluginInstanceAction extends \Google\Model
{
  /**
   * Default unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The action is enabled in the plugin instance i.e., executions can be
   * triggered for this action.
   */
  public const STATE_ENABLED = 'ENABLED';
  /**
   * The action is disabled in the plugin instance i.e., no executions can be
   * triggered for this action. This state indicates that the user explicitly
   * disabled the instance, and no further action is needed unless the user
   * wants to re-enable it.
   */
  public const STATE_DISABLED = 'DISABLED';
  /**
   * The action in the plugin instance is being enabled.
   */
  public const STATE_ENABLING = 'ENABLING';
  /**
   * The action in the plugin instance is being disabled.
   */
  public const STATE_DISABLING = 'DISABLING';
  /**
   * The ERROR state can come while enabling/disabling plugin instance action.
   * Users can retrigger enable, disable via EnablePluginInstanceAction and
   * DisablePluginInstanceAction to restore the action back to enabled/disabled
   * state. Note enable/disable on actions can only be triggered if plugin
   * instance is in Active state.
   */
  public const STATE_ERROR = 'ERROR';
  /**
   * Required. This should map to one of the action id specified in
   * actions_config in the plugin.
   *
   * @var string
   */
  public $actionId;
  protected $curationConfigType = GoogleCloudApihubV1CurationConfig::class;
  protected $curationConfigDataType = '';
  protected $hubInstanceActionType = GoogleCloudApihubV1ExecutionStatus::class;
  protected $hubInstanceActionDataType = '';
  protected $resourceConfigType = GoogleCloudApihubV1ResourceConfig::class;
  protected $resourceConfigDataType = '';
  /**
   * Optional. The schedule for this plugin instance action. This can only be
   * set if the plugin supports API_HUB_SCHEDULE_TRIGGER mode for this action.
   *
   * @var string
   */
  public $scheduleCronExpression;
  /**
   * Optional. The time zone for the schedule cron expression. If not provided,
   * UTC will be used.
   *
   * @var string
   */
  public $scheduleTimeZone;
  /**
   * Optional. The service account used to publish data. Note, the service
   * account will only be accepted for non GCP plugins like OPDK.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. The current state of the plugin action in the plugin instance.
   *
   * @var string
   */
  public $state;

  /**
   * Required. This should map to one of the action id specified in
   * actions_config in the plugin.
   *
   * @param string $actionId
   */
  public function setActionId($actionId)
  {
    $this->actionId = $actionId;
  }
  /**
   * @return string
   */
  public function getActionId()
  {
    return $this->actionId;
  }
  /**
   * Optional. This configuration should be provided if the plugin action is
   * publishing data to API hub curate layer.
   *
   * @param GoogleCloudApihubV1CurationConfig $curationConfig
   */
  public function setCurationConfig(GoogleCloudApihubV1CurationConfig $curationConfig)
  {
    $this->curationConfig = $curationConfig;
  }
  /**
   * @return GoogleCloudApihubV1CurationConfig
   */
  public function getCurationConfig()
  {
    return $this->curationConfig;
  }
  /**
   * Optional. The execution information for the plugin instance action done
   * corresponding to an API hub instance.
   *
   * @param GoogleCloudApihubV1ExecutionStatus $hubInstanceAction
   */
  public function setHubInstanceAction(GoogleCloudApihubV1ExecutionStatus $hubInstanceAction)
  {
    $this->hubInstanceAction = $hubInstanceAction;
  }
  /**
   * @return GoogleCloudApihubV1ExecutionStatus
   */
  public function getHubInstanceAction()
  {
    return $this->hubInstanceAction;
  }
  /**
   * Output only. The configuration of resources created for a given plugin
   * instance action. Note these will be returned only in case of Non-GCP
   * plugins like OPDK.
   *
   * @param GoogleCloudApihubV1ResourceConfig $resourceConfig
   */
  public function setResourceConfig(GoogleCloudApihubV1ResourceConfig $resourceConfig)
  {
    $this->resourceConfig = $resourceConfig;
  }
  /**
   * @return GoogleCloudApihubV1ResourceConfig
   */
  public function getResourceConfig()
  {
    return $this->resourceConfig;
  }
  /**
   * Optional. The schedule for this plugin instance action. This can only be
   * set if the plugin supports API_HUB_SCHEDULE_TRIGGER mode for this action.
   *
   * @param string $scheduleCronExpression
   */
  public function setScheduleCronExpression($scheduleCronExpression)
  {
    $this->scheduleCronExpression = $scheduleCronExpression;
  }
  /**
   * @return string
   */
  public function getScheduleCronExpression()
  {
    return $this->scheduleCronExpression;
  }
  /**
   * Optional. The time zone for the schedule cron expression. If not provided,
   * UTC will be used.
   *
   * @param string $scheduleTimeZone
   */
  public function setScheduleTimeZone($scheduleTimeZone)
  {
    $this->scheduleTimeZone = $scheduleTimeZone;
  }
  /**
   * @return string
   */
  public function getScheduleTimeZone()
  {
    return $this->scheduleTimeZone;
  }
  /**
   * Optional. The service account used to publish data. Note, the service
   * account will only be accepted for non GCP plugins like OPDK.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. The current state of the plugin action in the plugin instance.
   *
   * Accepted values: STATE_UNSPECIFIED, ENABLED, DISABLED, ENABLING, DISABLING,
   * ERROR
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
class_alias(GoogleCloudApihubV1PluginInstanceAction::class, 'Google_Service_APIhub_GoogleCloudApihubV1PluginInstanceAction');
