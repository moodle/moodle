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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ImportModelEvaluationRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListModelEvaluationsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ModelEvaluation;

/**
 * The "evaluations" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $evaluations = $aiplatformService->projects_locations_models_evaluations;
 *  </code>
 */
class ProjectsLocationsModelsEvaluations extends \Google\Service\Resource
{
  /**
   * Gets a ModelEvaluation. (evaluations.get)
   *
   * @param string $name Required. The name of the ModelEvaluation resource.
   * Format: `projects/{project}/locations/{location}/models/{model}/evaluations/{
   * evaluation}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ModelEvaluation
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1ModelEvaluation::class);
  }
  /**
   * Imports an externally generated ModelEvaluation. (evaluations.import)
   *
   * @param string $parent Required. The name of the parent model resource.
   * Format: `projects/{project}/locations/{location}/models/{model}`
   * @param GoogleCloudAiplatformV1ImportModelEvaluationRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ModelEvaluation
   * @throws \Google\Service\Exception
   */
  public function import($parent, GoogleCloudAiplatformV1ImportModelEvaluationRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], GoogleCloudAiplatformV1ModelEvaluation::class);
  }
  /**
   * Lists ModelEvaluations in a Model.
   * (evaluations.listProjectsLocationsModelsEvaluations)
   *
   * @param string $parent Required. The resource name of the Model to list the
   * ModelEvaluations from. Format:
   * `projects/{project}/locations/{location}/models/{model}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter.
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListModelEvaluationsResponse.next_page_token of the previous
   * ModelService.ListModelEvaluations call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListModelEvaluationsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsModelsEvaluations($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListModelEvaluationsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsModelsEvaluations::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsModelsEvaluations');
