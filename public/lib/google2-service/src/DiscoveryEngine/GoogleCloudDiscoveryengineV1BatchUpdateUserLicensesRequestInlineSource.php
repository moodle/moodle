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

class GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource extends \Google\Collection
{
  protected $collection_key = 'userLicenses';
  /**
   * Optional. The list of fields to update.
   *
   * @var string
   */
  public $updateMask;
  protected $userLicensesType = GoogleCloudDiscoveryengineV1UserLicense::class;
  protected $userLicensesDataType = 'array';

  /**
   * Optional. The list of fields to update.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
  /**
   * Required. A list of user licenses to update. Each user license must have a
   * valid UserLicense.user_principal.
   *
   * @param GoogleCloudDiscoveryengineV1UserLicense[] $userLicenses
   */
  public function setUserLicenses($userLicenses)
  {
    $this->userLicenses = $userLicenses;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1UserLicense[]
   */
  public function getUserLicenses()
  {
    return $this->userLicenses;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1BatchUpdateUserLicensesRequestInlineSource');
