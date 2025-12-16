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

namespace Google\Service\Dataflow;

class Stack extends \Google\Model
{
  /**
   * The raw stack trace.
   *
   * @var string
   */
  public $stackContent;
  /**
   * With java thread dumps we may get collapsed stacks e.g., N threads in stack
   * "". Instead of having to copy over the same stack trace N times, this int
   * field captures this.
   *
   * @var int
   */
  public $threadCount;
  /**
   * Thread name. For example, "CommitThread-0,10,main"
   *
   * @var string
   */
  public $threadName;
  /**
   * The state of the thread. For example, "WAITING".
   *
   * @var string
   */
  public $threadState;
  /**
   * Timestamp at which the stack was captured.
   *
   * @var string
   */
  public $timestamp;

  /**
   * The raw stack trace.
   *
   * @param string $stackContent
   */
  public function setStackContent($stackContent)
  {
    $this->stackContent = $stackContent;
  }
  /**
   * @return string
   */
  public function getStackContent()
  {
    return $this->stackContent;
  }
  /**
   * With java thread dumps we may get collapsed stacks e.g., N threads in stack
   * "". Instead of having to copy over the same stack trace N times, this int
   * field captures this.
   *
   * @param int $threadCount
   */
  public function setThreadCount($threadCount)
  {
    $this->threadCount = $threadCount;
  }
  /**
   * @return int
   */
  public function getThreadCount()
  {
    return $this->threadCount;
  }
  /**
   * Thread name. For example, "CommitThread-0,10,main"
   *
   * @param string $threadName
   */
  public function setThreadName($threadName)
  {
    $this->threadName = $threadName;
  }
  /**
   * @return string
   */
  public function getThreadName()
  {
    return $this->threadName;
  }
  /**
   * The state of the thread. For example, "WAITING".
   *
   * @param string $threadState
   */
  public function setThreadState($threadState)
  {
    $this->threadState = $threadState;
  }
  /**
   * @return string
   */
  public function getThreadState()
  {
    return $this->threadState;
  }
  /**
   * Timestamp at which the stack was captured.
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Stack::class, 'Google_Service_Dataflow_Stack');
