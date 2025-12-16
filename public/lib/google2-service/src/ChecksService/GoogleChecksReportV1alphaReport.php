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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaReport extends \Google\Collection
{
  protected $collection_key = 'checks';
  protected $appBundleType = GoogleChecksReportV1alphaAppBundle::class;
  protected $appBundleDataType = '';
  protected $checksType = GoogleChecksReportV1alphaCheck::class;
  protected $checksDataType = 'array';
  protected $dataMonitoringType = GoogleChecksReportV1alphaDataMonitoring::class;
  protected $dataMonitoringDataType = '';
  /**
   * Resource name of the report.
   *
   * @var string
   */
  public $name;
  /**
   * A URL to view results.
   *
   * @var string
   */
  public $resultsUri;

  /**
   * Information about the analyzed app bundle.
   *
   * @param GoogleChecksReportV1alphaAppBundle $appBundle
   */
  public function setAppBundle(GoogleChecksReportV1alphaAppBundle $appBundle)
  {
    $this->appBundle = $appBundle;
  }
  /**
   * @return GoogleChecksReportV1alphaAppBundle
   */
  public function getAppBundle()
  {
    return $this->appBundle;
  }
  /**
   * List of checks that were run on the app bundle.
   *
   * @param GoogleChecksReportV1alphaCheck[] $checks
   */
  public function setChecks($checks)
  {
    $this->checks = $checks;
  }
  /**
   * @return GoogleChecksReportV1alphaCheck[]
   */
  public function getChecks()
  {
    return $this->checks;
  }
  /**
   * Information related to data monitoring.
   *
   * @param GoogleChecksReportV1alphaDataMonitoring $dataMonitoring
   */
  public function setDataMonitoring(GoogleChecksReportV1alphaDataMonitoring $dataMonitoring)
  {
    $this->dataMonitoring = $dataMonitoring;
  }
  /**
   * @return GoogleChecksReportV1alphaDataMonitoring
   */
  public function getDataMonitoring()
  {
    return $this->dataMonitoring;
  }
  /**
   * Resource name of the report.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * A URL to view results.
   *
   * @param string $resultsUri
   */
  public function setResultsUri($resultsUri)
  {
    $this->resultsUri = $resultsUri;
  }
  /**
   * @return string
   */
  public function getResultsUri()
  {
    return $this->resultsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaReport::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaReport');
