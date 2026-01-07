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

namespace Google\Service\ChecksService;

class GoogleChecksReportV1alphaDataMonitoringSdkResult extends \Google\Model
{
  protected $metadataType = GoogleChecksReportV1alphaDataMonitoringResultMetadata::class;
  protected $metadataDataType = '';
  protected $sdkType = GoogleChecksReportV1alphaSdk::class;
  protected $sdkDataType = '';

  /**
   * Metadata about the result.
   *
   * @param GoogleChecksReportV1alphaDataMonitoringResultMetadata $metadata
   */
  public function setMetadata(GoogleChecksReportV1alphaDataMonitoringResultMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return GoogleChecksReportV1alphaDataMonitoringResultMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The SDK that was found in your app.
   *
   * @param GoogleChecksReportV1alphaSdk $sdk
   */
  public function setSdk(GoogleChecksReportV1alphaSdk $sdk)
  {
    $this->sdk = $sdk;
  }
  /**
   * @return GoogleChecksReportV1alphaSdk
   */
  public function getSdk()
  {
    return $this->sdk;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChecksReportV1alphaDataMonitoringSdkResult::class, 'Google_Service_ChecksService_GoogleChecksReportV1alphaDataMonitoringSdkResult');
