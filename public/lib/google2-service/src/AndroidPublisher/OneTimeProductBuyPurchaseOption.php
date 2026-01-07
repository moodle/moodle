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

namespace Google\Service\AndroidPublisher;

class OneTimeProductBuyPurchaseOption extends \Google\Model
{
  /**
   * Optional. Whether this purchase option will be available in legacy PBL
   * flows that do not support one-time products model. Up to one "buy" purchase
   * option can be marked as backwards compatible.
   *
   * @var bool
   */
  public $legacyCompatible;
  /**
   * Optional. Whether this purchase option allows multi-quantity. Multi-
   * quantity allows buyer to purchase more than one item in a single checkout.
   *
   * @var bool
   */
  public $multiQuantityEnabled;

  /**
   * Optional. Whether this purchase option will be available in legacy PBL
   * flows that do not support one-time products model. Up to one "buy" purchase
   * option can be marked as backwards compatible.
   *
   * @param bool $legacyCompatible
   */
  public function setLegacyCompatible($legacyCompatible)
  {
    $this->legacyCompatible = $legacyCompatible;
  }
  /**
   * @return bool
   */
  public function getLegacyCompatible()
  {
    return $this->legacyCompatible;
  }
  /**
   * Optional. Whether this purchase option allows multi-quantity. Multi-
   * quantity allows buyer to purchase more than one item in a single checkout.
   *
   * @param bool $multiQuantityEnabled
   */
  public function setMultiQuantityEnabled($multiQuantityEnabled)
  {
    $this->multiQuantityEnabled = $multiQuantityEnabled;
  }
  /**
   * @return bool
   */
  public function getMultiQuantityEnabled()
  {
    return $this->multiQuantityEnabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OneTimeProductBuyPurchaseOption::class, 'Google_Service_AndroidPublisher_OneTimeProductBuyPurchaseOption');
