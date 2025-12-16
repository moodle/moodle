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

namespace Google\Service\CloudSearch;

class EnterpriseTopazSidekickCardMetadata extends \Google\Model
{
  /**
   * Let MSCR decide how this card should be packed. Most cards should choose
   * this type. This type should largely be considered equivalent to ANSWER.
   * However, this is not guaranteed to be the case as the request to MSCR may
   * override the static configuration.
   */
  public const CARD_CATEGORY_DEFAULT = 'DEFAULT';
  /**
   * This card should be rendered as an answer card.
   */
  public const CARD_CATEGORY_ANSWER = 'ANSWER';
  /**
   * This card should be rendered as a knowledge card (a non-organic result).
   */
  public const CARD_CATEGORY_KNOWLEDGE = 'KNOWLEDGE';
  /**
   * This card should be rendered in the Homepage.
   */
  public const CARD_CATEGORY_HOMEPAGE = 'HOMEPAGE';
  /**
   * Unknown chronology (default).
   */
  public const CHRONOLOGY_UNKNOWN = 'UNKNOWN';
  /**
   * Past.
   */
  public const CHRONOLOGY_PAST = 'PAST';
  /**
   * Recently past.
   */
  public const CHRONOLOGY_RECENTLY_PAST = 'RECENTLY_PAST';
  /**
   * Present.
   */
  public const CHRONOLOGY_PRESENT = 'PRESENT';
  /**
   * Near future.
   */
  public const CHRONOLOGY_NEAR_FUTURE = 'NEAR_FUTURE';
  /**
   * Future.
   */
  public const CHRONOLOGY_FUTURE = 'FUTURE';
  /**
   * Unknown mode (default).
   */
  public const RENDER_MODE_UNKNOWN_RENDER = 'UNKNOWN_RENDER';
  /**
   * Collapsed.
   */
  public const RENDER_MODE_COLLAPSED = 'COLLAPSED';
  /**
   * Expanded.
   */
  public const RENDER_MODE_EXPANDED = 'EXPANDED';
  /**
   * Declares a preference for how this card should be packed in MSCR. All cards
   * in a response must correspond to a single category. As a result, cards may
   * be dropped from the response if this field is set. Any card that does not
   * match the category of the card with the highest priority in the response
   * will be dropped.
   *
   * @var string
   */
  public $cardCategory;
  /**
   * An ID to identify the card and match actions to it. Be thoughtful of new
   * card IDs since actions will be associated to that ID. E.g., if two card IDs
   * collide, the system will think that the actions have been applied to the
   * same card. Similarly, if EAS can return multiple cards of the same type
   * (e.g., Meetings), ensure that the card_id identifies a given instance of
   * the card so that, e.g., dismissals only affect the dismissed card as
   * opposed to affecting all meeting cards.
   *
   * @var string
   */
  public $cardId;
  /**
   * Chronology.
   *
   * @var string
   */
  public $chronology;
  /**
   * Debug info (only reported if request's debug_level > 0).
   *
   * @var string
   */
  public $debugInfo;
  protected $nlpMetadataType = EnterpriseTopazSidekickNlpMetadata::class;
  protected $nlpMetadataDataType = '';
  protected $rankingParamsType = EnterpriseTopazSidekickRankingParams::class;
  protected $rankingParamsDataType = '';
  /**
   * Render mode.
   *
   * @var string
   */
  public $renderMode;

  /**
   * Declares a preference for how this card should be packed in MSCR. All cards
   * in a response must correspond to a single category. As a result, cards may
   * be dropped from the response if this field is set. Any card that does not
   * match the category of the card with the highest priority in the response
   * will be dropped.
   *
   * Accepted values: DEFAULT, ANSWER, KNOWLEDGE, HOMEPAGE
   *
   * @param self::CARD_CATEGORY_* $cardCategory
   */
  public function setCardCategory($cardCategory)
  {
    $this->cardCategory = $cardCategory;
  }
  /**
   * @return self::CARD_CATEGORY_*
   */
  public function getCardCategory()
  {
    return $this->cardCategory;
  }
  /**
   * An ID to identify the card and match actions to it. Be thoughtful of new
   * card IDs since actions will be associated to that ID. E.g., if two card IDs
   * collide, the system will think that the actions have been applied to the
   * same card. Similarly, if EAS can return multiple cards of the same type
   * (e.g., Meetings), ensure that the card_id identifies a given instance of
   * the card so that, e.g., dismissals only affect the dismissed card as
   * opposed to affecting all meeting cards.
   *
   * @param string $cardId
   */
  public function setCardId($cardId)
  {
    $this->cardId = $cardId;
  }
  /**
   * @return string
   */
  public function getCardId()
  {
    return $this->cardId;
  }
  /**
   * Chronology.
   *
   * Accepted values: UNKNOWN, PAST, RECENTLY_PAST, PRESENT, NEAR_FUTURE, FUTURE
   *
   * @param self::CHRONOLOGY_* $chronology
   */
  public function setChronology($chronology)
  {
    $this->chronology = $chronology;
  }
  /**
   * @return self::CHRONOLOGY_*
   */
  public function getChronology()
  {
    return $this->chronology;
  }
  /**
   * Debug info (only reported if request's debug_level > 0).
   *
   * @param string $debugInfo
   */
  public function setDebugInfo($debugInfo)
  {
    $this->debugInfo = $debugInfo;
  }
  /**
   * @return string
   */
  public function getDebugInfo()
  {
    return $this->debugInfo;
  }
  /**
   * Information about the NLP done to get the card.
   *
   * @param EnterpriseTopazSidekickNlpMetadata $nlpMetadata
   */
  public function setNlpMetadata(EnterpriseTopazSidekickNlpMetadata $nlpMetadata)
  {
    $this->nlpMetadata = $nlpMetadata;
  }
  /**
   * @return EnterpriseTopazSidekickNlpMetadata
   */
  public function getNlpMetadata()
  {
    return $this->nlpMetadata;
  }
  /**
   * Ranking params.
   *
   * @param EnterpriseTopazSidekickRankingParams $rankingParams
   */
  public function setRankingParams(EnterpriseTopazSidekickRankingParams $rankingParams)
  {
    $this->rankingParams = $rankingParams;
  }
  /**
   * @return EnterpriseTopazSidekickRankingParams
   */
  public function getRankingParams()
  {
    return $this->rankingParams;
  }
  /**
   * Render mode.
   *
   * Accepted values: UNKNOWN_RENDER, COLLAPSED, EXPANDED
   *
   * @param self::RENDER_MODE_* $renderMode
   */
  public function setRenderMode($renderMode)
  {
    $this->renderMode = $renderMode;
  }
  /**
   * @return self::RENDER_MODE_*
   */
  public function getRenderMode()
  {
    return $this->renderMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseTopazSidekickCardMetadata::class, 'Google_Service_CloudSearch_EnterpriseTopazSidekickCardMetadata');
