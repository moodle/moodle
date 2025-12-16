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

use Google\Service\Aiplatform\GoogleCloudAiplatformV1ListSchedulesResponse;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1PauseScheduleRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1ResumeScheduleRequest;
use Google\Service\Aiplatform\GoogleCloudAiplatformV1Schedule;
use Google\Service\Aiplatform\GoogleLongrunningOperation;
use Google\Service\Aiplatform\GoogleProtobufEmpty;

/**
 * The "schedules" collection of methods.
 * Typical usage is:
 *  <code>
 *   $aiplatformService = new Google\Service\Aiplatform(...);
 *   $schedules = $aiplatformService->projects_locations_schedules;
 *  </code>
 */
class ProjectsLocationsSchedules extends \Google\Service\Resource
{
  /**
   * Creates a Schedule. (schedules.create)
   *
   * @param string $parent Required. The resource name of the Location to create
   * the Schedule in. Format: `projects/{project}/locations/{location}`
   * @param GoogleCloudAiplatformV1Schedule $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Schedule
   * @throws \Google\Service\Exception
   */
  public function create($parent, GoogleCloudAiplatformV1Schedule $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], GoogleCloudAiplatformV1Schedule::class);
  }
  /**
   * Deletes a Schedule. (schedules.delete)
   *
   * @param string $name Required. The name of the Schedule resource to be
   * deleted. Format:
   * `projects/{project}/locations/{location}/schedules/{schedule}`
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
   * Gets a Schedule. (schedules.get)
   *
   * @param string $name Required. The name of the Schedule resource. Format:
   * `projects/{project}/locations/{location}/schedules/{schedule}`
   * @param array $optParams Optional parameters.
   * @return GoogleCloudAiplatformV1Schedule
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], GoogleCloudAiplatformV1Schedule::class);
  }
  /**
   * Lists Schedules in a Location. (schedules.listProjectsLocationsSchedules)
   *
   * @param string $parent Required. The resource name of the Location to list the
   * Schedules from. Format: `projects/{project}/locations/{location}`
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Lists the Schedules that match the filter
   * expression. The following fields are supported: * `display_name`: Supports
   * `=`, `!=` comparisons, and `:` wildcard. * `state`: Supports `=` and `!=`
   * comparisons. * `request`: Supports existence of the check. (e.g.
   * `create_pipeline_job_request:*` --> Schedule has
   * create_pipeline_job_request). * `create_time`: Supports `=`, `!=`, `<`, `>`,
   * `<=`, and `>=` comparisons. Values must be in RFC 3339 format. *
   * `start_time`: Supports `=`, `!=`, `<`, `>`, `<=`, and `>=` comparisons.
   * Values must be in RFC 3339 format. * `end_time`: Supports `=`, `!=`, `<`,
   * `>`, `<=`, `>=` comparisons and `:*` existence check. Values must be in RFC
   * 3339 format. * `next_run_time`: Supports `=`, `!=`, `<`, `>`, `<=`, and `>=`
   * comparisons. Values must be in RFC 3339 format. Filter expressions can be
   * combined together using logical operators (`NOT`, `AND` & `OR`). The syntax
   * to define filter expression is based on https://google.aip.dev/160. Examples:
   * * `state="ACTIVE" AND display_name:"my_schedule_*"` * `NOT
   * display_name="my_schedule"` * `create_time>"2021-05-18T00:00:00Z"` *
   * `end_time>"2021-05-18T00:00:00Z" OR NOT end_time:*` *
   * `create_pipeline_job_request:*`
   * @opt_param string orderBy A comma-separated list of fields to order by. The
   * default sort order is in ascending order. Use "desc" after a field name for
   * descending. You can have multiple order_by fields provided. For example,
   * using "create_time desc, end_time" will order results by create time in
   * descending order, and if there are multiple schedules having the same create
   * time, order them by the end time in ascending order. If order_by is not
   * specified, it will order by default with create_time in descending order.
   * Supported fields: * `create_time` * `start_time` * `end_time` *
   * `next_run_time`
   * @opt_param int pageSize The standard list page size. Default to 100 if not
   * specified.
   * @opt_param string pageToken The standard list page token. Typically obtained
   * via ListSchedulesResponse.next_page_token of the previous
   * ScheduleService.ListSchedules call.
   * @return GoogleCloudAiplatformV1ListSchedulesResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSchedules($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], GoogleCloudAiplatformV1ListSchedulesResponse::class);
  }
  /**
   * Updates an active or paused Schedule. When the Schedule is updated, new runs
   * will be scheduled starting from the updated next execution time after the
   * update time based on the time_specification in the updated Schedule. All
   * unstarted runs before the update time will be skipped while already created
   * runs will NOT be paused or canceled. (schedules.patch)
   *
   * @param string $name Immutable. The resource name of the Schedule.
   * @param GoogleCloudAiplatformV1Schedule $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. See google.protobuf.FieldMask.
   * @return GoogleCloudAiplatformV1Schedule
   * @throws \Google\Service\Exception
   */
  public function patch($name, GoogleCloudAiplatformV1Schedule $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], GoogleCloudAiplatformV1Schedule::class);
  }
  /**
   * Pauses a Schedule. Will mark Schedule.state to 'PAUSED'. If the schedule is
   * paused, no new runs will be created. Already created runs will NOT be paused
   * or canceled. (schedules.pause)
   *
   * @param string $name Required. The name of the Schedule resource to be paused.
   * Format: `projects/{project}/locations/{location}/schedules/{schedule}`
   * @param GoogleCloudAiplatformV1PauseScheduleRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function pause($name, GoogleCloudAiplatformV1PauseScheduleRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pause', [$params], GoogleProtobufEmpty::class);
  }
  /**
   * Resumes a paused Schedule to start scheduling new runs. Will mark
   * Schedule.state to 'ACTIVE'. Only paused Schedule can be resumed. When the
   * Schedule is resumed, new runs will be scheduled starting from the next
   * execution time after the current time based on the time_specification in the
   * Schedule. If Schedule.catch_up is set up true, all missed runs will be
   * scheduled for backfill first. (schedules.resume)
   *
   * @param string $name Required. The name of the Schedule resource to be
   * resumed. Format:
   * `projects/{project}/locations/{location}/schedules/{schedule}`
   * @param GoogleCloudAiplatformV1ResumeScheduleRequest $postBody
   * @param array $optParams Optional parameters.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function resume($name, GoogleCloudAiplatformV1ResumeScheduleRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('resume', [$params], GoogleProtobufEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSchedules::class, 'Google_Service_Aiplatform_Resource_ProjectsLocationsSchedules');
