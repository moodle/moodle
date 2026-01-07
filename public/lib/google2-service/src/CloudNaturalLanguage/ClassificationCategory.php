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

class ClassificationCategory extends \Google\Model
{
  /**
   * The classifier's confidence of the category. Number represents how certain
   * the classifier is that this category represents the given text.
   *
   * @var float
   */
  public $confidence;
  /**
   * The name of the category representing the document.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The classifier's severity of the category. This is only present
   * when the ModerateTextRequest.ModelVersion is set to MODEL_VERSION_2, and
   * the corresponding category has a severity score.
   *
   * @var float
   */
  public $severity;

  /**
   * The classifier's confidence of the category. Number represents how certain
   * the classifier is that this category represents the given text.
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
   * The name of the category representing the document.
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
   * Optional. The classifier's severity of the category. This is only present
   * when the ModerateTextRequest.ModelVersion is set to MODEL_VERSION_2, and
   * the corresponding category has a severity score.
   *
   * @param float $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return float
   */
  public function getSeverity()
  {
    return $this->severity;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ClassificationCategory::class, 'Google_Service_CloudNaturalLanguage_ClassificationCategory');
