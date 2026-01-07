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

class Error extends \Google\Model
{
  /**
   * Domain, or broad category, of the error.
   *
   * @var string
   */
  public $domain;
  /**
   * Specific reason for the error. Some of the possible values are: -
   * "groupTooBig" - The group of users requested is too large for a single
   * query.  - "tooManyCalendarsRequested" - The number of calendars requested
   * is too large for a single query.  - "notFound" - The requested resource was
   * not found.  - "internalError" - The API service has encountered an internal
   * error.  Additional error types may be added in the future, so clients
   * should gracefully handle additional error statuses not included in this
   * list.
   *
   * @var string
   */
  public $reason;

  /**
   * Domain, or broad category, of the error.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Specific reason for the error. Some of the possible values are: -
   * "groupTooBig" - The group of users requested is too large for a single
   * query.  - "tooManyCalendarsRequested" - The number of calendars requested
   * is too large for a single query.  - "notFound" - The requested resource was
   * not found.  - "internalError" - The API service has encountered an internal
   * error.  Additional error types may be added in the future, so clients
   * should gracefully handle additional error statuses not included in this
   * list.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Error::class, 'Google_Service_Calendar_Error');
