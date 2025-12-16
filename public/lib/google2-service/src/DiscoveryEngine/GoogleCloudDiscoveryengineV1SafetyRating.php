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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1SafetyRating extends \Google\Model
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
   * Harm probability unspecified.
   */
  public const PROBABILITY_HARM_PROBABILITY_UNSPECIFIED = 'HARM_PROBABILITY_UNSPECIFIED';
  /**
   * Negligible level of harm.
   */
  public const PROBABILITY_NEGLIGIBLE = 'NEGLIGIBLE';
  /**
   * Low level of harm.
   */
  public const PROBABILITY_LOW = 'LOW';
  /**
   * Medium level of harm.
   */
  public const PROBABILITY_MEDIUM = 'MEDIUM';
  /**
   * High level of harm.
   */
  public const PROBABILITY_HIGH = 'HIGH';
  /**
   * Harm severity unspecified.
   */
  public const SEVERITY_HARM_SEVERITY_UNSPECIFIED = 'HARM_SEVERITY_UNSPECIFIED';
  /**
   * Negligible level of harm severity.
   */
  public const SEVERITY_HARM_SEVERITY_NEGLIGIBLE = 'HARM_SEVERITY_NEGLIGIBLE';
  /**
   * Low level of harm severity.
   */
  public const SEVERITY_HARM_SEVERITY_LOW = 'HARM_SEVERITY_LOW';
  /**
   * Medium level of harm severity.
   */
  public const SEVERITY_HARM_SEVERITY_MEDIUM = 'HARM_SEVERITY_MEDIUM';
  /**
   * High level of harm severity.
   */
  public const SEVERITY_HARM_SEVERITY_HIGH = 'HARM_SEVERITY_HIGH';
  /**
   * Output only. Indicates whether the content was filtered out because of this
   * rating.
   *
   * @var bool
   */
  public $blocked;
  /**
   * Output only. Harm category.
   *
   * @var string
   */
  public $category;
  /**
   * Output only. Harm probability levels in the content.
   *
   * @var string
   */
  public $probability;
  /**
   * Output only. Harm probability score.
   *
   * @var float
   */
  public $probabilityScore;
  /**
   * Output only. Harm severity levels in the content.
   *
   * @var string
   */
  public $severity;
  /**
   * Output only. Harm severity score.
   *
   * @var float
   */
  public $severityScore;

  /**
   * Output only. Indicates whether the content was filtered out because of this
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
   * Output only. Harm category.
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
   * Output only. Harm probability levels in the content.
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
   * Output only. Harm probability score.
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
   * Output only. Harm severity levels in the content.
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
   * Output only. Harm severity score.
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
class_alias(GoogleCloudDiscoveryengineV1SafetyRating::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SafetyRating');
