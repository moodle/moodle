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
use Google\Service\CloudComposer\ListUserWorkloadsSecretsResponse;
use Google\Service\CloudComposer\UserWorkloadsSecret;

/**
 * The "userWorkloadsSecrets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $composerService = new Google\Service\CloudComposer(...);
 *   $userWorkloadsSecrets = $composerService->projects_locations_environments_userWorkloadsSecrets;
 *  </code>
 */
class ProjectsLocationsEnvironmentsUserWorkloadsSecrets extends \Google\Service\Resource
{
  /**
   * Creates a user workloads Secret. This method is supported for Cloud Composer
   * environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsSecrets.create)
   *
   * @param string $parent Required. The environment name to create a Secret for,
   * in the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * @param UserWorkloadsSecret $postBody
   * @param array $optParams Optional parameters.
   * @return UserWorkloadsSecret
   * @throws \Google\Service\Exception
   */
  public function create($parent, UserWorkloadsSecret $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], UserWorkloadsSecret::class);
  }
  /**
   * Deletes a user workloads Secret. This method is supported for Cloud Composer
   * environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsSecrets.delete)
   *
   * @param string $name Required. The Secret to delete, in the form: "projects/{p
   * rojectId}/locations/{locationId}/environments/{environmentId}/userWorkloadsSe
   * crets/{userWorkloadsSecretId}"
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
   * Gets an existing user workloads Secret. Values of the "data" field in the
   * response are cleared. This method is supported for Cloud Composer
   * environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsSecrets.get)
   *
   * @param string $name Required. The resource name of the Secret to get, in the
   * form: "projects/{projectId}/locations/{locationId}/environments/{environmentI
   * d}/userWorkloadsSecrets/{userWorkloadsSecretId}"
   * @param array $optParams Optional parameters.
   * @return UserWorkloadsSecret
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], UserWorkloadsSecret::class);
  }
  /**
   * Lists user workloads Secrets. This method is supported for Cloud Composer
   * environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsSecrets.listProjectsLocationsEnvironmentsUserWorkloadsSecrets)
   *
   * @param string $parent Required. List Secrets in the given environment, in the
   * form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}"
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. The maximum number of Secrets to return.
   * @opt_param string pageToken Optional. The next_page_token value returned from
   * a previous List request, if any.
   * @return ListUserWorkloadsSecretsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsEnvironmentsUserWorkloadsSecrets($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListUserWorkloadsSecretsResponse::class);
  }
  /**
   * Updates a user workloads Secret. This method is supported for Cloud Composer
   * environments in versions composer-3-airflow-*.*.*-build.* and newer.
   * (userWorkloadsSecrets.update)
   *
   * @param string $name Identifier. The resource name of the Secret, in the form:
   * "projects/{projectId}/locations/{locationId}/environments/{environmentId}/use
   * rWorkloadsSecrets/{userWorkloadsSecretId}"
   * @param UserWorkloadsSecret $postBody
   * @param array $optParams Optional parameters.
   * @return UserWorkloadsSecret
   * @throws \Google\Service\Exception
   */
  public function update($name, UserWorkloadsSecret $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], UserWorkloadsSecret::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsEnvironmentsUserWorkloadsSecrets::class, 'Google_Service_CloudComposer_Resource_ProjectsLocationsEnvironmentsUserWorkloadsSecrets');
