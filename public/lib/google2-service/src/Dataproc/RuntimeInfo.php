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

namespace Google\Service\Dataproc;

class RuntimeInfo extends \Google\Model
{
  protected $approximateUsageType = UsageMetrics::class;
  protected $approximateUsageDataType = '';
  protected $currentUsageType = UsageSnapshot::class;
  protected $currentUsageDataType = '';
  /**
   * Output only. A URI pointing to the location of the diagnostics tarball.
   *
   * @var string
   */
  public $diagnosticOutputUri;
  /**
   * Output only. Map of remote access endpoints (such as web interfaces and
   * APIs) to their URIs.
   *
   * @var string[]
   */
  public $endpoints;
  /**
   * Output only. A URI pointing to the location of the stdout and stderr of the
   * workload.
   *
   * @var string
   */
  public $outputUri;
  protected $propertiesInfoType = PropertiesInfo::class;
  protected $propertiesInfoDataType = '';

  /**
   * Output only. Approximate workload resource usage, calculated when the
   * workload completes (see Dataproc Serverless pricing
   * (https://cloud.google.com/dataproc-serverless/pricing)).Note: This metric
   * calculation may change in the future, for example, to capture cumulative
   * workload resource consumption during workload execution (see the Dataproc
   * Serverless release notes (https://cloud.google.com/dataproc-
   * serverless/docs/release-notes) for announcements, changes, fixes and other
   * Dataproc developments).
   *
   * @param UsageMetrics $approximateUsage
   */
  public function setApproximateUsage(UsageMetrics $approximateUsage)
  {
    $this->approximateUsage = $approximateUsage;
  }
  /**
   * @return UsageMetrics
   */
  public function getApproximateUsage()
  {
    return $this->approximateUsage;
  }
  /**
   * Output only. Snapshot of current workload resource usage.
   *
   * @param UsageSnapshot $currentUsage
   */
  public function setCurrentUsage(UsageSnapshot $currentUsage)
  {
    $this->currentUsage = $currentUsage;
  }
  /**
   * @return UsageSnapshot
   */
  public function getCurrentUsage()
  {
    return $this->currentUsage;
  }
  /**
   * Output only. A URI pointing to the location of the diagnostics tarball.
   *
   * @param string $diagnosticOutputUri
   */
  public function setDiagnosticOutputUri($diagnosticOutputUri)
  {
    $this->diagnosticOutputUri = $diagnosticOutputUri;
  }
  /**
   * @return string
   */
  public function getDiagnosticOutputUri()
  {
    return $this->diagnosticOutputUri;
  }
  /**
   * Output only. Map of remote access endpoints (such as web interfaces and
   * APIs) to their URIs.
   *
   * @param string[] $endpoints
   */
  public function setEndpoints($endpoints)
  {
    $this->endpoints = $endpoints;
  }
  /**
   * @return string[]
   */
  public function getEndpoints()
  {
    return $this->endpoints;
  }
  /**
   * Output only. A URI pointing to the location of the stdout and stderr of the
   * workload.
   *
   * @param string $outputUri
   */
  public function setOutputUri($outputUri)
  {
    $this->outputUri = $outputUri;
  }
  /**
   * @return string
   */
  public function getOutputUri()
  {
    return $this->outputUri;
  }
  /**
   * Optional. Properties of the workload organized by origin.
   *
   * @param PropertiesInfo $propertiesInfo
   */
  public function setPropertiesInfo(PropertiesInfo $propertiesInfo)
  {
    $this->propertiesInfo = $propertiesInfo;
  }
  /**
   * @return PropertiesInfo
   */
  public function getPropertiesInfo()
  {
    return $this->propertiesInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuntimeInfo::class, 'Google_Service_Dataproc_RuntimeInfo');
