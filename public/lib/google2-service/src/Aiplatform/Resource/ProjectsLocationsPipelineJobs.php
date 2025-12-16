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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchCancelPipelineJobsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1BatchDeletePipelineJobsRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1CancelPipelineJobRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListPipelineJobsResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1PipelineJob;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "pipelineJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $pipelineJobs = $aiplatformService->projects_locations_pipelineJobs;
 *  </code>
 */
class ProjectsLocationsPipelineJobs extends \Google\Service\Resource
{
  /**
   * Batch cancel PipelineJobs. Firstly the server will check if all the jobs are
   * in non-terminal states, and skip the jobs that are already terminated. If the
   * operation failed, none of the pipeline jobs are cancelled. The server will
   * poll the states of all the pipeline jobs periodically to check the
   * cancellation status. This operation will return an LRO.
   * (pipelineJobs.batchCancel)
   *
   * @param string $parent Required. The name of the PipelineJobs' parent
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1BatchCancelPipelineJobsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchCancel($parent, GoogleCloudAiplatformV1BatchCancelPipelineJobsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchCancel', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Batch deletes PipelineJobs The Operation is atomic. If it fails, none of the
   * PipelineJobs are deleted. If it succeeds, all of the PipelineJobs are
   * deleted. (pipelineJobs.batchDelete)
   *
   * @param string $parent Required. The name of the PipelineJobs' parent
   * resource. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1BatchDeletePipelineJobsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleLongrunningOperation
   * @throws \Google\Service\Exception
   */
  public function batchDelete($parent, GoogleCloudAiplatformV1BatchDeletePipelineJobsRequest $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('batchDelete', [$params], GoogleLongrunningOperation::class);
  }
  /**
   * Cancels a PipelineJob. Starts asynchronous cancellation on the PipelineJob.
   * The server makes a best effort to cancel the pipeline, but success is not
   * guaranteed. Clients can use PipelineService.GetPipelineJob or other methods
   * to check whether the cancellation succeeded or whether the pipeline completed
   * despite cancellation. On successful cancellation, the PipelineJob is not
   * deleted; instead it becomes a pipeline with a PipelineJob.error value with a
   * google.rpc.Status.code of 1, corresponding to `Code.CANCELLED`, and
   * PipelineJob.state is set to `CANCELLED`. (pipelineJobs.cancel)
   *
   * @param string $name Required. The name of the PipelineJob to cancel. Format:
   * `projects/{project}/locations/{location}/pipelineJobs/{pipeline_job}`
   * @param GoogleCloudAiplatformV1CancelPipelineJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function cancel($name, GoogleCloudAiplatformV1CancelPipelineJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Creates a PipelineJob. A PipelineJob will run immediately when created.
   * (pipelineJobs.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the PipelineJob in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1PipelineJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pipelineJobId The ID to use for the PipelineJob, which will
   * become the final component of the PipelineJob name. If not provided, an ID
   * will be automatically generated. This value should be less than 128
   * characters, and valid characters are `/a-z-/`.
   * @return GoogleCloudAiplatformV1PipelineJob
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1PipelineJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1PipelineJob::class);
  }
  /**
   * Deletes a PipelineJob. (pipelineJobs.delete)
   *
   * @param string $name Required. The name of the PipelineJob resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/pipelineJobs/{pipeline_job}`
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
   * Gets a PipelineJob. (pipelineJobs.get)
   *
   * @param string $name Required. The name of the PipelineJob resource. Format:
   * `projects/{project}/locations/{location}/pipelineJobs/{pipeline_job}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1PipelineJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1PipelineJob::class);
  }
  /**
   * Lists PipelineJobs in a Location.
   * (pipelineJobs.listProjectsLocationsPipelineJobs)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * PipelineJobs from. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the PipelineJobs that match the filter
   * expression. The following fields are supported: * `pipeline_name`: Supports
   * `=` and `!=` comparisons. * `display_name`: Supports `=`, `!=` comparisons,
   * and `:` wildcard. * `pipeline_job_user_id`: Supports `=`, `!=` comparisons,
   * and `:` wildcard. for example, can check if pipeline's display_name contains
   * *step* by doing display_name:\"*step*\" * `state`: Supports `=` and `!=`
   * comparisons. * `create_time`: Supports `=`, `!=`, `<`, `>`, `<=`, and `>=`
   * comparisons. Values must be in RFC 3339 format. * `update_time`: Supports
   * `=`, `!=`, `<`, `>`, `<=`, and `>=` comparisons. Values must be in RFC 3339
   * format. * `end_time`: Supports `=`, `!=`, `<`, `>`, `<=`, and `>=`
   * comparisons. Values must be in RFC 3339 format. * `labels`: Supports key-
   * value equality and key presence. * `template_uri`: Supports `=`, `!=`
   * comparisons, and `:` wildcard. * `template_metadata.version`: Supports `=`,
   * `!=` comparisons, and `:` wildcard. Filter expressions can be combined
   * together using logical operators (`AND` & `OR`). For example:
   * `pipeline_name="test" AND create_time>"2020-05-18T13:30:00Z"`. The syntax to
   * define filter expression is based on https://google.aip.dev/160. Examples: *
   * `create_time>"2021-05-18T00:00:00Z" OR update_time>"2020-05-18T00:00:00Z"`
   * PipelineJobs created or updated after 2020-05-18 00:00:00 UTC. * `labels.env
   * = "prod"` PipelineJobs with label "env" set to "prod".
   * @opt_param string orderBy A comma-separated list of fields to order by. The
   * default sort order is in ascending order. Use "desc" after a field name for
   * descending. You can have multiple order_by fields provided e.g. "create_time
   * desc, end_time", "end_time, start_time, update_time" For example, using
   * "create_time desc, end_time" will order results by create time in descending
   * order, and if there are multiple jobs having the same create time, order them
   * by the end time in ascending order. if order_by is not specified, it will
   * order by default order is create time in descending order. Supported fields:
   * * `create_time` * `update_time` * `end_time` * `start_time`
   * @opt_param int pageSize The standard list page size.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListPipelineJobsResponse.next_page_token of the previous
   * PipelineService.ListPipelineJobs call.
   * @opt_param string readMask Mask specifying which fields to read.
   * @return GoogleCloudAiplatformV1ListPipelineJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsPipelineJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListPipelineJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsPipelineJobs::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsPipelineJobs');
