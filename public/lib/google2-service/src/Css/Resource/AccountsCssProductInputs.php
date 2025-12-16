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

use Google\Service\Css\CssEmpty;
use Google\Service\Css\CssProductInput;

/**
 * The "cssProductInputs" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cssService = new Google\Service\Css(...);
 *   $cssProductInputs = $cssService->accounts_cssProductInputs;
 *  </code>
 */
class AccountsCssProductInputs extends \Google\Service\Resource
{
  /**
   * Deletes a CSS Product input from your CSS Center account. After a delete it
   * may take several minutes until the input is no longer available.
   * (cssProductInputs.delete)
   *
   * @param string $name Required. The name of the CSS product input resource to
   * delete. Format: accounts/{account}/cssProductInputs/{css_product_input},
   * where the last section `css_product_input` consists of 3 parts:
   * contentLanguage~feedLabel~offerId. Example:
   * accounts/123/cssProductInputs/de~DE~rawProvidedId123
   * @param array $optParams Optional parameters.
   *
   * @opt_param string supplementalFeedId The Content API Supplemental Feed ID.
   * The field must not be set if the action applies to a primary feed. If the
   * field is set, then product action applies to a supplemental feed instead of
   * primary Content API feed.
   * @return CssEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], CssEmpty::class);
  }
  /**
   * Uploads a CssProductInput to your CSS Center account. If an input with the
   * same contentLanguage, identity, feedLabel and feedId already exists, this
   * method replaces that entry. After inserting, updating, or deleting a CSS
   * Product input, it may take several minutes before the processed CSS Product
   * can be retrieved. (cssProductInputs.insert)
   *
   * @param string $parent Required. The account where this CSS Product will be
   * inserted. Format: accounts/{account}
   * @param CssProductInput $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string feedId Optional. DEPRECATED. Feed id is not required for
   * CSS Products. The primary or supplemental feed id. If CSS Product already
   * exists and feed id provided is different, then the CSS Product will be moved
   * to a new feed. Note: For now, CSSs do not need to provide feed ids as we
   * create feeds on the fly. We do not have supplemental feed support for CSS
   * Products yet.
   * @return CssProductInput
   * @throws \Google\Service\Exception
   */
  public function insert($parent, CssProductInput $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], CssProductInput::class);
  }
  /**
   * Updates the existing Css Product input in your CSS Center account. After
   * inserting, updating, or deleting a CSS Product input, it may take several
   * minutes before the processed Css Product can be retrieved.
   * (cssProductInputs.patch)
   *
   * @param string $name Identifier. The name of the CSS Product input. Format:
   * `accounts/{account}/cssProductInputs/{css_product_input}`, where the last
   * section `css_product_input` consists of 3 parts:
   * contentLanguage~feedLabel~offerId. Example:
   * accounts/123/cssProductInputs/de~DE~rawProvidedId123
   * @param CssProductInput $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask The list of CSS product attributes to be
   * updated. If the update mask is omitted, then it is treated as implied field
   * mask equivalent to all fields that are populated (have a non-empty value).
   * Attributes specified in the update mask without a value specified in the body
   * will be deleted from the CSS product. Update mask can only be specified for
   * top level fields in attributes and custom attributes. To specify the update
   * mask for custom attributes you need to add the `custom_attribute.` prefix.
   * Providing special "*" value for full CSS product replacement is not
   * supported.
   * @return CssProductInput
   * @throws \Google\Service\Exception
   */
  public function patch($name, CssProductInput $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], CssProductInput::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsCssProductInputs::class, 'Google_Service_Css_Resource_AccountsCssProductInputs');
