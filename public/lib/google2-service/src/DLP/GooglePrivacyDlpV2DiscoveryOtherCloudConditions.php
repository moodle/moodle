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

class GooglePrivacyDlpV2DiscoveryOtherCloudConditions extends \Google\Model
{
  protected $amazonS3BucketConditionsType = GooglePrivacyDlpV2AmazonS3BucketConditions::class;
  protected $amazonS3BucketConditionsDataType = '';
  /**
   * Minimum age a resource must be before Cloud DLP can profile it. Value must
   * be 1 hour or greater.
   *
   * @var string
   */
  public $minAge;

  /**
   * Amazon S3 bucket conditions.
   *
   * @param GooglePrivacyDlpV2AmazonS3BucketConditions $amazonS3BucketConditions
   */
  public function setAmazonS3BucketConditions(GooglePrivacyDlpV2AmazonS3BucketConditions $amazonS3BucketConditions)
  {
    $this->amazonS3BucketConditions = $amazonS3BucketConditions;
  }
  /**
   * @return GooglePrivacyDlpV2AmazonS3BucketConditions
   */
  public function getAmazonS3BucketConditions()
  {
    return $this->amazonS3BucketConditions;
  }
  /**
   * Minimum age a resource must be before Cloud DLP can profile it. Value must
   * be 1 hour or greater.
   *
   * @param string $minAge
   */
  public function setMinAge($minAge)
  {
    $this->minAge = $minAge;
  }
  /**
   * @return string
   */
  public function getMinAge()
  {
    return $this->minAge;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryOtherCloudConditions::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryOtherCloudConditions');
