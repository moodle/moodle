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

class AnalyticsProperty extends \Google\Model
{
  /**
   * Output only. The ID of the [Google Analytics
   * account](https://www.google.com/analytics/) for the Google Analytics
   * property associated with the specified FirebaseProject.
   *
   * @var string
   */
  public $analyticsAccountId;
  /**
   * The display name of the Google Analytics property associated with the
   * specified `FirebaseProject`.
   *
   * @var string
   */
  public $displayName;
  /**
   * The globally unique, Google-assigned identifier of the Google Analytics
   * property associated with the specified `FirebaseProject`. If you called
   * [`AddGoogleAnalytics`](../../v1beta1/projects/addGoogleAnalytics) to link
   * the `FirebaseProject` with a Google Analytics account, the value in this
   * `id` field is the same as the ID of the property either specified or
   * provisioned with that call to `AddGoogleAnalytics`.
   *
   * @var string
   */
  public $id;

  /**
   * Output only. The ID of the [Google Analytics
   * account](https://www.google.com/analytics/) for the Google Analytics
   * property associated with the specified FirebaseProject.
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
   * The display name of the Google Analytics property associated with the
   * specified `FirebaseProject`.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The globally unique, Google-assigned identifier of the Google Analytics
   * property associated with the specified `FirebaseProject`. If you called
   * [`AddGoogleAnalytics`](../../v1beta1/projects/addGoogleAnalytics) to link
   * the `FirebaseProject` with a Google Analytics account, the value in this
   * `id` field is the same as the ID of the property either specified or
   * provisioned with that call to `AddGoogleAnalytics`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AnalyticsProperty::class, 'Google_Service_FirebaseManagement_AnalyticsProperty');
