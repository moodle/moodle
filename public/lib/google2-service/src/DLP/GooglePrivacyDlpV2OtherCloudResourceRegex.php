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

class GooglePrivacyDlpV2OtherCloudResourceRegex extends \Google\Model
{
  protected $amazonS3BucketRegexType = GooglePrivacyDlpV2AmazonS3BucketRegex::class;
  protected $amazonS3BucketRegexDataType = '';

  /**
   * Regex for Amazon S3 buckets.
   *
   * @param GooglePrivacyDlpV2AmazonS3BucketRegex $amazonS3BucketRegex
   */
  public function setAmazonS3BucketRegex(GooglePrivacyDlpV2AmazonS3BucketRegex $amazonS3BucketRegex)
  {
    $this->amazonS3BucketRegex = $amazonS3BucketRegex;
  }
  /**
   * @return GooglePrivacyDlpV2AmazonS3BucketRegex
   */
  public function getAmazonS3BucketRegex()
  {
    return $this->amazonS3BucketRegex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2OtherCloudResourceRegex::class, 'Google_Service_DLP_GooglePrivacyDlpV2OtherCloudResourceRegex');
