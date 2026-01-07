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

namespace Google\Service\Advisorynotifications;

class GoogleCloudAdvisorynotificationsV1Text extends \Google\Model
{
  /**
   * Not used.
   */
  public const LOCALIZATION_STATE_LOCALIZATION_STATE_UNSPECIFIED = 'LOCALIZATION_STATE_UNSPECIFIED';
  /**
   * Localization is not applicable for requested language. This can happen
   * when: - The requested language was not supported by Advisory Notifications
   * at the time of localization (including notifications created before the
   * localization feature was launched). - The requested language is English, so
   * only the English text is returned.
   */
  public const LOCALIZATION_STATE_LOCALIZATION_STATE_NOT_APPLICABLE = 'LOCALIZATION_STATE_NOT_APPLICABLE';
  /**
   * Localization for requested language is in progress, and not ready yet.
   */
  public const LOCALIZATION_STATE_LOCALIZATION_STATE_PENDING = 'LOCALIZATION_STATE_PENDING';
  /**
   * Localization for requested language is completed.
   */
  public const LOCALIZATION_STATE_LOCALIZATION_STATE_COMPLETED = 'LOCALIZATION_STATE_COMPLETED';
  /**
   * The English copy.
   *
   * @var string
   */
  public $enText;
  /**
   * Status of the localization.
   *
   * @var string
   */
  public $localizationState;
  /**
   * The requested localized copy (if applicable).
   *
   * @var string
   */
  public $localizedText;

  /**
   * The English copy.
   *
   * @param string $enText
   */
  public function setEnText($enText)
  {
    $this->enText = $enText;
  }
  /**
   * @return string
   */
  public function getEnText()
  {
    return $this->enText;
  }
  /**
   * Status of the localization.
   *
   * Accepted values: LOCALIZATION_STATE_UNSPECIFIED,
   * LOCALIZATION_STATE_NOT_APPLICABLE, LOCALIZATION_STATE_PENDING,
   * LOCALIZATION_STATE_COMPLETED
   *
   * @param self::LOCALIZATION_STATE_* $localizationState
   */
  public function setLocalizationState($localizationState)
  {
    $this->localizationState = $localizationState;
  }
  /**
   * @return self::LOCALIZATION_STATE_*
   */
  public function getLocalizationState()
  {
    return $this->localizationState;
  }
  /**
   * The requested localized copy (if applicable).
   *
   * @param string $localizedText
   */
  public function setLocalizedText($localizedText)
  {
    $this->localizedText = $localizedText;
  }
  /**
   * @return string
   */
  public function getLocalizedText()
  {
    return $this->localizedText;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAdvisorynotificationsV1Text::class, 'Google_Service_Advisorynotifications_GoogleCloudAdvisorynotificationsV1Text');
