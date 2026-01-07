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

namespace Google\Service\Vault;

class HeldOrgUnit extends \Google\Model
{
  /**
   * When the organizational unit was put on hold. This property is immutable.
   *
   * @var string
   */
  public $holdTime;
  /**
   * The organizational unit's immutable ID as provided by the [Admin
   * SDK](https://developers.google.com/admin-sdk/).
   *
   * @var string
   */
  public $orgUnitId;

  /**
   * When the organizational unit was put on hold. This property is immutable.
   *
   * @param string $holdTime
   */
  public function setHoldTime($holdTime)
  {
    $this->holdTime = $holdTime;
  }
  /**
   * @return string
   */
  public function getHoldTime()
  {
    return $this->holdTime;
  }
  /**
   * The organizational unit's immutable ID as provided by the [Admin
   * SDK](https://developers.google.com/admin-sdk/).
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(HeldOrgUnit::class, 'Google_Service_Vault_HeldOrgUnit');
