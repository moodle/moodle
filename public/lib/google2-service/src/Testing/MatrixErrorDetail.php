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

namespace Google\Service\Testing;

class MatrixErrorDetail extends \Google\Model
{
  /**
   * Output only. A human-readable message about how the error in the
   * TestMatrix. Expands on the `reason` field with additional details and
   * possible options to fix the issue.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. The reason for the error. This is a constant value in
   * UPPER_SNAKE_CASE that identifies the cause of the error.
   *
   * @var string
   */
  public $reason;

  /**
   * Output only. A human-readable message about how the error in the
   * TestMatrix. Expands on the `reason` field with additional details and
   * possible options to fix the issue.
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
   * Output only. The reason for the error. This is a constant value in
   * UPPER_SNAKE_CASE that identifies the cause of the error.
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
class_alias(MatrixErrorDetail::class, 'Google_Service_Testing_MatrixErrorDetail');
