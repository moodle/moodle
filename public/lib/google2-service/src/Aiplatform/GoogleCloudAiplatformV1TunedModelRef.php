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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1TunedModelRef extends \Google\Model
{
  /**
   * Support migration from tuning job list page, from bison model to gemini
   * model.
   *
   * @var string
   */
  public $pipelineJob;
  /**
   * Support migration from model registry.
   *
   * @var string
   */
  public $tunedModel;
  /**
   * Support migration from tuning job list page, from gemini-1.0-pro-002 to 1.5
   * and above.
   *
   * @var string
   */
  public $tuningJob;

  /**
   * Support migration from tuning job list page, from bison model to gemini
   * model.
   *
   * @param string $pipelineJob
   */
  public function setPipelineJob($pipelineJob)
  {
    $this->pipelineJob = $pipelineJob;
  }
  /**
   * @return string
   */
  public function getPipelineJob()
  {
    return $this->pipelineJob;
  }
  /**
   * Support migration from model registry.
   *
   * @param string $tunedModel
   */
  public function setTunedModel($tunedModel)
  {
    $this->tunedModel = $tunedModel;
  }
  /**
   * @return string
   */
  public function getTunedModel()
  {
    return $this->tunedModel;
  }
  /**
   * Support migration from tuning job list page, from gemini-1.0-pro-002 to 1.5
   * and above.
   *
   * @param string $tuningJob
   */
  public function setTuningJob($tuningJob)
  {
    $this->tuningJob = $tuningJob;
  }
  /**
   * @return string
   */
  public function getTuningJob()
  {
    return $this->tuningJob;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TunedModelRef::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TunedModelRef');
