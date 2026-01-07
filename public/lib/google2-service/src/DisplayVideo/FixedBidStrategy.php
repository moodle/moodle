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

namespace Google\Service\DisplayVideo;

class FixedBidStrategy extends \Google\Model
{
  /**
   * The fixed bid amount, in micros of the advertiser's currency. For insertion
   * order entity, bid_amount_micros should be set as 0. For line item entity,
   * bid_amount_micros must be greater than or equal to billable unit of the
   * given currency and smaller than or equal to the upper limit 1000000000. For
   * example, 1500000 represents 1.5 standard units of the currency.
   *
   * @var string
   */
  public $bidAmountMicros;

  /**
   * The fixed bid amount, in micros of the advertiser's currency. For insertion
   * order entity, bid_amount_micros should be set as 0. For line item entity,
   * bid_amount_micros must be greater than or equal to billable unit of the
   * given currency and smaller than or equal to the upper limit 1000000000. For
   * example, 1500000 represents 1.5 standard units of the currency.
   *
   * @param string $bidAmountMicros
   */
  public function setBidAmountMicros($bidAmountMicros)
  {
    $this->bidAmountMicros = $bidAmountMicros;
  }
  /**
   * @return string
   */
  public function getBidAmountMicros()
  {
    return $this->bidAmountMicros;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FixedBidStrategy::class, 'Google_Service_DisplayVideo_FixedBidStrategy');
