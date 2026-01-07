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

class GoogleCloudAiplatformV1ToolGoogleSearch extends \Google\Collection
{
  /**
   * Defaults to unspecified.
   */
  public const BLOCKING_CONFIDENCE_PHISH_BLOCK_THRESHOLD_UNSPECIFIED = 'PHISH_BLOCK_THRESHOLD_UNSPECIFIED';
  /**
   * Blocks Low and above confidence URL that is risky.
   */
  public const BLOCKING_CONFIDENCE_BLOCK_LOW_AND_ABOVE = 'BLOCK_LOW_AND_ABOVE';
  /**
   * Blocks Medium and above confidence URL that is risky.
   */
  public const BLOCKING_CONFIDENCE_BLOCK_MEDIUM_AND_ABOVE = 'BLOCK_MEDIUM_AND_ABOVE';
  /**
   * Blocks High and above confidence URL that is risky.
   */
  public const BLOCKING_CONFIDENCE_BLOCK_HIGH_AND_ABOVE = 'BLOCK_HIGH_AND_ABOVE';
  /**
   * Blocks Higher and above confidence URL that is risky.
   */
  public const BLOCKING_CONFIDENCE_BLOCK_HIGHER_AND_ABOVE = 'BLOCK_HIGHER_AND_ABOVE';
  /**
   * Blocks Very high and above confidence URL that is risky.
   */
  public const BLOCKING_CONFIDENCE_BLOCK_VERY_HIGH_AND_ABOVE = 'BLOCK_VERY_HIGH_AND_ABOVE';
  /**
   * Blocks Extremely high confidence URL that is risky.
   */
  public const BLOCKING_CONFIDENCE_BLOCK_ONLY_EXTREMELY_HIGH = 'BLOCK_ONLY_EXTREMELY_HIGH';
  protected $collection_key = 'excludeDomains';
  /**
   * Optional. Sites with confidence level chosen & above this value will be
   * blocked from the search results.
   *
   * @var string
   */
  public $blockingConfidence;
  /**
   * Optional. List of domains to be excluded from the search results. The
   * default limit is 2000 domains. Example: ["amazon.com", "facebook.com"].
   *
   * @var string[]
   */
  public $excludeDomains;

  /**
   * Optional. Sites with confidence level chosen & above this value will be
   * blocked from the search results.
   *
   * Accepted values: PHISH_BLOCK_THRESHOLD_UNSPECIFIED, BLOCK_LOW_AND_ABOVE,
   * BLOCK_MEDIUM_AND_ABOVE, BLOCK_HIGH_AND_ABOVE, BLOCK_HIGHER_AND_ABOVE,
   * BLOCK_VERY_HIGH_AND_ABOVE, BLOCK_ONLY_EXTREMELY_HIGH
   *
   * @param self::BLOCKING_CONFIDENCE_* $blockingConfidence
   */
  public function setBlockingConfidence($blockingConfidence)
  {
    $this->blockingConfidence = $blockingConfidence;
  }
  /**
   * @return self::BLOCKING_CONFIDENCE_*
   */
  public function getBlockingConfidence()
  {
    return $this->blockingConfidence;
  }
  /**
   * Optional. List of domains to be excluded from the search results. The
   * default limit is 2000 domains. Example: ["amazon.com", "facebook.com"].
   *
   * @param string[] $excludeDomains
   */
  public function setExcludeDomains($excludeDomains)
  {
    $this->excludeDomains = $excludeDomains;
  }
  /**
   * @return string[]
   */
  public function getExcludeDomains()
  {
    return $this->excludeDomains;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ToolGoogleSearch::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ToolGoogleSearch');
