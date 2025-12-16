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

namespace Google\Service\Logging;

class MetricDescriptorMetadata extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our Terms of Service
   * (https://cloud.google.com/terms/) and the Google Cloud Platform Subject to
   * the Deprecation Policy (https://cloud.google.com/terms/deprecation)
   * documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'timeSeriesResourceHierarchyLevel';
  /**
   * The delay of data points caused by ingestion. Data points older than this
   * age are guaranteed to be ingested and available to be read, excluding data
   * loss due to errors.
   *
   * @var string
   */
  public $ingestDelay;
  /**
   * Deprecated. Must use the MetricDescriptor.launch_stage instead.
   *
   * @deprecated
   * @var string
   */
  public $launchStage;
  /**
   * The sampling period of metric data points. For metrics which are written
   * periodically, consecutive data points are stored at this time interval,
   * excluding data loss due to errors. Metrics with a higher granularity have a
   * smaller sampling period.
   *
   * @var string
   */
  public $samplePeriod;
  /**
   * The scope of the timeseries data of the metric.
   *
   * @var string[]
   */
  public $timeSeriesResourceHierarchyLevel;

  /**
   * The delay of data points caused by ingestion. Data points older than this
   * age are guaranteed to be ingested and available to be read, excluding data
   * loss due to errors.
   *
   * @param string $ingestDelay
   */
  public function setIngestDelay($ingestDelay)
  {
    $this->ingestDelay = $ingestDelay;
  }
  /**
   * @return string
   */
  public function getIngestDelay()
  {
    return $this->ingestDelay;
  }
  /**
   * Deprecated. Must use the MetricDescriptor.launch_stage instead.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @deprecated
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @deprecated
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * The sampling period of metric data points. For metrics which are written
   * periodically, consecutive data points are stored at this time interval,
   * excluding data loss due to errors. Metrics with a higher granularity have a
   * smaller sampling period.
   *
   * @param string $samplePeriod
   */
  public function setSamplePeriod($samplePeriod)
  {
    $this->samplePeriod = $samplePeriod;
  }
  /**
   * @return string
   */
  public function getSamplePeriod()
  {
    return $this->samplePeriod;
  }
  /**
   * The scope of the timeseries data of the metric.
   *
   * @param string[] $timeSeriesResourceHierarchyLevel
   */
  public function setTimeSeriesResourceHierarchyLevel($timeSeriesResourceHierarchyLevel)
  {
    $this->timeSeriesResourceHierarchyLevel = $timeSeriesResourceHierarchyLevel;
  }
  /**
   * @return string[]
   */
  public function getTimeSeriesResourceHierarchyLevel()
  {
    return $this->timeSeriesResourceHierarchyLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MetricDescriptorMetadata::class, 'Google_Service_Logging_MetricDescriptorMetadata');
