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

namespace Google\Service\Directory;

class AuxiliaryMessage extends \Google\Model
{
  /**
   * Message type unspecified.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Message of severity: info.
   */
  public const SEVERITY_SEVERITY_INFO = 'SEVERITY_INFO';
  /**
   * Message of severity: warning.
   */
  public const SEVERITY_SEVERITY_WARNING = 'SEVERITY_WARNING';
  /**
   * Message of severity: error.
   */
  public const SEVERITY_SEVERITY_ERROR = 'SEVERITY_ERROR';
  /**
   * Human readable message in English. Example: "Given printer is invalid or no
   * longer supported."
   *
   * @var string
   */
  public $auxiliaryMessage;
  /**
   * Field that this message concerns.
   *
   * @var string
   */
  public $fieldMask;
  /**
   * Message severity
   *
   * @var string
   */
  public $severity;

  /**
   * Human readable message in English. Example: "Given printer is invalid or no
   * longer supported."
   *
   * @param string $auxiliaryMessage
   */
  public function setAuxiliaryMessage($auxiliaryMessage)
  {
    $this->auxiliaryMessage = $auxiliaryMessage;
  }
  /**
   * @return string
   */
  public function getAuxiliaryMessage()
  {
    return $this->auxiliaryMessage;
  }
  /**
   * Field that this message concerns.
   *
   * @param string $fieldMask
   */
  public function setFieldMask($fieldMask)
  {
    $this->fieldMask = $fieldMask;
  }
  /**
   * @return string
   */
  public function getFieldMask()
  {
    return $this->fieldMask;
  }
  /**
   * Message severity
   *
   * Accepted values: SEVERITY_UNSPECIFIED, SEVERITY_INFO, SEVERITY_WARNING,
   * SEVERITY_ERROR
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
class_alias(AuxiliaryMessage::class, 'Google_Service_Directory_AuxiliaryMessage');
