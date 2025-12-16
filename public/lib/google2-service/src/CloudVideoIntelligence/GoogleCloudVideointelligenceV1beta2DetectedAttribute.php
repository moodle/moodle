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

class GoogleCloudVideointelligenceV1beta2DetectedAttribute extends \Google\Model
{
  /**
   * Detected attribute confidence. Range [0, 1].
   *
   * @var float
   */
  public $confidence;
  /**
   * The name of the attribute, for example, glasses, dark_glasses, mouth_open.
   * A full list of supported type names will be provided in the document.
   *
   * @var string
   */
  public $name;
  /**
   * Text value of the detection result. For example, the value for "HairColor"
   * can be "black", "blonde", etc.
   *
   * @var string
   */
  public $value;

  /**
   * Detected attribute confidence. Range [0, 1].
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * The name of the attribute, for example, glasses, dark_glasses, mouth_open.
   * A full list of supported type names will be provided in the document.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Text value of the detection result. For example, the value for "HairColor"
   * can be "black", "blonde", etc.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudVideointelligenceV1beta2DetectedAttribute::class, 'Google_Service_CloudVideoIntelligence_GoogleCloudVideointelligenceV1beta2DetectedAttribute');
