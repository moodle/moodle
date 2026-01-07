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

class GoogleCloudDiscoveryengineV1SuggestionDenyListEntry extends \Google\Model
{
  /**
   * Default value. Should not be used
   */
  public const MATCH_OPERATOR_MATCH_OPERATOR_UNSPECIFIED = 'MATCH_OPERATOR_UNSPECIFIED';
  /**
   * If the suggestion is an exact match to the block_phrase, then block it.
   */
  public const MATCH_OPERATOR_EXACT_MATCH = 'EXACT_MATCH';
  /**
   * If the suggestion contains the block_phrase, then block it.
   */
  public const MATCH_OPERATOR_CONTAINS = 'CONTAINS';
  /**
   * Required. Phrase to block from suggestions served. Can be maximum 125
   * characters.
   *
   * @var string
   */
  public $blockPhrase;
  /**
   * Required. The match operator to apply for this phrase. Whether to block the
   * exact phrase, or block any suggestions containing this phrase.
   *
   * @var string
   */
  public $matchOperator;

  /**
   * Required. Phrase to block from suggestions served. Can be maximum 125
   * characters.
   *
   * @param string $blockPhrase
   */
  public function setBlockPhrase($blockPhrase)
  {
    $this->blockPhrase = $blockPhrase;
  }
  /**
   * @return string
   */
  public function getBlockPhrase()
  {
    return $this->blockPhrase;
  }
  /**
   * Required. The match operator to apply for this phrase. Whether to block the
   * exact phrase, or block any suggestions containing this phrase.
   *
   * Accepted values: MATCH_OPERATOR_UNSPECIFIED, EXACT_MATCH, CONTAINS
   *
   * @param self::MATCH_OPERATOR_* $matchOperator
   */
  public function setMatchOperator($matchOperator)
  {
    $this->matchOperator = $matchOperator;
  }
  /**
   * @return self::MATCH_OPERATOR_*
   */
  public function getMatchOperator()
  {
    return $this->matchOperator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1SuggestionDenyListEntry::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1SuggestionDenyListEntry');
