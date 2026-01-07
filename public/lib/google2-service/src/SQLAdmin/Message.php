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

namespace Google\Service\SQLAdmin;

class Message extends \Google\Model
{
  /**
   * The full message string. For PostgreSQL, this is a formatted string that
   * may include severity, code, and the notice/warning message. For MySQL, this
   * contains the warning message.
   *
   * @var string
   */
  public $message;
  /**
   * The severity of the message (e.g., "NOTICE" for PostgreSQL, "WARNING" for
   * MySQL).
   *
   * @var string
   */
  public $severity;

  /**
   * The full message string. For PostgreSQL, this is a formatted string that
   * may include severity, code, and the notice/warning message. For MySQL, this
   * contains the warning message.
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
   * The severity of the message (e.g., "NOTICE" for PostgreSQL, "WARNING" for
   * MySQL).
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Message::class, 'Google_Service_SQLAdmin_Message');
