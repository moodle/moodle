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

namespace Google\Service\CloudDeploy;

class CloudRunMetadata extends \Google\Collection
{
  protected $collection_key = 'serviceUrls';
  /**
   * Output only. The name of the Cloud Run job that is associated with a
   * `Rollout`. Format is
   * `projects/{project}/locations/{location}/jobs/{job_name}`.
   *
   * @var string
   */
  public $job;
  /**
   * Output only. The Cloud Run Revision id associated with a `Rollout`.
   *
   * @var string
   */
  public $revision;
  /**
   * Output only. The name of the Cloud Run Service that is associated with a
   * `Rollout`. Format is
   * `projects/{project}/locations/{location}/services/{service}`.
   *
   * @var string
   */
  public $service;
  /**
   * Output only. The Cloud Run Service urls that are associated with a
   * `Rollout`.
   *
   * @var string[]
   */
  public $serviceUrls;

  /**
   * Output only. The name of the Cloud Run job that is associated with a
   * `Rollout`. Format is
   * `projects/{project}/locations/{location}/jobs/{job_name}`.
   *
   * @param string $job
   */
  public function setJob($job)
  {
    $this->job = $job;
  }
  /**
   * @return string
   */
  public function getJob()
  {
    return $this->job;
  }
  /**
   * Output only. The Cloud Run Revision id associated with a `Rollout`.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Output only. The name of the Cloud Run Service that is associated with a
   * `Rollout`. Format is
   * `projects/{project}/locations/{location}/services/{service}`.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * Output only. The Cloud Run Service urls that are associated with a
   * `Rollout`.
   *
   * @param string[] $serviceUrls
   */
  public function setServiceUrls($serviceUrls)
  {
    $this->serviceUrls = $serviceUrls;
  }
  /**
   * @return string[]
   */
  public function getServiceUrls()
  {
    return $this->serviceUrls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudRunMetadata::class, 'Google_Service_CloudDeploy_CloudRunMetadata');
