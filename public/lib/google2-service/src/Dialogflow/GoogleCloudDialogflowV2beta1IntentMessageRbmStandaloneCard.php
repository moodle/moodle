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

class GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard extends \Google\Model
{
  /**
   * Not specified.
   */
  public const CARD_ORIENTATION_CARD_ORIENTATION_UNSPECIFIED = 'CARD_ORIENTATION_UNSPECIFIED';
  /**
   * Horizontal layout.
   */
  public const CARD_ORIENTATION_HORIZONTAL = 'HORIZONTAL';
  /**
   * Vertical layout.
   */
  public const CARD_ORIENTATION_VERTICAL = 'VERTICAL';
  /**
   * Not specified.
   */
  public const THUMBNAIL_IMAGE_ALIGNMENT_THUMBNAIL_IMAGE_ALIGNMENT_UNSPECIFIED = 'THUMBNAIL_IMAGE_ALIGNMENT_UNSPECIFIED';
  /**
   * Thumbnail preview is left-aligned.
   */
  public const THUMBNAIL_IMAGE_ALIGNMENT_LEFT = 'LEFT';
  /**
   * Thumbnail preview is right-aligned.
   */
  public const THUMBNAIL_IMAGE_ALIGNMENT_RIGHT = 'RIGHT';
  protected $cardContentType = GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent::class;
  protected $cardContentDataType = '';
  /**
   * Required. Orientation of the card.
   *
   * @var string
   */
  public $cardOrientation;
  /**
   * Required if orientation is horizontal. Image preview alignment for
   * standalone cards with horizontal layout.
   *
   * @var string
   */
  public $thumbnailImageAlignment;

  /**
   * Required. Card content.
   *
   * @param GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent $cardContent
   */
  public function setCardContent(GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent $cardContent)
  {
    $this->cardContent = $cardContent;
  }
  /**
   * @return GoogleCloudDialogflowV2beta1IntentMessageRbmCardContent
   */
  public function getCardContent()
  {
    return $this->cardContent;
  }
  /**
   * Required. Orientation of the card.
   *
   * Accepted values: CARD_ORIENTATION_UNSPECIFIED, HORIZONTAL, VERTICAL
   *
   * @param self::CARD_ORIENTATION_* $cardOrientation
   */
  public function setCardOrientation($cardOrientation)
  {
    $this->cardOrientation = $cardOrientation;
  }
  /**
   * @return self::CARD_ORIENTATION_*
   */
  public function getCardOrientation()
  {
    return $this->cardOrientation;
  }
  /**
   * Required if orientation is horizontal. Image preview alignment for
   * standalone cards with horizontal layout.
   *
   * Accepted values: THUMBNAIL_IMAGE_ALIGNMENT_UNSPECIFIED, LEFT, RIGHT
   *
   * @param self::THUMBNAIL_IMAGE_ALIGNMENT_* $thumbnailImageAlignment
   */
  public function setThumbnailImageAlignment($thumbnailImageAlignment)
  {
    $this->thumbnailImageAlignment = $thumbnailImageAlignment;
  }
  /**
   * @return self::THUMBNAIL_IMAGE_ALIGNMENT_*
   */
  public function getThumbnailImageAlignment()
  {
    return $this->thumbnailImageAlignment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2beta1IntentMessageRbmStandaloneCard');
