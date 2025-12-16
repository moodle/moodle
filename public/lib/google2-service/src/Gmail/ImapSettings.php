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

class ImapSettings extends \Google\Model
{
  /**
   * Unspecified behavior.
   */
  public const EXPUNGE_BEHAVIOR_expungeBehaviorUnspecified = 'expungeBehaviorUnspecified';
  /**
   * Archive messages marked as deleted.
   */
  public const EXPUNGE_BEHAVIOR_archive = 'archive';
  /**
   * Move messages marked as deleted to the trash.
   */
  public const EXPUNGE_BEHAVIOR_trash = 'trash';
  /**
   * Immediately and permanently delete messages marked as deleted. The expunged
   * messages cannot be recovered.
   */
  public const EXPUNGE_BEHAVIOR_deleteForever = 'deleteForever';
  /**
   * If this value is true, Gmail will immediately expunge a message when it is
   * marked as deleted in IMAP. Otherwise, Gmail will wait for an update from
   * the client before expunging messages marked as deleted.
   *
   * @var bool
   */
  public $autoExpunge;
  /**
   * Whether IMAP is enabled for the account.
   *
   * @var bool
   */
  public $enabled;
  /**
   * The action that will be executed on a message when it is marked as deleted
   * and expunged from the last visible IMAP folder.
   *
   * @var string
   */
  public $expungeBehavior;
  /**
   * An optional limit on the number of messages that an IMAP folder may
   * contain. Legal values are 0, 1000, 2000, 5000 or 10000. A value of zero is
   * interpreted to mean that there is no limit.
   *
   * @var int
   */
  public $maxFolderSize;

  /**
   * If this value is true, Gmail will immediately expunge a message when it is
   * marked as deleted in IMAP. Otherwise, Gmail will wait for an update from
   * the client before expunging messages marked as deleted.
   *
   * @param bool $autoExpunge
   */
  public function setAutoExpunge($autoExpunge)
  {
    $this->autoExpunge = $autoExpunge;
  }
  /**
   * @return bool
   */
  public function getAutoExpunge()
  {
    return $this->autoExpunge;
  }
  /**
   * Whether IMAP is enabled for the account.
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
  /**
   * The action that will be executed on a message when it is marked as deleted
   * and expunged from the last visible IMAP folder.
   *
   * Accepted values: expungeBehaviorUnspecified, archive, trash, deleteForever
   *
   * @param self::EXPUNGE_BEHAVIOR_* $expungeBehavior
   */
  public function setExpungeBehavior($expungeBehavior)
  {
    $this->expungeBehavior = $expungeBehavior;
  }
  /**
   * @return self::EXPUNGE_BEHAVIOR_*
   */
  public function getExpungeBehavior()
  {
    return $this->expungeBehavior;
  }
  /**
   * An optional limit on the number of messages that an IMAP folder may
   * contain. Legal values are 0, 1000, 2000, 5000 or 10000. A value of zero is
   * interpreted to mean that there is no limit.
   *
   * @param int $maxFolderSize
   */
  public function setMaxFolderSize($maxFolderSize)
  {
    $this->maxFolderSize = $maxFolderSize;
  }
  /**
   * @return int
   */
  public function getMaxFolderSize()
  {
    return $this->maxFolderSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImapSettings::class, 'Google_Service_Gmail_ImapSettings');
