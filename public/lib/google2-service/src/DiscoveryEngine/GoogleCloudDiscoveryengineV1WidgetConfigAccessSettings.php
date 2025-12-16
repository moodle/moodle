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

class GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings extends \Google\Collection
{
  protected $collection_key = 'allowlistedDomains';
  /**
   * Whether public unauthenticated access is allowed.
   *
   * @var bool
   */
  public $allowPublicAccess;
  /**
   * List of domains that are allowed to integrate the search widget.
   *
   * @var string[]
   */
  public $allowlistedDomains;
  /**
   * Whether web app access is enabled.
   *
   * @var bool
   */
  public $enableWebApp;
  /**
   * Optional. Language code for user interface. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). If unset, the
   * default language code is "en-US".
   *
   * @var string
   */
  public $languageCode;
  /**
   * Optional. The workforce identity pool provider used to access the widget.
   *
   * @var string
   */
  public $workforceIdentityPoolProvider;

  /**
   * Whether public unauthenticated access is allowed.
   *
   * @param bool $allowPublicAccess
   */
  public function setAllowPublicAccess($allowPublicAccess)
  {
    $this->allowPublicAccess = $allowPublicAccess;
  }
  /**
   * @return bool
   */
  public function getAllowPublicAccess()
  {
    return $this->allowPublicAccess;
  }
  /**
   * List of domains that are allowed to integrate the search widget.
   *
   * @param string[] $allowlistedDomains
   */
  public function setAllowlistedDomains($allowlistedDomains)
  {
    $this->allowlistedDomains = $allowlistedDomains;
  }
  /**
   * @return string[]
   */
  public function getAllowlistedDomains()
  {
    return $this->allowlistedDomains;
  }
  /**
   * Whether web app access is enabled.
   *
   * @param bool $enableWebApp
   */
  public function setEnableWebApp($enableWebApp)
  {
    $this->enableWebApp = $enableWebApp;
  }
  /**
   * @return bool
   */
  public function getEnableWebApp()
  {
    return $this->enableWebApp;
  }
  /**
   * Optional. Language code for user interface. Use language tags defined by
   * [BCP47](https://www.rfc-editor.org/rfc/bcp/bcp47.txt). If unset, the
   * default language code is "en-US".
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
   * Optional. The workforce identity pool provider used to access the widget.
   *
   * @param string $workforceIdentityPoolProvider
   */
  public function setWorkforceIdentityPoolProvider($workforceIdentityPoolProvider)
  {
    $this->workforceIdentityPoolProvider = $workforceIdentityPoolProvider;
  }
  /**
   * @return string
   */
  public function getWorkforceIdentityPoolProvider()
  {
    return $this->workforceIdentityPoolProvider;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigAccessSettings');
