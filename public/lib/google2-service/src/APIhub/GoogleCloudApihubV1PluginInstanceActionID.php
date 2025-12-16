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

class GoogleCloudApihubV1PluginInstanceActionID extends \Google\Model
{
  /**
   * Output only. The action ID that is using the curation. This should map to
   * one of the action IDs specified in action configs in the plugin.
   *
   * @var string
   */
  public $actionId;
  /**
   * Output only. Plugin instance that is using the curation. Format is `project
   * s/{project}/locations/{location}/plugins/{plugin}/instances/{instance}`
   *
   * @var string
   */
  public $pluginInstance;

  /**
   * Output only. The action ID that is using the curation. This should map to
   * one of the action IDs specified in action configs in the plugin.
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
   * Output only. Plugin instance that is using the curation. Format is `project
   * s/{project}/locations/{location}/plugins/{plugin}/instances/{instance}`
   *
   * @param string $pluginInstance
   */
  public function setPluginInstance($pluginInstance)
  {
    $this->pluginInstance = $pluginInstance;
  }
  /**
   * @return string
   */
  public function getPluginInstance()
  {
    return $this->pluginInstance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1PluginInstanceActionID::class, 'Google_Service_APIhub_GoogleCloudApihubV1PluginInstanceActionID');
