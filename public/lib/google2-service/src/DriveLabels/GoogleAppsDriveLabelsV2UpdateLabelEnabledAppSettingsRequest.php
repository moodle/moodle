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

class GoogleAppsDriveLabelsV2UpdateLabelEnabledAppSettingsRequest extends \Google\Model
{
  /**
   * Implies the field mask: `name,id,revision_id,label_type,properties.*`
   */
  public const VIEW_LABEL_VIEW_BASIC = 'LABEL_VIEW_BASIC';
  /**
   * All possible fields.
   */
  public const VIEW_LABEL_VIEW_FULL = 'LABEL_VIEW_FULL';
  protected $enabledAppSettingsType = GoogleAppsDriveLabelsV2LabelEnabledAppSettings::class;
  protected $enabledAppSettingsDataType = '';
  /**
   * Optional. The BCP-47 language code to use for evaluating localized field
   * labels. When not specified, values in the default configured language will
   * be used.
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. Set to `true` in order to use the user's admin credentials. The
   * server will verify the user is an admin for the label before allowing
   * access.
   *
   * @var bool
   */
  public $useAdminAccess;
  /**
   * Optional. When specified, only certain fields belonging to the indicated
   * view will be returned.
   *
   * @var string
   */
  public $view;

  /**
   * Required. The new `EnabledAppSettings` value for the label.
   *
   * @param GoogleAppsDriveLabelsV2LabelEnabledAppSettings $enabledAppSettings
   */
  public function setEnabledAppSettings(GoogleAppsDriveLabelsV2LabelEnabledAppSettings $enabledAppSettings)
  {
    $this->enabledAppSettings = $enabledAppSettings;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelEnabledAppSettings
   */
  public function getEnabledAppSettings()
  {
    return $this->enabledAppSettings;
  }
  /**
   * Optional. The BCP-47 language code to use for evaluating localized field
   * labels. When not specified, values in the default configured language will
   * be used.
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
   * Optional. Set to `true` in order to use the user's admin credentials. The
   * server will verify the user is an admin for the label before allowing
   * access.
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
   * Optional. When specified, only certain fields belonging to the indicated
   * view will be returned.
   *
   * Accepted values: LABEL_VIEW_BASIC, LABEL_VIEW_FULL
   *
   * @param self::VIEW_* $view
   */
  public function setView($view)
  {
    $this->view = $view;
  }
  /**
   * @return self::VIEW_*
   */
  public function getView()
  {
    return $this->view;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2UpdateLabelEnabledAppSettingsRequest::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2UpdateLabelEnabledAppSettingsRequest');
