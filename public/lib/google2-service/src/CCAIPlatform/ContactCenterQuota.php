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

namespace Google\Service\CCAIPlatform;

class ContactCenterQuota extends \Google\Collection
{
  protected $collection_key = 'quotas';
  /**
   * Deprecated: Use the Quota fields instead. Reflects the count limit of
   * contact centers on a billing account.
   *
   * @deprecated
   * @var int
   */
  public $contactCenterCountLimit;
  /**
   * Deprecated: Use the Quota fields instead. Reflects the count sum of contact
   * centers on a billing account.
   *
   * @deprecated
   * @var int
   */
  public $contactCenterCountSum;
  protected $quotasType = Quota::class;
  protected $quotasDataType = 'array';

  /**
   * Deprecated: Use the Quota fields instead. Reflects the count limit of
   * contact centers on a billing account.
   *
   * @deprecated
   * @param int $contactCenterCountLimit
   */
  public function setContactCenterCountLimit($contactCenterCountLimit)
  {
    $this->contactCenterCountLimit = $contactCenterCountLimit;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getContactCenterCountLimit()
  {
    return $this->contactCenterCountLimit;
  }
  /**
   * Deprecated: Use the Quota fields instead. Reflects the count sum of contact
   * centers on a billing account.
   *
   * @deprecated
   * @param int $contactCenterCountSum
   */
  public function setContactCenterCountSum($contactCenterCountSum)
  {
    $this->contactCenterCountSum = $contactCenterCountSum;
  }
  /**
   * @deprecated
   * @return int
   */
  public function getContactCenterCountSum()
  {
    return $this->contactCenterCountSum;
  }
  /**
   * Quota details per contact center instance type.
   *
   * @param Quota[] $quotas
   */
  public function setQuotas($quotas)
  {
    $this->quotas = $quotas;
  }
  /**
   * @return Quota[]
   */
  public function getQuotas()
  {
    return $this->quotas;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContactCenterQuota::class, 'Google_Service_CCAIPlatform_ContactCenterQuota');
