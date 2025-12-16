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

class GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec extends \Google\Model
{
  /**
   * Default is no filter
   */
  public const FILTERING_LEVEL_FILTERING_LEVEL_UNSPECIFIED = 'FILTERING_LEVEL_UNSPECIFIED';
  /**
   * Filter answers based on a low threshold.
   */
  public const FILTERING_LEVEL_FILTERING_LEVEL_LOW = 'FILTERING_LEVEL_LOW';
  /**
   * Filter answers based on a high threshold.
   */
  public const FILTERING_LEVEL_FILTERING_LEVEL_HIGH = 'FILTERING_LEVEL_HIGH';
  /**
   * Optional. Specifies whether to enable the filtering based on grounding
   * score and at what level.
   *
   * @var string
   */
  public $filteringLevel;
  /**
   * Optional. Specifies whether to include grounding_supports in the answer.
   * The default value is `false`. When this field is set to `true`, returned
   * answer will have `grounding_score` and will contain GroundingSupports for
   * each claim.
   *
   * @var bool
   */
  public $includeGroundingSupports;

  /**
   * Optional. Specifies whether to enable the filtering based on grounding
   * score and at what level.
   *
   * Accepted values: FILTERING_LEVEL_UNSPECIFIED, FILTERING_LEVEL_LOW,
   * FILTERING_LEVEL_HIGH
   *
   * @param self::FILTERING_LEVEL_* $filteringLevel
   */
  public function setFilteringLevel($filteringLevel)
  {
    $this->filteringLevel = $filteringLevel;
  }
  /**
   * @return self::FILTERING_LEVEL_*
   */
  public function getFilteringLevel()
  {
    return $this->filteringLevel;
  }
  /**
   * Optional. Specifies whether to include grounding_supports in the answer.
   * The default value is `false`. When this field is set to `true`, returned
   * answer will have `grounding_score` and will contain GroundingSupports for
   * each claim.
   *
   * @param bool $includeGroundingSupports
   */
  public function setIncludeGroundingSupports($includeGroundingSupports)
  {
    $this->includeGroundingSupports = $includeGroundingSupports;
  }
  /**
   * @return bool
   */
  public function getIncludeGroundingSupports()
  {
    return $this->includeGroundingSupports;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AnswerQueryRequestGroundingSpec');
