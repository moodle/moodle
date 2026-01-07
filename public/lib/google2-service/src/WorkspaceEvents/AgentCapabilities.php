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

namespace Google\Service\WorkspaceEvents;

class AgentCapabilities extends \Google\Collection
{
  protected $collection_key = 'extensions';
  protected $extensionsType = AgentExtension::class;
  protected $extensionsDataType = 'array';
  /**
   * If the agent can send push notifications to the clients webhook
   *
   * @var bool
   */
  public $pushNotifications;
  /**
   * If the agent will support streaming responses
   *
   * @var bool
   */
  public $streaming;

  /**
   * Extensions supported by this agent.
   *
   * @param AgentExtension[] $extensions
   */
  public function setExtensions($extensions)
  {
    $this->extensions = $extensions;
  }
  /**
   * @return AgentExtension[]
   */
  public function getExtensions()
  {
    return $this->extensions;
  }
  /**
   * If the agent can send push notifications to the clients webhook
   *
   * @param bool $pushNotifications
   */
  public function setPushNotifications($pushNotifications)
  {
    $this->pushNotifications = $pushNotifications;
  }
  /**
   * @return bool
   */
  public function getPushNotifications()
  {
    return $this->pushNotifications;
  }
  /**
   * If the agent will support streaming responses
   *
   * @param bool $streaming
   */
  public function setStreaming($streaming)
  {
    $this->streaming = $streaming;
  }
  /**
   * @return bool
   */
  public function getStreaming()
  {
    return $this->streaming;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentCapabilities::class, 'Google_Service_WorkspaceEvents_AgentCapabilities');
