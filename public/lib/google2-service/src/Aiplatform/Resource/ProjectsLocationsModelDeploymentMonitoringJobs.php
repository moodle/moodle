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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListModelDeploymentMonitoringJobsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ModelDeploymentMonitoringJob;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1PauseModelDeploymentMonitoringJobRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ResumeModelDeploymentMonitoringJobRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesResponse;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "modelDeploymentMonitoringJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $modelDeploymentMonitoringJobs = $aiplatformService->projects_locations_modelDeploymentMonitoringJobs;
 *  </code>
 */
class ProjectsLocationsModelDeploymentMonitoringJobs extends \Google\Service\Resource
{
  /**
   * Creates a ModelDeploymentMonitoringJob. It will run periodically on a
   * configured interval. (modelDeploymentMonitoringJobs.create)
   *
   * @param string $parent Required. The parent of the
   * ModelDeploymentMonitoringJob. Format:
   * `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1ModelDeploymentMonitoringJob $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ModelDeploymentMonitoringJob
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1ModelDeploymentMonitoringJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1ModelDeploymentMonitoringJob::class);
  }
  /**
   * Deletes a ModelDeploymentMonitoringJob.
   * (modelDeploymentMonitoringJobs.delete)
   *
   * @param string $name Required. The resource name of the model monitoring job
   * to delete. Format: `projects/{project}/locations/{location}/modelDeploymentMo
   * nitoringJobs/{model_deployment_monitoring_job}`
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
   * Gets a ModelDeploymentMonitoringJob. (modelDeploymentMonitoringJobs.get)
   *
   * @param string $name Required. The resource name of the
   * ModelDeploymentMonitoringJob. Format: `projects/{project}/locations/{location
   * }/modelDeploymentMonitoringJobs/{model_deployment_monitoring_job}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1ModelDeploymentMonitoringJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1ModelDeploymentMonitoringJob::class);
  }
  /**
   * Lists ModelDeploymentMonitoringJobs in a Location. (modelDeploymentMonitoring
   * Jobs.listProjectsLocationsModelDeploymentMonitoringJobs)
   *
   * @param string $parent Required. The parent of the
   * ModelDeploymentMonitoringJob. Format:
   * `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter The standard list filter. Supported fields: *
   * `display_name` supports `=`, `!=` comparisons, and `:` wildcard. * `state`
   * supports `=`, `!=` comparisons. * `create_time` supports `=`, `!=`,`<`,
   * `<=`,`>`, `>=` comparisons. `create_time` must be in RFC 3339 format. *
   * `labels` supports general map functions that is: `labels.key=value` -
   * key:value equality `labels.key:* - key existence Some examples of using the
   * filter are: * `state="JOB_STATE_SUCCEEDED" AND display_name:"my_job_*"` *
   * `state!="JOB_STATE_FAILED" OR display_name="my_job"` * `NOT
   * display_name="my_job"` * `create_time>"2021-05-18T00:00:00Z"` *
   * `labels.keyA=valueA` * `labels.keyB:*`
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token.
   * @opt_param string readMask Mask specifying which fields to read
   * @return GoogleCloudAiplatformV1ListModelDeploymentMonitoringJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsModelDeploymentMonitoringJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListModelDeploymentMonitoringJobsResponse::class);
  }
  /**
   * Updates a ModelDeploymentMonitoringJob. (modelDeploymentMonitoringJobs.patch)
   *
   * @param string $name Output only. Resource name of a
   * ModelDeploymentMonitoringJob.
   * @param GoogleCloudAiplatformV1ModelDeploymentMonitoringJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask is used to specify the
   * fields to be overwritten in the ModelDeploymentMonitoringJob resource by the
   * update. The fields specified in the update_mask are relative to the resource,
   * not the full request. A field will be overwritten if it is in the mask. If
   * the user does not provide a mask then only the non-empty fields present in
   * the request will be overwritten. Set the update_mask to `*` to override all
   * fields. For the objective config, the user can either provide the update mask
   * for model_deployment_monitoring_objective_configs or any combination of its
   * nested fields, such as: model_deployment_monitoring_objective_configs.objecti
   * ve_config.training_dataset. Updatable fields: * `display_name` *
   * `model_deployment_monitoring_schedule_config` *
   * `model_monitoring_alert_config` * `logging_sampling_strategy` * `labels` *
   * `log_ttl` * `enable_monitoring_pipeline_logs` . and *
   * `model_deployment_monitoring_objective_configs` . or * `model_deployment_moni
   * toring_objective_configs.objective_config.training_dataset` * `model_deployme
   * nt_monitoring_objective_configs.objective_config.training_prediction_skew_det
   * ection_config` * `model_deployment_monitoring_objective_configs.objective_con
   * fig.prediction_drift_detection_config`
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1ModelDeploymentMonitoringJob $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Pauses a ModelDeploymentMonitoringJob. If the job is running, the server
   * makes a best effort to cancel the job. Will mark
   * ModelDeploymentMonitoringJob.state to 'PAUSED'.
   * (modelDeploymentMonitoringJobs.pause)
   *
   * @param string $name Required. The resource name of the
   * ModelDeploymentMonitoringJob to pause. Format: `projects/{project}/locations/
   * {location}/modelDeploymentMonitoringJobs/{model_deployment_monitoring_job}`
   * @param GoogleCloudAiplatformV1PauseModelDeploymentMonitoringJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function pause($name, GoogleCloudAiplatformV1PauseModelDeploymentMonitoringJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Resumes a paused ModelDeploymentMonitoringJob. It will start to run from next
   * scheduled time. A deleted ModelDeploymentMonitoringJob can't be resumed.
   * (modelDeploymentMonitoringJobs.resume)
   *
   * @param string $name Required. The resource name of the
   * ModelDeploymentMonitoringJob to resume. Format: `projects/{project}/locations
   * /{location}/modelDeploymentMonitoringJobs/{model_deployment_monitoring_job}`
   * @param GoogleCloudAiplatformV1ResumeModelDeploymentMonitoringJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function resume($name, GoogleCloudAiplatformV1ResumeModelDeploymentMonitoringJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Searches Model Monitoring Statistics generated within a given time window.
   * (modelDeploymentMonitoringJobs.searchModelDeploymentMonitoringStatsAnomalies)
   *
   * @param string $modelDeploymentMonitoringJob Required.
   * ModelDeploymentMonitoring Job resource name. Format: `projects/{project}/loca
   * tions/{location}/modelDeploymentMonitoringJobs/{model_deployment_monitoring_j
   * ob}`
   * @param GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesResponse
   * @throws \Google\Service\Exception
   */
  public function searchModelDeploymentMonitoringStatsAnomalies($modelDeploymentMonitoringJob, GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesRequest $postBody, $optParams = [])
  {
    $params = ['modelDeploymentMonitoringJob' => $modelDeploymentMonitoringJob, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('searchModelDeploymentMonitoringStatsAnomalies', [$params], GoogleCloudAiplatformV1SearchModelDeploymentMonitoringStatsAnomaliesResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsModelDeploymentMonitoringJobs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsModelDeploymentMonitoringJobs');
