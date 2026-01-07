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

namespace Google\Service\PlayGrouping\Resource;

use Google\Service\PlayGrouping\CreateOrUpdateTagsRequest;
use Google\Service\PlayGrouping\CreateOrUpdateTagsResponse;

/**
 * The "tags" collection of methods.
 * Typical usage is:
 *  <code>
 *   $playgroupingService = new Google\Service\PlayGrouping(...);
 *   $tags = $playgroupingService->apps_tokens_tags;
 *  </code>
 */
class AppsTokensTags extends \Google\Service\Resource
{
  /**
   * Create or update tags for the user and app that are represented by the given
   * token. (tags.createOrUpdate)
   *
   * @param string $appPackage Required. App whose tags are being manipulated.
   * Format: apps/{package_name}
   * @param string $token Required. Token for which the tags are being inserted or
   * updated. Format: tokens/{token}
   * @param CreateOrUpdateTagsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CreateOrUpdateTagsResponse
   * @throws \Google\Service\Exception
   */
  public function createOrUpdate($appPackage, $token, CreateOrUpdateTagsRequest $postBody, $optParams = [])
  {
    $params = ['appPackage' => $appPackage, 'token' => $token, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('createOrUpdate', [$params], CreateOrUpdateTagsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsTokensTags::class, 'Google_Service_PlayGrouping_Resource_AppsTokensTags');
