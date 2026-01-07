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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2SafetySetting extends \Google\Model
{
  /**
   * The harm category is unspecified.
   */
  public const CATEGORY_HARM_CATEGORY_UNSPECIFIED = 'HARM_CATEGORY_UNSPECIFIED';
  /**
   * The harm category is hate speech.
   */
  public const CATEGORY_HARM_CATEGORY_HATE_SPEECH = 'HARM_CATEGORY_HATE_SPEECH';
  /**
   * The harm category is dangerous content.
   */
  public const CATEGORY_HARM_CATEGORY_DANGEROUS_CONTENT = 'HARM_CATEGORY_DANGEROUS_CONTENT';
  /**
   * The harm category is harassment.
   */
  public const CATEGORY_HARM_CATEGORY_HARASSMENT = 'HARM_CATEGORY_HARASSMENT';
  /**
   * The harm category is sexually explicit content.
   */
  public const CATEGORY_HARM_CATEGORY_SEXUALLY_EXPLICIT = 'HARM_CATEGORY_SEXUALLY_EXPLICIT';
  /**
   * The harm category is civic integrity.
   */
  public const CATEGORY_HARM_CATEGORY_CIVIC_INTEGRITY = 'HARM_CATEGORY_CIVIC_INTEGRITY';
  /**
   * The harm block method is unspecified.
   */
  public const METHOD_HARM_BLOCK_METHOD_UNSPECIFIED = 'HARM_BLOCK_METHOD_UNSPECIFIED';
  /**
   * The harm block method uses both probability and severity scores.
   */
  public const METHOD_SEVERITY = 'SEVERITY';
  /**
   * The harm block method uses the probability score.
   */
  public const METHOD_PROBABILITY = 'PROBABILITY';
  /**
   * Unspecified harm block threshold.
   */
  public const THRESHOLD_HARM_BLOCK_THRESHOLD_UNSPECIFIED = 'HARM_BLOCK_THRESHOLD_UNSPECIFIED';
  /**
   * Block low threshold and above (i.e. block more).
   */
  public const THRESHOLD_BLOCK_LOW_AND_ABOVE = 'BLOCK_LOW_AND_ABOVE';
  /**
   * Block medium threshold and above.
   */
  public const THRESHOLD_BLOCK_MEDIUM_AND_ABOVE = 'BLOCK_MEDIUM_AND_ABOVE';
  /**
   * Block only high threshold (i.e. block less).
   */
  public const THRESHOLD_BLOCK_ONLY_HIGH = 'BLOCK_ONLY_HIGH';
  /**
   * Block none.
   */
  public const THRESHOLD_BLOCK_NONE = 'BLOCK_NONE';
  /**
   * Turn off the safety filter.
   */
  public const THRESHOLD_OFF = 'OFF';
  /**
   * Harm category.
   *
   * @var string
   */
  public $category;
  /**
   * Optional. Specify if the threshold is used for probability or severity
   * score. If not specified, the threshold is used for probability score.
   *
   * @var string
   */
  public $method;
  /**
   * The harm block threshold.
   *
   * @var string
   */
  public $threshold;

  /**
   * Harm category.
   *
   * Accepted values: HARM_CATEGORY_UNSPECIFIED, HARM_CATEGORY_HATE_SPEECH,
   * HARM_CATEGORY_DANGEROUS_CONTENT, HARM_CATEGORY_HARASSMENT,
   * HARM_CATEGORY_SEXUALLY_EXPLICIT, HARM_CATEGORY_CIVIC_INTEGRITY
   *
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Optional. Specify if the threshold is used for probability or severity
   * score. If not specified, the threshold is used for probability score.
   *
   * Accepted values: HARM_BLOCK_METHOD_UNSPECIFIED, SEVERITY, PROBABILITY
   *
   * @param self::METHOD_* $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return self::METHOD_*
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * The harm block threshold.
   *
   * Accepted values: HARM_BLOCK_THRESHOLD_UNSPECIFIED, BLOCK_LOW_AND_ABOVE,
   * BLOCK_MEDIUM_AND_ABOVE, BLOCK_ONLY_HIGH, BLOCK_NONE, OFF
   *
   * @param self::THRESHOLD_* $threshold
   */
  public function setThreshold($threshold)
  {
    $this->threshold = $threshold;
  }
  /**
   * @return self::THRESHOLD_*
   */
  public function getThreshold()
  {
    return $this->threshold;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2SafetySetting::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2SafetySetting');
