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

namespace Google\Service\Dfareporting;

class DynamicFeedsInsertRequest extends \Google\Model
{
  protected $dynamicFeedType = DynamicFeed::class;
  protected $dynamicFeedDataType = '';
  /**
   * Required. Dynamic profile ID of the inserted dynamic feed.
   *
   * @var string
   */
  public $dynamicProfileId;

  /**
   * Required. Dynamic feed to insert.
   *
   * @param DynamicFeed $dynamicFeed
   */
  public function setDynamicFeed(DynamicFeed $dynamicFeed)
  {
    $this->dynamicFeed = $dynamicFeed;
  }
  /**
   * @return DynamicFeed
   */
  public function getDynamicFeed()
  {
    return $this->dynamicFeed;
  }
  /**
   * Required. Dynamic profile ID of the inserted dynamic feed.
   *
   * @param string $dynamicProfileId
   */
  public function setDynamicProfileId($dynamicProfileId)
  {
    $this->dynamicProfileId = $dynamicProfileId;
  }
  /**
   * @return string
   */
  public function getDynamicProfileId()
  {
    return $this->dynamicProfileId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicFeedsInsertRequest::class, 'Google_Service_Dfareporting_DynamicFeedsInsertRequest');
