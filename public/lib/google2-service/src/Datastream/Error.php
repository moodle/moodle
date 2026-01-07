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

namespace Google\Service\Datastream;

class Error extends \Google\Model
{
  /**
   * Additional information about the error.
   *
   * @var string[]
   */
  public $details;
  /**
   * The time when the error occurred.
   *
   * @var string
   */
  public $errorTime;
  /**
   * A unique identifier for this specific error, allowing it to be traced
   * throughout the system in logs and API responses.
   *
   * @var string
   */
  public $errorUuid;
  /**
   * A message containing more information about the error that occurred.
   *
   * @var string
   */
  public $message;
  /**
   * A title that explains the reason for the error.
   *
   * @var string
   */
  public $reason;

  /**
   * Additional information about the error.
   *
   * @param string[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * The time when the error occurred.
   *
   * @param string $errorTime
   */
  public function setErrorTime($errorTime)
  {
    $this->errorTime = $errorTime;
  }
  /**
   * @return string
   */
  public function getErrorTime()
  {
    return $this->errorTime;
  }
  /**
   * A unique identifier for this specific error, allowing it to be traced
   * throughout the system in logs and API responses.
   *
   * @param string $errorUuid
   */
  public function setErrorUuid($errorUuid)
  {
    $this->errorUuid = $errorUuid;
  }
  /**
   * @return string
   */
  public function getErrorUuid()
  {
    return $this->errorUuid;
  }
  /**
   * A message containing more information about the error that occurred.
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
   * A title that explains the reason for the error.
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
class_alias(Error::class, 'Google_Service_Datastream_Error');
