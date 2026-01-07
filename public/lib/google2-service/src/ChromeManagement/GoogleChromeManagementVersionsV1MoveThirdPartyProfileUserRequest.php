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

class GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserRequest extends \Google\Model
{
  /**
   * Required. Destination organizational unit where the third party chrome
   * profile user will be moved to.
   *
   * @var string
   */
  public $destinationOrgUnit;

  /**
   * Required. Destination organizational unit where the third party chrome
   * profile user will be moved to.
   *
   * @param string $destinationOrgUnit
   */
  public function setDestinationOrgUnit($destinationOrgUnit)
  {
    $this->destinationOrgUnit = $destinationOrgUnit;
  }
  /**
   * @return string
   */
  public function getDestinationOrgUnit()
  {
    return $this->destinationOrgUnit;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserRequest::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1MoveThirdPartyProfileUserRequest');
