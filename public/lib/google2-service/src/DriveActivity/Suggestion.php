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

namespace Google\Service\DriveActivity;

class Suggestion extends \Google\Model
{
  /**
   * Subtype not available.
   */
  public const SUBTYPE_SUBTYPE_UNSPECIFIED = 'SUBTYPE_UNSPECIFIED';
  /**
   * A suggestion was added.
   */
  public const SUBTYPE_ADDED = 'ADDED';
  /**
   * A suggestion was deleted.
   */
  public const SUBTYPE_DELETED = 'DELETED';
  /**
   * A suggestion reply was added.
   */
  public const SUBTYPE_REPLY_ADDED = 'REPLY_ADDED';
  /**
   * A suggestion reply was deleted.
   */
  public const SUBTYPE_REPLY_DELETED = 'REPLY_DELETED';
  /**
   * A suggestion was accepted.
   */
  public const SUBTYPE_ACCEPTED = 'ACCEPTED';
  /**
   * A suggestion was rejected.
   */
  public const SUBTYPE_REJECTED = 'REJECTED';
  /**
   * An accepted suggestion was deleted.
   */
  public const SUBTYPE_ACCEPT_DELETED = 'ACCEPT_DELETED';
  /**
   * A rejected suggestion was deleted.
   */
  public const SUBTYPE_REJECT_DELETED = 'REJECT_DELETED';
  /**
   * The sub-type of this event.
   *
   * @var string
   */
  public $subtype;

  /**
   * The sub-type of this event.
   *
   * Accepted values: SUBTYPE_UNSPECIFIED, ADDED, DELETED, REPLY_ADDED,
   * REPLY_DELETED, ACCEPTED, REJECTED, ACCEPT_DELETED, REJECT_DELETED
   *
   * @param self::SUBTYPE_* $subtype
   */
  public function setSubtype($subtype)
  {
    $this->subtype = $subtype;
  }
  /**
   * @return self::SUBTYPE_*
   */
  public function getSubtype()
  {
    return $this->subtype;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Suggestion::class, 'Google_Service_DriveActivity_Suggestion');
