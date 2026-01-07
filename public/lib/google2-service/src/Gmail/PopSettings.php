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

class PopSettings extends \Google\Model
{
  /**
   * Unspecified range.
   */
  public const ACCESS_WINDOW_accessWindowUnspecified = 'accessWindowUnspecified';
  /**
   * Indicates that no messages are accessible via POP.
   */
  public const ACCESS_WINDOW_disabled = 'disabled';
  /**
   * Indicates that unfetched messages received after some past point in time
   * are accessible via POP.
   */
  public const ACCESS_WINDOW_fromNowOn = 'fromNowOn';
  /**
   * Indicates that all unfetched messages are accessible via POP.
   */
  public const ACCESS_WINDOW_allMail = 'allMail';
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
   * The range of messages which are accessible via POP.
   *
   * @var string
   */
  public $accessWindow;
  /**
   * The action that will be executed on a message after it has been fetched via
   * POP.
   *
   * @var string
   */
  public $disposition;

  /**
   * The range of messages which are accessible via POP.
   *
   * Accepted values: accessWindowUnspecified, disabled, fromNowOn, allMail
   *
   * @param self::ACCESS_WINDOW_* $accessWindow
   */
  public function setAccessWindow($accessWindow)
  {
    $this->accessWindow = $accessWindow;
  }
  /**
   * @return self::ACCESS_WINDOW_*
   */
  public function getAccessWindow()
  {
    return $this->accessWindow;
  }
  /**
   * The action that will be executed on a message after it has been fetched via
   * POP.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PopSettings::class, 'Google_Service_Gmail_PopSettings');
