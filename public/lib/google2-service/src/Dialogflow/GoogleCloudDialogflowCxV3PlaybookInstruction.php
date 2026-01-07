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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3PlaybookInstruction extends \Google\Collection
{
  protected $collection_key = 'steps';
  /**
   * General guidelines for the playbook. These are unstructured instructions
   * that are not directly part of the goal, e.g. "Always be polite". It's valid
   * for this text to be long and used instead of steps altogether.
   *
   * @var string
   */
  public $guidelines;
  protected $stepsType = GoogleCloudDialogflowCxV3PlaybookStep::class;
  protected $stepsDataType = 'array';

  /**
   * General guidelines for the playbook. These are unstructured instructions
   * that are not directly part of the goal, e.g. "Always be polite". It's valid
   * for this text to be long and used instead of steps altogether.
   *
   * @param string $guidelines
   */
  public function setGuidelines($guidelines)
  {
    $this->guidelines = $guidelines;
  }
  /**
   * @return string
   */
  public function getGuidelines()
  {
    return $this->guidelines;
  }
  /**
   * Ordered list of step by step execution instructions to accomplish target
   * goal.
   *
   * @param GoogleCloudDialogflowCxV3PlaybookStep[] $steps
   */
  public function setSteps($steps)
  {
    $this->steps = $steps;
  }
  /**
   * @return GoogleCloudDialogflowCxV3PlaybookStep[]
   */
  public function getSteps()
  {
    return $this->steps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3PlaybookInstruction::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3PlaybookInstruction');
