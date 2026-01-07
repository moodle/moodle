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

namespace Google\Service\Books;

class VolumeSaleInfoOffers extends \Google\Model
{
  /**
   * The finsky offer type (e.g., PURCHASE=0 RENTAL=3)
   *
   * @var int
   */
  public $finskyOfferType;
  /**
   * Indicates whether the offer is giftable.
   *
   * @var bool
   */
  public $giftable;
  protected $listPriceType = VolumeSaleInfoOffersListPrice::class;
  protected $listPriceDataType = '';
  protected $rentalDurationType = VolumeSaleInfoOffersRentalDuration::class;
  protected $rentalDurationDataType = '';
  protected $retailPriceType = VolumeSaleInfoOffersRetailPrice::class;
  protected $retailPriceDataType = '';

  /**
   * The finsky offer type (e.g., PURCHASE=0 RENTAL=3)
   *
   * @param int $finskyOfferType
   */
  public function setFinskyOfferType($finskyOfferType)
  {
    $this->finskyOfferType = $finskyOfferType;
  }
  /**
   * @return int
   */
  public function getFinskyOfferType()
  {
    return $this->finskyOfferType;
  }
  /**
   * Indicates whether the offer is giftable.
   *
   * @param bool $giftable
   */
  public function setGiftable($giftable)
  {
    $this->giftable = $giftable;
  }
  /**
   * @return bool
   */
  public function getGiftable()
  {
    return $this->giftable;
  }
  /**
   * Offer list (=undiscounted) price in Micros.
   *
   * @param VolumeSaleInfoOffersListPrice $listPrice
   */
  public function setListPrice(VolumeSaleInfoOffersListPrice $listPrice)
  {
    $this->listPrice = $listPrice;
  }
  /**
   * @return VolumeSaleInfoOffersListPrice
   */
  public function getListPrice()
  {
    return $this->listPrice;
  }
  /**
   * The rental duration (for rental offers only).
   *
   * @param VolumeSaleInfoOffersRentalDuration $rentalDuration
   */
  public function setRentalDuration(VolumeSaleInfoOffersRentalDuration $rentalDuration)
  {
    $this->rentalDuration = $rentalDuration;
  }
  /**
   * @return VolumeSaleInfoOffersRentalDuration
   */
  public function getRentalDuration()
  {
    return $this->rentalDuration;
  }
  /**
   * Offer retail (=discounted) price in Micros
   *
   * @param VolumeSaleInfoOffersRetailPrice $retailPrice
   */
  public function setRetailPrice(VolumeSaleInfoOffersRetailPrice $retailPrice)
  {
    $this->retailPrice = $retailPrice;
  }
  /**
   * @return VolumeSaleInfoOffersRetailPrice
   */
  public function getRetailPrice()
  {
    return $this->retailPrice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeSaleInfoOffers::class, 'Google_Service_Books_VolumeSaleInfoOffers');
