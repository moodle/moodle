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

class Label extends \Google\Model
{
  /**
   * Show the label in the label list.
   */
  public const LABEL_LIST_VISIBILITY_labelShow = 'labelShow';
  /**
   * Show the label if there are any unread messages with that label.
   */
  public const LABEL_LIST_VISIBILITY_labelShowIfUnread = 'labelShowIfUnread';
  /**
   * Do not show the label in the label list.
   */
  public const LABEL_LIST_VISIBILITY_labelHide = 'labelHide';
  /**
   * Show the label in the message list.
   */
  public const MESSAGE_LIST_VISIBILITY_show = 'show';
  /**
   * Do not show the label in the message list.
   */
  public const MESSAGE_LIST_VISIBILITY_hide = 'hide';
  /**
   * Labels created by Gmail.
   */
  public const TYPE_system = 'system';
  /**
   * Custom labels created by the user or application.
   */
  public const TYPE_user = 'user';
  protected $colorType = LabelColor::class;
  protected $colorDataType = '';
  /**
   * The immutable ID of the label.
   *
   * @var string
   */
  public $id;
  /**
   * The visibility of the label in the label list in the Gmail web interface.
   *
   * @var string
   */
  public $labelListVisibility;
  /**
   * The visibility of messages with this label in the message list in the Gmail
   * web interface.
   *
   * @var string
   */
  public $messageListVisibility;
  /**
   * The total number of messages with the label.
   *
   * @var int
   */
  public $messagesTotal;
  /**
   * The number of unread messages with the label.
   *
   * @var int
   */
  public $messagesUnread;
  /**
   * The display name of the label.
   *
   * @var string
   */
  public $name;
  /**
   * The total number of threads with the label.
   *
   * @var int
   */
  public $threadsTotal;
  /**
   * The number of unread threads with the label.
   *
   * @var int
   */
  public $threadsUnread;
  /**
   * The owner type for the label. User labels are created by the user and can
   * be modified and deleted by the user and can be applied to any message or
   * thread. System labels are internally created and cannot be added, modified,
   * or deleted. System labels may be able to be applied to or removed from
   * messages and threads under some circumstances but this is not guaranteed.
   * For example, users can apply and remove the `INBOX` and `UNREAD` labels
   * from messages and threads, but cannot apply or remove the `DRAFTS` or
   * `SENT` labels from messages or threads.
   *
   * @var string
   */
  public $type;

  /**
   * The color to assign to the label. Color is only available for labels that
   * have their `type` set to `user`.
   *
   * @param LabelColor $color
   */
  public function setColor(LabelColor $color)
  {
    $this->color = $color;
  }
  /**
   * @return LabelColor
   */
  public function getColor()
  {
    return $this->color;
  }
  /**
   * The immutable ID of the label.
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
   * The visibility of the label in the label list in the Gmail web interface.
   *
   * Accepted values: labelShow, labelShowIfUnread, labelHide
   *
   * @param self::LABEL_LIST_VISIBILITY_* $labelListVisibility
   */
  public function setLabelListVisibility($labelListVisibility)
  {
    $this->labelListVisibility = $labelListVisibility;
  }
  /**
   * @return self::LABEL_LIST_VISIBILITY_*
   */
  public function getLabelListVisibility()
  {
    return $this->labelListVisibility;
  }
  /**
   * The visibility of messages with this label in the message list in the Gmail
   * web interface.
   *
   * Accepted values: show, hide
   *
   * @param self::MESSAGE_LIST_VISIBILITY_* $messageListVisibility
   */
  public function setMessageListVisibility($messageListVisibility)
  {
    $this->messageListVisibility = $messageListVisibility;
  }
  /**
   * @return self::MESSAGE_LIST_VISIBILITY_*
   */
  public function getMessageListVisibility()
  {
    return $this->messageListVisibility;
  }
  /**
   * The total number of messages with the label.
   *
   * @param int $messagesTotal
   */
  public function setMessagesTotal($messagesTotal)
  {
    $this->messagesTotal = $messagesTotal;
  }
  /**
   * @return int
   */
  public function getMessagesTotal()
  {
    return $this->messagesTotal;
  }
  /**
   * The number of unread messages with the label.
   *
   * @param int $messagesUnread
   */
  public function setMessagesUnread($messagesUnread)
  {
    $this->messagesUnread = $messagesUnread;
  }
  /**
   * @return int
   */
  public function getMessagesUnread()
  {
    return $this->messagesUnread;
  }
  /**
   * The display name of the label.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The total number of threads with the label.
   *
   * @param int $threadsTotal
   */
  public function setThreadsTotal($threadsTotal)
  {
    $this->threadsTotal = $threadsTotal;
  }
  /**
   * @return int
   */
  public function getThreadsTotal()
  {
    return $this->threadsTotal;
  }
  /**
   * The number of unread threads with the label.
   *
   * @param int $threadsUnread
   */
  public function setThreadsUnread($threadsUnread)
  {
    $this->threadsUnread = $threadsUnread;
  }
  /**
   * @return int
   */
  public function getThreadsUnread()
  {
    return $this->threadsUnread;
  }
  /**
   * The owner type for the label. User labels are created by the user and can
   * be modified and deleted by the user and can be applied to any message or
   * thread. System labels are internally created and cannot be added, modified,
   * or deleted. System labels may be able to be applied to or removed from
   * messages and threads under some circumstances but this is not guaranteed.
   * For example, users can apply and remove the `INBOX` and `UNREAD` labels
   * from messages and threads, but cannot apply or remove the `DRAFTS` or
   * `SENT` labels from messages or threads.
   *
   * Accepted values: system, user
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Label::class, 'Google_Service_Gmail_Label');
