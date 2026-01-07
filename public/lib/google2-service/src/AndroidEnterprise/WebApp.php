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

namespace Google\Service\AndroidEnterprise;

class WebApp extends \Google\Collection
{
  public const DISPLAY_MODE_displayModeUnspecified = 'displayModeUnspecified';
  /**
   * Opens the web app with a minimal set of browser UI elements for controlling
   * navigation and viewing the page URL.
   */
  public const DISPLAY_MODE_minimalUi = 'minimalUi';
  /**
   * Opens the web app to look and feel like a standalone native application.
   * The browser UI elements and page URL are not visible, however the system
   * status bar and back button are visible.
   */
  public const DISPLAY_MODE_standalone = 'standalone';
  /**
   * Opens the web app in full screen without any visible controls. The browser
   * UI elements, page URL, system status bar and back button are not visible,
   * and the web app takes up the entirety of the available display area.
   */
  public const DISPLAY_MODE_fullScreen = 'fullScreen';
  protected $collection_key = 'icons';
  /**
   * The display mode of the web app. Possible values include: - "minimalUi",
   * the device's status bar, navigation bar, the app's URL, and a refresh
   * button are visible when the app is open. For HTTP URLs, you can only select
   * this option. - "standalone", the device's status bar and navigation bar are
   * visible when the app is open. - "fullScreen", the app opens in full screen
   * mode, hiding the device's status and navigation bars. All browser UI
   * elements, page URL, system status bar and back button are not visible, and
   * the web app takes up the entirety of the available display area.
   *
   * @var string
   */
  public $displayMode;
  protected $iconsType = WebAppIcon::class;
  protected $iconsDataType = 'array';
  /**
   * A flag whether the app has been published to the Play store yet.
   *
   * @var bool
   */
  public $isPublished;
  /**
   * The start URL, i.e. the URL that should load when the user opens the
   * application.
   *
   * @var string
   */
  public $startUrl;
  /**
   * The title of the web app as displayed to the user (e.g., amongst a list of
   * other applications, or as a label for an icon).
   *
   * @var string
   */
  public $title;
  /**
   * The current version of the app. Note that the version can automatically
   * increase during the lifetime of the web app, while Google does internal
   * housekeeping to keep the web app up-to-date.
   *
   * @var string
   */
  public $versionCode;
  /**
   * The ID of the application. A string of the form "app:" where the package
   * name always starts with the prefix "com.google.enterprise.webapp." followed
   * by a random id.
   *
   * @var string
   */
  public $webAppId;

  /**
   * The display mode of the web app. Possible values include: - "minimalUi",
   * the device's status bar, navigation bar, the app's URL, and a refresh
   * button are visible when the app is open. For HTTP URLs, you can only select
   * this option. - "standalone", the device's status bar and navigation bar are
   * visible when the app is open. - "fullScreen", the app opens in full screen
   * mode, hiding the device's status and navigation bars. All browser UI
   * elements, page URL, system status bar and back button are not visible, and
   * the web app takes up the entirety of the available display area.
   *
   * Accepted values: displayModeUnspecified, minimalUi, standalone, fullScreen
   *
   * @param self::DISPLAY_MODE_* $displayMode
   */
  public function setDisplayMode($displayMode)
  {
    $this->displayMode = $displayMode;
  }
  /**
   * @return self::DISPLAY_MODE_*
   */
  public function getDisplayMode()
  {
    return $this->displayMode;
  }
  /**
   * A list of icons representing this website. If absent, a default icon (for
   * create) or the current icon (for update) will be used.
   *
   * @param WebAppIcon[] $icons
   */
  public function setIcons($icons)
  {
    $this->icons = $icons;
  }
  /**
   * @return WebAppIcon[]
   */
  public function getIcons()
  {
    return $this->icons;
  }
  /**
   * A flag whether the app has been published to the Play store yet.
   *
   * @param bool $isPublished
   */
  public function setIsPublished($isPublished)
  {
    $this->isPublished = $isPublished;
  }
  /**
   * @return bool
   */
  public function getIsPublished()
  {
    return $this->isPublished;
  }
  /**
   * The start URL, i.e. the URL that should load when the user opens the
   * application.
   *
   * @param string $startUrl
   */
  public function setStartUrl($startUrl)
  {
    $this->startUrl = $startUrl;
  }
  /**
   * @return string
   */
  public function getStartUrl()
  {
    return $this->startUrl;
  }
  /**
   * The title of the web app as displayed to the user (e.g., amongst a list of
   * other applications, or as a label for an icon).
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The current version of the app. Note that the version can automatically
   * increase during the lifetime of the web app, while Google does internal
   * housekeeping to keep the web app up-to-date.
   *
   * @param string $versionCode
   */
  public function setVersionCode($versionCode)
  {
    $this->versionCode = $versionCode;
  }
  /**
   * @return string
   */
  public function getVersionCode()
  {
    return $this->versionCode;
  }
  /**
   * The ID of the application. A string of the form "app:" where the package
   * name always starts with the prefix "com.google.enterprise.webapp." followed
   * by a random id.
   *
   * @param string $webAppId
   */
  public function setWebAppId($webAppId)
  {
    $this->webAppId = $webAppId;
  }
  /**
   * @return string
   */
  public function getWebAppId()
  {
    return $this->webAppId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WebApp::class, 'Google_Service_AndroidEnterprise_WebApp');
