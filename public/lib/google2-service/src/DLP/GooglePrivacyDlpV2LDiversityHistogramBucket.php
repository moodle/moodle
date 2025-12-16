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

class GooglePrivacyDlpV2LDiversityHistogramBucket extends \Google\Collection
{
  protected $collection_key = 'bucketValues';
  /**
   * Total number of equivalence classes in this bucket.
   *
   * @var string
   */
  public $bucketSize;
  /**
   * Total number of distinct equivalence classes in this bucket.
   *
   * @var string
   */
  public $bucketValueCount;
  protected $bucketValuesType = GooglePrivacyDlpV2LDiversityEquivalenceClass::class;
  protected $bucketValuesDataType = 'array';
  /**
   * Lower bound on the sensitive value frequencies of the equivalence classes
   * in this bucket.
   *
   * @var string
   */
  public $sensitiveValueFrequencyLowerBound;
  /**
   * Upper bound on the sensitive value frequencies of the equivalence classes
   * in this bucket.
   *
   * @var string
   */
  public $sensitiveValueFrequencyUpperBound;

  /**
   * Total number of equivalence classes in this bucket.
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
   * Total number of distinct equivalence classes in this bucket.
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
   * Sample of equivalence classes in this bucket. The total number of classes
   * returned per bucket is capped at 20.
   *
   * @param GooglePrivacyDlpV2LDiversityEquivalenceClass[] $bucketValues
   */
  public function setBucketValues($bucketValues)
  {
    $this->bucketValues = $bucketValues;
  }
  /**
   * @return GooglePrivacyDlpV2LDiversityEquivalenceClass[]
   */
  public function getBucketValues()
  {
    return $this->bucketValues;
  }
  /**
   * Lower bound on the sensitive value frequencies of the equivalence classes
   * in this bucket.
   *
   * @param string $sensitiveValueFrequencyLowerBound
   */
  public function setSensitiveValueFrequencyLowerBound($sensitiveValueFrequencyLowerBound)
  {
    $this->sensitiveValueFrequencyLowerBound = $sensitiveValueFrequencyLowerBound;
  }
  /**
   * @return string
   */
  public function getSensitiveValueFrequencyLowerBound()
  {
    return $this->sensitiveValueFrequencyLowerBound;
  }
  /**
   * Upper bound on the sensitive value frequencies of the equivalence classes
   * in this bucket.
   *
   * @param string $sensitiveValueFrequencyUpperBound
   */
  public function setSensitiveValueFrequencyUpperBound($sensitiveValueFrequencyUpperBound)
  {
    $this->sensitiveValueFrequencyUpperBound = $sensitiveValueFrequencyUpperBound;
  }
  /**
   * @return string
   */
  public function getSensitiveValueFrequencyUpperBound()
  {
    return $this->sensitiveValueFrequencyUpperBound;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2LDiversityHistogramBucket::class, 'Google_Service_DLP_GooglePrivacyDlpV2LDiversityHistogramBucket');
