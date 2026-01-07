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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1AssistantToolInfo extends \Google\Model
{
  /**
   * The display name of the tool.
   *
   * @var string
   */
  public $toolDisplayName;
  /**
   * The name of the tool as defined by
   * DataConnectorService.QueryAvailableActions. Note: it's using `action` in
   * the DataConnectorService apis, but they are the same as the `tool` here.
   *
   * @var string
   */
  public $toolName;

  /**
   * The display name of the tool.
   *
   * @param string $toolDisplayName
   */
  public function setToolDisplayName($toolDisplayName)
  {
    $this->toolDisplayName = $toolDisplayName;
  }
  /**
   * @return string
   */
  public function getToolDisplayName()
  {
    return $this->toolDisplayName;
  }
  /**
   * The name of the tool as defined by
   * DataConnectorService.QueryAvailableActions. Note: it's using `action` in
   * the DataConnectorService apis, but they are the same as the `tool` here.
   *
   * @param string $toolName
   */
  public function setToolName($toolName)
  {
    $this->toolName = $toolName;
  }
  /**
   * @return string
   */
  public function getToolName()
  {
    return $this->toolName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantToolInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantToolInfo');
