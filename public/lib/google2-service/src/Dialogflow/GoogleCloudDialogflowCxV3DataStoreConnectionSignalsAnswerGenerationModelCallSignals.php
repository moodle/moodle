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

class GoogleCloudDialogflowCxV3DataStoreConnectionSignalsAnswerGenerationModelCallSignals extends \Google\Model
{
  /**
   * Name of the generative model. For example, "gemini-ultra", "gemini-pro",
   * "gemini-1.5-flash" etc. Defaults to "Other" if the model is unknown.
   *
   * @var string
   */
  public $model;
  /**
   * Output of the generative model.
   *
   * @var string
   */
  public $modelOutput;
  /**
   * Prompt as sent to the model.
   *
   * @var string
   */
  public $renderedPrompt;

  /**
   * Name of the generative model. For example, "gemini-ultra", "gemini-pro",
   * "gemini-1.5-flash" etc. Defaults to "Other" if the model is unknown.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * Output of the generative model.
   *
   * @param string $modelOutput
   */
  public function setModelOutput($modelOutput)
  {
    $this->modelOutput = $modelOutput;
  }
  /**
   * @return string
   */
  public function getModelOutput()
  {
    return $this->modelOutput;
  }
  /**
   * Prompt as sent to the model.
   *
   * @param string $renderedPrompt
   */
  public function setRenderedPrompt($renderedPrompt)
  {
    $this->renderedPrompt = $renderedPrompt;
  }
  /**
   * @return string
   */
  public function getRenderedPrompt()
  {
    return $this->renderedPrompt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DataStoreConnectionSignalsAnswerGenerationModelCallSignals::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DataStoreConnectionSignalsAnswerGenerationModelCallSignals');
