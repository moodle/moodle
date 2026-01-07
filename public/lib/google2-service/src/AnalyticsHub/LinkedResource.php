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

namespace Google\Service\AnalyticsHub;

class LinkedResource extends \Google\Model
{
  /**
   * Output only. Name of the linked dataset, e.g.
   * projects/subscriberproject/datasets/linked_dataset
   *
   * @var string
   */
  public $linkedDataset;
  /**
   * Output only. Name of the Pub/Sub subscription, e.g.
   * projects/subscriberproject/subscriptions/subscriptions/sub_id
   *
   * @var string
   */
  public $linkedPubsubSubscription;
  /**
   * Output only. Listing for which linked resource is created.
   *
   * @var string
   */
  public $listing;

  /**
   * Output only. Name of the linked dataset, e.g.
   * projects/subscriberproject/datasets/linked_dataset
   *
   * @param string $linkedDataset
   */
  public function setLinkedDataset($linkedDataset)
  {
    $this->linkedDataset = $linkedDataset;
  }
  /**
   * @return string
   */
  public function getLinkedDataset()
  {
    return $this->linkedDataset;
  }
  /**
   * Output only. Name of the Pub/Sub subscription, e.g.
   * projects/subscriberproject/subscriptions/subscriptions/sub_id
   *
   * @param string $linkedPubsubSubscription
   */
  public function setLinkedPubsubSubscription($linkedPubsubSubscription)
  {
    $this->linkedPubsubSubscription = $linkedPubsubSubscription;
  }
  /**
   * @return string
   */
  public function getLinkedPubsubSubscription()
  {
    return $this->linkedPubsubSubscription;
  }
  /**
   * Output only. Listing for which linked resource is created.
   *
   * @param string $listing
   */
  public function setListing($listing)
  {
    $this->listing = $listing;
  }
  /**
   * @return string
   */
  public function getListing()
  {
    return $this->listing;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LinkedResource::class, 'Google_Service_AnalyticsHub_LinkedResource');
