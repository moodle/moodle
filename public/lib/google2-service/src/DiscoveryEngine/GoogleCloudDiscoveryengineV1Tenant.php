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

class GoogleCloudDiscoveryengineV1Tenant extends \Google\Model
{
  /**
   * Optional display name for the tenant, e.g. "My Slack Team".
   *
   * @var string
   */
  public $displayName;
  /**
   * The tenant's instance ID. Examples: Jira
   * ("8594f221-9797-5f78-1fa4-485e198d7cd0"), Slack ("T123456").
   *
   * @var string
   */
  public $id;
  /**
   * The URI of the tenant, if applicable. For example, the URI of a Jira
   * instance is https://my-jira-instance.atlassian.net, and a Slack tenant does
   * not have a URI.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional display name for the tenant, e.g. "My Slack Team".
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
   * The tenant's instance ID. Examples: Jira
   * ("8594f221-9797-5f78-1fa4-485e198d7cd0"), Slack ("T123456").
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
   * The URI of the tenant, if applicable. For example, the URI of a Jira
   * instance is https://my-jira-instance.atlassian.net, and a Slack tenant does
   * not have a URI.
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
class_alias(GoogleCloudDiscoveryengineV1Tenant::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Tenant');
