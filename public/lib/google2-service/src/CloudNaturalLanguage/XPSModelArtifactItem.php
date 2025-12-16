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

namespace Google\Service\CloudNaturalLanguage;

class XPSModelArtifactItem extends \Google\Model
{
  /**
   * Should not be used.
   */
  public const ARTIFACT_FORMAT_ARTIFACT_FORMAT_UNSPECIFIED = 'ARTIFACT_FORMAT_UNSPECIFIED';
  /**
   * The Tensorflow checkpoints. See
   * https://www.tensorflow.org/guide/checkpoint.
   */
  public const ARTIFACT_FORMAT_TF_CHECKPOINT = 'TF_CHECKPOINT';
  /**
   * The Tensorflow SavedModel binary.
   */
  public const ARTIFACT_FORMAT_TF_SAVED_MODEL = 'TF_SAVED_MODEL';
  /**
   * Model artifact in generic TensorFlow Lite (.tflite) format. See
   * https://www.tensorflow.org/lite.
   */
  public const ARTIFACT_FORMAT_TF_LITE = 'TF_LITE';
  /**
   * Used for [Edge TPU](https://cloud.google.com/edge-tpu/) devices.
   */
  public const ARTIFACT_FORMAT_EDGE_TPU_TF_LITE = 'EDGE_TPU_TF_LITE';
  /**
   * A [TensorFlow.js](https://www.tensorflow.org/js) model that can be used in
   * the browser and in Node.js using JavaScript.
   */
  public const ARTIFACT_FORMAT_TF_JS = 'TF_JS';
  /**
   * Used for iOS mobile devices in (.mlmodel) format. See
   * https://developer.apple.com/documentation/coreml
   */
  public const ARTIFACT_FORMAT_CORE_ML = 'CORE_ML';
  /**
   * The model artifact format.
   *
   * @var string
   */
  public $artifactFormat;
  /**
   * The Google Cloud Storage URI that stores the model binary files.
   *
   * @var string
   */
  public $gcsUri;

  /**
   * The model artifact format.
   *
   * Accepted values: ARTIFACT_FORMAT_UNSPECIFIED, TF_CHECKPOINT,
   * TF_SAVED_MODEL, TF_LITE, EDGE_TPU_TF_LITE, TF_JS, CORE_ML
   *
   * @param self::ARTIFACT_FORMAT_* $artifactFormat
   */
  public function setArtifactFormat($artifactFormat)
  {
    $this->artifactFormat = $artifactFormat;
  }
  /**
   * @return self::ARTIFACT_FORMAT_*
   */
  public function getArtifactFormat()
  {
    return $this->artifactFormat;
  }
  /**
   * The Google Cloud Storage URI that stores the model binary files.
   *
   * @param string $gcsUri
   */
  public function setGcsUri($gcsUri)
  {
    $this->gcsUri = $gcsUri;
  }
  /**
   * @return string
   */
  public function getGcsUri()
  {
    return $this->gcsUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSModelArtifactItem::class, 'Google_Service_CloudNaturalLanguage_XPSModelArtifactItem');
