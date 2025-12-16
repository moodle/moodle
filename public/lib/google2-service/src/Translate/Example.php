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

namespace Google\Service\Translate;

class Example extends \Google\Model
{
  /**
   * Output only. The resource name of the example, in form of
   * `projects/{project-number-or-
   * id}/locations/{location_id}/datasets/{dataset_id}/examples/{example_id}`
   *
   * @var string
   */
  public $name;
  /**
   * Sentence in source language.
   *
   * @var string
   */
  public $sourceText;
  /**
   * Sentence in target language.
   *
   * @var string
   */
  public $targetText;
  /**
   * Output only. Usage of the sentence pair. Options are TRAIN|VALIDATION|TEST.
   *
   * @var string
   */
  public $usage;

  /**
   * Output only. The resource name of the example, in form of
   * `projects/{project-number-or-
   * id}/locations/{location_id}/datasets/{dataset_id}/examples/{example_id}`
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
   * Sentence in source language.
   *
   * @param string $sourceText
   */
  public function setSourceText($sourceText)
  {
    $this->sourceText = $sourceText;
  }
  /**
   * @return string
   */
  public function getSourceText()
  {
    return $this->sourceText;
  }
  /**
   * Sentence in target language.
   *
   * @param string $targetText
   */
  public function setTargetText($targetText)
  {
    $this->targetText = $targetText;
  }
  /**
   * @return string
   */
  public function getTargetText()
  {
    return $this->targetText;
  }
  /**
   * Output only. Usage of the sentence pair. Options are TRAIN|VALIDATION|TEST.
   *
   * @param string $usage
   */
  public function setUsage($usage)
  {
    $this->usage = $usage;
  }
  /**
   * @return string
   */
  public function getUsage()
  {
    return $this->usage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Example::class, 'Google_Service_Translate_Example');
