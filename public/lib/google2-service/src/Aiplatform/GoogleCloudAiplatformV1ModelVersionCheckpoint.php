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

class GoogleCloudAiplatformV1ModelVersionCheckpoint extends \Google\Model
{
  /**
   * The ID of the checkpoint.
   *
   * @var string
   */
  public $checkpointId;
  /**
   * The epoch of the checkpoint.
   *
   * @var string
   */
  public $epoch;
  /**
   * The step of the checkpoint.
   *
   * @var string
   */
  public $step;

  /**
   * The ID of the checkpoint.
   *
   * @param string $checkpointId
   */
  public function setCheckpointId($checkpointId)
  {
    $this->checkpointId = $checkpointId;
  }
  /**
   * @return string
   */
  public function getCheckpointId()
  {
    return $this->checkpointId;
  }
  /**
   * The epoch of the checkpoint.
   *
   * @param string $epoch
   */
  public function setEpoch($epoch)
  {
    $this->epoch = $epoch;
  }
  /**
   * @return string
   */
  public function getEpoch()
  {
    return $this->epoch;
  }
  /**
   * The step of the checkpoint.
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
class_alias(GoogleCloudAiplatformV1ModelVersionCheckpoint::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelVersionCheckpoint');
