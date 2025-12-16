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

namespace Google\Service\TagManager;

class ContainerFeatures extends \Google\Model
{
  /**
   * Whether this Container supports built-in variables
   *
   * @var bool
   */
  public $supportBuiltInVariables;
  /**
   * Whether this Container supports clients.
   *
   * @var bool
   */
  public $supportClients;
  /**
   * Whether this Container supports environments.
   *
   * @var bool
   */
  public $supportEnvironments;
  /**
   * Whether this Container supports folders.
   *
   * @var bool
   */
  public $supportFolders;
  /**
   * Whether this Container supports Google tag config.
   *
   * @var bool
   */
  public $supportGtagConfigs;
  /**
   * Whether this Container supports tags.
   *
   * @var bool
   */
  public $supportTags;
  /**
   * Whether this Container supports templates.
   *
   * @var bool
   */
  public $supportTemplates;
  /**
   * Whether this Container supports transformations.
   *
   * @var bool
   */
  public $supportTransformations;
  /**
   * Whether this Container supports triggers.
   *
   * @var bool
   */
  public $supportTriggers;
  /**
   * Whether this Container supports user permissions managed by GTM.
   *
   * @var bool
   */
  public $supportUserPermissions;
  /**
   * Whether this Container supports variables.
   *
   * @var bool
   */
  public $supportVariables;
  /**
   * Whether this Container supports Container versions.
   *
   * @var bool
   */
  public $supportVersions;
  /**
   * Whether this Container supports workspaces.
   *
   * @var bool
   */
  public $supportWorkspaces;
  /**
   * Whether this Container supports zones.
   *
   * @var bool
   */
  public $supportZones;

  /**
   * Whether this Container supports built-in variables
   *
   * @param bool $supportBuiltInVariables
   */
  public function setSupportBuiltInVariables($supportBuiltInVariables)
  {
    $this->supportBuiltInVariables = $supportBuiltInVariables;
  }
  /**
   * @return bool
   */
  public function getSupportBuiltInVariables()
  {
    return $this->supportBuiltInVariables;
  }
  /**
   * Whether this Container supports clients.
   *
   * @param bool $supportClients
   */
  public function setSupportClients($supportClients)
  {
    $this->supportClients = $supportClients;
  }
  /**
   * @return bool
   */
  public function getSupportClients()
  {
    return $this->supportClients;
  }
  /**
   * Whether this Container supports environments.
   *
   * @param bool $supportEnvironments
   */
  public function setSupportEnvironments($supportEnvironments)
  {
    $this->supportEnvironments = $supportEnvironments;
  }
  /**
   * @return bool
   */
  public function getSupportEnvironments()
  {
    return $this->supportEnvironments;
  }
  /**
   * Whether this Container supports folders.
   *
   * @param bool $supportFolders
   */
  public function setSupportFolders($supportFolders)
  {
    $this->supportFolders = $supportFolders;
  }
  /**
   * @return bool
   */
  public function getSupportFolders()
  {
    return $this->supportFolders;
  }
  /**
   * Whether this Container supports Google tag config.
   *
   * @param bool $supportGtagConfigs
   */
  public function setSupportGtagConfigs($supportGtagConfigs)
  {
    $this->supportGtagConfigs = $supportGtagConfigs;
  }
  /**
   * @return bool
   */
  public function getSupportGtagConfigs()
  {
    return $this->supportGtagConfigs;
  }
  /**
   * Whether this Container supports tags.
   *
   * @param bool $supportTags
   */
  public function setSupportTags($supportTags)
  {
    $this->supportTags = $supportTags;
  }
  /**
   * @return bool
   */
  public function getSupportTags()
  {
    return $this->supportTags;
  }
  /**
   * Whether this Container supports templates.
   *
   * @param bool $supportTemplates
   */
  public function setSupportTemplates($supportTemplates)
  {
    $this->supportTemplates = $supportTemplates;
  }
  /**
   * @return bool
   */
  public function getSupportTemplates()
  {
    return $this->supportTemplates;
  }
  /**
   * Whether this Container supports transformations.
   *
   * @param bool $supportTransformations
   */
  public function setSupportTransformations($supportTransformations)
  {
    $this->supportTransformations = $supportTransformations;
  }
  /**
   * @return bool
   */
  public function getSupportTransformations()
  {
    return $this->supportTransformations;
  }
  /**
   * Whether this Container supports triggers.
   *
   * @param bool $supportTriggers
   */
  public function setSupportTriggers($supportTriggers)
  {
    $this->supportTriggers = $supportTriggers;
  }
  /**
   * @return bool
   */
  public function getSupportTriggers()
  {
    return $this->supportTriggers;
  }
  /**
   * Whether this Container supports user permissions managed by GTM.
   *
   * @param bool $supportUserPermissions
   */
  public function setSupportUserPermissions($supportUserPermissions)
  {
    $this->supportUserPermissions = $supportUserPermissions;
  }
  /**
   * @return bool
   */
  public function getSupportUserPermissions()
  {
    return $this->supportUserPermissions;
  }
  /**
   * Whether this Container supports variables.
   *
   * @param bool $supportVariables
   */
  public function setSupportVariables($supportVariables)
  {
    $this->supportVariables = $supportVariables;
  }
  /**
   * @return bool
   */
  public function getSupportVariables()
  {
    return $this->supportVariables;
  }
  /**
   * Whether this Container supports Container versions.
   *
   * @param bool $supportVersions
   */
  public function setSupportVersions($supportVersions)
  {
    $this->supportVersions = $supportVersions;
  }
  /**
   * @return bool
   */
  public function getSupportVersions()
  {
    return $this->supportVersions;
  }
  /**
   * Whether this Container supports workspaces.
   *
   * @param bool $supportWorkspaces
   */
  public function setSupportWorkspaces($supportWorkspaces)
  {
    $this->supportWorkspaces = $supportWorkspaces;
  }
  /**
   * @return bool
   */
  public function getSupportWorkspaces()
  {
    return $this->supportWorkspaces;
  }
  /**
   * Whether this Container supports zones.
   *
   * @param bool $supportZones
   */
  public function setSupportZones($supportZones)
  {
    $this->supportZones = $supportZones;
  }
  /**
   * @return bool
   */
  public function getSupportZones()
  {
    return $this->supportZones;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContainerFeatures::class, 'Google_Service_TagManager_ContainerFeatures');
