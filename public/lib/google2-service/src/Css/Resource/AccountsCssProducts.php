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

namespace Google\Service\Css\Resource;

use Google\Service\Css\CssProduct;
use Google\Service\Css\ListCssProductsResponse;

/**
 * The "cssProducts" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cssService = new Google\Service\Css(...);
 *   $cssProducts = $cssService->accounts_cssProducts;
 *  </code>
 */
class AccountsCssProducts extends \Google\Service\Resource
{
  /**
   * Retrieves the processed CSS Product from your CSS Center account. After
   * inserting, updating, or deleting a product input, it may take several minutes
   * before the updated final product can be retrieved. (cssProducts.get)
   *
   * @param string $name Required. The name of the CSS product to retrieve.
   * Format: `accounts/{account}/cssProducts/{css_product}`
   * @param array $optParams Optional parameters.
   * @return CssProduct
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], CssProduct::class);
  }
  /**
   * Lists the processed CSS Products in your CSS Center account. The response
   * might contain fewer items than specified by pageSize. Rely on pageToken to
   * determine if there are more items to be requested. After inserting, updating,
   * or deleting a CSS product input, it may take several minutes before the
   * updated processed CSS product can be retrieved.
   * (cssProducts.listAccountsCssProducts)
   *
   * @param string $parent Required. The account/domain to list processed CSS
   * Products for. Format: accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of CSS Products to return. The
   * service may return fewer than this value. The maximum value is 1000; values
   * above 1000 will be coerced to 1000. If unspecified, the maximum number of CSS
   * products will be returned.
   * @opt_param string pageToken A page token, received from a previous
   * `ListCssProducts` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListCssProducts` must match the
   * call that provided the page token.
   * @return ListCssProductsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsCssProducts($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListCssProductsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsCssProducts::class, 'Google_Service_Css_Resource_AccountsCssProducts');
