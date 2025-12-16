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

class Operation extends \Google\Model
{
  /**
   * This field indicates whether the script execution has completed. A
   * completed execution has a populated `response` field containing the
   * ExecutionResponse from function that was executed.
   *
   * @var bool
   */
  public $done;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * If the script function returns successfully, this field contains an
   * ExecutionResponse object with the function's return value.
   *
   * @var array[]
   */
  public $response;

  /**
   * This field indicates whether the script execution has completed. A
   * completed execution has a populated `response` field containing the
   * ExecutionResponse from function that was executed.
   *
   * @param bool $done
   */
  public function setDone($done)
  {
    $this->done = $done;
  }
  /**
   * @return bool
   */
  public function getDone()
  {
    return $this->done;
  }
  /**
   * If a `run` call succeeds but the script function (or Apps Script itself)
   * throws an exception, this field contains a Status object. The `Status`
   * object's `details` field contains an array with a single ExecutionError
   * object that provides information about the nature of the error.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * If the script function returns successfully, this field contains an
   * ExecutionResponse object with the function's return value.
   *
   * @param array[] $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
  }
  /**
   * @return array[]
   */
  public function getResponse()
  {
    return $this->response;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Operation::class, 'Google_Service_Script_Operation');
