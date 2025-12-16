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

namespace Google\Service\CloudVideoIntelligence;

class GoogleCloudVideointelligenceV1TextDetectionConfig extends \Google\Collection
{
  protected $collection_key = 'languageHints';
  /**
   * Language hint can be specified if the language to be detected is known a
   * priori. It can increase the accuracy of the detection. Language hint must
   * be language code in BCP-47 format. Automatic language detection is
   * performed if no hint is provided.
   *
   * @var string[]
   */
  public $languageHints;
  /**
   * Model to use for text detection. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest".
   *
   * @var string
   */
  public $model;

  /**
   * Language hint can be specified if the language to be detected is known a
   * priori. It can increase the accuracy of the detection. Language hint must
   * be language code in BCP-47 format. Automatic language detection is
   * performed if no hint is provided.
   *
   * @param string[] $languageHints
   */
  public function setLanguageHints($languageHints)
  {
    $this->languageHints = $languageHints;
  }
  /**
   * @return string[]
   */
  public function getLanguageHints()
  {
    return $this->languageHints;
  }
  /**
   * Model to use for text detection. Supported values: "builtin/stable" (the
   * default if unset) and "builtin/latest".
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1TextDetectionConfig::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1TextDetectionConfig');
