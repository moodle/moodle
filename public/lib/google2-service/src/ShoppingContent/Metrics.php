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

namespace Google\Service\ShoppingContent;

class Metrics extends \Google\Model
{
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Average order size - the average number of items in
   * an order. **This metric cannot be segmented by product dimensions and
   * customer_country_code.**
   *
   * @var 
   */
  public $aos;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Average order value in micros (1 millionth of a
   * standard unit, 1 USD = 1000000 micros) - the average value (total price of
   * items) of all placed orders. The currency of the returned value is stored
   * in the currency_code segment. If this metric is selected,
   * 'segments.currency_code' is automatically added to the SELECT clause in the
   * search query (unless it is explicitly selected by the user) and the
   * currency_code segment is populated in the response. **This metric cannot be
   * segmented by product dimensions and customer_country_code.**
   *
   * @var 
   */
  public $aovMicros;
  /**
   * Number of clicks.
   *
   * @var string
   */
  public $clicks;
  /**
   * Number of conversions divided by the number of clicks, reported on the
   * impression date. The metric is currently available only for the
   * `FREE_PRODUCT_LISTING` program.
   *
   * @var 
   */
  public $conversionRate;
  /**
   * Value of conversions in micros (1 millionth of a standard unit, 1 USD =
   * 1000000 micros) attributed to the product, reported on the conversion date.
   * The metric is currently available only for the `FREE_PRODUCT_LISTING`
   * program. The currency of the returned value is stored in the currency_code
   * segment. If this metric is selected, 'segments.currency_code' is
   * automatically added to the SELECT clause in the search query (unless it is
   * explicitly selected by the user) and the currency_code segment is populated
   * in the response.
   *
   * @var string
   */
  public $conversionValueMicros;
  /**
   * Number of conversions attributed to the product, reported on the conversion
   * date. Depending on the attribution model, a conversion might be distributed
   * across multiple clicks, where each click gets its own credit assigned. This
   * metric is a sum of all such credits. The metric is currently available only
   * for the `FREE_PRODUCT_LISTING` program.
   *
   * @var 
   */
  public $conversions;
  /**
   * Click-through rate - the number of clicks merchant's products receive
   * (clicks) divided by the number of times the products are shown
   * (impressions).
   *
   * @var 
   */
  public $ctr;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Average number of days between an order being
   * placed and the order being fully shipped, reported on the last shipment
   * date. **This metric cannot be segmented by product dimensions and
   * customer_country_code.**
   *
   * @var 
   */
  public $daysToShip;
  /**
   * Number of times merchant's products are shown.
   *
   * @var string
   */
  public $impressions;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Average number of days between an item being
   * ordered and the item being **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @var 
   */
  public $itemDaysToShip;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Percentage of shipped items in relation to all
   * finalized items (shipped or rejected by the merchant; unshipped items are
   * not taken into account), reported on the order date. Item fill rate is
   * lowered by merchant rejections. **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @var 
   */
  public $itemFillRate;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of ordered items in micros (1 millionth
   * of a standard unit, 1 USD = 1000000 micros). Excludes shipping, taxes (US
   * only), and customer cancellations that happened within 30 minutes of
   * placing the order. The currency of the returned value is stored in the
   * currency_code segment. If this metric is selected, 'segments.currency_code'
   * is automatically added to the SELECT clause in the search query (unless it
   * is explicitly selected by the user) and the currency_code segment is
   * populated in the response. **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @var string
   */
  public $orderedItemSalesMicros;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items. Excludes customer
   * cancellations that happened within 30 minutes of placing the order. **This
   * metric cannot be segmented by customer_country_code.**
   *
   * @var string
   */
  public $orderedItems;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of placed orders. Excludes customer
   * cancellations that happened within 30 minutes of placing the order. **This
   * metric cannot be segmented by product dimensions and
   * customer_country_code.**
   *
   * @var string
   */
  public $orders;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items canceled by the merchant,
   * reported on the order date. **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @var string
   */
  public $rejectedItems;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of returned items divided by the total
   * price of shipped items, reported on the order date. If this metric is
   * selected, 'segments.currency_code' is automatically added to the SELECT
   * clause in the search query (unless it is explicitly selected by the user)
   * and the currency_code segment is populated in the response. **This metric
   * cannot be segmented by customer_country_code.**
   *
   * @var 
   */
  public $returnRate;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items sent back for return,
   * reported on the date when the merchant accepted the return. **This metric
   * cannot be segmented by customer_country_code.**
   *
   * @var string
   */
  public $returnedItems;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of ordered items sent back for return
   * in micros (1 millionth of a standard unit, 1 USD = 1000000 micros),
   * reported on the date when the merchant accepted the return. The currency of
   * the returned value is stored in the currency_code segment. If this metric
   * is selected, 'segments.currency_code' is automatically added to the SELECT
   * clause in the search query (unless it is explicitly selected by the user)
   * and the currency_code segment is populated in the response. **This metric
   * cannot be segmented by customer_country_code.**
   *
   * @var string
   */
  public $returnsMicros;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of shipped items in micros (1 millionth
   * of a standard unit, 1 USD = 1000000 micros), reported on the order date.
   * Excludes shipping and taxes (US only). The currency of the returned value
   * is stored in the currency_code segment. If this metric is selected,
   * 'segments.currency_code' is automatically added to the SELECT clause in the
   * search query (unless it is explicitly selected by the user) and the
   * currency_code segment is populated in the response. **This metric cannot be
   * segmented by customer_country_code.**
   *
   * @var string
   */
  public $shippedItemSalesMicros;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of shipped items, reported on the shipment
   * date. **This metric cannot be segmented by customer_country_code.**
   *
   * @var string
   */
  public $shippedItems;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of fully shipped orders, reported on the
   * last shipment date. **This metric cannot be segmented by product dimensions
   * and customer_country_code.**
   *
   * @var string
   */
  public $shippedOrders;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items not shipped up until the
   * end of the queried day. If a multi-day period is specified in the search
   * query, the returned value is the average number of unshipped items over the
   * days in the queried period. **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @var 
   */
  public $unshippedItems;
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of orders not shipped or partially shipped
   * up until the end of the queried day. If a multi-day period is specified in
   * the search query, the returned value is the average number of unshipped
   * orders over the days in the queried period. **This metric cannot be
   * segmented by product dimensions and customer_country_code.**
   *
   * @var 
   */
  public $unshippedOrders;

