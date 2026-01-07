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

class GoogleAppsCardV1Icon extends \Google\Model
{
  /**
   * Default value. Applies a square mask to the image. For example, a 4x3 image
   * becomes 3x3.
   */
  public const IMAGE_TYPE_SQUARE = 'SQUARE';
  /**
   * Applies a circular mask to the image. For example, a 4x3 image becomes a
   * circle with a diameter of 3.
   */
  public const IMAGE_TYPE_CIRCLE = 'CIRCLE';
  /**
   * Optional. A description of the icon used for accessibility. If unspecified,
   * the default value `Button` is provided. As a best practice, you should set
   * a helpful description for what the icon displays, and if applicable, what
   * it does. For example, `A user's account portrait`, or `Opens a new browser
   * tab and navigates to the Google Chat developer documentation at
   * https://developers.google.com/workspace/chat`. If the icon is set in a
   * `Button`, the `altText` appears as helper text when the user hovers over
   * the button. However, if the button also sets `text`, the icon's `altText`
   * is ignored.
   *
   * @var string
   */
  public $altText;
  /**
   * Display a custom icon hosted at an HTTPS URL. For example: ``` "iconUrl":
   * "https://developers.google.com/workspace/chat/images/quickstart-app-
   * avatar.png" ``` Supported file types include `.png` and `.jpg`.
   *
   * @var string
   */
  public $iconUrl;
  /**
   * The crop style applied to the image. In some cases, applying a `CIRCLE`
   * crop causes the image to be drawn larger than a built-in icon.
   *
   * @var string
   */
  public $imageType;
  /**
   * Display one of the built-in icons provided by Google Workspace. For
   * example, to display an airplane icon, specify `AIRPLANE`. For a bus,
   * specify `BUS`. For a full list of supported icons, see [built-in
   * icons](https://developers.google.com/workspace/chat/format-
   * messages#builtinicons).
   *
   * @var string
   */
  public $knownIcon;
  protected $materialIconType = GoogleAppsCardV1MaterialIcon::class;
  protected $materialIconDataType = '';

  /**
   * Optional. A description of the icon used for accessibility. If unspecified,
   * the default value `Button` is provided. As a best practice, you should set
   * a helpful description for what the icon displays, and if applicable, what
   * it does. For example, `A user's account portrait`, or `Opens a new browser
   * tab and navigates to the Google Chat developer documentation at
   * https://developers.google.com/workspace/chat`. If the icon is set in a
   * `Button`, the `altText` appears as helper text when the user hovers over
   * the button. However, if the button also sets `text`, the icon's `altText`
   * is ignored.
   *
   * @param string $altText
   */
  public function setAltText($altText)
  {
    $this->altText = $altText;
  }
  /**
   * @return string
   */
  public function getAltText()
  {
    return $this->altText;
  }
  /**
   * Display a custom icon hosted at an HTTPS URL. For example: ``` "iconUrl":
   * "https://developers.google.com/workspace/chat/images/quickstart-app-
   * avatar.png" ``` Supported file types include `.png` and `.jpg`.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * The crop style applied to the image. In some cases, applying a `CIRCLE`
   * crop causes the image to be drawn larger than a built-in icon.
   *
   * Accepted values: SQUARE, CIRCLE
   *
   * @param self::IMAGE_TYPE_* $imageType
   */
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  /**
   * @return self::IMAGE_TYPE_*
   */
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * Display one of the built-in icons provided by Google Workspace. For
   * example, to display an airplane icon, specify `AIRPLANE`. For a bus,
   * specify `BUS`. For a full list of supported icons, see [built-in
   * icons](https://developers.google.com/workspace/chat/format-
   * messages#builtinicons).
   *
   * @param string $knownIcon
   */
  public function setKnownIcon($knownIcon)
  {
    $this->knownIcon = $knownIcon;
  }
  /**
   * @return string
   */
  public function getKnownIcon()
  {
    return $this->knownIcon;
  }
  /**
   * Display one of the [Google Material Icons](https://fonts.google.com/icons).
   * For example, to display a [checkbox icon](https://fonts.google.com/icons?se
   * lected=Material%20Symbols%20Outlined%3Acheck_box%3AFILL%400%3Bwght%40400%3B
   * GRAD%400%3Bopsz%4048), use ``` "material_icon": { "name": "check_box" } ```
   * [Google Chat apps](https://developers.google.com/workspace/chat):
   *
   * @param GoogleAppsCardV1MaterialIcon $materialIcon
   */
  public function setMaterialIcon(GoogleAppsCardV1MaterialIcon $materialIcon)
  {
    $this->materialIcon = $materialIcon;
  }
  /**
   * @return GoogleAppsCardV1MaterialIcon
   */
  public function getMaterialIcon()
  {
    return $this->materialIcon;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Icon::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Icon');
