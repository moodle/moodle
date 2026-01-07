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

namespace Google\Service\Gmail;

class History extends \Google\Collection
{
  protected $collection_key = 'messagesDeleted';
  /**
   * The mailbox sequence ID.
   *
   * @var string
   */
  public $id;
  protected $labelsAddedType = HistoryLabelAdded::class;
  protected $labelsAddedDataType = 'array';
  protected $labelsRemovedType = HistoryLabelRemoved::class;
  protected $labelsRemovedDataType = 'array';
  protected $messagesType = Message::class;
  protected $messagesDataType = 'array';
  protected $messagesAddedType = HistoryMessageAdded::class;
  protected $messagesAddedDataType = 'array';
  protected $messagesDeletedType = HistoryMessageDeleted::class;
  protected $messagesDeletedDataType = 'array';

  /**
   * The mailbox sequence ID.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Labels added to messages in this history record.
   *
   * @param HistoryLabelAdded[] $labelsAdded
   */
  public function setLabelsAdded($labelsAdded)
  {
    $this->labelsAdded = $labelsAdded;
  }
  /**
   * @return HistoryLabelAdded[]
   */
  public function getLabelsAdded()
  {
    return $this->labelsAdded;
  }
  /**
   * Labels removed from messages in this history record.
   *
   * @param HistoryLabelRemoved[] $labelsRemoved
   */
  public function setLabelsRemoved($labelsRemoved)
  {
    $this->labelsRemoved = $labelsRemoved;
  }
  /**
   * @return HistoryLabelRemoved[]
   */
  public function getLabelsRemoved()
  {
    return $this->labelsRemoved;
  }
  /**
   * List of messages changed in this history record. The fields for specific
   * change types, such as `messagesAdded` may duplicate messages in this field.
   * We recommend using the specific change-type fields instead of this.
   *
   * @param Message[] $messages
   */
  public function setMessages($messages)
  {
    $this->messages = $messages;
  }
  /**
   * @return Message[]
   */
  public function getMessages()
  {
    return $this->messages;
  }
  /**
   * Messages added to the mailbox in this history record.
   *
   * @param HistoryMessageAdded[] $messagesAdded
   */
  public function setMessagesAdded($messagesAdded)
  {
    $this->messagesAdded = $messagesAdded;
  }
  /**
   * @return HistoryMessageAdded[]
   */
  public function getMessagesAdded()
  {
    return $this->messagesAdded;
  }
  /**
   * Messages deleted (not Trashed) from the mailbox in this history record.
   *
   * @param HistoryMessageDeleted[] $messagesDeleted
   */
  public function setMessagesDeleted($messagesDeleted)
  {
    $this->messagesDeleted = $messagesDeleted;
  }
  /**
   * @return HistoryMessageDeleted[]
   */
  public function getMessagesDeleted()
  {
    return $this->messagesDeleted;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(History::class, 'Google_Service_Gmail_History');
