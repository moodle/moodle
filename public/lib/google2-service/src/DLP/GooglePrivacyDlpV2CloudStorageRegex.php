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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2CloudStorageRegex extends \Google\Model
{
  /**
   * Optional. Regex to test the bucket name against. If empty, all buckets
   * match. Example: "marketing2021" or "(marketing)\d{4}" will both match the
   * bucket gs://marketing2021
   *
   * @var string
   */
  public $bucketNameRegex;
  /**
   * Optional. For organizations, if unset, will match all projects.
   *
   * @var string
   */
  public $projectIdRegex;

  /**
   * Optional. Regex to test the bucket name against. If empty, all buckets
   * match. Example: "marketing2021" or "(marketing)\d{4}" will both match the
   * bucket gs://marketing2021
   *
   * @param string $bucketNameRegex
   */
  public function setBucketNameRegex($bucketNameRegex)
  {
    $this->bucketNameRegex = $bucketNameRegex;
  }
  /**
   * @return string
   */
  public function getBucketNameRegex()
  {
    return $this->bucketNameRegex;
  }
  /**
   * Optional. For organizations, if unset, will match all projects.
   *
   * @param string $projectIdRegex
   */
  public function setProjectIdRegex($projectIdRegex)
  {
    $this->projectIdRegex = $projectIdRegex;
  }
  /**
   * @return string
   */
  public function getProjectIdRegex()
  {
    return $this->projectIdRegex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2CloudStorageRegex::class, 'Google_Service_DLP_GooglePrivacyDlpV2CloudStorageRegex');
