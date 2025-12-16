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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailLoggingErrorContext extends \Google\Model
{
  protected $httpRequestType = GoogleCloudRetailLoggingHttpRequestContext::class;
  protected $httpRequestDataType = '';
  protected $reportLocationType = GoogleCloudRetailLoggingSourceLocation::class;
  protected $reportLocationDataType = '';

  /**
   * The HTTP request which was processed when the error was triggered.
   *
   * @param GoogleCloudRetailLoggingHttpRequestContext $httpRequest
   */
  public function setHttpRequest(GoogleCloudRetailLoggingHttpRequestContext $httpRequest)
  {
    $this->httpRequest = $httpRequest;
  }
  /**
   * @return GoogleCloudRetailLoggingHttpRequestContext
   */
  public function getHttpRequest()
  {
    return $this->httpRequest;
  }
  /**
   * The location in the source code where the decision was made to report the
   * error, usually the place where it was logged.
   *
   * @param GoogleCloudRetailLoggingSourceLocation $reportLocation
   */
  public function setReportLocation(GoogleCloudRetailLoggingSourceLocation $reportLocation)
  {
    $this->reportLocation = $reportLocation;
  }
  /**
   * @return GoogleCloudRetailLoggingSourceLocation
   */
  public function getReportLocation()
  {
    return $this->reportLocation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailLoggingErrorContext::class, 'Google_Service_CloudRetail_GoogleCloudRetailLoggingErrorContext');
