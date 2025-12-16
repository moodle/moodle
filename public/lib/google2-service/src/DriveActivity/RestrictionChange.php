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

class RestrictionChange extends \Google\Model
{
  /**
   * The feature which changed restriction settings was not available.
   */
  public const FEATURE_FEATURE_UNSPECIFIED = 'FEATURE_UNSPECIFIED';
  /**
   * When restricted, this prevents items from being shared outside the domain.
   */
  public const FEATURE_SHARING_OUTSIDE_DOMAIN = 'SHARING_OUTSIDE_DOMAIN';
  /**
   * When restricted, this prevents direct sharing of individual items.
   */
  public const FEATURE_DIRECT_SHARING = 'DIRECT_SHARING';
  /**
   * Deprecated: Use READERS_CAN_DOWNLOAD instead.
   *
   * @deprecated
   */
  public const FEATURE_ITEM_DUPLICATION = 'ITEM_DUPLICATION';
  /**
   * When restricted, this prevents use of Drive File Stream.
   */
  public const FEATURE_DRIVE_FILE_STREAM = 'DRIVE_FILE_STREAM';
  /**
   * When restricted, this limits sharing of folders to managers only.
   */
  public const FEATURE_FILE_ORGANIZER_CAN_SHARE_FOLDERS = 'FILE_ORGANIZER_CAN_SHARE_FOLDERS';
  /**
   * When restricted, this prevents actions like copy, download, and print for
   * readers. Replaces ITEM_DUPLICATION.
   */
  public const FEATURE_READERS_CAN_DOWNLOAD = 'READERS_CAN_DOWNLOAD';
  /**
   * When restricted, this prevents actions like copy, download, and print for
   * writers.
   */
  public const FEATURE_WRITERS_CAN_DOWNLOAD = 'WRITERS_CAN_DOWNLOAD';
  /**
   * The type of restriction is not available.
   */
  public const NEW_RESTRICTION_RESTRICTION_UNSPECIFIED = 'RESTRICTION_UNSPECIFIED';
  /**
   * The feature is available without restriction.
   */
  public const NEW_RESTRICTION_UNRESTRICTED = 'UNRESTRICTED';
  /**
   * The use of this feature is fully restricted.
   */
  public const NEW_RESTRICTION_FULLY_RESTRICTED = 'FULLY_RESTRICTED';
  /**
   * The feature which had a change in restriction policy.
   *
   * @var string
   */
  public $feature;
  /**
   * The restriction in place after the change.
   *
   * @var string
   */
  public $newRestriction;

  /**
   * The feature which had a change in restriction policy.
   *
   * Accepted values: FEATURE_UNSPECIFIED, SHARING_OUTSIDE_DOMAIN,
   * DIRECT_SHARING, ITEM_DUPLICATION, DRIVE_FILE_STREAM,
   * FILE_ORGANIZER_CAN_SHARE_FOLDERS, READERS_CAN_DOWNLOAD,
   * WRITERS_CAN_DOWNLOAD
   *
   * @param self::FEATURE_* $feature
   */
  public function setFeature($feature)
  {
    $this->feature = $feature;
  }
  /**
   * @return self::FEATURE_*
   */
  public function getFeature()
  {
    return $this->feature;
  }
  /**
   * The restriction in place after the change.
   *
   * Accepted values: RESTRICTION_UNSPECIFIED, UNRESTRICTED, FULLY_RESTRICTED
   *
   * @param self::NEW_RESTRICTION_* $newRestriction
   */
  public function setNewRestriction($newRestriction)
  {
    $this->newRestriction = $newRestriction;
  }
  /**
   * @return self::NEW_RESTRICTION_*
   */
  public function getNewRestriction()
  {
    return $this->newRestriction;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RestrictionChange::class, 'Google_Service_DriveActivity_RestrictionChange');
