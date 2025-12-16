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

class AddTargetedPublishersRequest extends \Google\Collection
{
  /**
   * Placeholder for undefined targeting mode.
   */
  public const TARGETING_MODE_TARGETING_MODE_UNSPECIFIED = 'TARGETING_MODE_UNSPECIFIED';
  /**
   * The inclusive list type. Inventory must match an item in this list to be
   * targeted.
   */
  public const TARGETING_MODE_INCLUSIVE = 'INCLUSIVE';
  /**
   * The exclusive list type. Inventory must not match any item in this list to
   * be targeted.
   */
  public const TARGETING_MODE_EXCLUSIVE = 'EXCLUSIVE';
  protected $collection_key = 'publisherIds';
  /**
   * A list of publisher IDs to target in the pretargeting configuration. These
   * values will be added to the list of targeted publisher IDs in
   * PretargetingConfig.publisherTargeting.values. Publishers are identified by
   * their publisher ID from ads.txt / app-ads.txt. See
   * https://iabtechlab.com/ads-txt/ and https://iabtechlab.com/app-ads-txt/ for
   * more details.
   *
   * @var string[]
   */
  public $publisherIds;
  /**
   * Required. The targeting mode that should be applied to the list of
   * publisher IDs. If are existing publisher IDs, must be equal to the existing
   * PretargetingConfig.publisherTargeting.targetingMode or a 400 bad request
   * error will be returned.
   *
   * @var string
   */
  public $targetingMode;

  /**
   * A list of publisher IDs to target in the pretargeting configuration. These
   * values will be added to the list of targeted publisher IDs in
   * PretargetingConfig.publisherTargeting.values. Publishers are identified by
   * their publisher ID from ads.txt / app-ads.txt. See
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
  /**
   * Required. The targeting mode that should be applied to the list of
   * publisher IDs. If are existing publisher IDs, must be equal to the existing
   * PretargetingConfig.publisherTargeting.targetingMode or a 400 bad request
   * error will be returned.
   *
   * Accepted values: TARGETING_MODE_UNSPECIFIED, INCLUSIVE, EXCLUSIVE
   *
   * @param self::TARGETING_MODE_* $targetingMode
   */
  public function setTargetingMode($targetingMode)
  {
    $this->targetingMode = $targetingMode;
  }
  /**
   * @return self::TARGETING_MODE_*
   */
  public function getTargetingMode()
  {
    return $this->targetingMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddTargetedPublishersRequest::class, 'Google_Service_RealTimeBidding_AddTargetedPublishersRequest');
