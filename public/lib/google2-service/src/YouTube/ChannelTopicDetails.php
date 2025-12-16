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

namespace Google\Service\YouTube;

class ChannelTopicDetails extends \Google\Collection
{
  protected $collection_key = 'topicIds';
  /**
   * A list of Wikipedia URLs that describe the channel's content.
   *
   * @var string[]
   */
  public $topicCategories;
  /**
   * A list of Freebase topic IDs associated with the channel. You can retrieve
   * information about each topic using the Freebase Topic API.
   *
   * @deprecated
   * @var string[]
   */
  public $topicIds;

  /**
   * A list of Wikipedia URLs that describe the channel's content.
   *
   * @param string[] $topicCategories
   */
  public function setTopicCategories($topicCategories)
  {
    $this->topicCategories = $topicCategories;
  }
  /**
   * @return string[]
   */
  public function getTopicCategories()
  {
    return $this->topicCategories;
  }
  /**
   * A list of Freebase topic IDs associated with the channel. You can retrieve
   * information about each topic using the Freebase Topic API.
   *
   * @deprecated
   * @param string[] $topicIds
   */
  public function setTopicIds($topicIds)
  {
    $this->topicIds = $topicIds;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getTopicIds()
  {
    return $this->topicIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChannelTopicDetails::class, 'Google_Service_YouTube_ChannelTopicDetails');
