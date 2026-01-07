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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0CommonKeywordInfo extends \Google\Model
{
  /**
   * Not specified.
   */
  public const MATCH_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const MATCH_TYPE_UNKNOWN = 'UNKNOWN';
  /**
   * Exact match.
   */
  public const MATCH_TYPE_EXACT = 'EXACT';
  /**
   * Phrase match.
   */
  public const MATCH_TYPE_PHRASE = 'PHRASE';
  /**
   * Broad match.
   */
  public const MATCH_TYPE_BROAD = 'BROAD';
  /**
   * The match type of the keyword.
   *
   * @var string
   */
  public $matchType;
  /**
   * The text of the keyword (at most 80 characters and 10 words).
   *
   * @var string
   */
  public $text;

  /**
   * The match type of the keyword.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, EXACT, PHRASE, BROAD
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
   * The text of the keyword (at most 80 characters and 10 words).
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0CommonKeywordInfo::class, 'Google_Service_SA360_GoogleAdsSearchads360V0CommonKeywordInfo');
