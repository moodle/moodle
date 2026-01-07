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

class GoogleAppsCardV1UpdateVisibilityAction extends \Google\Model
{
  /**
   * Unspecified visibility. Do not use.
   */
  public const VISIBILITY_VISIBILITY_UNSPECIFIED = 'VISIBILITY_UNSPECIFIED';
  /**
   * The UI element is visible.
   */
  public const VISIBILITY_VISIBLE = 'VISIBLE';
  /**
   * The UI element is hidden.
   */
  public const VISIBILITY_HIDDEN = 'HIDDEN';
  /**
   * The new visibility.
   *
   * @var string
   */
  public $visibility;

  /**
   * The new visibility.
   *
   * Accepted values: VISIBILITY_UNSPECIFIED, VISIBLE, HIDDEN
   *
   * @param self::VISIBILITY_* $visibility
   */
  public function setVisibility($visibility)
  {
    $this->visibility = $visibility;
  }
  /**
   * @return self::VISIBILITY_*
   */
  public function getVisibility()
  {
    return $this->visibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1UpdateVisibilityAction::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1UpdateVisibilityAction');
