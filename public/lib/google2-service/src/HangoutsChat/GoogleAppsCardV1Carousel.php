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

namespace Google\Service\HangoutsChat;

class GoogleAppsCardV1Carousel extends \Google\Collection
{
  protected $collection_key = 'carouselCards';
  protected $carouselCardsType = GoogleAppsCardV1CarouselCard::class;
  protected $carouselCardsDataType = 'array';

  /**
   * A list of cards included in the carousel.
   *
   * @param GoogleAppsCardV1CarouselCard[] $carouselCards
   */
  public function setCarouselCards($carouselCards)
  {
    $this->carouselCards = $carouselCards;
  }
  /**
   * @return GoogleAppsCardV1CarouselCard[]
   */
  public function getCarouselCards()
  {
    return $this->carouselCards;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Carousel::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Carousel');
