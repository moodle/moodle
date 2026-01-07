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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1CancelTrainingPipelineRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListTrainingPipelinesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1TrainingPipeline;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "trainingPipelines" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $trainingPipelines = $aiplatformService->projects_locations_trainingPipelines;
 *  </code>
 */
class ProjectsLocationsTrainingPipelines extends \Google\Service\Resource
{
  /**
   * Cancels a TrainingPipeline. Starts asynchronous cancellation on the
   * TrainingPipeline. The server makes a best effort to cancel the pipeline, but
   * success is not guaranteed. Clients can use
   * PipelineService.GetTrainingPipeline or other methods to check whether the
   * cancellation succeeded or whether the pipeline completed despite
   * cancellation. On successful cancellation, the TrainingPipeline is not
   * deleted; instead it becomes a pipeline with a TrainingPipeline.error value
   * with a google.rpc.Status.code of 1, corresponding to `Code.CANCELLED`, and
   * TrainingPipeline.state is set to `CANCELLED`. (trainingPipelines.cancel)
   *
   * @param string $name Required. The name of the TrainingPipeline to cancel.
   * Format: `projects/{project}/locations/{location}/trainingPipelines/{training_
   * pipeline}`
   * @param GoogleCloudAiplatformV1CancelTrainingPipelineRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudAiplatformV1CancelTrainingPipelineRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Creates a TrainingPipeline. A created TrainingPipeline right away will be
   * attempted to be run. (trainingPipelines.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the TrainingPipeline in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1TrainingPipeline $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1TrainingPipeline
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1TrainingPipeline $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1TrainingPipeline::class);
  }
  /**
   * Deletes a TrainingPipeline. (trainingPipelines.delete)
   *
   * @param string $name Required. The name of the TrainingPipeline resource to be
   * deleted. Format: `projects/{project}/locations/{location}/trainingPipelines/{
   * training_pipeline}`
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Gets a TrainingPipeline. (trainingPipelines.get)
   *
   * @param string $name Required. The name of the TrainingPipeline resource.
   * Format: `projects/{project}/locations/{location}/trainingPipelines/{training_
   * pipeline}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1TrainingPipeline
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1TrainingPipeline::class);
  }
  /**
   * Lists TrainingPipelines in a Location.
   * (trainingPipelines.listProjectsLocationsTrainingPipelines)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * TrainingPipelines from. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter. Supported fields: *
   * `display_name` supports `=`, `!=` comparisons, and `:` wildcard. * `state`
   * supports `=`, `!=` comparisons. * `training_task_definition` `=`, `!=`
   * comparisons, and `:` wildcard. * `create_time` supports `=`, `!=`,`<`,
   * `<=`,`>`, `>=` comparisons. `create_time` must be in RFC 3339 format. *
   * `labels` supports general map functions that is: `labels.key=value` -
   * key:value equality `labels.key:* - key existence Some examples of using the
   * filter are: * `state="PIPELINE_STATE_SUCCEEDED" AND
   * display_name:"my_pipeline_*"` * `state!="PIPELINE_STATE_FAILED" OR
   * display_name="my_pipeline"` * `NOT display_name="my_pipeline"` *
   * `create_time>"2021-05-18T00:00:00Z"` *
   * `training_task_definition:"*automl_text_classification*"`
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListTrainingPipelinesResponse.next_page_token of the previous
   * PipelineService.ListTrainingPipelines call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListTrainingPipelinesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsTrainingPipelines($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListTrainingPipelinesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsTrainingPipelines::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsTrainingPipelines');
