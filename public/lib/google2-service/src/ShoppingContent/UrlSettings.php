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

class UrlSettings extends \Google\Model
{
  /**
   * URL template when the placeholders are expanded will redirect the buyer to
   * the cart page on the merchant website with the selected item in cart.
   *
   * @var string
   */
  public $cartUriTemplate;
  /**
   * URL template when the placeholders are expanded will redirect the buyer to
   * the merchant checkout page with the item in the cart.
   *
   * @var string
   */
  public $checkoutUriTemplate;

  /**
   * URL template when the placeholders are expanded will redirect the buyer to
   * the cart page on the merchant website with the selected item in cart.
   *
   * @param string $cartUriTemplate
   */
  public function setCartUriTemplate($cartUriTemplate)
  {
    $this->cartUriTemplate = $cartUriTemplate;
  }
  /**
   * @return string
   */
  public function getCartUriTemplate()
  {
    return $this->cartUriTemplate;
  }
  /**
   * URL template when the placeholders are expanded will redirect the buyer to
   * the merchant checkout page with the item in the cart.
   *
   * @param string $checkoutUriTemplate
   */
  public function setCheckoutUriTemplate($checkoutUriTemplate)
  {
    $this->checkoutUriTemplate = $checkoutUriTemplate;
  }
  /**
   * @return string
   */
  public function getCheckoutUriTemplate()
  {
    return $this->checkoutUriTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UrlSettings::class, 'Google_Service_ShoppingContent_UrlSettings');
