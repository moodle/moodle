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

class GoogleCloudDiscoveryengineV1WidgetConfigHomepageSettingShortcut extends \Google\Model
{
  /**
   * Optional. Destination URL of shortcut.
   *
   * @var string
   */
  public $destinationUri;
  protected $iconType = GoogleCloudDiscoveryengineV1WidgetConfigImage::class;
  protected $iconDataType = '';
  /**
   * Optional. Title of the shortcut.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. Destination URL of shortcut.
   *
   * @param string $destinationUri
   */
  public function setDestinationUri($destinationUri)
  {
    $this->destinationUri = $destinationUri;
  }
  /**
   * @return string
   */
  public function getDestinationUri()
  {
    return $this->destinationUri;
  }
  /**
   * Optional. Icon URL of shortcut.
   *
   * @param GoogleCloudDiscoveryengineV1WidgetConfigImage $icon
   */
  public function setIcon(GoogleCloudDiscoveryengineV1WidgetConfigImage $icon)
  {
    $this->icon = $icon;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1WidgetConfigImage
   */
  public function getIcon()
  {
    return $this->icon;
  }
  /**
   * Optional. Title of the shortcut.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1WidgetConfigHomepageSettingShortcut::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1WidgetConfigHomepageSettingShortcut');
