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

namespace Google\Service\YouTube;

class PlaylistItemStatus extends \Google\Model
{
  public const PRIVACY_STATUS_public = 'public';
  public const PRIVACY_STATUS_unlisted = 'unlisted';
  public const PRIVACY_STATUS_private = 'private';
  /**
   * This resource's privacy status.
   *
   * @var string
   */
  public $privacyStatus;

  /**
   * This resource's privacy status.
   *
   * Accepted values: public, unlisted, private
   *
   * @param self::PRIVACY_STATUS_* $privacyStatus
   */
  public function setPrivacyStatus($privacyStatus)
  {
    $this->privacyStatus = $privacyStatus;
  }
  /**
   * @return self::PRIVACY_STATUS_*
   */
  public function getPrivacyStatus()
  {
    return $this->privacyStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlaylistItemStatus::class, 'Google_Service_YouTube_PlaylistItemStatus');
