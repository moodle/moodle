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

namespace Google\Service\Aiplatform\Resource;

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchImportEvaluatedAnnotationsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchImportEvaluatedAnnotationsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListModelEvaluationSlicesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ModelEvaluationSlice;

/**
 * The "slices" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $slices = $aiplatformService->projects_locations_models_evaluations_slices;
 *  </code>
 */
class ProjectsLocationsModelsEvaluationsSlices extends \Google\Service\Resource
{
  /**
   * Imports a list of externally generated EvaluatedAnnotations.
   * (slices.batchImport)
   *
   * @param string $parent Required. The name of the parent ModelEvaluationSlice
   * resource. Format: `projects/{project}/locations/{location}/models/{model}/eva
   * luations/{evaluation}/slices/{slice}`
   * @param GoogleCloudAiplatformV1BatchImportEvaluatedAnnotationsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1BatchImportEvaluatedAnnotationsResponse
   * @throws \Google\Service\Exception
   */
  public function batchImport($parent, GoogleCloudAiplatformV1BatchImportEvaluatedAnnotationsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchImport', [$params], GoogleCloudAiplatformV1BatchImportEvaluatedAnnotationsResponse::class);
  }
  /**
   * Gets a ModelEvaluationSlice. (slices.get)
   *
   * @param string $name Required. The name of the ModelEvaluationSlice resource.
   * Format: `projects/{project}/locations/{location}/models/{model}/evaluations/{
   * evaluation}/slices/{slice}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ModelEvaluationSlice
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1ModelEvaluationSlice::class);
  }
  /**
   * Lists ModelEvaluationSlices in a ModelEvaluation.
   * (slices.listProjectsLocationsModelsEvaluationsSlices)
   *
   * @param string $parent Required. The resource name of the ModelEvaluation to
   * list the ModelEvaluationSlices from. Format: `projects/{project}/locations/{l
   * ocation}/models/{model}/evaluations/{evaluation}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter. * `slice.dimension` - for
   * =.
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListModelEvaluationSlicesResponse.next_page_token of the previous
   * ModelService.ListModelEvaluationSlices call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListModelEvaluationSlicesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsModelsEvaluationsSlices($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListModelEvaluationSlicesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsModelsEvaluationsSlices::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsModelsEvaluationsSlices');
