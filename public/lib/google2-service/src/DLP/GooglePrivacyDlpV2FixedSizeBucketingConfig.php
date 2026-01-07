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

class GooglePrivacyDlpV2FixedSizeBucketingConfig extends \Google\Model
{
  /**
   * Required. Size of each bucket (except for minimum and maximum buckets). So
   * if `lower_bound` = 10, `upper_bound` = 89, and `bucket_size` = 10, then the
   * following buckets would be used: -10, 10-20, 20-30, 30-40, 40-50, 50-60,
   * 60-70, 70-80, 80-89, 89+. Precision up to 2 decimals works.
   *
   * @var 
   */
  public $bucketSize;
  protected $lowerBoundType = GooglePrivacyDlpV2Value::class;
  protected $lowerBoundDataType = '';
  protected $upperBoundType = GooglePrivacyDlpV2Value::class;
  protected $upperBoundDataType = '';

  public function setBucketSize($bucketSize)
  {
    $this->bucketSize = $bucketSize;
  }
  public function getBucketSize()
  {
    return $this->bucketSize;
  }
  /**
   * Required. Lower bound value of buckets. All values less than `lower_bound`
   * are grouped together into a single bucket; for example if `lower_bound` =
   * 10, then all values less than 10 are replaced with the value "-10".
   *
   * @param GooglePrivacyDlpV2Value $lowerBound
   */
  public function setLowerBound(GooglePrivacyDlpV2Value $lowerBound)
  {
    $this->lowerBound = $lowerBound;
  }
  /**
   * @return GooglePrivacyDlpV2Value
   */
  public function getLowerBound()
  {
    return $this->lowerBound;
  }
  /**
   * Required. Upper bound value of buckets. All values greater than upper_bound
   * are grouped together into a single bucket; for example if `upper_bound` =
   * 89, then all values greater than 89 are replaced with the value "89+".
   *
   * @param GooglePrivacyDlpV2Value $upperBound
   */
  public function setUpperBound(GooglePrivacyDlpV2Value $upperBound)
  {
    $this->upperBound = $upperBound;
  }
  /**
   * @return GooglePrivacyDlpV2Value
   */
  public function getUpperBound()
  {
    return $this->upperBound;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2FixedSizeBucketingConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2FixedSizeBucketingConfig');
