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

class VolumeSaleInfo extends \Google\Collection
{
  protected $collection_key = 'offers';
  /**
   * URL to purchase this volume on the Google Books site. (In LITE projection)
   *
   * @var string
   */
  public $buyLink;
  /**
   * The two-letter ISO_3166-1 country code for which this sale information is
   * valid. (In LITE projection.)
   *
   * @var string
   */
  public $country;
  /**
   * Whether or not this volume is an eBook (can be added to the My eBooks
   * shelf).
   *
   * @var bool
   */
  public $isEbook;
  protected $listPriceType = VolumeSaleInfoListPrice::class;
  protected $listPriceDataType = '';
  protected $offersType = VolumeSaleInfoOffers::class;
  protected $offersDataType = 'array';
  /**
   * The date on which this book is available for sale.
   *
   * @var string
   */
  public $onSaleDate;
  protected $retailPriceType = VolumeSaleInfoRetailPrice::class;
  protected $retailPriceDataType = '';
  /**
   * Whether or not this book is available for sale or offered for free in the
   * Google eBookstore for the country listed above. Possible values are
   * FOR_SALE, FOR_RENTAL_ONLY, FOR_SALE_AND_RENTAL, FREE, NOT_FOR_SALE, or
   * FOR_PREORDER.
   *
   * @var string
   */
  public $saleability;

  /**
   * URL to purchase this volume on the Google Books site. (In LITE projection)
   *
   * @param string $buyLink
   */
  public function setBuyLink($buyLink)
  {
    $this->buyLink = $buyLink;
  }
  /**
   * @return string
   */
  public function getBuyLink()
  {
    return $this->buyLink;
  }
  /**
   * The two-letter ISO_3166-1 country code for which this sale information is
   * valid. (In LITE projection.)
   *
   * @param string $country
   */
  public function setCountry($country)
  {
    $this->country = $country;
  }
  /**
   * @return string
   */
  public function getCountry()
  {
    return $this->country;
  }
  /**
   * Whether or not this volume is an eBook (can be added to the My eBooks
   * shelf).
   *
   * @param bool $isEbook
   */
  public function setIsEbook($isEbook)
  {
    $this->isEbook = $isEbook;
  }
  /**
   * @return bool
   */
  public function getIsEbook()
  {
    return $this->isEbook;
  }
  /**
   * Suggested retail price. (In LITE projection.)
   *
   * @param VolumeSaleInfoListPrice $listPrice
   */
  public function setListPrice(VolumeSaleInfoListPrice $listPrice)
  {
    $this->listPrice = $listPrice;
  }
  /**
   * @return VolumeSaleInfoListPrice
   */
  public function getListPrice()
  {
    return $this->listPrice;
  }
  /**
   * Offers available for this volume (sales and rentals).
   *
   * @param VolumeSaleInfoOffers[] $offers
   */
  public function setOffers($offers)
  {
    $this->offers = $offers;
  }
  /**
   * @return VolumeSaleInfoOffers[]
   */
  public function getOffers()
  {
    return $this->offers;
  }
  /**
   * The date on which this book is available for sale.
   *
   * @param string $onSaleDate
   */
  public function setOnSaleDate($onSaleDate)
  {
    $this->onSaleDate = $onSaleDate;
  }
  /**
   * @return string
   */
  public function getOnSaleDate()
  {
    return $this->onSaleDate;
  }
  /**
   * The actual selling price of the book. This is the same as the suggested
   * retail or list price unless there are offers or discounts on this volume.
   * (In LITE projection.)
   *
   * @param VolumeSaleInfoRetailPrice $retailPrice
   */
  public function setRetailPrice(VolumeSaleInfoRetailPrice $retailPrice)
  {
    $this->retailPrice = $retailPrice;
  }
  /**
   * @return VolumeSaleInfoRetailPrice
   */
  public function getRetailPrice()
  {
    return $this->retailPrice;
  }
  /**
   * Whether or not this book is available for sale or offered for free in the
   * Google eBookstore for the country listed above. Possible values are
   * FOR_SALE, FOR_RENTAL_ONLY, FOR_SALE_AND_RENTAL, FREE, NOT_FOR_SALE, or
   * FOR_PREORDER.
   *
   * @param string $saleability
   */
  public function setSaleability($saleability)
  {
    $this->saleability = $saleability;
  }
  /**
   * @return string
   */
  public function getSaleability()
  {
    return $this->saleability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeSaleInfo::class, 'Google_Service_Books_VolumeSaleInfo');
