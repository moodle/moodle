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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings extends \Google\Model
{
  /**
   * Default, unspecified setting. This is the same as disabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_UNSPECIFIED = 'WEB_GROUNDING_TYPE_UNSPECIFIED';
  /**
   * Web grounding is disabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_DISABLED = 'WEB_GROUNDING_TYPE_DISABLED';
  /**
   * Grounding with Google Search is enabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_GOOGLE_SEARCH = 'WEB_GROUNDING_TYPE_GOOGLE_SEARCH';
  /**
   * Grounding with Enterprise Web Search is enabled.
   */
  public const WEB_GROUNDING_TYPE_WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH = 'WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH';
  /**
   * Output only. This field controls the default web grounding toggle for end
   * users if `web_grounding_type` is set to `WEB_GROUNDING_TYPE_GOOGLE_SEARCH`
   * or `WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH`. By default, this field is
   * set to false. If `web_grounding_type` is `WEB_GROUNDING_TYPE_GOOGLE_SEARCH`
   * or `WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH`, end users will have web
   * grounding enabled by default on UI. If true, grounding toggle will be
   * disabled by default on UI. End users can still enable web grounding in the
   * UI if web grounding is enabled.
   *
   * @var bool
   */
  public $defaultWebGroundingToggleOff;
  /**
   * Optional. Output only. Whether to disable user location context.
   *
   * @var bool
   */
  public $disableLocationContext;
  /**
   * Whether or not the Google search grounding toggle is shown. Deprecated. Use
   * web_grounding_type instead.
   *
   * @deprecated
   * @var bool
   */
  public $googleSearchGroundingEnabled;
  /**
   * Optional. The type of web grounding to use.
   *
   * @var string
   */
  public $webGroundingType;

  /**
   * Output only. This field controls the default web grounding toggle for end
   * users if `web_grounding_type` is set to `WEB_GROUNDING_TYPE_GOOGLE_SEARCH`
   * or `WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH`. By default, this field is
   * set to false. If `web_grounding_type` is `WEB_GROUNDING_TYPE_GOOGLE_SEARCH`
   * or `WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH`, end users will have web
   * grounding enabled by default on UI. If true, grounding toggle will be
   * disabled by default on UI. End users can still enable web grounding in the
   * UI if web grounding is enabled.
   *
   * @param bool $defaultWebGroundingToggleOff
   */
  public function setDefaultWebGroundingToggleOff($defaultWebGroundingToggleOff)
  {
    $this->defaultWebGroundingToggleOff = $defaultWebGroundingToggleOff;
  }
  /**
   * @return bool
   */
  public function getDefaultWebGroundingToggleOff()
  {
    return $this->defaultWebGroundingToggleOff;
  }
  /**
   * Optional. Output only. Whether to disable user location context.
   *
   * @param bool $disableLocationContext
   */
  public function setDisableLocationContext($disableLocationContext)
  {
    $this->disableLocationContext = $disableLocationContext;
  }
  /**
   * @return bool
   */
  public function getDisableLocationContext()
  {
    return $this->disableLocationContext;
  }
  /**
   * Whether or not the Google search grounding toggle is shown. Deprecated. Use
   * web_grounding_type instead.
   *
   * @deprecated
   * @param bool $googleSearchGroundingEnabled
   */
  public function setGoogleSearchGroundingEnabled($googleSearchGroundingEnabled)
  {
    $this->googleSearchGroundingEnabled = $googleSearchGroundingEnabled;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getGoogleSearchGroundingEnabled()
  {
    return $this->googleSearchGroundingEnabled;
  }
  /**
   * Optional. The type of web grounding to use.
   *
   * Accepted values: WEB_GROUNDING_TYPE_UNSPECIFIED,
   * WEB_GROUNDING_TYPE_DISABLED, WEB_GROUNDING_TYPE_GOOGLE_SEARCH,
   * WEB_GROUNDING_TYPE_ENTERPRISE_WEB_SEARCH
   *
   * @param self::WEB_GROUNDING_TYPE_* $webGroundingType
   */
  public function setWebGroundingType($webGroundingType)
  {
    $this->webGroundingType = $webGroundingType;
  }
  /**
   * @return self::WEB_GROUNDING_TYPE_*
   */
  public function getWebGroundingType()
  {
    return $this->webGroundingType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigAssistantSettings');
