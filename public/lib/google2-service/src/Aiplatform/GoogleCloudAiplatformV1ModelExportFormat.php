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

class GoogleCloudAiplatformV1ModelExportFormat extends \Google\Collection
{
  protected $collection_key = 'exportableContents';
  /**
   * Output only. The content of this Model that may be exported.
   *
   * @var string[]
   */
  public $exportableContents;
  /**
   * Output only. The ID of the export format. The possible format IDs are: *
   * `tflite` Used for Android mobile devices. * `edgetpu-tflite` Used for [Edge
   * TPU](https://cloud.google.com/edge-tpu/) devices. * `tf-saved-model` A
   * tensorflow model in SavedModel format. * `tf-js` A
   * [TensorFlow.js](https://www.tensorflow.org/js) model that can be used in
   * the browser and in Node.js using JavaScript. * `core-ml` Used for iOS
   * mobile devices. * `custom-trained` A Model that was uploaded or trained by
   * custom code. * `genie` A tuned Model Garden model.
   *
   * @var string
   */
  public $id;

  /**
   * Output only. The content of this Model that may be exported.
   *
   * @param string[] $exportableContents
   */
  public function setExportableContents($exportableContents)
  {
    $this->exportableContents = $exportableContents;
  }
  /**
   * @return string[]
   */
  public function getExportableContents()
  {
    return $this->exportableContents;
  }
  /**
   * Output only. The ID of the export format. The possible format IDs are: *
   * `tflite` Used for Android mobile devices. * `edgetpu-tflite` Used for [Edge
   * TPU](https://cloud.google.com/edge-tpu/) devices. * `tf-saved-model` A
   * tensorflow model in SavedModel format. * `tf-js` A
   * [TensorFlow.js](https://www.tensorflow.org/js) model that can be used in
   * the browser and in Node.js using JavaScript. * `core-ml` Used for iOS
   * mobile devices. * `custom-trained` A Model that was uploaded or trained by
   * custom code. * `genie` A tuned Model Garden model.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelExportFormat::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelExportFormat');
