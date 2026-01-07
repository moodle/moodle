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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ErrorsErrorDetails extends \Google\Model
{
  protected $quotaErrorDetailsType = GoogleAdsSearchads360V0ErrorsQuotaErrorDetails::class;
  protected $quotaErrorDetailsDataType = '';
  /**
   * The error code that should have been returned, but wasn't. This is used
   * when the error code is not published in the client specified version.
   *
   * @var string
   */
  public $unpublishedErrorCode;

  /**
   * Details on the quota error, including the scope (account or developer), the
   * rate bucket name and the retry delay.
   *
   * @param GoogleAdsSearchads360V0ErrorsQuotaErrorDetails $quotaErrorDetails
   */
  public function setQuotaErrorDetails(GoogleAdsSearchads360V0ErrorsQuotaErrorDetails $quotaErrorDetails)
  {
    $this->quotaErrorDetails = $quotaErrorDetails;
  }
  /**
   * @return GoogleAdsSearchads360V0ErrorsQuotaErrorDetails
   */
  public function getQuotaErrorDetails()
  {
    return $this->quotaErrorDetails;
  }
  /**
   * The error code that should have been returned, but wasn't. This is used
   * when the error code is not published in the client specified version.
   *
   * @param string $unpublishedErrorCode
   */
  public function setUnpublishedErrorCode($unpublishedErrorCode)
  {
    $this->unpublishedErrorCode = $unpublishedErrorCode;
  }
  /**
   * @return string
   */
  public function getUnpublishedErrorCode()
  {
    return $this->unpublishedErrorCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ErrorsErrorDetails::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ErrorsErrorDetails');
