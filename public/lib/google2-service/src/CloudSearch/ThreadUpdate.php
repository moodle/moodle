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

class ThreadUpdate extends \Google\Collection
{
  protected $collection_key = 'preState';
  protected $attributeRemovedType = AttributeRemoved::class;
  protected $attributeRemovedDataType = '';
  protected $attributeSetType = AttributeSet::class;
  protected $attributeSetDataType = '';
  protected $labelAddedType = LabelAdded::class;
  protected $labelAddedDataType = '';
  protected $labelRemovedType = LabelRemoved::class;
  protected $labelRemovedDataType = '';
  /**
   * @var string
   */
  public $lastHistoryRecordId;
  protected $messageAddedType = MessageAdded::class;
  protected $messageAddedDataType = '';
  protected $messageDeletedType = MessageDeleted::class;
  protected $messageDeletedDataType = '';
  protected $originalThreadKeyType = MultiKey::class;
  protected $originalThreadKeyDataType = '';
  protected $preStateType = PreState::class;
  protected $preStateDataType = 'array';
  protected $threadKeyType = MultiKey::class;
  protected $threadKeyDataType = '';
  protected $threadKeySetType = ThreadKeySet::class;
  protected $threadKeySetDataType = '';
  /**
   * @var string
   */
  public $threadLocator;
  protected $topicStateUpdateType = TopicStateUpdate::class;
  protected $topicStateUpdateDataType = '';

  /**
   * @param AttributeRemoved
   */
  public function setAttributeRemoved(AttributeRemoved $attributeRemoved)
  {
    $this->attributeRemoved = $attributeRemoved;
  }
  /**
   * @return AttributeRemoved
   */
  public function getAttributeRemoved()
  {
    return $this->attributeRemoved;
  }
  /**
   * @param AttributeSet
   */
  public function setAttributeSet(AttributeSet $attributeSet)
  {
    $this->attributeSet = $attributeSet;
  }
  /**
   * @return AttributeSet
   */
  public function getAttributeSet()
  {
    return $this->attributeSet;
  }
  /**
   * @param LabelAdded
   */
  public function setLabelAdded(LabelAdded $labelAdded)
  {
    $this->labelAdded = $labelAdded;
  }
  /**
   * @return LabelAdded
   */
  public function getLabelAdded()
  {
    return $this->labelAdded;
  }
  /**
   * @param LabelRemoved
   */
  public function setLabelRemoved(LabelRemoved $labelRemoved)
  {
    $this->labelRemoved = $labelRemoved;
  }
  /**
   * @return LabelRemoved
   */
  public function getLabelRemoved()
  {
    return $this->labelRemoved;
  }
  /**
   * @param string
   */
  public function setLastHistoryRecordId($lastHistoryRecordId)
  {
    $this->lastHistoryRecordId = $lastHistoryRecordId;
  }
  /**
   * @return string
   */
  public function getLastHistoryRecordId()
  {
    return $this->lastHistoryRecordId;
  }
  /**
   * @param MessageAdded
   */
  public function setMessageAdded(MessageAdded $messageAdded)
  {
    $this->messageAdded = $messageAdded;
  }
  /**
   * @return MessageAdded
   */
  public function getMessageAdded()
  {
    return $this->messageAdded;
  }
  /**
   * @param MessageDeleted
   */
  public function setMessageDeleted(MessageDeleted $messageDeleted)
  {
    $this->messageDeleted = $messageDeleted;
  }
  /**
   * @return MessageDeleted
   */
  public function getMessageDeleted()
  {
    return $this->messageDeleted;
  }
  /**
   * @param MultiKey
   */
  public function setOriginalThreadKey(MultiKey $originalThreadKey)
  {
    $this->originalThreadKey = $originalThreadKey;
  }
  /**
   * @return MultiKey
   */
  public function getOriginalThreadKey()
  {
    return $this->originalThreadKey;
  }
  /**
   * @param PreState[]
   */
  public function setPreState($preState)
  {
    $this->preState = $preState;
  }
  /**
   * @return PreState[]
   */
  public function getPreState()
  {
    return $this->preState;
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
   * @param ThreadKeySet
   */
  public function setThreadKeySet(ThreadKeySet $threadKeySet)
  {
    $this->threadKeySet = $threadKeySet;
  }
  /**
   * @return ThreadKeySet
   */
  public function getThreadKeySet()
  {
    return $this->threadKeySet;
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
   * @param TopicStateUpdate
   */
  public function setTopicStateUpdate(TopicStateUpdate $topicStateUpdate)
  {
    $this->topicStateUpdate = $topicStateUpdate;
  }
  /**
   * @return TopicStateUpdate
   */
  public function getTopicStateUpdate()
  {
    return $this->topicStateUpdate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ThreadUpdate::class, 'Google_Service_CloudSearch_ThreadUpdate');
