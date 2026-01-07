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

class DcrExchangeConfig extends \Google\Model
{
  /**
   * Output only. If True, when subscribing to this DCR, it will create only one
   * linked dataset containing all resources shared within the cleanroom. If
   * False, when subscribing to this DCR, it will create 1 linked dataset per
   * listing. This is not configurable, and by default, all new DCRs will have
   * the restriction set to True.
   *
   * @var bool
   */
  public $singleLinkedDatasetPerCleanroom;
  /**
   * Output only. If True, this DCR restricts the contributors to sharing only a
   * single resource in a Listing. And no two resources should have the same
   * IDs. So if a contributor adds a view with a conflicting name, the
   * CreateListing API will reject the request. if False, the data contributor
   * can publish an entire dataset (as before). This is not configurable, and by
   * default, all new DCRs will have the restriction set to True.
   *
   * @var bool
   */
  public $singleSelectedResourceSharingRestriction;

  /**
   * Output only. If True, when subscribing to this DCR, it will create only one
   * linked dataset containing all resources shared within the cleanroom. If
   * False, when subscribing to this DCR, it will create 1 linked dataset per
   * listing. This is not configurable, and by default, all new DCRs will have
   * the restriction set to True.
   *
   * @param bool $singleLinkedDatasetPerCleanroom
   */
  public function setSingleLinkedDatasetPerCleanroom($singleLinkedDatasetPerCleanroom)
  {
    $this->singleLinkedDatasetPerCleanroom = $singleLinkedDatasetPerCleanroom;
  }
  /**
   * @return bool
   */
  public function getSingleLinkedDatasetPerCleanroom()
  {
    return $this->singleLinkedDatasetPerCleanroom;
  }
  /**
   * Output only. If True, this DCR restricts the contributors to sharing only a
   * single resource in a Listing. And no two resources should have the same
   * IDs. So if a contributor adds a view with a conflicting name, the
   * CreateListing API will reject the request. if False, the data contributor
   * can publish an entire dataset (as before). This is not configurable, and by
   * default, all new DCRs will have the restriction set to True.
   *
   * @param bool $singleSelectedResourceSharingRestriction
   */
  public function setSingleSelectedResourceSharingRestriction($singleSelectedResourceSharingRestriction)
  {
    $this->singleSelectedResourceSharingRestriction = $singleSelectedResourceSharingRestriction;
  }
  /**
   * @return bool
   */
  public function getSingleSelectedResourceSharingRestriction()
  {
    return $this->singleSelectedResourceSharingRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DcrExchangeConfig::class, 'Google_Service_AnalyticsHub_DcrExchangeConfig');
