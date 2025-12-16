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

namespace Google\Service\CloudComposer;

class ExitInfo extends \Google\Model
{
  /**
   * Error message. Empty if there was no error.
   *
   * @var string
   */
  public $error;
  /**
   * The exit code from the command execution.
   *
   * @var int
   */
  public $exitCode;

  /**
   * Error message. Empty if there was no error.
   *
   * @param string $error
   */
  public function setError($error)
  {
    $this->error = $error;
  }
  /**
   * @return string
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * The exit code from the command execution.
   *
   * @param int $exitCode
   */
  public function setExitCode($exitCode)
  {
    $this->exitCode = $exitCode;
  }
  /**
   * @return int
   */
  public function getExitCode()
  {
    return $this->exitCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExitInfo::class, 'Google_Service_CloudComposer_ExitInfo');
