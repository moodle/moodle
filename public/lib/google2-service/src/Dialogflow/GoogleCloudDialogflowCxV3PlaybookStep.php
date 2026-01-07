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

class GoogleCloudDialogflowCxV3PlaybookStep extends \Google\Collection
{
  protected $collection_key = 'steps';
  protected $stepsType = GoogleCloudDialogflowCxV3PlaybookStep::class;
  protected $stepsDataType = 'array';
  /**
   * Step instruction in text format.
   *
   * @var string
   */
  public $text;

  /**
   * Sub-processing needed to execute the current step.
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
  /**
   * Step instruction in text format.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3PlaybookStep::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3PlaybookStep');
