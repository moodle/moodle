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

namespace Google\Service\CloudAsset;

class CreateFeedRequest extends \Google\Model
{
  protected $feedType = Feed::class;
  protected $feedDataType = '';
  /**
   * Required. This is the client-assigned asset feed identifier and it needs to
   * be unique under a specific parent project/folder/organization.
   *
   * @var string
   */
  public $feedId;

  /**
   * Required. The feed details. The field `name` must be empty and it will be
   * generated in the format of: projects/project_number/feeds/feed_id
   * folders/folder_number/feeds/feed_id
   * organizations/organization_number/feeds/feed_id
   *
   * @param Feed $feed
   */
  public function setFeed(Feed $feed)
  {
    $this->feed = $feed;
  }
  /**
   * @return Feed
   */
  public function getFeed()
  {
    return $this->feed;
  }
  /**
   * Required. This is the client-assigned asset feed identifier and it needs to
   * be unique under a specific parent project/folder/organization.
   *
   * @param string $feedId
   */
  public function setFeedId($feedId)
  {
    $this->feedId = $feedId;
  }
  /**
   * @return string
   */
  public function getFeedId()
  {
    return $this->feedId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateFeedRequest::class, 'Google_Service_CloudAsset_CreateFeedRequest');
