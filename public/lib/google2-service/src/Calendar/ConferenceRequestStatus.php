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

namespace Google\Service\Calendar;

class ConferenceRequestStatus extends \Google\Model
{
  /**
   * The current status of the conference create request. Read-only. The
   * possible values are:   - "pending": the conference create request is still
   * being processed. - "success": the conference create request succeeded, the
   * entry points are populated. - "failure": the conference create request
   * failed, there are no entry points.
   *
   * @var string
   */
  public $statusCode;

  /**
   * The current status of the conference create request. Read-only. The
   * possible values are:   - "pending": the conference create request is still
   * being processed. - "success": the conference create request succeeded, the
   * entry points are populated. - "failure": the conference create request
   * failed, there are no entry points.
   *
   * @param string $statusCode
   */
  public function setStatusCode($statusCode)
  {
    $this->statusCode = $statusCode;
  }
  /**
   * @return string
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConferenceRequestStatus::class, 'Google_Service_Calendar_ConferenceRequestStatus');
