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

namespace Google\Service\DataPortability\Resource;

use Google\Service\DataPortability\CancelPortabilityArchiveRequest;
use Google\Service\DataPortability\CancelPortabilityArchiveResponse;
use Google\Service\DataPortability\PortabilityArchiveState;
use Google\Service\DataPortability\RetryPortabilityArchiveRequest;
use Google\Service\DataPortability\RetryPortabilityArchiveResponse;

/**
 * The "archiveJobs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dataportabilityService = new Google\Service\DataPortability(...);
 *   $archiveJobs = $dataportabilityService->archiveJobs;
 *  </code>
 */
class ArchiveJobs extends \Google\Service\Resource
{
  /**
   * Cancels a Portability Archive job. (archiveJobs.cancel)
   *
   * @param string $name Required. The Archive job ID you're canceling. This is
   * returned by the InitiatePortabilityArchive response. The format is:
   * archiveJobs/{archive_job}. Canceling is only executed if the job is in
   * progress.
   * @param CancelPortabilityArchiveRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelPortabilityArchiveResponse
   * @throws \Google\Service\Exception
   */
  public function cancel($name, CancelPortabilityArchiveRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancel', [$params], CancelPortabilityArchiveResponse::class);
  }
  /**
   * Retrieves the state of an Archive job for the Portability API.
   * (archiveJobs.getPortabilityArchiveState)
   *
   * @param string $name Required. The archive job ID that is returned when you
   * request the state of the job. The format is:
   * archiveJobs/{archive_job}/portabilityArchiveState. archive_job is the job ID
   * returned by the InitiatePortabilityArchiveResponse.
   * @param array $optParams Optional parameters.
   * @return PortabilityArchiveState
   * @throws \Google\Service\Exception
   */
  public function getPortabilityArchiveState($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getPortabilityArchiveState', [$params], PortabilityArchiveState::class);
  }
  /**
   * Retries a failed Portability Archive job. (archiveJobs.retry)
   *
   * @param string $name Required. The Archive job ID you're retrying. This is
   * returned by the InitiatePortabilityArchiveResponse. Retrying is only executed
   * if the initial job failed.
   * @param RetryPortabilityArchiveRequest $postBody
   * @param array $optParams Optional parameters.
   * @return RetryPortabilityArchiveResponse
   * @throws \Google\Service\Exception
   */
  public function retry($name, RetryPortabilityArchiveRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('retry', [$params], RetryPortabilityArchiveResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArchiveJobs::class, 'Google_Service_DataPortability_Resource_ArchiveJobs');
