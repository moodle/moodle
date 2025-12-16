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

namespace Google\Service\CloudRetail\Resource;

use Google\Service\CloudRetail\GoogleCloudRetailV2ListModelsResponse;
use Google\Service\CloudRetail\GoogleCloudRetailV2Model;
use Google\Service\CloudRetail\GoogleCloudRetailV2PauseModelRequest;
use Google\Service\CloudRetail\GoogleCloudRetailV2ResumeModelRequest;
use Google\Service\CloudRetail\GoogleCloudRetailV2TuneModelRequest;
use Google\Service\CloudRetail\GoogleLongrunningOperation;
use Google\Service\CloudRetail\GoogleProtobufEmpty;

/**
 * The "models" collection of methods.
 * Typical usage is:
 *  <code>
 *   $retailService = new Google\Service\CloudRetail(...);
 *   $models = $retailService->projects_locations_catalogs_models;
 *  </code>
 */
class ProjectsLocationsCatalogsModels extends \Google\Service\Resource
{
  /**
   * Creates a new model. (models.create)
   *
   * @param string $parent Required. The parent resource under which to create the
   * model. Format:
   * `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}`
   * @param GoogleCloudRetailV2Model $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool dryRun Optional. Whether to run a dry run to validate the
   * request (without actually creating the model).
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudRetailV2Model $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Deletes an existing model. (models.delete)
   *
   * @param string $name Required. The resource name of the Model to delete.
   * Format: `projects/{project_number}/locations/{location_id}/catalogs/{catalog_
   * id}/models/{model_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Gets a model. (models.get)
   *
   * @param string $name Required. The resource name of the Model to get. Format:
   * `projects/{project_number}/locations/{location_id}/catalogs/{catalog}/models/
   * {model_id}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRetailV2Model
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudRetailV2Model::class);
  }
  /**
   * Lists all the models linked to this event store.
   * (models.listProjectsLocationsCatalogsModels)
   *
   * @param string $parent Required. The parent for which to list models. Format:
   * `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize Optional. Maximum number of results to return. If
   * unspecified, defaults to 50. Max allowed value is 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListModels` call. Provide this to retrieve the subsequent page.
   * @return GoogleCloudRetailV2ListModelsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsCatalogsModels($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudRetailV2ListModelsResponse::class);
  }
  /**
   * Update of model metadata. Only fields that currently can be updated are:
   * `filtering_option` and `periodic_tuning_state`. If other values are provided,
   * this API method ignores them. (models.patch)
   *
   * @param string $name Required. The fully qualified resource name of the model.
   * Format: `projects/{project_number}/locations/{location_id}/catalogs/{catalog_
   * id}/models/{model_id}` catalog_id has char limit of 50.
   * recommendation_model_id has char limit of 40.
   * @param GoogleCloudRetailV2Model $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Optional. Indicates which fields in the provided
   * 'model' to update. If not set, by default updates all fields.
   * @return GoogleCloudRetailV2Model
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudRetailV2Model $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudRetailV2Model::class);
  }
  /**
   * Pauses the training of an existing model. (models.pause)
   *
   * @param string $name Required. The name of the model to pause. Format: `projec
   * ts/{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{mod
   * el_id}`
   * @param GoogleCloudRetailV2PauseModelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRetailV2Model
   * @throws \Google\Service\Exception
   */
  public function pause($name, GoogleCloudRetailV2PauseModelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], GoogleCloudRetailV2Model::class);
  }
  /**
   * Resumes the training of an existing model. (models.resume)
   *
   * @param string $name Required. The name of the model to resume. Format: `proje
   * cts/{project_number}/locations/{location_id}/catalogs/{catalog_id}/models/{mo
   * del_id}`
   * @param GoogleCloudRetailV2ResumeModelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudRetailV2Model
   * @throws \Google\Service\Exception
   */
  public function resume($name, GoogleCloudRetailV2ResumeModelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], GoogleCloudRetailV2Model::class);
  }
  /**
   * Tunes an existing model. (models.tune)
   *
   * @param string $name Required. The resource name of the model to tune. Format:
   * `projects/{project_number}/locations/{location_id}/catalogs/{catalog_id}/mode
   * ls/{model_id}`
   * @param GoogleCloudRetailV2TuneModelRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function tune($name, GoogleCloudRetailV2TuneModelRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('tune', [$params], GoogleLongrunningOperation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsCatalogsModels::class, 'Google_Service_CloudRetail_Resource_ProjectsLocationsCatalogsModels');
