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

class FuseboxItem extends \Google\Model
{
  protected $attributesType = Attributes::class;
  protected $attributesDataType = '';
  /**
   * @var string
   */
  public $creationTimeMicroseconds;
  protected $historyType = History::class;
  protected $historyDataType = '';
  protected $itemKeyType = MultiKey::class;
  protected $itemKeyDataType = '';
  protected $labelsType = Labels::class;
  protected $labelsDataType = '';
  /**
   * @var string
   */
  public $lastModificationTimeUs;
  protected $lockerReferencesType = References::class;
  protected $lockerReferencesDataType = '';
  protected $matchInfoType = MatchInfo::class;
  protected $matchInfoDataType = '';
  protected $partsType = ItemParts::class;
  protected $partsDataType = '';
  /**
   * @var string
   */
  public $readTs;
  protected $referencesType = References::class;
  protected $referencesDataType = '';
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
  protected $triggersType = Triggers::class;
  protected $triggersDataType = '';
  /**
   * @var string
   */
  public $version;

  /**
   * @param Attributes
   */
  public function setAttributes(Attributes $attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return Attributes
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * @param string
   */
  public function setCreationTimeMicroseconds($creationTimeMicroseconds)
  {
    $this->creationTimeMicroseconds = $creationTimeMicroseconds;
  }
  /**
   * @return string
   */
  public function getCreationTimeMicroseconds()
  {
    return $this->creationTimeMicroseconds;
  }
  /**
   * @param History
   */
  public function setHistory(History $history)
  {
    $this->history = $history;
  }
  /**
   * @return History
   */
  public function getHistory()
  {
    return $this->history;
  }
  /**
   * @param MultiKey
   */
  public function setItemKey(MultiKey $itemKey)
  {
    $this->itemKey = $itemKey;
  }
  /**
   * @return MultiKey
   */
  public function getItemKey()
  {
    return $this->itemKey;
  }
  /**
   * @param Labels
   */
  public function setLabels(Labels $labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return Labels
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * @param string
   */
  public function setLastModificationTimeUs($lastModificationTimeUs)
  {
    $this->lastModificationTimeUs = $lastModificationTimeUs;
  }
  /**
   * @return string
   */
  public function getLastModificationTimeUs()
  {
    return $this->lastModificationTimeUs;
  }
  /**
   * @param References
   */
  public function setLockerReferences(References $lockerReferences)
  {
    $this->lockerReferences = $lockerReferences;
  }
  /**
   * @return References
   */
  public function getLockerReferences()
  {
    return $this->lockerReferences;
  }
  /**
   * @param MatchInfo
   */
  public function setMatchInfo(MatchInfo $matchInfo)
  {
    $this->matchInfo = $matchInfo;
  }
  /**
   * @return MatchInfo
   */
  public function getMatchInfo()
  {
    return $this->matchInfo;
  }
  /**
   * @param ItemParts
   */
  public function setParts(ItemParts $parts)
  {
    $this->parts = $parts;
  }
  /**
   * @return ItemParts
   */
  public function getParts()
  {
    return $this->parts;
  }
  /**
   * @param string
   */
  public function setReadTs($readTs)
  {
    $this->readTs = $readTs;
  }
  /**
   * @return string
   */
  public function getReadTs()
  {
    return $this->readTs;
  }
  /**
   * @param References
   */
  public function setReferences(References $references)
  {
    $this->references = $references;
  }
  /**
   * @return References
   */
  public function getReferences()
  {
    return $this->references;
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
   * @param Triggers
   */
  public function setTriggers(Triggers $triggers)
  {
    $this->triggers = $triggers;
  }
  /**
   * @return Triggers
   */
  public function getTriggers()
  {
    return $this->triggers;
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
class_alias(FuseboxItem::class, 'Google_Service_CloudSearch_FuseboxItem');
