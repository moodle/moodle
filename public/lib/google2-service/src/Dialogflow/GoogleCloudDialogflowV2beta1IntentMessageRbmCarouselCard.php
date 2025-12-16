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

class GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const CARD_WIDTH_CARD_WIDTH_UNSPECIFIED = 'CARD_WIDTH_UNSPECIFIED';
  /**
   * 120 DP. Note that tall media cannot be used.
   */
  public const CARD_WIDTH_SMALL = 'SMALL';
  /**
   * 232 DP.
   */
  public const CARD_WIDTH_MEDIUM = 'MEDIUM';
  protected $collection_key = 'cardContents';
  protected $cardContentsType = GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent::class;
  protected $cardContentsDataType = 'array';
  /**
   * Required. The width of the cards in the carousel.
   *
   * @var string
   */
  public $cardWidth;

  /**
   * Required. The cards in the carousel. A carousel must have at least 2 cards
   * and at most 10.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent[] $cardContents
   */
  public function setCardContents($cardContents)
  {
    $this->cardContents = $cardContents;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent[]
   */
  public function getCardContents()
  {
    return $this->cardContents;
  }
  /**
   * Required. The width of the cards in the carousel.
   *
   * Accepted values: CARD_WIDTH_UNSPECIFIED, SMALL, MEDIUM
   *
   * @param self::CARD_WIDTH_* $cardWidth
   */
  public function setCardWidth($cardWidth)
  {
    $this->cardWidth = $cardWidth;
  }
  /**
   * @return self::CARD_WIDTH_*
   */
  public function getCardWidth()
  {
    return $this->cardWidth;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageRbmCarouselCard');
