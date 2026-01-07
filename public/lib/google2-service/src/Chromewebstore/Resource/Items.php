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

namespace Google\Service\Chromewebstore\Resource;

use Google\Service\Chromewebstore\Item;
use Google\Service\Chromewebstore\Item2;
use Google\Service\Chromewebstore\PublishRequest;

/**
 * The "items" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromewebstoreService = new Google\Service\Chromewebstore(...);
 *   $items = $chromewebstoreService->items;
 *  </code>
 */
class Items extends \Google\Service\Resource
{
  /**
   * Gets your own Chrome Web Store item. (items.get)
   *
   * @param string $itemId Unique identifier representing the Chrome App, Chrome
   * Extension, or the Chrome Theme.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string projection Determines which subset of the item information
   * to return.
   * @return Item
   * @throws \Google\Service\Exception
   */
  public function get($itemId, $optParams = [])
  {
    $params = ['itemId' => $itemId];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], Item::class);
  }
  /**
   * Inserts a new item. (items.insert)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string publisherEmail The email of the publisher who owns the
   * items. Defaults to the caller's email address.
   * @return Item
   * @throws \Google\Service\Exception
   */
  public function insert($optParams = [])
  {
    $params = [];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Item::class);
  }
  /**
   * Publishes an item. (items.publish)
   *
   * @param string $itemId The ID of the item to publish.
   * @param PublishRequest $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param int deployPercentage The deploy percentage you want to set for
   * your item. Valid values are [0, 100]. If set to any number less than 100,
   * only that many percentage of users will be allowed to get the update.
   * @opt_param string publishTarget Provide defined publishTarget in URL (case
   * sensitive): publishTarget="trustedTesters" or publishTarget="default".
   * Defaults to publishTarget="default".
   * @opt_param bool reviewExemption Optional. The caller request to exempt the
   * review and directly publish because the update is within the list that we can
   * automatically validate. The API will check if the exemption can be granted
   * using real time data.
   * @return Item2
   * @throws \Google\Service\Exception
   */
  public function publish($itemId, PublishRequest $postBody, $optParams = [])
  {
    $params = ['itemId' => $itemId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params], Item2::class);
  }
  /**
   * Updates an existing item. (items.update)
   *
   * @param string $itemId The ID of the item to upload.
   * @param Item $postBody
   * @param array $optParams Optional parameters.
   * @return Item
   * @throws \Google\Service\Exception
   */
  public function update($itemId, Item $postBody, $optParams = [])
  {
    $params = ['itemId' => $itemId, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Item::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Items::class, 'Google_Service_Chromewebstore_Resource_Items');
