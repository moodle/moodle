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

namespace Google\Service\Translate\Resource;

use Google\Service\Translate\ListModelsResponse;
use Google\Service\Translate\Model;
use Google\Service\Translate\Operation;

/**
 * The "models" collection of methods.
 * Typical usage is:
 *  <code>
 *   $translateService = new Google\Service\Translate(...);
 *   $models = $translateService->projects_locations_models;
 *  </code>
 */
class ProjectsLocationsModels extends \Google\Service\Resource
{
  /**
   * Creates a Model. (models.create)
   *
   * @param string $parent Required. The project name, in form of
   * `projects/{project}/locations/{location}`
   * @param Model $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, Model $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a model. (models.delete)
   *
   * @param string $name Required. The name of the model to delete.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Gets a model. (models.get)
   *
   * @param string $name Required. The resource name of the model to retrieve.
   * @param array $optParams Optional parameters.
   * @return Model
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Model::class);
  }
  /**
   * Lists models. (models.listProjectsLocationsModels)
   *
   * @param string $parent Required. Name of the parent project. In form of
   * `projects/{project-number-or-id}/locations/{location-id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. An expression for filtering the models
   * that will be returned. Supported filter: `dataset_id=${dataset_id}`
   * @opt_param int pageSize Optional. Requested page size. The server can return
   * fewer results than requested.
   * @opt_param string pageToken Optional. A token identifying a page of results
   * for the server to return. Typically obtained from next_page_token field in
   * the response of a ListModels call.
   * @return ListModelsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsModels($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListModelsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsModels::class, 'Google_Service_Translate_Resource_ProjectsLocationsModels');
