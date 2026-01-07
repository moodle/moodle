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

class GoogleCloudAiplatformV1SafetyRating extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const CATEGORY_HARM_CATEGORY_UNSPECIFIED = 'HARM_CATEGORY_UNSPECIFIED';
  /**
   * Content that promotes violence or incites hatred against individuals or
   * groups based on certain attributes.
   */
  public const CATEGORY_HARM_CATEGORY_HATE_SPEECH = 'HARM_CATEGORY_HATE_SPEECH';
  /**
   * Content that promotes, facilitates, or enables dangerous activities.
   */
  public const CATEGORY_HARM_CATEGORY_DANGEROUS_CONTENT = 'HARM_CATEGORY_DANGEROUS_CONTENT';
  /**
   * Abusive, threatening, or content intended to bully, torment, or ridicule.
   */
  public const CATEGORY_HARM_CATEGORY_HARASSMENT = 'HARM_CATEGORY_HARASSMENT';
  /**
   * Content that contains sexually explicit material.
   */
  public const CATEGORY_HARM_CATEGORY_SEXUALLY_EXPLICIT = 'HARM_CATEGORY_SEXUALLY_EXPLICIT';
  /**
   * Deprecated: Election filter is not longer supported. The harm category is
   * civic integrity.
   *
   * @deprecated
   */
  public const CATEGORY_HARM_CATEGORY_CIVIC_INTEGRITY = 'HARM_CATEGORY_CIVIC_INTEGRITY';
  /**
   * Images that contain hate speech.
   */
  public const CATEGORY_HARM_CATEGORY_IMAGE_HATE = 'HARM_CATEGORY_IMAGE_HATE';
  /**
   * Images that contain dangerous content.
   */
  public const CATEGORY_HARM_CATEGORY_IMAGE_DANGEROUS_CONTENT = 'HARM_CATEGORY_IMAGE_DANGEROUS_CONTENT';
  /**
   * Images that contain harassment.
   */
  public const CATEGORY_HARM_CATEGORY_IMAGE_HARASSMENT = 'HARM_CATEGORY_IMAGE_HARASSMENT';
  /**
   * Images that contain sexually explicit content.
   */
  public const CATEGORY_HARM_CATEGORY_IMAGE_SEXUALLY_EXPLICIT = 'HARM_CATEGORY_IMAGE_SEXUALLY_EXPLICIT';
  /**
   * Prompts designed to bypass safety filters.
   */
  public const CATEGORY_HARM_CATEGORY_JAILBREAK = 'HARM_CATEGORY_JAILBREAK';
  /**
   * The harm block threshold is unspecified.
   */
  public const OVERWRITTEN_THRESHOLD_HARM_BLOCK_THRESHOLD_UNSPECIFIED = 'HARM_BLOCK_THRESHOLD_UNSPECIFIED';
  /**
   * Block content with a low harm probability or higher.
   */
  public const OVERWRITTEN_THRESHOLD_BLOCK_LOW_AND_ABOVE = 'BLOCK_LOW_AND_ABOVE';
  /**
   * Block content with a medium harm probability or higher.
   */
  public const OVERWRITTEN_THRESHOLD_BLOCK_MEDIUM_AND_ABOVE = 'BLOCK_MEDIUM_AND_ABOVE';
  /**
   * Block content with a high harm probability.
   */
  public const OVERWRITTEN_THRESHOLD_BLOCK_ONLY_HIGH = 'BLOCK_ONLY_HIGH';
  /**
   * Do not block any content, regardless of its harm probability.
   */
  public const OVERWRITTEN_THRESHOLD_BLOCK_NONE = 'BLOCK_NONE';
  /**
   * Turn off the safety filter entirely.
   */
  public const OVERWRITTEN_THRESHOLD_OFF = 'OFF';
  /**
   * The harm probability is unspecified.
   */
  public const PROBABILITY_HARM_PROBABILITY_UNSPECIFIED = 'HARM_PROBABILITY_UNSPECIFIED';
  /**
   * The harm probability is negligible.
   */
  public const PROBABILITY_NEGLIGIBLE = 'NEGLIGIBLE';
  /**
   * The harm probability is low.
   */
  public const PROBABILITY_LOW = 'LOW';
  /**
   * The harm probability is medium.
   */
  public const PROBABILITY_MEDIUM = 'MEDIUM';
  /**
   * The harm probability is high.
   */
  public const PROBABILITY_HIGH = 'HIGH';
  /**
   * The harm severity is unspecified.
   */
  public const SEVERITY_HARM_SEVERITY_UNSPECIFIED = 'HARM_SEVERITY_UNSPECIFIED';
  /**
   * The harm severity is negligible.
   */
  public const SEVERITY_HARM_SEVERITY_NEGLIGIBLE = 'HARM_SEVERITY_NEGLIGIBLE';
  /**
   * The harm severity is low.
   */
  public const SEVERITY_HARM_SEVERITY_LOW = 'HARM_SEVERITY_LOW';
  /**
   * The harm severity is medium.
   */
  public const SEVERITY_HARM_SEVERITY_MEDIUM = 'HARM_SEVERITY_MEDIUM';
  /**
   * The harm severity is high.
   */
  public const SEVERITY_HARM_SEVERITY_HIGH = 'HARM_SEVERITY_HIGH';
  /**
   * Output only. Indicates whether the content was blocked because of this
   * rating.
   *
   * @var bool
   */
  public $blocked;
  /**
   * Output only. The harm category of this rating.
   *
   * @var string
   */
  public $category;
  /**
   * Output only. The overwritten threshold for the safety category of Gemini
   * 2.0 image out. If minors are detected in the output image, the threshold of
   * each safety category will be overwritten if user sets a lower threshold.
   *
   * @var string
   */
  public $overwrittenThreshold;
  /**
   * Output only. The probability of harm for this category.
   *
   * @var string
   */
  public $probability;
  /**
   * Output only. The probability score of harm for this category.
   *
   * @var float
   */
  public $probabilityScore;
  /**
   * Output only. The severity of harm for this category.
   *
   * @var string
   */
  public $severity;
  /**
   * Output only. The severity score of harm for this category.
   *
   * @var float
   */
  public $severityScore;

  /**
   * Output only. Indicates whether the content was blocked because of this
   * rating.
   *
   * @param bool $blocked
   */
  public function setBlocked($blocked)
  {
    $this->blocked = $blocked;
  }
  /**
   * @return bool
   */
  public function getBlocked()
  {
    return $this->blocked;
  }
  /**
   * Output only. The harm category of this rating.
   *
   * Accepted values: HARM_CATEGORY_UNSPECIFIED, HARM_CATEGORY_HATE_SPEECH,
   * HARM_CATEGORY_DANGEROUS_CONTENT, HARM_CATEGORY_HARASSMENT,
   * HARM_CATEGORY_SEXUALLY_EXPLICIT, HARM_CATEGORY_CIVIC_INTEGRITY,
   * HARM_CATEGORY_IMAGE_HATE, HARM_CATEGORY_IMAGE_DANGEROUS_CONTENT,
   * HARM_CATEGORY_IMAGE_HARASSMENT, HARM_CATEGORY_IMAGE_SEXUALLY_EXPLICIT,
   * HARM_CATEGORY_JAILBREAK
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
   * Output only. The overwritten threshold for the safety category of Gemini
   * 2.0 image out. If minors are detected in the output image, the threshold of
   * each safety category will be overwritten if user sets a lower threshold.
   *
   * Accepted values: HARM_BLOCK_THRESHOLD_UNSPECIFIED, BLOCK_LOW_AND_ABOVE,
   * BLOCK_MEDIUM_AND_ABOVE, BLOCK_ONLY_HIGH, BLOCK_NONE, OFF
   *
   * @param self::OVERWRITTEN_THRESHOLD_* $overwrittenThreshold
   */
  public function setOverwrittenThreshold($overwrittenThreshold)
  {
    $this->overwrittenThreshold = $overwrittenThreshold;
  }
  /**
   * @return self::OVERWRITTEN_THRESHOLD_*
   */
  public function getOverwrittenThreshold()
  {
    return $this->overwrittenThreshold;
  }
  /**
   * Output only. The probability of harm for this category.
   *
   * Accepted values: HARM_PROBABILITY_UNSPECIFIED, NEGLIGIBLE, LOW, MEDIUM,
   * HIGH
   *
   * @param self::PROBABILITY_* $probability
   */
  public function setProbability($probability)
  {
    $this->probability = $probability;
  }
  /**
   * @return self::PROBABILITY_*
   */
  public function getProbability()
  {
    return $this->probability;
  }
  /**
   * Output only. The probability score of harm for this category.
   *
   * @param float $probabilityScore
   */
  public function setProbabilityScore($probabilityScore)
  {
    $this->probabilityScore = $probabilityScore;
  }
  /**
   * @return float
   */
  public function getProbabilityScore()
  {
    return $this->probabilityScore;
  }
  /**
   * Output only. The severity of harm for this category.
   *
   * Accepted values: HARM_SEVERITY_UNSPECIFIED, HARM_SEVERITY_NEGLIGIBLE,
   * HARM_SEVERITY_LOW, HARM_SEVERITY_MEDIUM, HARM_SEVERITY_HIGH
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Output only. The severity score of harm for this category.
   *
   * @param float $severityScore
   */
  public function setSeverityScore($severityScore)
  {
    $this->severityScore = $severityScore;
  }
  /**
   * @return float
   */
  public function getSeverityScore()
  {
    return $this->severityScore;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SafetyRating::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SafetyRating');
