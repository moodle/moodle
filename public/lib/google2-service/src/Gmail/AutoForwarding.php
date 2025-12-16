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

class AutoForwarding extends \Google\Model
{
  /**
   * Unspecified disposition.
   */
  public const DISPOSITION_dispositionUnspecified = 'dispositionUnspecified';
  /**
   * Leave the message in the `INBOX`.
   */
  public const DISPOSITION_leaveInInbox = 'leaveInInbox';
  /**
   * Archive the message.
   */
  public const DISPOSITION_archive = 'archive';
  /**
   * Move the message to the `TRASH`.
   */
  public const DISPOSITION_trash = 'trash';
  /**
   * Leave the message in the `INBOX` and mark it as read.
   */
  public const DISPOSITION_markRead = 'markRead';
  /**
   * The state that a message should be left in after it has been forwarded.
   *
   * @var string
   */
  public $disposition;
  /**
   * Email address to which all incoming messages are forwarded. This email
   * address must be a verified member of the forwarding addresses.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * Whether all incoming mail is automatically forwarded to another address.
   *
   * @var bool
   */
  public $enabled;

  /**
   * The state that a message should be left in after it has been forwarded.
   *
   * Accepted values: dispositionUnspecified, leaveInInbox, archive, trash,
   * markRead
   *
   * @param self::DISPOSITION_* $disposition
   */
  public function setDisposition($disposition)
  {
    $this->disposition = $disposition;
  }
  /**
   * @return self::DISPOSITION_*
   */
  public function getDisposition()
  {
    return $this->disposition;
  }
  /**
   * Email address to which all incoming messages are forwarded. This email
   * address must be a verified member of the forwarding addresses.
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
   * Whether all incoming mail is automatically forwarded to another address.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AutoForwarding::class, 'Google_Service_Gmail_AutoForwarding');
