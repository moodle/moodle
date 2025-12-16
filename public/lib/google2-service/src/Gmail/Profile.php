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

class Profile extends \Google\Model
{
  /**
   * The user's email address.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * The ID of the mailbox's current history record.
   *
   * @var string
   */
  public $historyId;
  /**
   * The total number of messages in the mailbox.
   *
   * @var int
   */
  public $messagesTotal;
  /**
   * The total number of threads in the mailbox.
   *
   * @var int
   */
  public $threadsTotal;

  /**
   * The user's email address.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * The ID of the mailbox's current history record.
   *
   * @param string $historyId
   */
  public function setHistoryId($historyId)
  {
    $this->historyId = $historyId;
  }
  /**
   * @return string
   */
  public function getHistoryId()
  {
    return $this->historyId;
  }
  /**
   * The total number of messages in the mailbox.
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
   * The total number of threads in the mailbox.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Profile::class, 'Google_Service_Gmail_Profile');
