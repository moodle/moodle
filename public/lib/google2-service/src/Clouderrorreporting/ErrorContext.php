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

namespace Google\Service\Clouderrorreporting;

class ErrorContext extends \Google\Collection
{
  protected $collection_key = 'sourceReferences';
  protected $httpRequestType = HttpRequestContext::class;
  protected $httpRequestDataType = '';
  protected $reportLocationType = SourceLocation::class;
  protected $reportLocationDataType = '';
  protected $sourceReferencesType = SourceReference::class;
  protected $sourceReferencesDataType = 'array';
  /**
   * The user who caused or was affected by the crash. This can be a user ID, an
   * email address, or an arbitrary token that uniquely identifies the user.
   * When sending an error report, leave this field empty if the user was not
   * logged in. In this case the Error Reporting system will use other data,
   * such as remote IP address, to distinguish affected users. See
   * `affected_users_count` in `ErrorGroupStats`.
   *
   * @var string
   */
  public $user;

  /**
   * The HTTP request which was processed when the error was triggered.
   *
   * @param HttpRequestContext $httpRequest
   */
  public function setHttpRequest(HttpRequestContext $httpRequest)
  {
    $this->httpRequest = $httpRequest;
  }
  /**
   * @return HttpRequestContext
   */
  public function getHttpRequest()
  {
    return $this->httpRequest;
  }
  /**
   * The location in the source code where the decision was made to report the
   * error, usually the place where it was logged. For a logged exception this
   * would be the source line where the exception is logged, usually close to
   * the place where it was caught.
   *
   * @param SourceLocation $reportLocation
   */
  public function setReportLocation(SourceLocation $reportLocation)
  {
    $this->reportLocation = $reportLocation;
  }
  /**
   * @return SourceLocation
   */
  public function getReportLocation()
  {
    return $this->reportLocation;
  }
  /**
   * Source code that was used to build the executable which has caused the
   * given error message.
   *
   * @param SourceReference[] $sourceReferences
   */
  public function setSourceReferences($sourceReferences)
  {
    $this->sourceReferences = $sourceReferences;
  }
  /**
   * @return SourceReference[]
   */
  public function getSourceReferences()
  {
    return $this->sourceReferences;
  }
  /**
   * The user who caused or was affected by the crash. This can be a user ID, an
   * email address, or an arbitrary token that uniquely identifies the user.
   * When sending an error report, leave this field empty if the user was not
   * logged in. In this case the Error Reporting system will use other data,
   * such as remote IP address, to distinguish affected users. See
   * `affected_users_count` in `ErrorGroupStats`.
   *
   * @param string $user
   */
  public function setUser($user)
  {
    $this->user = $user;
  }
  /**
   * @return string
   */
  public function getUser()
  {
    return $this->user;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorContext::class, 'Google_Service_Clouderrorreporting_ErrorContext');
