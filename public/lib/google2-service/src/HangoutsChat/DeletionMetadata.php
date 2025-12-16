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

namespace Google\Service\HangoutsChat;

class DeletionMetadata extends \Google\Model
{
  /**
   * This value is unused.
   */
  public const DELETION_TYPE_DELETION_TYPE_UNSPECIFIED = 'DELETION_TYPE_UNSPECIFIED';
  /**
   * User deleted their own message.
   */
  public const DELETION_TYPE_CREATOR = 'CREATOR';
  /**
   * An owner or manager deleted the message.
   */
  public const DELETION_TYPE_SPACE_OWNER = 'SPACE_OWNER';
  /**
   * A Google Workspace administrator deleted the message. Administrators can
   * delete any message in the space, including messages sent by any space
   * member or Chat app.
   */
  public const DELETION_TYPE_ADMIN = 'ADMIN';
  /**
   * A Chat app deleted its own message when it expired.
   */
  public const DELETION_TYPE_APP_MESSAGE_EXPIRY = 'APP_MESSAGE_EXPIRY';
  /**
   * A Chat app deleted the message on behalf of the creator (using user
   * authentication).
   */
  public const DELETION_TYPE_CREATOR_VIA_APP = 'CREATOR_VIA_APP';
  /**
   * A Chat app deleted the message on behalf of a space manager (using user
   * authentication).
   */
  public const DELETION_TYPE_SPACE_OWNER_VIA_APP = 'SPACE_OWNER_VIA_APP';
  /**
   * A member of the space deleted the message. Users can delete messages sent
   * by apps.
   */
  public const DELETION_TYPE_SPACE_MEMBER = 'SPACE_MEMBER';
  /**
   * Indicates who deleted the message.
   *
   * @var string
   */
  public $deletionType;

  /**
   * Indicates who deleted the message.
   *
   * Accepted values: DELETION_TYPE_UNSPECIFIED, CREATOR, SPACE_OWNER, ADMIN,
   * APP_MESSAGE_EXPIRY, CREATOR_VIA_APP, SPACE_OWNER_VIA_APP, SPACE_MEMBER
   *
   * @param self::DELETION_TYPE_* $deletionType
   */
  public function setDeletionType($deletionType)
  {
    $this->deletionType = $deletionType;
  }
  /**
   * @return self::DELETION_TYPE_*
   */
  public function getDeletionType()
  {
    return $this->deletionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeletionMetadata::class, 'Google_Service_HangoutsChat_DeletionMetadata');
