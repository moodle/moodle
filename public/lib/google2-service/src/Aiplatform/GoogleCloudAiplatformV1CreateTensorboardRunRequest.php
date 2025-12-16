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

class GoogleCloudAiplatformV1CreateTensorboardRunRequest extends \Google\Model
{
  /**
   * Required. The resource name of the TensorboardExperiment to create the
   * TensorboardRun in. Format: `projects/{project}/locations/{location}/tensorb
   * oards/{tensorboard}/experiments/{experiment}`
   *
   * @var string
   */
  public $parent;
  protected $tensorboardRunType = GoogleCloudAiplatformV1TensorboardRun::class;
  protected $tensorboardRunDataType = '';
  /**
   * Required. The ID to use for the Tensorboard run, which becomes the final
   * component of the Tensorboard run's resource name. This value should be
   * 1-128 characters, and valid characters are `/a-z-/`.
   *
   * @var string
   */
  public $tensorboardRunId;

  /**
   * Required. The resource name of the TensorboardExperiment to create the
   * TensorboardRun in. Format: `projects/{project}/locations/{location}/tensorb
   * oards/{tensorboard}/experiments/{experiment}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. The TensorboardRun to create.
   *
   * @param GoogleCloudAiplatformV1TensorboardRun $tensorboardRun
   */
  public function setTensorboardRun(GoogleCloudAiplatformV1TensorboardRun $tensorboardRun)
  {
    $this->tensorboardRun = $tensorboardRun;
  }
  /**
   * @return GoogleCloudAiplatformV1TensorboardRun
   */
  public function getTensorboardRun()
  {
    return $this->tensorboardRun;
  }
  /**
   * Required. The ID to use for the Tensorboard run, which becomes the final
   * component of the Tensorboard run's resource name. This value should be
   * 1-128 characters, and valid characters are `/a-z-/`.
   *
   * @param string $tensorboardRunId
   */
  public function setTensorboardRunId($tensorboardRunId)
  {
    $this->tensorboardRunId = $tensorboardRunId;
  }
  /**
   * @return string
   */
  public function getTensorboardRunId()
  {
    return $this->tensorboardRunId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1CreateTensorboardRunRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1CreateTensorboardRunRequest');
