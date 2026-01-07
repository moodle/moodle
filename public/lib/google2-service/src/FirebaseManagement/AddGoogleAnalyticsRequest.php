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

namespace Google\Service\FirebaseManagement;

class AddGoogleAnalyticsRequest extends \Google\Model
{
  /**
   * The ID for the existing [Google Analytics
   * account](http://www.google.com/analytics/) that you want to link with the
   * `FirebaseProject`. Specifying this field will provision a new Google
   * Analytics property in your Google Analytics account and associate the new
   * property with the `FirebaseProject`.
   *
   * @var string
   */
  public $analyticsAccountId;
  /**
   * The ID for the existing Google Analytics property that you want to
   * associate with the `FirebaseProject`.
   *
   * @var string
   */
  public $analyticsPropertyId;

  /**
   * The ID for the existing [Google Analytics
   * account](http://www.google.com/analytics/) that you want to link with the
   * `FirebaseProject`. Specifying this field will provision a new Google
   * Analytics property in your Google Analytics account and associate the new
   * property with the `FirebaseProject`.
   *
   * @param string $analyticsAccountId
   */
  public function setAnalyticsAccountId($analyticsAccountId)
  {
    $this->analyticsAccountId = $analyticsAccountId;
  }
  /**
   * @return string
   */
  public function getAnalyticsAccountId()
  {
    return $this->analyticsAccountId;
  }
  /**
   * The ID for the existing Google Analytics property that you want to
   * associate with the `FirebaseProject`.
   *
   * @param string $analyticsPropertyId
   */
  public function setAnalyticsPropertyId($analyticsPropertyId)
  {
    $this->analyticsPropertyId = $analyticsPropertyId;
  }
  /**
   * @return string
   */
  public function getAnalyticsPropertyId()
  {
    return $this->analyticsPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AddGoogleAnalyticsRequest::class, 'Google_Service_FirebaseManagement_AddGoogleAnalyticsRequest');
