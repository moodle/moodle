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

namespace Google\Service\AndroidEnterprise;

class ProductsGenerateApprovalUrlResponse extends \Google\Model
{
  /**
   * A URL that can be rendered in an iframe to display the permissions (if any)
   * of a product. This URL can be used to approve the product only once and
   * only within 24 hours of being generated, using the Products.approve call.
   * If the product is currently unapproved and has no permissions, this URL
   * will point to an empty page. If the product is currently approved, a URL
   * will only be generated if that product has added permissions since it was
   * last approved, and the URL will only display those new permissions that
   * have not yet been accepted.
   *
   * @var string
   */
  public $url;

  /**
   * A URL that can be rendered in an iframe to display the permissions (if any)
   * of a product. This URL can be used to approve the product only once and
   * only within 24 hours of being generated, using the Products.approve call.
   * If the product is currently unapproved and has no permissions, this URL
   * will point to an empty page. If the product is currently approved, a URL
   * will only be generated if that product has added permissions since it was
   * last approved, and the URL will only display those new permissions that
   * have not yet been accepted.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProductsGenerateApprovalUrlResponse::class, 'Google_Service_AndroidEnterprise_ProductsGenerateApprovalUrlResponse');
