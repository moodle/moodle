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

class GoogleCloudDiscoveryengineV1AssistantCustomerPolicyBannedPhrase extends \Google\Model
{
  /**
   * Defaults to SIMPLE_STRING_MATCH.
   */
  public const MATCH_TYPE_BANNED_PHRASE_MATCH_TYPE_UNSPECIFIED = 'BANNED_PHRASE_MATCH_TYPE_UNSPECIFIED';
  /**
   * The banned phrase matches if it is found anywhere in the text as an exact
   * substring.
   */
  public const MATCH_TYPE_SIMPLE_STRING_MATCH = 'SIMPLE_STRING_MATCH';
  /**
   * Banned phrase only matches if the pattern found in the text is surrounded
   * by word delimiters. The phrase itself may still contain word delimiters.
   */
  public const MATCH_TYPE_WORD_BOUNDARY_STRING_MATCH = 'WORD_BOUNDARY_STRING_MATCH';
  /**
   * Optional. If true, diacritical marks (e.g., accents, umlauts) are ignored
   * when matching banned phrases. For example, "cafe" would match "café".
   *
   * @var bool
   */
  public $ignoreDiacritics;
  /**
   * Optional. Match type for the banned phrase.
   *
   * @var string
   */
  public $matchType;
  /**
   * Required. The raw string content to be banned.
   *
   * @var string
   */
  public $phrase;

  /**
   * Optional. If true, diacritical marks (e.g., accents, umlauts) are ignored
   * when matching banned phrases. For example, "cafe" would match "café".
   *
   * @param bool $ignoreDiacritics
   */
  public function setIgnoreDiacritics($ignoreDiacritics)
  {
    $this->ignoreDiacritics = $ignoreDiacritics;
  }
  /**
   * @return bool
   */
  public function getIgnoreDiacritics()
  {
    return $this->ignoreDiacritics;
  }
  /**
   * Optional. Match type for the banned phrase.
   *
   * Accepted values: BANNED_PHRASE_MATCH_TYPE_UNSPECIFIED, SIMPLE_STRING_MATCH,
   * WORD_BOUNDARY_STRING_MATCH
   *
   * @param self::MATCH_TYPE_* $matchType
   */
  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }
  /**
   * @return self::MATCH_TYPE_*
   */
  public function getMatchType()
  {
    return $this->matchType;
  }
  /**
   * Required. The raw string content to be banned.
   *
   * @param string $phrase
   */
  public function setPhrase($phrase)
  {
    $this->phrase = $phrase;
  }
  /**
   * @return string
   */
  public function getPhrase()
  {
    return $this->phrase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1AssistantCustomerPolicyBannedPhrase::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1AssistantCustomerPolicyBannedPhrase');
