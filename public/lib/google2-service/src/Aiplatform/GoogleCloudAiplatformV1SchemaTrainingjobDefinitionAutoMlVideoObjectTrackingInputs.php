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

class GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlVideoObjectTrackingInputs extends \Google\Model
{
  /**
   * Should not be set.
   */
  public const MODEL_TYPE_MODEL_TYPE_UNSPECIFIED = 'MODEL_TYPE_UNSPECIFIED';
  /**
   * A model best tailored to be used within Google Cloud, and which c annot be
   * exported. Default.
   */
  public const MODEL_TYPE_CLOUD = 'CLOUD';
  /**
   * A model that, in addition to being available within Google Cloud, can also
   * be exported (see ModelService.ExportModel) as a TensorFlow or TensorFlow
   * Lite model and used on a mobile or edge device afterwards.
   */
  public const MODEL_TYPE_MOBILE_VERSATILE_1 = 'MOBILE_VERSATILE_1';
  /**
   * A versatile model that is meant to be exported (see
   * ModelService.ExportModel) and used on a Google Coral device.
   */
  public const MODEL_TYPE_MOBILE_CORAL_VERSATILE_1 = 'MOBILE_CORAL_VERSATILE_1';
  /**
   * A model that trades off quality for low latency, to be exported (see
   * ModelService.ExportModel) and used on a Google Coral device.
   */
  public const MODEL_TYPE_MOBILE_CORAL_LOW_LATENCY_1 = 'MOBILE_CORAL_LOW_LATENCY_1';
  /**
   * A versatile model that is meant to be exported (see
   * ModelService.ExportModel) and used on an NVIDIA Jetson device.
   */
  public const MODEL_TYPE_MOBILE_JETSON_VERSATILE_1 = 'MOBILE_JETSON_VERSATILE_1';
  /**
   * A model that trades off quality for low latency, to be exported (see
   * ModelService.ExportModel) and used on an NVIDIA Jetson device.
   */
  public const MODEL_TYPE_MOBILE_JETSON_LOW_LATENCY_1 = 'MOBILE_JETSON_LOW_LATENCY_1';
  /**
   * @var string
   */
  public $modelType;

  /**
   * @param self::MODEL_TYPE_* $modelType
   */
  public function setModelType($modelType)
  {
    $this->modelType = $modelType;
  }
  /**
   * @return self::MODEL_TYPE_*
   */
  public function getModelType()
  {
    return $this->modelType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlVideoObjectTrackingInputs::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SchemaTrainingjobDefinitionAutoMlVideoObjectTrackingInputs');
