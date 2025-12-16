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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy extends \Google\Model
{
  /**
   * Copy mode unspecified.
   */
  public const COPY_MODE_COPY_MODE_UNSPECIFIED = 'COPY_MODE_UNSPECIFIED';
  /**
   * The applied label and field values aren't copied by default when the Drive
   * item it's applied to is copied.
   */
  public const COPY_MODE_DO_NOT_COPY = 'DO_NOT_COPY';
  /**
   * The applied label and field values are always copied when the Drive item
   * it's applied to is copied. Only admins can use this mode.
   */
  public const COPY_MODE_ALWAYS_COPY = 'ALWAYS_COPY';
  /**
   * The applied label and field values are copied if the label is appliable by
   * the user making the copy.
   */
  public const COPY_MODE_COPY_APPLIABLE = 'COPY_APPLIABLE';
  /**
   * Indicates how the applied label and field values should be copied when a
   * Drive item is copied.
   *
   * @var string
   */
  public $copyMode;

  /**
   * Indicates how the applied label and field values should be copied when a
   * Drive item is copied.
   *
   * Accepted values: COPY_MODE_UNSPECIFIED, DO_NOT_COPY, ALWAYS_COPY,
   * COPY_APPLIABLE
   *
   * @param self::COPY_MODE_* $copyMode
   */
  public function setCopyMode($copyMode)
  {
    $this->copyMode = $copyMode;
  }
  /**
   * @return self::COPY_MODE_*
   */
  public function getCopyMode()
  {
    return $this->copyMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy');
