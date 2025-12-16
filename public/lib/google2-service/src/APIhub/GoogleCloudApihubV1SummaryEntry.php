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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1SummaryEntry extends \Google\Model
{
  /**
   * Severity unspecified.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Severity error.
   */
  public const SEVERITY_SEVERITY_ERROR = 'SEVERITY_ERROR';
  /**
   * Severity warning.
   */
  public const SEVERITY_SEVERITY_WARNING = 'SEVERITY_WARNING';
  /**
   * Severity info.
   */
  public const SEVERITY_SEVERITY_INFO = 'SEVERITY_INFO';
  /**
   * Severity hint.
   */
  public const SEVERITY_SEVERITY_HINT = 'SEVERITY_HINT';
  /**
   * Required. Count of issues with the given severity.
   *
   * @var int
   */
  public $count;
  /**
   * Required. Severity of the issue.
   *
   * @var string
   */
  public $severity;

  /**
   * Required. Count of issues with the given severity.
   *
   * @param int $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return int
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * Required. Severity of the issue.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, SEVERITY_ERROR, SEVERITY_WARNING,
   * SEVERITY_INFO, SEVERITY_HINT
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1SummaryEntry::class, 'Google_Service_APIhub_GoogleCloudApihubV1SummaryEntry');
