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

class GooglePrivacyDlpV2KMapEstimationHistogramBucket extends \Google\Collection
{
  protected $collection_key = 'bucketValues';
  /**
   * Number of records within these anonymity bounds.
   *
   * @var string
   */
  public $bucketSize;
  /**
   * Total number of distinct quasi-identifier tuple values in this bucket.
   *
   * @var string
   */
  public $bucketValueCount;
  protected $bucketValuesType = GooglePrivacyDlpV2KMapEstimationQuasiIdValues::class;
  protected $bucketValuesDataType = 'array';
  /**
   * Always greater than or equal to min_anonymity.
   *
   * @var string
   */
  public $maxAnonymity;
  /**
   * Always positive.
   *
   * @var string
   */
  public $minAnonymity;

  /**
   * Number of records within these anonymity bounds.
   *
   * @param string $bucketSize
   */
  public function setBucketSize($bucketSize)
  {
    $this->bucketSize = $bucketSize;
  }
  /**
   * @return string
   */
  public function getBucketSize()
  {
    return $this->bucketSize;
  }
  /**
   * Total number of distinct quasi-identifier tuple values in this bucket.
   *
   * @param string $bucketValueCount
   */
  public function setBucketValueCount($bucketValueCount)
  {
    $this->bucketValueCount = $bucketValueCount;
  }
  /**
   * @return string
   */
  public function getBucketValueCount()
  {
    return $this->bucketValueCount;
  }
  /**
   * Sample of quasi-identifier tuple values in this bucket. The total number of
   * classes returned per bucket is capped at 20.
   *
   * @param GooglePrivacyDlpV2KMapEstimationQuasiIdValues[] $bucketValues
   */
  public function setBucketValues($bucketValues)
  {
    $this->bucketValues = $bucketValues;
  }
  /**
   * @return GooglePrivacyDlpV2KMapEstimationQuasiIdValues[]
   */
  public function getBucketValues()
  {
    return $this->bucketValues;
  }
  /**
   * Always greater than or equal to min_anonymity.
   *
   * @param string $maxAnonymity
   */
  public function setMaxAnonymity($maxAnonymity)
  {
    $this->maxAnonymity = $maxAnonymity;
  }
  /**
   * @return string
   */
  public function getMaxAnonymity()
  {
    return $this->maxAnonymity;
  }
  /**
   * Always positive.
   *
   * @param string $minAnonymity
   */
  public function setMinAnonymity($minAnonymity)
  {
    $this->minAnonymity = $minAnonymity;
  }
  /**
   * @return string
   */
  public function getMinAnonymity()
  {
    return $this->minAnonymity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2KMapEstimationHistogramBucket::class, 'Google_Service_DLP_GooglePrivacyDlpV2KMapEstimationHistogramBucket');
