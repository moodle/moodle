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

use Google\Service\Css\AccountLabel;
use Google\Service\Css\CssEmpty;
use Google\Service\Css\ListAccountLabelsResponse;

/**
 * The "labels" collection of methods.
 * Typical usage is:
 *  <code>
 *   $cssService = new Google\Service\Css(...);
 *   $labels = $cssService->accounts_labels;
 *  </code>
 */
class AccountsLabels extends \Google\Service\Resource
{
  /**
   * Creates a new label, not assigned to any account. (labels.create)
   *
   * @param string $parent Required. The parent account. Format:
   * accounts/{account}
   * @param AccountLabel $postBody
   * @param array $optParams Optional parameters.
   * @return AccountLabel
   * @throws \Google\Service\Exception
   */
  public function create($parent, AccountLabel $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], AccountLabel::class);
  }
  /**
   * Deletes a label and removes it from all accounts to which it was assigned.
   * (labels.delete)
   *
   * @param string $name Required. The name of the label to delete. Format:
   * accounts/{account}/labels/{label}
   * @param array $optParams Optional parameters.
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
   * Lists the labels owned by an account. (labels.listAccountsLabels)
   *
   * @param string $parent Required. The parent account. Format:
   * accounts/{account}
   * @param array $optParams Optional parameters.
   *
   * @opt_param int pageSize The maximum number of labels to return. The service
   * may return fewer than this value. If unspecified, at most 50 labels will be
   * returned. The maximum value is 1000; values above 1000 will be coerced to
   * 1000.
   * @opt_param string pageToken A page token, received from a previous
   * `ListAccountLabels` call. Provide this to retrieve the subsequent page. When
   * paginating, all other parameters provided to `ListAccountLabels` must match
   * the call that provided the page token.
   * @return ListAccountLabelsResponse
   * @throws \Google\Service\Exception
   */
  public function listAccountsLabels($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListAccountLabelsResponse::class);
  }
  /**
   * Updates a label. (labels.patch)
   *
   * @param string $name Identifier. The resource name of the label. Format:
   * accounts/{account}/labels/{label}
   * @param AccountLabel $postBody
   * @param array $optParams Optional parameters.
   * @return AccountLabel
   * @throws \Google\Service\Exception
   */
  public function patch($name, AccountLabel $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], AccountLabel::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountsLabels::class, 'Google_Service_Css_Resource_AccountsLabels');
