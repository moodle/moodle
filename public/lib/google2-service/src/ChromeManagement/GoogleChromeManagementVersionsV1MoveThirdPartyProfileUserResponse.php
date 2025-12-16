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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserResponse extends \Google\Model
{
  protected $thirdPartyProfileUserType = GoogleChromeManagementVersionsV1ThirdPartyProfileUser::class;
  protected $thirdPartyProfileUserDataType = '';

  /**
   * Output only. The moved third party profile user.
   *
   * @param GoogleChromeManagementVersionsV1ThirdPartyProfileUser $thirdPartyProfileUser
   */
  public function setThirdPartyProfileUser(GoogleChromeManagementVersionsV1ThirdPartyProfileUser $thirdPartyProfileUser)
  {
    $this->thirdPartyProfileUser = $thirdPartyProfileUser;
  }
  /**
   * @return GoogleChromeManagementVersionsV1ThirdPartyProfileUser
   */
  public function getThirdPartyProfileUser()
  {
    return $this->thirdPartyProfileUser;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserResponse::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserResponse');
