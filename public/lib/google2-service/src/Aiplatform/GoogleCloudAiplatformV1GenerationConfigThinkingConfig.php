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

class GoogleCloudAiplatformV1GenerationConfigThinkingConfig extends \Google\Model
{
  /**
   * Unspecified thinking level.
   */
  public const THINKING_LEVEL_THINKING_LEVEL_UNSPECIFIED = 'THINKING_LEVEL_UNSPECIFIED';
  /**
   * Low thinking level.
   */
  public const THINKING_LEVEL_LOW = 'LOW';
  /**
   * High thinking level.
   */
  public const THINKING_LEVEL_HIGH = 'HIGH';
  /**
   * Optional. If true, the model will include its thoughts in the response.
   * "Thoughts" are the intermediate steps the model takes to arrive at the
   * final response. They can provide insights into the model's reasoning
   * process and help with debugging. If this is true, thoughts are returned
   * only when available.
   *
   * @var bool
   */
  public $includeThoughts;
  /**
   * Optional. The token budget for the model's thinking process. The model will
   * make a best effort to stay within this budget. This can be used to control
   * the trade-off between response quality and latency.
   *
   * @var int
   */
  public $thinkingBudget;
  /**
   * Optional. The number of thoughts tokens that the model should generate.
   *
   * @var string
   */
  public $thinkingLevel;

  /**
   * Optional. If true, the model will include its thoughts in the response.
   * "Thoughts" are the intermediate steps the model takes to arrive at the
   * final response. They can provide insights into the model's reasoning
   * process and help with debugging. If this is true, thoughts are returned
   * only when available.
   *
   * @param bool $includeThoughts
   */
  public function setIncludeThoughts($includeThoughts)
  {
    $this->includeThoughts = $includeThoughts;
  }
  /**
   * @return bool
   */
  public function getIncludeThoughts()
  {
    return $this->includeThoughts;
  }
  /**
   * Optional. The token budget for the model's thinking process. The model will
   * make a best effort to stay within this budget. This can be used to control
   * the trade-off between response quality and latency.
   *
   * @param int $thinkingBudget
   */
  public function setThinkingBudget($thinkingBudget)
  {
    $this->thinkingBudget = $thinkingBudget;
  }
  /**
   * @return int
   */
  public function getThinkingBudget()
  {
    return $this->thinkingBudget;
  }
  /**
   * Optional. The number of thoughts tokens that the model should generate.
   *
   * Accepted values: THINKING_LEVEL_UNSPECIFIED, LOW, HIGH
   *
   * @param self::THINKING_LEVEL_* $thinkingLevel
   */
  public function setThinkingLevel($thinkingLevel)
  {
    $this->thinkingLevel = $thinkingLevel;
  }
  /**
   * @return self::THINKING_LEVEL_*
   */
  public function getThinkingLevel()
  {
    return $this->thinkingLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1GenerationConfigThinkingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1GenerationConfigThinkingConfig');
