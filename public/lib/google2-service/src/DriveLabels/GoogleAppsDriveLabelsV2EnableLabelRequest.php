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

class GoogleAppsDriveLabelsV2EnableLabelRequest extends \Google\Model
{
  /**
   * The BCP-47 language code to use for evaluating localized field labels. When
   * not specified, values in the default configured language will be used.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Set to `true` in order to use the user's admin credentials. The server will
   * verify the user is an admin for the label before allowing access.
   *
   * @var bool
   */
  public $useAdminAccess;
  protected $writeControlType = GoogleAppsDriveLabelsV2WriteControl::class;
  protected $writeControlDataType = '';

  /**
   * The BCP-47 language code to use for evaluating localized field labels. When
   * not specified, values in the default configured language will be used.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * Set to `true` in order to use the user's admin credentials. The server will
   * verify the user is an admin for the label before allowing access.
   *
   * @param bool $useAdminAccess
   */
  public function setUseAdminAccess($useAdminAccess)
  {
    $this->useAdminAccess = $useAdminAccess;
  }
  /**
   * @return bool
   */
  public function getUseAdminAccess()
  {
    return $this->useAdminAccess;
  }
  /**
   * Provides control over how write requests are executed. Defaults to unset,
   * which means the last write wins.
   *
   * @param GoogleAppsDriveLabelsV2WriteControl $writeControl
   */
  public function setWriteControl(GoogleAppsDriveLabelsV2WriteControl $writeControl)
  {
    $this->writeControl = $writeControl;
  }
  /**
   * @return GoogleAppsDriveLabelsV2WriteControl
   */
  public function getWriteControl()
  {
    return $this->writeControl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2EnableLabelRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2EnableLabelRequest');
