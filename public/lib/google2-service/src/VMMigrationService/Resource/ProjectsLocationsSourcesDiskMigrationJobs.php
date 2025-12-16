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

namespace Google\Service\VMMigrationService\Resource;

use Google\Service\VMMigrationService\CancelDiskMigrationJobRequest;
use Google\Service\VMMigrationService\DiskMigrationJob;
use Google\Service\VMMigrationService\ListDiskMigrationJobsResponse;
use Google\Service\VMMigrationService\Operation;
use Google\Service\VMMigrationService\RunDiskMigrationJobRequest;

/**
 * The "diskMigrationJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmmigrationService = new Google\Service\VMMigrationService(...);
 *   $diskMigrationJobs = $vmmigrationService->projects_locations_sources_diskMigrationJobs;
 *  </code>
 */
class ProjectsLocationsSourcesDiskMigrationJobs extends \Google\Service\Resource
{
  /**
   * Cancels the disk migration job. (diskMigrationJobs.cancel)
   *
   * @param string $name Required. The name of the DiskMigrationJob.
   * @param CancelDiskMigrationJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelDiskMigrationJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], Operation::class);
  }
  /**
   * Creates a new disk migration job in a given Source.
   * (diskMigrationJobs.create)
   *
   * @param string $parent Required. The DiskMigrationJob's parent.
   * @param DiskMigrationJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string diskMigrationJobId Required. The DiskMigrationJob
   * identifier. The maximum length of this value is 63 characters. Valid
   * characters are lower case Latin letters, digits and hyphen. It must start
   * with a Latin letter and must not end with a hyphen.
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request timed out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function create($parent, DiskMigrationJob $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], Operation::class);
  }
  /**
   * Deletes a single DiskMigrationJob. (diskMigrationJobs.delete)
   *
   * @param string $name Required. The name of the DiskMigrationJob.
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
   * Gets details of a single DiskMigrationJob. (diskMigrationJobs.get)
   *
   * @param string $name Required. The name of the DiskMigrationJob.
   * @param array $optParams Optional parameters.
   * @return DiskMigrationJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DiskMigrationJob::class);
  }
  /**
   * Lists DiskMigrationJobs in a given Source.
   * (diskMigrationJobs.listProjectsLocationsSourcesDiskMigrationJobs)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * DiskMigrationJobs.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter request (according to AIP-160).
   * @opt_param string orderBy Optional. Ordering of the result list.
   * @opt_param int pageSize Optional. The maximum number of disk migration jobs
   * to return. The service may return fewer than this value. If unspecified, at
   * most 500 disk migration jobs will be returned. The maximum value is 1000;
   * values above 1000 will be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListDiskMigrationJobs` call. Provide this to retrieve the subsequent page.
   * When paginating, all parameters provided to `ListDiskMigrationJobs` except
   * `page_size` must match the call that provided the page token.
   * @return ListDiskMigrationJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsSourcesDiskMigrationJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListDiskMigrationJobsResponse::class);
  }
  /**
   * Updates the parameters of a single DiskMigrationJob.
   * (diskMigrationJobs.patch)
   *
   * @param string $name Output only. Identifier. The identifier of the
   * DiskMigrationJob.
   * @param DiskMigrationJob $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string requestId Optional. A request ID to identify requests.
   * Specify a unique request ID so that if you must retry your request, the
   * server will know to ignore the request if it has already been completed. The
   * server will guarantee that for at least 60 minutes since the first request.
   * For example, consider a situation where you make an initial request and the
   * request timed out. If you make the request again with the same request ID,
   * the server can check if original operation with the same request ID was
   * received, and if so, will ignore the second request. This prevents clients
   * from accidentally creating duplicate commitments. The request ID must be a
   * valid UUID with the exception that zero UUID is not supported
   * (00000000-0000-0000-0000-000000000000).
   * @opt_param string updateMask Optional. Field mask is used to specify the
   * fields to be overwritten in the DiskMigrationJob resource by the update. The
   * fields specified in the update_mask are relative to the resource, not the
   * full request. A field will be overwritten if it is in the mask. If the user
   * does not provide a mask, then a mask equivalent to all fields that are
   * populated (have a non-empty value), will be implied.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($name, DiskMigrationJob $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Runs the disk migration job. (diskMigrationJobs.run)
   *
   * @param string $name Required. The name of the DiskMigrationJob.
   * @param RunDiskMigrationJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function run($name, RunDiskMigrationJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('run', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsSourcesDiskMigrationJobs::class, 'Google_Service_VMMigrationService_Resource_ProjectsLocationsSourcesDiskMigrationJobs');
