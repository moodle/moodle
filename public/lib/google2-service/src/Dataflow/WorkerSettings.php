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

namespace Google\Service\Dataflow;

class WorkerSettings extends \Google\Model
{
  /**
   * The base URL for accessing Google Cloud APIs. When workers access Google
   * Cloud APIs, they logically do so via relative URLs. If this field is
   * specified, it supplies the base URL to use for resolving these relative
   * URLs. The normative algorithm used is defined by RFC 1808, "Relative
   * Uniform Resource Locators". If not specified, the default value is
   * "http://www.googleapis.com/"
   *
   * @var string
   */
  public $baseUrl;
  /**
   * Whether to send work progress updates to the service.
   *
   * @var bool
   */
  public $reportingEnabled;
  /**
   * The Cloud Dataflow service path relative to the root URL, for example,
   * "dataflow/v1b3/projects".
   *
   * @var string
   */
  public $servicePath;
  /**
   * The Shuffle service path relative to the root URL, for example,
   * "shuffle/v1beta1".
   *
   * @var string
   */
  public $shuffleServicePath;
  /**
   * The prefix of the resources the system should use for temporary storage.
   * The supported resource type is: Google Cloud Storage:
   * storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @var string
   */
  public $tempStoragePrefix;
  /**
   * The ID of the worker running this pipeline.
   *
   * @var string
   */
  public $workerId;

  /**
   * The base URL for accessing Google Cloud APIs. When workers access Google
   * Cloud APIs, they logically do so via relative URLs. If this field is
   * specified, it supplies the base URL to use for resolving these relative
   * URLs. The normative algorithm used is defined by RFC 1808, "Relative
   * Uniform Resource Locators". If not specified, the default value is
   * "http://www.googleapis.com/"
   *
   * @param string $baseUrl
   */
  public function setBaseUrl($baseUrl)
  {
    $this->baseUrl = $baseUrl;
  }
  /**
   * @return string
   */
  public function getBaseUrl()
  {
    return $this->baseUrl;
  }
  /**
   * Whether to send work progress updates to the service.
   *
   * @param bool $reportingEnabled
   */
  public function setReportingEnabled($reportingEnabled)
  {
    $this->reportingEnabled = $reportingEnabled;
  }
  /**
   * @return bool
   */
  public function getReportingEnabled()
  {
    return $this->reportingEnabled;
  }
  /**
   * The Cloud Dataflow service path relative to the root URL, for example,
   * "dataflow/v1b3/projects".
   *
   * @param string $servicePath
   */
  public function setServicePath($servicePath)
  {
    $this->servicePath = $servicePath;
  }
  /**
   * @return string
   */
  public function getServicePath()
  {
    return $this->servicePath;
  }
  /**
   * The Shuffle service path relative to the root URL, for example,
   * "shuffle/v1beta1".
   *
   * @param string $shuffleServicePath
   */
  public function setShuffleServicePath($shuffleServicePath)
  {
    $this->shuffleServicePath = $shuffleServicePath;
  }
  /**
   * @return string
   */
  public function getShuffleServicePath()
  {
    return $this->shuffleServicePath;
  }
  /**
   * The prefix of the resources the system should use for temporary storage.
   * The supported resource type is: Google Cloud Storage:
   * storage.googleapis.com/{bucket}/{object}
   * bucket.storage.googleapis.com/{object}
   *
   * @param string $tempStoragePrefix
   */
  public function setTempStoragePrefix($tempStoragePrefix)
  {
    $this->tempStoragePrefix = $tempStoragePrefix;
  }
  /**
   * @return string
   */
  public function getTempStoragePrefix()
  {
    return $this->tempStoragePrefix;
  }
  /**
   * The ID of the worker running this pipeline.
   *
   * @param string $workerId
   */
  public function setWorkerId($workerId)
  {
    $this->workerId = $workerId;
  }
  /**
   * @return string
   */
  public function getWorkerId()
  {
    return $this->workerId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WorkerSettings::class, 'Google_Service_Dataflow_WorkerSettings');
