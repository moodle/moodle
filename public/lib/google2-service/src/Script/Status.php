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

namespace Google\Service\Script;

class Status extends \Google\Collection
{
  protected $collection_key = 'details';
  /**
   * The status code. For this API, this value either: - 10, indicating a
   * `SCRIPT_TIMEOUT` error, - 3, indicating an `INVALID_ARGUMENT` error, or -
   * 1, indicating a `CANCELLED` execution.
   *
   * @var int
   */
  public $code;
  /**
   * An array that contains a single ExecutionError object that provides
   * information about the nature of the error.
   *
   * @var array[]
   */
  public $details;
  /**
   * A developer-facing error message, which is in English. Any user-facing
   * error message is localized and sent in the details field, or localized by
   * the client.
   *
   * @var string
   */
  public $message;

  /**
   * The status code. For this API, this value either: - 10, indicating a
   * `SCRIPT_TIMEOUT` error, - 3, indicating an `INVALID_ARGUMENT` error, or -
   * 1, indicating a `CANCELLED` execution.
   *
   * @param int $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return int
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * An array that contains a single ExecutionError object that provides
   * information about the nature of the error.
   *
   * @param array[] $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return array[]
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * A developer-facing error message, which is in English. Any user-facing
   * error message is localized and sent in the details field, or localized by
   * the client.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Status::class, 'Google_Service_Script_Status');
