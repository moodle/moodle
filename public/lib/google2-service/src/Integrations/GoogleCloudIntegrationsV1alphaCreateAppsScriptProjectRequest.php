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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaCreateAppsScriptProjectRequest extends \Google\Model
{
  /**
   * The name of the Apps Script project to be created.
   *
   * @var string
   */
  public $appsScriptProject;
  /**
   * The auth config id necessary to fetch the necessary credentials to create
   * the project for external clients
   *
   * @var string
   */
  public $authConfigId;

  /**
   * The name of the Apps Script project to be created.
   *
   * @param string $appsScriptProject
   */
  public function setAppsScriptProject($appsScriptProject)
  {
    $this->appsScriptProject = $appsScriptProject;
  }
  /**
   * @return string
   */
  public function getAppsScriptProject()
  {
    return $this->appsScriptProject;
  }
  /**
   * The auth config id necessary to fetch the necessary credentials to create
   * the project for external clients
   *
   * @param string $authConfigId
   */
  public function setAuthConfigId($authConfigId)
  {
    $this->authConfigId = $authConfigId;
  }
  /**
   * @return string
   */
  public function getAuthConfigId()
  {
    return $this->authConfigId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaCreateAppsScriptProjectRequest::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaCreateAppsScriptProjectRequest');
