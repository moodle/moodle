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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSafetySignals extends \Google\Model
{
  /**
   * No banned phrase check was executed.
   */
  public const BANNED_PHRASE_MATCH_BANNED_PHRASE_MATCH_UNSPECIFIED = 'BANNED_PHRASE_MATCH_UNSPECIFIED';
  /**
   * All banned phrase checks led to no match.
   */
  public const BANNED_PHRASE_MATCH_BANNED_PHRASE_MATCH_NONE = 'BANNED_PHRASE_MATCH_NONE';
  /**
   * A banned phrase matched the query.
   */
  public const BANNED_PHRASE_MATCH_BANNED_PHRASE_MATCH_QUERY = 'BANNED_PHRASE_MATCH_QUERY';
  /**
   * A banned phrase matched the response.
   */
  public const BANNED_PHRASE_MATCH_BANNED_PHRASE_MATCH_RESPONSE = 'BANNED_PHRASE_MATCH_RESPONSE';
  /**
   * Decision not specified.
   */
  public const DECISION_SAFETY_DECISION_UNSPECIFIED = 'SAFETY_DECISION_UNSPECIFIED';
  /**
   * No manual or automatic safety check fired.
   */
  public const DECISION_ACCEPTED_BY_SAFETY_CHECK = 'ACCEPTED_BY_SAFETY_CHECK';
  /**
   * One ore more safety checks fired.
   */
  public const DECISION_REJECTED_BY_SAFETY_CHECK = 'REJECTED_BY_SAFETY_CHECK';
  /**
   * Specifies banned phrase match subject.
   *
   * @var string
   */
  public $bannedPhraseMatch;
  /**
   * Safety decision.
   *
   * @var string
   */
  public $decision;
  /**
   * The matched banned phrase if there was a match.
   *
   * @var string
   */
  public $matchedBannedPhrase;

  /**
   * Specifies banned phrase match subject.
   *
   * Accepted values: BANNED_PHRASE_MATCH_UNSPECIFIED, BANNED_PHRASE_MATCH_NONE,
   * BANNED_PHRASE_MATCH_QUERY, BANNED_PHRASE_MATCH_RESPONSE
   *
   * @param self::BANNED_PHRASE_MATCH_* $bannedPhraseMatch
   */
  public function setBannedPhraseMatch($bannedPhraseMatch)
  {
    $this->bannedPhraseMatch = $bannedPhraseMatch;
  }
  /**
   * @return self::BANNED_PHRASE_MATCH_*
   */
  public function getBannedPhraseMatch()
  {
    return $this->bannedPhraseMatch;
  }
  /**
   * Safety decision.
   *
   * Accepted values: SAFETY_DECISION_UNSPECIFIED, ACCEPTED_BY_SAFETY_CHECK,
   * REJECTED_BY_SAFETY_CHECK
   *
   * @param self::DECISION_* $decision
   */
  public function setDecision($decision)
  {
    $this->decision = $decision;
  }
  /**
   * @return self::DECISION_*
   */
  public function getDecision()
  {
    return $this->decision;
  }
  /**
   * The matched banned phrase if there was a match.
   *
   * @param string $matchedBannedPhrase
   */
  public function setMatchedBannedPhrase($matchedBannedPhrase)
  {
    $this->matchedBannedPhrase = $matchedBannedPhrase;
  }
  /**
   * @return string
   */
  public function getMatchedBannedPhrase()
  {
    return $this->matchedBannedPhrase;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSafetySignals::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSafetySignals');