  public function setAos($aos)
  {
    $this->aos = $aos;
  }
  public function getAos()
  {
    return $this->aos;
  }
  public function setAovMicros($aovMicros)
  {
    $this->aovMicros = $aovMicros;
  }
  public function getAovMicros()
  {
    return $this->aovMicros;
  }
  /**
   * Number of clicks.
   *
   * @param string $clicks
   */
  public function setClicks($clicks)
  {
    $this->clicks = $clicks;
  }
  /**
   * @return string
   */
  public function getClicks()
  {
    return $this->clicks;
  }
  public function setConversionRate($conversionRate)
  {
    $this->conversionRate = $conversionRate;
  }
  public function getConversionRate()
  {
    return $this->conversionRate;
  }
  /**
   * Value of conversions in micros (1 millionth of a standard unit, 1 USD =
   * 1000000 micros) attributed to the product, reported on the conversion date.
   * The metric is currently available only for the `FREE_PRODUCT_LISTING`
   * program. The currency of the returned value is stored in the currency_code
   * segment. If this metric is selected, 'segments.currency_code' is
   * automatically added to the SELECT clause in the search query (unless it is
   * explicitly selected by the user) and the currency_code segment is populated
   * in the response.
   *
   * @param string $conversionValueMicros
   */
  public function setConversionValueMicros($conversionValueMicros)
  {
    $this->conversionValueMicros = $conversionValueMicros;
  }
  /**
   * @return string
   */
  public function getConversionValueMicros()
  {
    return $this->conversionValueMicros;
  }
  public function setConversions($conversions)
  {
    $this->conversions = $conversions;
  }
  public function getConversions()
  {
    return $this->conversions;
  }
  public function setCtr($ctr)
  {
    $this->ctr = $ctr;
  }
  public function getCtr()
  {
    return $this->ctr;
  }
  public function setDaysToShip($daysToShip)
  {
    $this->daysToShip = $daysToShip;
  }
  public function getDaysToShip()
  {
    return $this->daysToShip;
  }
  /**
   * Number of times merchant's products are shown.
   *
   * @param string $impressions
   */
  public function setImpressions($impressions)
  {
    $this->impressions = $impressions;
  }
  /**
   * @return string
   */
  public function getImpressions()
  {
    return $this->impressions;
  }
  public function setItemDaysToShip($itemDaysToShip)
  {
    $this->itemDaysToShip = $itemDaysToShip;
  }
  public function getItemDaysToShip()
  {
    return $this->itemDaysToShip;
  }
  public function setItemFillRate($itemFillRate)
  {
    $this->itemFillRate = $itemFillRate;
  }
  public function getItemFillRate()
  {
    return $this->itemFillRate;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of ordered items in micros (1 millionth
   * of a standard unit, 1 USD = 1000000 micros). Excludes shipping, taxes (US
   * only), and customer cancellations that happened within 30 minutes of
   * placing the order. The currency of the returned value is stored in the
   * currency_code segment. If this metric is selected, 'segments.currency_code'
   * is automatically added to the SELECT clause in the search query (unless it
   * is explicitly selected by the user) and the currency_code segment is
   * populated in the response. **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @param string $orderedItemSalesMicros
   */
  public function setOrderedItemSalesMicros($orderedItemSalesMicros)
  {
    $this->orderedItemSalesMicros = $orderedItemSalesMicros;
  }
  /**
   * @return string
   */
  public function getOrderedItemSalesMicros()
  {
    return $this->orderedItemSalesMicros;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items. Excludes customer
   * cancellations that happened within 30 minutes of placing the order. **This
   * metric cannot be segmented by customer_country_code.**
   *
   * @param string $orderedItems
   */
  public function setOrderedItems($orderedItems)
  {
    $this->orderedItems = $orderedItems;
  }
  /**
   * @return string
   */
  public function getOrderedItems()
  {
    return $this->orderedItems;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of placed orders. Excludes customer
   * cancellations that happened within 30 minutes of placing the order. **This
   * metric cannot be segmented by product dimensions and
   * customer_country_code.**
   *
   * @param string $orders
   */
  public function setOrders($orders)
  {
    $this->orders = $orders;
  }
  /**
   * @return string
   */
  public function getOrders()
  {
    return $this->orders;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items canceled by the merchant,
   * reported on the order date. **This metric cannot be segmented by
   * customer_country_code.**
   *
   * @param string $rejectedItems
   */
  public function setRejectedItems($rejectedItems)
  {
    $this->rejectedItems = $rejectedItems;
  }
  /**
   * @return string
   */
  public function getRejectedItems()
  {
    return $this->rejectedItems;
  }
  public function setReturnRate($returnRate)
  {
    $this->returnRate = $returnRate;
  }
  public function getReturnRate()
  {
    return $this->returnRate;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of ordered items sent back for return,
   * reported on the date when the merchant accepted the return. **This metric
   * cannot be segmented by customer_country_code.**
   *
   * @param string $returnedItems
   */
  public function setReturnedItems($returnedItems)
  {
    $this->returnedItems = $returnedItems;
  }
  /**
   * @return string
   */
  public function getReturnedItems()
  {
    return $this->returnedItems;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of ordered items sent back for return
   * in micros (1 millionth of a standard unit, 1 USD = 1000000 micros),
   * reported on the date when the merchant accepted the return. The currency of
   * the returned value is stored in the currency_code segment. If this metric
   * is selected, 'segments.currency_code' is automatically added to the SELECT
   * clause in the search query (unless it is explicitly selected by the user)
   * and the currency_code segment is populated in the response. **This metric
   * cannot be segmented by customer_country_code.**
   *
   * @param string $returnsMicros
   */
  public function setReturnsMicros($returnsMicros)
  {
    $this->returnsMicros = $returnsMicros;
  }
  /**
   * @return string
   */
  public function getReturnsMicros()
  {
    return $this->returnsMicros;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Total price of shipped items in micros (1 millionth
   * of a standard unit, 1 USD = 1000000 micros), reported on the order date.
   * Excludes shipping and taxes (US only). The currency of the returned value
   * is stored in the currency_code segment. If this metric is selected,
   * 'segments.currency_code' is automatically added to the SELECT clause in the
   * search query (unless it is explicitly selected by the user) and the
   * currency_code segment is populated in the response. **This metric cannot be
   * segmented by customer_country_code.**
   *
   * @param string $shippedItemSalesMicros
   */
  public function setShippedItemSalesMicros($shippedItemSalesMicros)
  {
    $this->shippedItemSalesMicros = $shippedItemSalesMicros;
  }
  /**
   * @return string
   */
  public function getShippedItemSalesMicros()
  {
    return $this->shippedItemSalesMicros;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of shipped items, reported on the shipment
   * date. **This metric cannot be segmented by customer_country_code.**
   *
   * @param string $shippedItems
   */
  public function setShippedItems($shippedItems)
  {
    $this->shippedItems = $shippedItems;
  }
  /**
   * @return string
   */
  public function getShippedItems()
  {
    return $this->shippedItems;
  }
  /**
   * *Deprecated*: This field is no longer supported and retrieving it returns 0
   * starting from May 2024. Number of fully shipped orders, reported on the
   * last shipment date. **This metric cannot be segmented by product dimensions
   * and customer_country_code.**
   *
   * @param string $shippedOrders
   */
  public function setShippedOrders($shippedOrders)
  {
    $this->shippedOrders = $shippedOrders;
  }
  /**
   * @return string
   */
  public function getShippedOrders()
  {
    return $this->shippedOrders;
  }
  public function setUnshippedItems($unshippedItems)
  {
    $this->unshippedItems = $unshippedItems;
  }
  public function getUnshippedItems()
  {
    return $this->unshippedItems;
  }
  public function setUnshippedOrders($unshippedOrders)
  {
    $this->unshippedOrders = $unshippedOrders;
  }
  public function getUnshippedOrders()
  {
    return $this->unshippedOrders;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Metrics::class, 'Google_Service_ShoppingContent_Metrics');
