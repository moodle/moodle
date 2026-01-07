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

namespace Google\Service\DiscoveryEngine;

class GoogleApiDistributionBucketOptions extends \Google\Model
{
  protected $explicitBucketsType = GoogleApiDistributionBucketOptionsExplicit::class;
  protected $explicitBucketsDataType = '';
  protected $exponentialBucketsType = GoogleApiDistributionBucketOptionsExponential::class;
  protected $exponentialBucketsDataType = '';
  protected $linearBucketsType = GoogleApiDistributionBucketOptionsLinear::class;
  protected $linearBucketsDataType = '';

  /**
   * The explicit buckets.
   *
   * @param GoogleApiDistributionBucketOptionsExplicit $explicitBuckets
   */
  public function setExplicitBuckets(GoogleApiDistributionBucketOptionsExplicit $explicitBuckets)
  {
    $this->explicitBuckets = $explicitBuckets;
  }
  /**
   * @return GoogleApiDistributionBucketOptionsExplicit
   */
  public function getExplicitBuckets()
  {
    return $this->explicitBuckets;
  }
  /**
   * The exponential buckets.
   *
   * @param GoogleApiDistributionBucketOptionsExponential $exponentialBuckets
   */
  public function setExponentialBuckets(GoogleApiDistributionBucketOptionsExponential $exponentialBuckets)
  {
    $this->exponentialBuckets = $exponentialBuckets;
  }
  /**
   * @return GoogleApiDistributionBucketOptionsExponential
   */
  public function getExponentialBuckets()
  {
    return $this->exponentialBuckets;
  }
  /**
   * The linear bucket.
   *
   * @param GoogleApiDistributionBucketOptionsLinear $linearBuckets
   */
  public function setLinearBuckets(GoogleApiDistributionBucketOptionsLinear $linearBuckets)
  {
    $this->linearBuckets = $linearBuckets;
  }
  /**
   * @return GoogleApiDistributionBucketOptionsLinear
   */
  public function getLinearBuckets()
  {
    return $this->linearBuckets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleApiDistributionBucketOptions::class, 'Google_Service_DiscoveryEngine_GoogleApiDistributionBucketOptions');
