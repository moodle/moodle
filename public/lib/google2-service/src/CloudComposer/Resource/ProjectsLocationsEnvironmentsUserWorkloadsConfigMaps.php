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

namespace Google\Service\CloudComposer\Resource;

use Google\Service\CloudComposer\ComposerEmpty;
use Google\Service\CloudComposer\ListUserWorkloadsConfigMapsResponse;
use Google\Service\CloudComposer\UserWorkloadsConfigMap;

/**
 * The "userWorkloadsConfigMaps" collection of methods.
 * Typical usage is:
 *  <code>
 *   $composerService = new Google\Service\CloudComposer(...);
 *   $userWorkloadsConfigMaps = $composerService->projects_locations_environments_userWorkloadsConfigMaps;
 *  </code>
 */
class ProjectsLocationsEnvironmentsUserWorkloadsConfigMaps extends \Google\Service\Resource
{
  /**
   * Creates a user workloads ConfigMap. This method is supported for Cloud
   * Composer environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsConfigMaps.create)
   *
   * @param string $parent Required. The environment name to create a ConfigMap
   * for, in the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * @param UserWorkloadsConfigMap $postBody
   * @param array $optParams Optional parameters.
   * @return UserWorkloadsConfigMap
   * @throws \Google\Service\Exception
   */
  public function create($parent, UserWorkloadsConfigMap $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], UserWorkloadsConfigMap::class);
  }
  /**
   * Deletes a user workloads ConfigMap. This method is supported for Cloud
   * Composer environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsConfigMaps.delete)
   *
   * @param string $name Required. The ConfigMap to delete, in the form: "projects
   * /{projectId}/locations/{locationId}/environments/{environmentId}/userWorkload
   * sConfigMaps/{userWorkloadsConfigMapId}"
   * @param array $optParams Optional parameters.
   * @return ComposerEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], ComposerEmpty::class);
  }
  /**
   * Gets an existing user workloads ConfigMap. This method is supported for Cloud
   * Composer environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsConfigMaps.get)
   *
   * @param string $name Required. The resource name of the ConfigMap to get, in
   * the form: "projects/{projectId}/locations/{locationId}/environments/{environm
   * entId}/userWorkloadsConfigMaps/{userWorkloadsConfigMapId}"
   * @param array $optParams Optional parameters.
   * @return UserWorkloadsConfigMap
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], UserWorkloadsConfigMap::class);
  }
  /**
   * Lists user workloads ConfigMaps. This method is supported for Cloud Composer
   * environments in versions composer-3-airflow-*.*.*-build.* and newer. (userWor
   * kloadsConfigMaps.listProjectsLocationsEnvironmentsUserWorkloadsConfigMaps)
   *
   * @param string $parent Required. List ConfigMaps in the given environment, in
   * the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of ConfigMaps to return.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous List request, if any.
   * @return ListUserWorkloadsConfigMapsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEnvironmentsUserWorkloadsConfigMaps($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListUserWorkloadsConfigMapsResponse::class);
  }
  /**
   * Updates a user workloads ConfigMap. This method is supported for Cloud
   * Composer environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsConfigMaps.update)
   *
   * @param string $name Identifier. The resource name of the ConfigMap, in the
   * form: "projects/{projectId}/locations/{locationId}/environments/{environmentI
   * d}/userWorkloadsConfigMaps/{userWorkloadsConfigMapId}"
   * @param UserWorkloadsConfigMap $postBody
   * @param array $optParams Optional parameters.
   * @return UserWorkloadsConfigMap
   * @throws \Google\Service\Exception
   */
  public function update($name, UserWorkloadsConfigMap $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], UserWorkloadsConfigMap::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEnvironmentsUserWorkloadsConfigMaps::class, 'Google_Service_CloudComposer_Resource_ProjectsLocationsEnvironmentsUserWorkloadsConfigMaps');
