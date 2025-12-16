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

class GoogleCloudDiscoveryengineLoggingErrorContext extends \Google\Model
{
  protected $httpRequestType = GoogleCloudDiscoveryengineLoggingHttpRequestContext::class;
  protected $httpRequestDataType = '';
  protected $reportLocationType = GoogleCloudDiscoveryengineLoggingSourceLocation::class;
  protected $reportLocationDataType = '';

  /**
   * The HTTP request which was processed when the error was triggered.
   *
   * @param GoogleCloudDiscoveryengineLoggingHttpRequestContext $httpRequest
   */
  public function setHttpRequest(GoogleCloudDiscoveryengineLoggingHttpRequestContext $httpRequest)
  {
    $this->httpRequest = $httpRequest;
  }
  /**
   * @return GoogleCloudDiscoveryengineLoggingHttpRequestContext
   */
  public function getHttpRequest()
  {
    return $this->httpRequest;
  }
  /**
   * The location in the source code where the decision was made to report the
   * error, usually the place where it was logged.
   *
   * @param GoogleCloudDiscoveryengineLoggingSourceLocation $reportLocation
   */
  public function setReportLocation(GoogleCloudDiscoveryengineLoggingSourceLocation $reportLocation)
  {
    $this->reportLocation = $reportLocation;
  }
  /**
   * @return GoogleCloudDiscoveryengineLoggingSourceLocation
   */
  public function getReportLocation()
  {
    return $this->reportLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineLoggingErrorContext::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineLoggingErrorContext');
