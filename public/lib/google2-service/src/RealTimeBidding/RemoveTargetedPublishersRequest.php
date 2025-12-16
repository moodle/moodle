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

namespace Google\Service\RealTimeBidding;

class RemoveTargetedPublishersRequest extends \Google\Collection
{
  protected $collection_key = 'publisherIds';
  /**
   * A list of publisher IDs to stop targeting in the pretargeting
   * configuration. These values will be removed from the list of targeted
   * publisher IDs in PretargetingConfig.publisherTargeting.values. Publishers
   * are identified by their publisher ID from ads.txt / app-ads.txt. See
   * https://iabtechlab.com/ads-txt/ and https://iabtechlab.com/app-ads-txt/ for
   * more details.
   *
   * @var string[]
   */
  public $publisherIds;

  /**
   * A list of publisher IDs to stop targeting in the pretargeting
   * configuration. These values will be removed from the list of targeted
   * publisher IDs in PretargetingConfig.publisherTargeting.values. Publishers
   * are identified by their publisher ID from ads.txt / app-ads.txt. See
   * https://iabtechlab.com/ads-txt/ and https://iabtechlab.com/app-ads-txt/ for
   * more details.
   *
   * @param string[] $publisherIds
   */
  public function setPublisherIds($publisherIds)
  {
    $this->publisherIds = $publisherIds;
  }
  /**
   * @return string[]
   */
  public function getPublisherIds()
  {
    return $this->publisherIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemoveTargetedPublishersRequest::class, 'Google_Service_RealTimeBidding_RemoveTargetedPublishersRequest');
