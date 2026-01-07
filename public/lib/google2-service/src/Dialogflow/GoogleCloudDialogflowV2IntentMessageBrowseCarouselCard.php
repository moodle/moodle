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

class GoogleCloudDialogflowV2IntentMessageBrowseCarouselCard extends \Google\Collection
{
  /**
   * Fill the gaps between the image and the image container with gray bars.
   */
  public const IMAGE_DISPLAY_OPTIONS_IMAGE_DISPLAY_OPTIONS_UNSPECIFIED = 'IMAGE_DISPLAY_OPTIONS_UNSPECIFIED';
  /**
   * Fill the gaps between the image and the image container with gray bars.
   */
  public const IMAGE_DISPLAY_OPTIONS_GRAY = 'GRAY';
  /**
   * Fill the gaps between the image and the image container with white bars.
   */
  public const IMAGE_DISPLAY_OPTIONS_WHITE = 'WHITE';
  /**
   * Image is scaled such that the image width and height match or exceed the
   * container dimensions. This may crop the top and bottom of the image if the
   * scaled image height is greater than the container height, or crop the left
   * and right of the image if the scaled image width is greater than the
   * container width. This is similar to "Zoom Mode" on a widescreen TV when
   * playing a 4:3 video.
   */
  public const IMAGE_DISPLAY_OPTIONS_CROPPED = 'CROPPED';
  /**
   * Pad the gaps between image and image frame with a blurred copy of the same
   * image.
   */
  public const IMAGE_DISPLAY_OPTIONS_BLURRED_BACKGROUND = 'BLURRED_BACKGROUND';
  protected $collection_key = 'items';
  /**
   * Optional. Settings for displaying the image. Applies to every image in
   * items.
   *
   * @var string
   */
  public $imageDisplayOptions;
  protected $itemsType = GoogleCloudDialogflowV2IntentMessageBrowseCarouselCardBrowseCarouselCardItem::class;
  protected $itemsDataType = 'array';

  /**
   * Optional. Settings for displaying the image. Applies to every image in
   * items.
   *
   * Accepted values: IMAGE_DISPLAY_OPTIONS_UNSPECIFIED, GRAY, WHITE, CROPPED,
   * BLURRED_BACKGROUND
   *
   * @param self::IMAGE_DISPLAY_OPTIONS_* $imageDisplayOptions
   */
  public function setImageDisplayOptions($imageDisplayOptions)
  {
    $this->imageDisplayOptions = $imageDisplayOptions;
  }
  /**
   * @return self::IMAGE_DISPLAY_OPTIONS_*
   */
  public function getImageDisplayOptions()
  {
    return $this->imageDisplayOptions;
  }
  /**
   * Required. List of items in the Browse Carousel Card. Minimum of two items,
   * maximum of ten.
   *
   * @param GoogleCloudDialogflowV2IntentMessageBrowseCarouselCardBrowseCarouselCardItem[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return GoogleCloudDialogflowV2IntentMessageBrowseCarouselCardBrowseCarouselCardItem[]
   */
  public function getItems()
  {
    return $this->items;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowV2IntentMessageBrowseCarouselCard::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowV2IntentMessageBrowseCarouselCard');
