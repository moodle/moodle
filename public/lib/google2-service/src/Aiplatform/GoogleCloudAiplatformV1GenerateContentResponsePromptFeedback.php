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

class GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback extends \Google\Collection
{
  /**
   * The blocked reason is unspecified.
   */
  public const BLOCK_REASON_BLOCKED_REASON_UNSPECIFIED = 'BLOCKED_REASON_UNSPECIFIED';
  /**
   * The prompt was blocked for safety reasons.
   */
  public const BLOCK_REASON_SAFETY = 'SAFETY';
  /**
   * The prompt was blocked for other reasons. For example, it may be due to the
   * prompt's language, or because it contains other harmful content.
   */
  public const BLOCK_REASON_OTHER = 'OTHER';
  /**
   * The prompt was blocked because it contains a term from the terminology
   * blocklist.
   */
  public const BLOCK_REASON_BLOCKLIST = 'BLOCKLIST';
  /**
   * The prompt was blocked because it contains prohibited content.
   */
  public const BLOCK_REASON_PROHIBITED_CONTENT = 'PROHIBITED_CONTENT';
  /**
   * The prompt was blocked by Model Armor.
   */
  public const BLOCK_REASON_MODEL_ARMOR = 'MODEL_ARMOR';
  /**
   * The prompt was blocked because it contains content that is unsafe for image
   * generation.
   */
  public const BLOCK_REASON_IMAGE_SAFETY = 'IMAGE_SAFETY';
  /**
   * The prompt was blocked as a jailbreak attempt.
   */
  public const BLOCK_REASON_JAILBREAK = 'JAILBREAK';
  protected $collection_key = 'safetyRatings';
  /**
   * Output only. The reason why the prompt was blocked.
   *
   * @var string
   */
  public $blockReason;
  /**
   * Output only. A readable message that explains the reason why the prompt was
   * blocked.
   *
   * @var string
   */
  public $blockReasonMessage;
  protected $safetyRatingsType = GoogleCloudAiplatformV1SafetyRating::class;
  protected $safetyRatingsDataType = 'array';

  /**
   * Output only. The reason why the prompt was blocked.
   *
   * Accepted values: BLOCKED_REASON_UNSPECIFIED, SAFETY, OTHER, BLOCKLIST,
   * PROHIBITED_CONTENT, MODEL_ARMOR, IMAGE_SAFETY, JAILBREAK
   *
   * @param self::BLOCK_REASON_* $blockReason
   */
  public function setBlockReason($blockReason)
  {
    $this->blockReason = $blockReason;
  }
  /**
   * @return self::BLOCK_REASON_*
   */
  public function getBlockReason()
  {
    return $this->blockReason;
  }
  /**
   * Output only. A readable message that explains the reason why the prompt was
   * blocked.
   *
   * @param string $blockReasonMessage
   */
  public function setBlockReasonMessage($blockReasonMessage)
  {
    $this->blockReasonMessage = $blockReasonMessage;
  }
  /**
   * @return string
   */
  public function getBlockReasonMessage()
  {
    return $this->blockReasonMessage;
  }
  /**
   * Output only. A list of safety ratings for the prompt. There is one rating
   * per category.
   *
   * @param GoogleCloudAiplatformV1SafetyRating[] $safetyRatings
   */
  public function setSafetyRatings($safetyRatings)
  {
    $this->safetyRatings = $safetyRatings;
  }
  /**
   * @return GoogleCloudAiplatformV1SafetyRating[]
   */
  public function getSafetyRatings()
  {
    return $this->safetyRatings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerateContentResponsePromptFeedback');
