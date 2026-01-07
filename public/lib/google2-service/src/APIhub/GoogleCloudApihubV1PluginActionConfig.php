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

class GoogleCloudApihubV1PluginActionConfig extends \Google\Model
{
  /**
   * Default unspecified mode.
   */
  public const TRIGGER_MODE_TRIGGER_MODE_UNSPECIFIED = 'TRIGGER_MODE_UNSPECIFIED';
  /**
   * This action can be executed by invoking ExecutePluginInstanceAction API
   * with the given action id. To support this, the plugin hosting service
   * should handle this action id as part of execute call.
   */
  public const TRIGGER_MODE_API_HUB_ON_DEMAND_TRIGGER = 'API_HUB_ON_DEMAND_TRIGGER';
  /**
   * This action will be executed on schedule by invoking
   * ExecutePluginInstanceAction API with the given action id. To set the
   * schedule, the user can provide the cron expression in the PluginAction
   * field for a given plugin instance. To support this, the plugin hosting
   * service should handle this action id as part of execute call. Note, on
   * demand execution will be supported by default in this trigger mode.
   */
  public const TRIGGER_MODE_API_HUB_SCHEDULE_TRIGGER = 'API_HUB_SCHEDULE_TRIGGER';
  /**
   * The execution of this plugin is not handled by API hub. In this case, the
   * plugin hosting service need not handle this action id as part of the
   * execute call.
   */
  public const TRIGGER_MODE_NON_API_HUB_MANAGED = 'NON_API_HUB_MANAGED';
  /**
   * Required. The description of the operation performed by the action.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the action.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The id of the action.
   *
   * @var string
   */
  public $id;
  /**
   * Required. The trigger mode supported by the action.
   *
   * @var string
   */
  public $triggerMode;

  /**
   * Required. The description of the operation performed by the action.
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
   * Required. The display name of the action.
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
   * Required. The id of the action.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The trigger mode supported by the action.
   *
   * Accepted values: TRIGGER_MODE_UNSPECIFIED, API_HUB_ON_DEMAND_TRIGGER,
   * API_HUB_SCHEDULE_TRIGGER, NON_API_HUB_MANAGED
   *
   * @param self::TRIGGER_MODE_* $triggerMode
   */
  public function setTriggerMode($triggerMode)
  {
    $this->triggerMode = $triggerMode;
  }
  /**
   * @return self::TRIGGER_MODE_*
   */
  public function getTriggerMode()
  {
    return $this->triggerMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1PluginActionConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1PluginActionConfig');
