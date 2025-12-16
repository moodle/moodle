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

use Google\Service\Chromewebstore\CancelSubmissionRequest;
use Google\Service\Chromewebstore\CancelSubmissionResponse;
use Google\Service\Chromewebstore\FetchItemStatusResponse;
use Google\Service\Chromewebstore\PublishItemRequest;
use Google\Service\Chromewebstore\PublishItemResponse;
use Google\Service\Chromewebstore\SetPublishedDeployPercentageRequest;
use Google\Service\Chromewebstore\SetPublishedDeployPercentageResponse;

/**
 * The "items" collection of methods.
 * Typical usage is:
 *  <code>
 *   $chromewebstoreService = new Google\Service\Chromewebstore(...);
 *   $items = $chromewebstoreService->publishers_items;
 *  </code>
 */
class PublishersItems extends \Google\Service\Resource
{
  /**
   * Cancel the current active submission of an item if present. This can be used
   * to cancel the review of a pending submission. (items.cancelSubmission)
   *
   * @param string $name Required. Name of the item to cancel the submission of in
   * the form `publishers/{publisherId}/items/{itemId}`
   * @param CancelSubmissionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return CancelSubmissionResponse
   * @throws \Google\Service\Exception
   */
  public function cancelSubmission($name, CancelSubmissionRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('cancelSubmission', [$params], CancelSubmissionResponse::class);
  }
  /**
   * Fetch the status of an item. (items.fetchStatus)
   *
   * @param string $name Required. Name of the item to retrieve the status of in
   * the form `publishers/{publisherId}/items/{itemId}`
   * @param array $optParams Optional parameters.
   * @return FetchItemStatusResponse
   * @throws \Google\Service\Exception
   */
  public function fetchStatus($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('fetchStatus', [$params], FetchItemStatusResponse::class);
  }
  /**
   * Submit the item to be published in the store. The item will be submitted for
   * review unless `skip_review` is set to true, or the item is staged from a
   * previous submission with `publish_type` set to `STAGED_PUBLISH`.
   * (items.publish)
   *
   * @param string $name Required. Name of the item in the form
   * `publishers/{publisherId}/items/{itemId}`
   * @param PublishItemRequest $postBody
   * @param array $optParams Optional parameters.
   * @return PublishItemResponse
   * @throws \Google\Service\Exception
   */
  public function publish($name, PublishItemRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('publish', [$params], PublishItemResponse::class);
  }
  /**
   * Set a higher target deploy percentage for the item's published revision. This
   * will be updated without the item being submitted for review. This is only
   * available to items with over 10,000 seven-day active users.
   * (items.setPublishedDeployPercentage)
   *
   * @param string $name Required. Name of the item to update the published
   * revision of in the form `publishers/{publisherId}/items/{itemId}`
   * @param SetPublishedDeployPercentageRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SetPublishedDeployPercentageResponse
   * @throws \Google\Service\Exception
   */
  public function setPublishedDeployPercentage($name, SetPublishedDeployPercentageRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setPublishedDeployPercentage', [$params], SetPublishedDeployPercentageResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PublishersItems::class, 'Google_Service_Chromewebstore_Resource_PublishersItems');
