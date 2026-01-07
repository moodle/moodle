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

namespace Google\Service\CloudSearch;

class ItemThread extends \Google\Collection
{
  protected $collection_key = 'item';
  protected $clusterInfoType = ClusterInfo::class;
  protected $clusterInfoDataType = '';
  protected $itemType = FuseboxItem::class;
  protected $itemDataType = 'array';
  /**
   * @var string
   */
  public $lastItemId;
  protected $matchInfoType = FuseboxItemThreadMatchInfo::class;
  protected $matchInfoDataType = '';
  /**
   * @var string
   */
  public $snippet;
  protected $threadKeyType = MultiKey::class;
  protected $threadKeyDataType = '';
  /**
   * @var string
   */
  public $threadLocator;
  protected $topicStateType = TopicState::class;
  protected $topicStateDataType = '';
  /**
   * @var string
   */
  public $version;

  /**
   * @param ClusterInfo
   */
  public function setClusterInfo(ClusterInfo $clusterInfo)
  {
    $this->clusterInfo = $clusterInfo;
  }
  /**
   * @return ClusterInfo
   */
  public function getClusterInfo()
  {
    return $this->clusterInfo;
  }
  /**
   * @param FuseboxItem[]
   */
  public function setItem($item)
  {
    $this->item = $item;
  }
  /**
   * @return FuseboxItem[]
   */
  public function getItem()
  {
    return $this->item;
  }
  /**
   * @param string
   */
  public function setLastItemId($lastItemId)
  {
    $this->lastItemId = $lastItemId;
  }
  /**
   * @return string
   */
  public function getLastItemId()
  {
    return $this->lastItemId;
  }
  /**
   * @param FuseboxItemThreadMatchInfo
   */
  public function setMatchInfo(FuseboxItemThreadMatchInfo $matchInfo)
  {
    $this->matchInfo = $matchInfo;
  }
  /**
   * @return FuseboxItemThreadMatchInfo
   */
  public function getMatchInfo()
  {
    return $this->matchInfo;
  }
  /**
   * @param string
   */
  public function setSnippet($snippet)
  {
    $this->snippet = $snippet;
  }
  /**
   * @return string
   */
  public function getSnippet()
  {
    return $this->snippet;
  }
  /**
   * @param MultiKey
   */
  public function setThreadKey(MultiKey $threadKey)
  {
    $this->threadKey = $threadKey;
  }
  /**
   * @return MultiKey
   */
  public function getThreadKey()
  {
    return $this->threadKey;
  }
  /**
   * @param string
   */
  public function setThreadLocator($threadLocator)
  {
    $this->threadLocator = $threadLocator;
  }
  /**
   * @return string
   */
  public function getThreadLocator()
  {
    return $this->threadLocator;
  }
  /**
   * @param TopicState
   */
  public function setTopicState(TopicState $topicState)
  {
    $this->topicState = $topicState;
  }
  /**
   * @return TopicState
   */
  public function getTopicState()
  {
    return $this->topicState;
  }
  /**
   * @param string
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemThread::class, 'Google_Service_CloudSearch_ItemThread');
