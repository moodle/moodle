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

class GoogleCloudAiplatformV1TensorboardTensor extends \Google\Model
{
  /**
   * Required. Serialized form of https://github.com/tensorflow/tensorflow/blob/
   * master/tensorflow/core/framework/tensor.proto
   *
   * @var string
   */
  public $value;
  /**
   * Optional. Version number of TensorProto used to serialize value.
   *
   * @var int
   */
  public $versionNumber;

  /**
   * Required. Serialized form of https://github.com/tensorflow/tensorflow/blob/
   * master/tensorflow/core/framework/tensor.proto
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
  /**
   * Optional. Version number of TensorProto used to serialize value.
   *
   * @param int $versionNumber
   */
  public function setVersionNumber($versionNumber)
  {
    $this->versionNumber = $versionNumber;
  }
  /**
   * @return int
   */
  public function getVersionNumber()
  {
    return $this->versionNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1TensorboardTensor::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1TensorboardTensor');
