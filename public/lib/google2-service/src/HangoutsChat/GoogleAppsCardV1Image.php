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

class GoogleAppsCardV1Image extends \Google\Model
{
  /**
   * The alternative text of this image that's used for accessibility.
   *
   * @var string
   */
  public $altText;
  /**
   * The HTTPS URL that hosts the image. For example: ```
   * https://developers.google.com/workspace/chat/images/quickstart-app-
   * avatar.png ```
   *
   * @var string
   */
  public $imageUrl;
  protected $onClickType = GoogleAppsCardV1OnClick::class;
  protected $onClickDataType = '';

  /**
   * The alternative text of this image that's used for accessibility.
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
   * The HTTPS URL that hosts the image. For example: ```
   * https://developers.google.com/workspace/chat/images/quickstart-app-
   * avatar.png ```
   *
   * @param string $imageUrl
   */
  public function setImageUrl($imageUrl)
  {
    $this->imageUrl = $imageUrl;
  }
  /**
   * @return string
   */
  public function getImageUrl()
  {
    return $this->imageUrl;
  }
  /**
   * When a user clicks the image, the click triggers this action.
   *
   * @param GoogleAppsCardV1OnClick $onClick
   */
  public function setOnClick(GoogleAppsCardV1OnClick $onClick)
  {
    $this->onClick = $onClick;
  }
  /**
   * @return GoogleAppsCardV1OnClick
   */
  public function getOnClick()
  {
    return $this->onClick;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsCardV1Image::class, 'Google_Service_HangoutsChat_GoogleAppsCardV1Image');
