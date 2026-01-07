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

namespace Google\Service\Dataform;

class CompilationError extends \Google\Model
{
  protected $actionTargetType = Target::class;
  protected $actionTargetDataType = '';
  /**
   * Output only. The error's top level message.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. The path of the file where this error occurred, if available,
   * relative to the project root.
   *
   * @var string
   */
  public $path;
  /**
   * Output only. The error's full stack trace.
   *
   * @var string
   */
  public $stack;

  /**
   * Output only. The identifier of the action where this error occurred, if
   * available.
   *
   * @param Target $actionTarget
   */
  public function setActionTarget(Target $actionTarget)
  {
    $this->actionTarget = $actionTarget;
  }
  /**
   * @return Target
   */
  public function getActionTarget()
  {
    return $this->actionTarget;
  }
  /**
   * Output only. The error's top level message.
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
   * Output only. The path of the file where this error occurred, if available,
   * relative to the project root.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Output only. The error's full stack trace.
   *
   * @param string $stack
   */
  public function setStack($stack)
  {
    $this->stack = $stack;
  }
  /**
   * @return string
   */
  public function getStack()
  {
    return $this->stack;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CompilationError::class, 'Google_Service_Dataform_CompilationError');
