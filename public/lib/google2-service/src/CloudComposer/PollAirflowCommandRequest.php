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

class PollAirflowCommandRequest extends \Google\Model
{
  /**
   * The unique ID of the command execution.
   *
   * @var string
   */
  public $executionId;
  /**
   * Line number from which new logs should be fetched.
   *
   * @var int
   */
  public $nextLineNumber;
  /**
   * The name of the pod where the command is executed.
   *
   * @var string
   */
  public $pod;
  /**
   * The namespace of the pod where the command is executed.
   *
   * @var string
   */
  public $podNamespace;

  /**
   * The unique ID of the command execution.
   *
   * @param string $executionId
   */
  public function setExecutionId($executionId)
  {
    $this->executionId = $executionId;
  }
  /**
   * @return string
   */
  public function getExecutionId()
  {
    return $this->executionId;
  }
  /**
   * Line number from which new logs should be fetched.
   *
   * @param int $nextLineNumber
   */
  public function setNextLineNumber($nextLineNumber)
  {
    $this->nextLineNumber = $nextLineNumber;
  }
  /**
   * @return int
   */
  public function getNextLineNumber()
  {
    return $this->nextLineNumber;
  }
  /**
   * The name of the pod where the command is executed.
   *
   * @param string $pod
   */
  public function setPod($pod)
  {
    $this->pod = $pod;
  }
  /**
   * @return string
   */
  public function getPod()
  {
    return $this->pod;
  }
  /**
   * The namespace of the pod where the command is executed.
   *
   * @param string $podNamespace
   */
  public function setPodNamespace($podNamespace)
  {
    $this->podNamespace = $podNamespace;
  }
  /**
   * @return string
   */
  public function getPodNamespace()
  {
    return $this->podNamespace;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PollAirflowCommandRequest::class, 'Google_Service_CloudComposer_PollAirflowCommandRequest');
