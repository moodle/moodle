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

class Status extends \Google\Collection
{
  protected $collection_key = 'currentSteps';
  protected $currentStepsType = Step::class;
  protected $currentStepsDataType = 'array';

  /**
   * A list of currently executing or last executed step names for the workflow
   * execution currently running. If the workflow has succeeded or failed, this
   * is the last attempted or executed step. Presently, if the current step is
   * inside a subworkflow, the list only includes that step. In the future, the
   * list will contain items for each step in the call stack, starting with the
   * outermost step in the `main` subworkflow, and ending with the most deeply
   * nested step.
   *
   * @param Step[] $currentSteps
   */
  public function setCurrentSteps($currentSteps)
  {
    $this->currentSteps = $currentSteps;
  }
  /**
   * @return Step[]
   */
  public function getCurrentSteps()
  {
    return $this->currentSteps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Status::class, 'Google_Service_WorkflowExecutions_Status');
