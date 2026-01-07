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

namespace Google\Service\WorkflowExecutions;

class StackTraceElement extends \Google\Model
{
  protected $positionType = Position::class;
  protected $positionDataType = '';
  /**
   * The routine where the error occurred.
   *
   * @var string
   */
  public $routine;
  /**
   * The step the error occurred at.
   *
   * @var string
   */
  public $step;

  /**
   * The source position information of the stack trace element.
   *
   * @param Position $position
   */
  public function setPosition(Position $position)
  {
    $this->position = $position;
  }
  /**
   * @return Position
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * The routine where the error occurred.
   *
   * @param string $routine
   */
  public function setRoutine($routine)
  {
    $this->routine = $routine;
  }
  /**
   * @return string
   */
  public function getRoutine()
  {
    return $this->routine;
  }
  /**
   * The step the error occurred at.
   *
   * @param string $step
   */
  public function setStep($step)
  {
    $this->step = $step;
  }
  /**
   * @return string
   */
  public function getStep()
  {
    return $this->step;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StackTraceElement::class, 'Google_Service_WorkflowExecutions_StackTraceElement');
