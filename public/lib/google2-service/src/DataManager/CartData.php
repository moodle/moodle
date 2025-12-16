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

namespace Google\Service\DataManager;

class CartData extends \Google\Collection
{
  protected $collection_key = 'items';
  protected $itemsType = Item::class;
  protected $itemsDataType = 'array';
  /**
   * Optional. The Merchant Center feed label associated with the feed of the
   * items.
   *
   * @var string
   */
  public $merchantFeedLabel;
  /**
   * Optional. The language code in ISO 639-1 associated with the Merchant
   * Center feed of the items.where your items are uploaded.
   *
   * @var string
   */
  public $merchantFeedLanguageCode;
  /**
   * Optional. The Merchant Center ID associated with the items.
   *
   * @var string
   */
  public $merchantId;
  /**
   * Optional. The sum of all discounts associated with the transaction.
   *
   * @var 
   */
  public $transactionDiscount;

  /**
   * Optional. The list of items associated with the event.
   *
   * @param Item[] $items
   */
  public function setItems($items)
  {
    $this->items = $items;
  }
  /**
   * @return Item[]
   */
  public function getItems()
  {
    return $this->items;
  }
  /**
   * Optional. The Merchant Center feed label associated with the feed of the
   * items.
   *
   * @param string $merchantFeedLabel
   */
  public function setMerchantFeedLabel($merchantFeedLabel)
  {
    $this->merchantFeedLabel = $merchantFeedLabel;
  }
  /**
   * @return string
   */
  public function getMerchantFeedLabel()
  {
    return $this->merchantFeedLabel;
  }
  /**
   * Optional. The language code in ISO 639-1 associated with the Merchant
   * Center feed of the items.where your items are uploaded.
   *
   * @param string $merchantFeedLanguageCode
   */
  public function setMerchantFeedLanguageCode($merchantFeedLanguageCode)
  {
    $this->merchantFeedLanguageCode = $merchantFeedLanguageCode;
  }
  /**
   * @return string
   */
  public function getMerchantFeedLanguageCode()
  {
    return $this->merchantFeedLanguageCode;
  }
  /**
   * Optional. The Merchant Center ID associated with the items.
   *
   * @param string $merchantId
   */
  public function setMerchantId($merchantId)
  {
    $this->merchantId = $merchantId;
  }
  /**
   * @return string
   */
  public function getMerchantId()
  {
    return $this->merchantId;
  }
  public function setTransactionDiscount($transactionDiscount)
  {
    $this->transactionDiscount = $transactionDiscount;
  }
  public function getTransactionDiscount()
  {
    return $this->transactionDiscount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CartData::class, 'Google_Service_DataManager_CartData');
