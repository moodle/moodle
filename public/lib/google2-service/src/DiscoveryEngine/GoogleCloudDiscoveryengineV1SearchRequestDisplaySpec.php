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

class GoogleCloudDiscoveryengineV1SearchRequestDisplaySpec extends \Google\Model
{
  /**
   * Server behavior is the same as `MATCH_HIGHLIGHTING_DISABLED`.
   */
  public const MATCH_HIGHLIGHTING_CONDITION_MATCH_HIGHLIGHTING_CONDITION_UNSPECIFIED = 'MATCH_HIGHLIGHTING_CONDITION_UNSPECIFIED';
  /**
   * Disables match highlighting on all documents.
   */
  public const MATCH_HIGHLIGHTING_CONDITION_MATCH_HIGHLIGHTING_DISABLED = 'MATCH_HIGHLIGHTING_DISABLED';
  /**
   * Enables match highlighting on all documents.
   */
  public const MATCH_HIGHLIGHTING_CONDITION_MATCH_HIGHLIGHTING_ENABLED = 'MATCH_HIGHLIGHTING_ENABLED';
  /**
   * The condition under which match highlighting should occur.
   *
   * @var string
   */
  public $matchHighlightingCondition;

  /**
   * The condition under which match highlighting should occur.
   *
   * Accepted values: MATCH_HIGHLIGHTING_CONDITION_UNSPECIFIED,
   * MATCH_HIGHLIGHTING_DISABLED, MATCH_HIGHLIGHTING_ENABLED
   *
   * @param self::MATCH_HIGHLIGHTING_CONDITION_* $matchHighlightingCondition
   */
  public function setMatchHighlightingCondition($matchHighlightingCondition)
  {
    $this->matchHighlightingCondition = $matchHighlightingCondition;
  }
  /**
   * @return self::MATCH_HIGHLIGHTING_CONDITION_*
   */
  public function getMatchHighlightingCondition()
  {
    return $this->matchHighlightingCondition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SearchRequestDisplaySpec::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SearchRequestDisplaySpec');
