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

class RichResultsIssue extends \Google\Model
{
  /**
   * Unknown severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Warning.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Error.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Rich Results issue type.
   *
   * @var string
   */
  public $issueMessage;
  /**
   * Severity of this issue: WARNING, or ERROR. Items with an issue of status
   * ERROR cannot appear with rich result features in Google Search results.
   *
   * @var string
   */
  public $severity;

  /**
   * Rich Results issue type.
   *
   * @param string $issueMessage
   */
  public function setIssueMessage($issueMessage)
  {
    $this->issueMessage = $issueMessage;
  }
  /**
   * @return string
   */
  public function getIssueMessage()
  {
    return $this->issueMessage;
  }
  /**
   * Severity of this issue: WARNING, or ERROR. Items with an issue of status
   * ERROR cannot appear with rich result features in Google Search results.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, WARNING, ERROR
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
class_alias(RichResultsIssue::class, 'Google_Service_SearchConsole_RichResultsIssue');
