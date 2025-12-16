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

namespace Google\Service\FirebaseRules;

class Issue extends \Google\Model
{
  /**
   * An unspecified severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Deprecation issue for statements and method that may no longer be supported
   * or maintained.
   */
  public const SEVERITY_DEPRECATION = 'DEPRECATION';
  /**
   * Warnings such as: unused variables.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Errors such as: unmatched curly braces or variable redefinition.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Short error description.
   *
   * @var string
   */
  public $description;
  /**
   * The severity of the issue.
   *
   * @var string
   */
  public $severity;
  protected $sourcePositionType = SourcePosition::class;
  protected $sourcePositionDataType = '';

  /**
   * Short error description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The severity of the issue.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, DEPRECATION, WARNING, ERROR
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
  /**
   * Position of the issue in the `Source`.
   *
   * @param SourcePosition $sourcePosition
   */
  public function setSourcePosition(SourcePosition $sourcePosition)
  {
    $this->sourcePosition = $sourcePosition;
  }
  /**
   * @return SourcePosition
   */
  public function getSourcePosition()
  {
    return $this->sourcePosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Issue::class, 'Google_Service_FirebaseRules_Issue');
