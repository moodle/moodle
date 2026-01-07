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

class GoogleCloudAiplatformV1SafetySetting extends \Google\Model
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
   * The harm block threshold is unspecified.
   */
  public const THRESHOLD_HARM_BLOCK_THRESHOLD_UNSPECIFIED = 'HARM_BLOCK_THRESHOLD_UNSPECIFIED';
  /**
   * Block content with a low harm probability or higher.
   */
  public const THRESHOLD_BLOCK_LOW_AND_ABOVE = 'BLOCK_LOW_AND_ABOVE';
  /**
   * Block content with a medium harm probability or higher.
   */
  public const THRESHOLD_BLOCK_MEDIUM_AND_ABOVE = 'BLOCK_MEDIUM_AND_ABOVE';
  /**
   * Block content with a high harm probability.
   */
  public const THRESHOLD_BLOCK_ONLY_HIGH = 'BLOCK_ONLY_HIGH';
  /**
   * Do not block any content, regardless of its harm probability.
   */
  public const THRESHOLD_BLOCK_NONE = 'BLOCK_NONE';
  /**
   * Turn off the safety filter entirely.
   */
  public const THRESHOLD_OFF = 'OFF';
  /**
   * Required. The harm category to be blocked.
   *
   * @var string
   */
  public $category;
  /**
   * Optional. The method for blocking content. If not specified, the default
   * behavior is to use the probability score.
   *
   * @var string
   */
  public $method;
  /**
   * Required. The threshold for blocking content. If the harm probability
   * exceeds this threshold, the content will be blocked.
   *
   * @var string
   */
  public $threshold;

  /**
   * Required. The harm category to be blocked.
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
   * Optional. The method for blocking content. If not specified, the default
   * behavior is to use the probability score.
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
   * Required. The threshold for blocking content. If the harm probability
   * exceeds this threshold, the content will be blocked.
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
class_alias(GoogleCloudAiplatformV1SafetySetting::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SafetySetting');
