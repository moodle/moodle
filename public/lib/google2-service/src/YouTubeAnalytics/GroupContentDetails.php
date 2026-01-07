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

namespace Google\Service\YouTubeAnalytics;

class GroupContentDetails extends \Google\Model
{
  /**
   * The number of items in the group.
   *
   * @var string
   */
  public $itemCount;
  /**
   * The type of resources that the group contains. Valid values for this
   * property are: * `youtube#channel` * `youtube#playlist` * `youtube#video` *
   * `youtubePartner#asset`
   *
   * @var string
   */
  public $itemType;

  /**
   * The number of items in the group.
   *
   * @param string $itemCount
   */
  public function setItemCount($itemCount)
  {
    $this->itemCount = $itemCount;
  }
  /**
   * @return string
   */
  public function getItemCount()
  {
    return $this->itemCount;
  }
  /**
   * The type of resources that the group contains. Valid values for this
   * property are: * `youtube#channel` * `youtube#playlist` * `youtube#video` *
   * `youtubePartner#asset`
   *
   * @param string $itemType
   */
  public function setItemType($itemType)
  {
    $this->itemType = $itemType;
  }
  /**
   * @return string
   */
  public function getItemType()
  {
    return $this->itemType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GroupContentDetails::class, 'Google_Service_YouTubeAnalytics_GroupContentDetails');
