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

namespace Google\Service\BigQueryDataTransfer;

class TransferMessage extends \Google\Model
{
  /**
   * No severity specified.
   */
  public const SEVERITY_MESSAGE_SEVERITY_UNSPECIFIED = 'MESSAGE_SEVERITY_UNSPECIFIED';
  /**
   * Informational message.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Warning message.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Error message.
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Message text.
   *
   * @var string
   */
  public $messageText;
  /**
   * Time when message was logged.
   *
   * @var string
   */
  public $messageTime;
  /**
   * Message severity.
   *
   * @var string
   */
  public $severity;

  /**
   * Message text.
   *
   * @param string $messageText
   */
  public function setMessageText($messageText)
  {
    $this->messageText = $messageText;
  }
  /**
   * @return string
   */
  public function getMessageText()
  {
    return $this->messageText;
  }
  /**
   * Time when message was logged.
   *
   * @param string $messageTime
   */
  public function setMessageTime($messageTime)
  {
    $this->messageTime = $messageTime;
  }
  /**
   * @return string
   */
  public function getMessageTime()
  {
    return $this->messageTime;
  }
  /**
   * Message severity.
   *
   * Accepted values: MESSAGE_SEVERITY_UNSPECIFIED, INFO, WARNING, ERROR
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
class_alias(TransferMessage::class, 'Google_Service_BigQueryDataTransfer_TransferMessage');
