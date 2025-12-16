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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1UserInfo extends \Google\Collection
{
  protected $collection_key = 'userIds';
  /**
   * Optional. For logged-in requests or login/registration requests, the unique
   * account identifier associated with this user. You can use the username if
   * it is stable (meaning it is the same for every request associated with the
   * same user), or any stable user ID of your choice. Leave blank for non
   * logged-in actions or guest checkout.
   *
   * @var string
   */
  public $accountId;
  /**
   * Optional. Creation time for this account associated with this user. Leave
   * blank for non logged-in actions, guest checkout, or when there is no
   * account associated with the current user.
   *
   * @var string
   */
  public $createAccountTime;
  protected $userIdsType = GoogleCloudRecaptchaenterpriseV1UserId::class;
  protected $userIdsDataType = 'array';

  /**
   * Optional. For logged-in requests or login/registration requests, the unique
   * account identifier associated with this user. You can use the username if
   * it is stable (meaning it is the same for every request associated with the
   * same user), or any stable user ID of your choice. Leave blank for non
   * logged-in actions or guest checkout.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Optional. Creation time for this account associated with this user. Leave
   * blank for non logged-in actions, guest checkout, or when there is no
   * account associated with the current user.
   *
   * @param string $createAccountTime
   */
  public function setCreateAccountTime($createAccountTime)
  {
    $this->createAccountTime = $createAccountTime;
  }
  /**
   * @return string
   */
  public function getCreateAccountTime()
  {
    return $this->createAccountTime;
  }
  /**
   * Optional. Identifiers associated with this user or request.
   *
   * @param GoogleCloudRecaptchaenterpriseV1UserId[] $userIds
   */
  public function setUserIds($userIds)
  {
    $this->userIds = $userIds;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1UserId[]
   */
  public function getUserIds()
  {
    return $this->userIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1UserInfo::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1UserInfo');
