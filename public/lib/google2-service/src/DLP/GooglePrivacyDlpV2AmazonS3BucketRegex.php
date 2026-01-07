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

class GooglePrivacyDlpV2AmazonS3BucketRegex extends \Google\Model
{
  protected $awsAccountRegexType = GooglePrivacyDlpV2AwsAccountRegex::class;
  protected $awsAccountRegexDataType = '';
  /**
   * Optional. Regex to test the bucket name against. If empty, all buckets
   * match.
   *
   * @var string
   */
  public $bucketNameRegex;

  /**
   * The AWS account regex.
   *
   * @param GooglePrivacyDlpV2AwsAccountRegex $awsAccountRegex
   */
  public function setAwsAccountRegex(GooglePrivacyDlpV2AwsAccountRegex $awsAccountRegex)
  {
    $this->awsAccountRegex = $awsAccountRegex;
  }
  /**
   * @return GooglePrivacyDlpV2AwsAccountRegex
   */
  public function getAwsAccountRegex()
  {
    return $this->awsAccountRegex;
  }
  /**
   * Optional. Regex to test the bucket name against. If empty, all buckets
   * match.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2AmazonS3BucketRegex::class, 'Google_Service_DLP_GooglePrivacyDlpV2AmazonS3BucketRegex');
