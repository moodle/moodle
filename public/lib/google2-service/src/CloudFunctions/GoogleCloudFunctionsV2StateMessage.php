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

namespace Google\Service\CloudFunctions;

class GoogleCloudFunctionsV2StateMessage extends \Google\Model
{
  /**
   * Not specified. Invalid severity.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * ERROR-level severity.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * WARNING-level severity.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * INFO-level severity.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * The message.
   *
   * @var string
   */
  public $message;
  /**
   * Severity of the state message.
   *
   * @var string
   */
  public $severity;
  /**
   * One-word CamelCase type of the state message.
   *
   * @var string
   */
  public $type;

  /**
   * The message.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Severity of the state message.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, ERROR, WARNING, INFO
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
   * One-word CamelCase type of the state message.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudFunctionsV2StateMessage::class, 'Google_Service_CloudFunctions_GoogleCloudFunctionsV2StateMessage');
