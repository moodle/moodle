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

use Google\Service\VMMigrationService\CancelImageImportJobRequest;
use Google\Service\VMMigrationService\ImageImportJob;
use Google\Service\VMMigrationService\ListImageImportJobsResponse;
use Google\Service\VMMigrationService\Operation;

/**
 * The "imageImportJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $vmmigrationService = new Google\Service\VMMigrationService(...);
 *   $imageImportJobs = $vmmigrationService->projects_locations_imageImports_imageImportJobs;
 *  </code>
 */
class ProjectsLocationsImageImportsImageImportJobs extends \Google\Service\Resource
{
  /**
   * Initiates the cancellation of a running ImageImportJob.
   * (imageImportJobs.cancel)
   *
   * @param string $name Required. The image import job id.
   * @param CancelImageImportJobRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelImageImportJobRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], Operation::class);
  }
  /**
   * Gets details of a single ImageImportJob. (imageImportJobs.get)
   *
   * @param string $name Required. The ImageImportJob name.
   * @param array $optParams Optional parameters.
   * @return ImageImportJob
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], ImageImportJob::class);
  }
  /**
   * Lists ImageImportJobs in a given project.
   * (imageImportJobs.listProjectsLocationsImageImportsImageImportJobs)
   *
   * @param string $parent Required. The parent, which owns this collection of
   * targets.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Optional. The filter request (according to AIP-160).
   * @opt_param string orderBy Optional. The order by fields for the result
   * (according to AIP-132). Currently ordering is only possible by "name" field.
   * @opt_param int pageSize Optional. The maximum number of targets to return.
   * The service may return fewer than this value. If unspecified, at most 500
   * targets will be returned. The maximum value is 1000; values above 1000 will
   * be coerced to 1000.
   * @opt_param string pageToken Optional. A page token, received from a previous
   * `ListImageImportJobs` call. Provide this to retrieve the subsequent page.
   * When paginating, all other parameters provided to `ListImageImportJobs` must
   * match the call that provided the page token.
   * @return ListImageImportJobsResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsImageImportsImageImportJobs($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListImageImportJobsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsImageImportsImageImportJobs::class, 'Google_Service_VMMigrationService_Resource_ProjectsLocationsImageImportsImageImportJobs');
