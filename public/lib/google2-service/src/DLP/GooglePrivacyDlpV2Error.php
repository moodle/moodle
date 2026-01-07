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

class GooglePrivacyDlpV2Error extends \Google\Collection
{
  /**
   * Unused.
   */
  public const EXTRA_INFO_ERROR_INFO_UNSPECIFIED = 'ERROR_INFO_UNSPECIFIED';
  /**
   * Image scan is not available in the region.
   */
  public const EXTRA_INFO_IMAGE_SCAN_UNAVAILABLE_IN_REGION = 'IMAGE_SCAN_UNAVAILABLE_IN_REGION';
  /**
   * File store cluster is not supported for profile generation.
   */
  public const EXTRA_INFO_FILE_STORE_CLUSTER_UNSUPPORTED = 'FILE_STORE_CLUSTER_UNSUPPORTED';
  protected $collection_key = 'timestamps';
  protected $detailsType = GoogleRpcStatus::class;
  protected $detailsDataType = '';
  /**
   * Additional information about the error.
   *
   * @var string
   */
  public $extraInfo;
  /**
   * The times the error occurred. List includes the oldest timestamp and the
   * last 9 timestamps.
   *
   * @var string[]
   */
  public $timestamps;

  /**
   * Detailed error codes and messages.
   *
   * @param GoogleRpcStatus $details
   */
  public function setDetails(GoogleRpcStatus $details)
  {
    $this->details = $details;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Additional information about the error.
   *
   * Accepted values: ERROR_INFO_UNSPECIFIED, IMAGE_SCAN_UNAVAILABLE_IN_REGION,
   * FILE_STORE_CLUSTER_UNSUPPORTED
   *
   * @param self::EXTRA_INFO_* $extraInfo
   */
  public function setExtraInfo($extraInfo)
  {
    $this->extraInfo = $extraInfo;
  }
  /**
   * @return self::EXTRA_INFO_*
   */
  public function getExtraInfo()
  {
    return $this->extraInfo;
  }
  /**
   * The times the error occurred. List includes the oldest timestamp and the
   * last 9 timestamps.
   *
   * @param string[] $timestamps
   */
  public function setTimestamps($timestamps)
  {
    $this->timestamps = $timestamps;
  }
  /**
   * @return string[]
   */
  public function getTimestamps()
  {
    return $this->timestamps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Error::class, 'Google_Service_DLP_GooglePrivacyDlpV2Error');
