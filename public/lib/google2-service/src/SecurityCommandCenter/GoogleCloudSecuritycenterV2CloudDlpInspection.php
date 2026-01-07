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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV2CloudDlpInspection extends \Google\Model
{
  /**
   * Whether Cloud DLP scanned the complete resource or a sampled subset.
   *
   * @var bool
   */
  public $fullScan;
  /**
   * The type of information (or
   * *[infoType](https://cloud.google.com/dlp/docs/infotypes-reference)*) found,
   * for example, `EMAIL_ADDRESS` or `STREET_ADDRESS`.
   *
   * @var string
   */
  public $infoType;
  /**
   * The number of times Cloud DLP found this infoType within this job and
   * resource.
   *
   * @var string
   */
  public $infoTypeCount;
  /**
   * Name of the inspection job, for example,
   * `projects/123/locations/europe/dlpJobs/i-8383929`.
   *
   * @var string
   */
  public $inspectJob;

  /**
   * Whether Cloud DLP scanned the complete resource or a sampled subset.
   *
   * @param bool $fullScan
   */
  public function setFullScan($fullScan)
  {
    $this->fullScan = $fullScan;
  }
  /**
   * @return bool
   */
  public function getFullScan()
  {
    return $this->fullScan;
  }
  /**
   * The type of information (or
   * *[infoType](https://cloud.google.com/dlp/docs/infotypes-reference)*) found,
   * for example, `EMAIL_ADDRESS` or `STREET_ADDRESS`.
   *
   * @param string $infoType
   */
  public function setInfoType($infoType)
  {
    $this->infoType = $infoType;
  }
  /**
   * @return string
   */
  public function getInfoType()
  {
    return $this->infoType;
  }
  /**
   * The number of times Cloud DLP found this infoType within this job and
   * resource.
   *
   * @param string $infoTypeCount
   */
  public function setInfoTypeCount($infoTypeCount)
  {
    $this->infoTypeCount = $infoTypeCount;
  }
  /**
   * @return string
   */
  public function getInfoTypeCount()
  {
    return $this->infoTypeCount;
  }
  /**
   * Name of the inspection job, for example,
   * `projects/123/locations/europe/dlpJobs/i-8383929`.
   *
   * @param string $inspectJob
   */
  public function setInspectJob($inspectJob)
  {
    $this->inspectJob = $inspectJob;
  }
  /**
   * @return string
   */
  public function getInspectJob()
  {
    return $this->inspectJob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV2CloudDlpInspection::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV2CloudDlpInspection');
