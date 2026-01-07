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

namespace Google\Service\FirebaseCloudMessaging;

class Notification extends \Google\Model
{
  /**
   * The notification's body text.
   *
   * @var string
   */
  public $body;
  /**
   * Contains the URL of an image that is going to be downloaded on the device
   * and displayed in a notification. JPEG, PNG, BMP have full support across
   * platforms. Animated GIF and video only work on iOS. WebP and HEIF have
   * varying levels of support across platforms and platform versions. Android
   * has 1MB image size limit. Quota usage and implications/costs for hosting
   * image on Firebase Storage: https://firebase.google.com/pricing
   *
   * @var string
   */
  public $image;
  /**
   * The notification's title.
   *
   * @var string
   */
  public $title;

  /**
   * The notification's body text.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Contains the URL of an image that is going to be downloaded on the device
   * and displayed in a notification. JPEG, PNG, BMP have full support across
   * platforms. Animated GIF and video only work on iOS. WebP and HEIF have
   * varying levels of support across platforms and platform versions. Android
   * has 1MB image size limit. Quota usage and implications/costs for hosting
   * image on Firebase Storage: https://firebase.google.com/pricing
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * The notification's title.
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
class_alias(Notification::class, 'Google_Service_FirebaseCloudMessaging_Notification');
