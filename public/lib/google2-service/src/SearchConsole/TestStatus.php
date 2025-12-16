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

namespace Google\Service\SearchConsole;

class TestStatus extends \Google\Model
{
  /**
   * Internal error when running this test. Please try running the test again.
   */
  public const STATUS_TEST_STATUS_UNSPECIFIED = 'TEST_STATUS_UNSPECIFIED';
  /**
   * Inspection has completed without errors.
   */
  public const STATUS_COMPLETE = 'COMPLETE';
  /**
   * Inspection terminated in an error state. This indicates a problem in
   * Google's infrastructure, not a user error. Please try again later.
   */
  public const STATUS_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * Google can not access the URL because of a user error such as a robots.txt
   * blockage, a 403 or 500 code etc. Please make sure that the URL provided is
   * accessible by Googlebot and is not password protected.
   */
  public const STATUS_PAGE_UNREACHABLE = 'PAGE_UNREACHABLE';
  /**
   * Error details if applicable.
   *
   * @var string
   */
  public $details;
  /**
   * Status of the test.
   *
   * @var string
   */
  public $status;

  /**
   * Error details if applicable.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Status of the test.
   *
   * Accepted values: TEST_STATUS_UNSPECIFIED, COMPLETE, INTERNAL_ERROR,
   * PAGE_UNREACHABLE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TestStatus::class, 'Google_Service_SearchConsole_TestStatus');
