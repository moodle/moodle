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

class AgentExtension extends \Google\Model
{
  /**
   * A description of how this agent uses this extension. Example: "Google OAuth
   * 2.0 authentication"
   *
   * @var string
   */
  public $description;
  /**
   * Optional configuration for the extension.
   *
   * @var array[]
   */
  public $params;
  /**
   * Whether the client must follow specific requirements of the extension.
   * Example: false
   *
   * @var bool
   */
  public $required;
  /**
   * The URI of the extension. Example:
   * "https://developers.google.com/identity/protocols/oauth2"
   *
   * @var string
   */
  public $uri;

  /**
   * A description of how this agent uses this extension. Example: "Google OAuth
   * 2.0 authentication"
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
   * Optional configuration for the extension.
   *
   * @param array[] $params
   */
  public function setParams($params)
  {
    $this->params = $params;
  }
  /**
   * @return array[]
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * Whether the client must follow specific requirements of the extension.
   * Example: false
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * The URI of the extension. Example:
   * "https://developers.google.com/identity/protocols/oauth2"
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentExtension::class, 'Google_Service_WorkspaceEvents_AgentExtension');
