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

namespace Google\Service\ShoppingContent;

class AccountUser extends \Google\Model
{
  /**
   * Whether user is an admin.
   *
   * @var bool
   */
  public $admin;
  /**
   * User's email address.
   *
   * @var string
   */
  public $emailAddress;
  /**
   * This role is deprecated and can no longer be assigned. Any value set will
   * be ignored.
   *
   * @deprecated
   * @var bool
   */
  public $orderManager;
  /**
   * This role is deprecated and can no longer be assigned. Any value set will
   * be ignored.
   *
   * @deprecated
   * @var bool
   */
  public $paymentsAnalyst;
  /**
   * This role is deprecated and can no longer be assigned. Any value set will
   * be ignored.
   *
   * @deprecated
   * @var bool
   */
  public $paymentsManager;
  /**
   * Optional. Whether user has standard read-only access.
   *
   * @var bool
   */
  public $readOnly;
  /**
   * Whether user is a reporting manager. This role is equivalent to the
   * Performance and insights role in Merchant Center.
   *
   * @var bool
   */
  public $reportingManager;

  /**
   * Whether user is an admin.
   *
   * @param bool $admin
   */
  public function setAdmin($admin)
  {
    $this->admin = $admin;
  }
  /**
   * @return bool
   */
  public function getAdmin()
  {
    return $this->admin;
  }
  /**
   * User's email address.
   *
   * @param string $emailAddress
   */
  public function setEmailAddress($emailAddress)
  {
    $this->emailAddress = $emailAddress;
  }
  /**
   * @return string
   */
  public function getEmailAddress()
  {
    return $this->emailAddress;
  }
  /**
   * This role is deprecated and can no longer be assigned. Any value set will
   * be ignored.
   *
   * @deprecated
   * @param bool $orderManager
   */
  public function setOrderManager($orderManager)
  {
    $this->orderManager = $orderManager;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getOrderManager()
  {
    return $this->orderManager;
  }
  /**
   * This role is deprecated and can no longer be assigned. Any value set will
   * be ignored.
   *
   * @deprecated
   * @param bool $paymentsAnalyst
   */
  public function setPaymentsAnalyst($paymentsAnalyst)
  {
    $this->paymentsAnalyst = $paymentsAnalyst;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getPaymentsAnalyst()
  {
    return $this->paymentsAnalyst;
  }
  /**
   * This role is deprecated and can no longer be assigned. Any value set will
   * be ignored.
   *
   * @deprecated
   * @param bool $paymentsManager
   */
  public function setPaymentsManager($paymentsManager)
  {
    $this->paymentsManager = $paymentsManager;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getPaymentsManager()
  {
    return $this->paymentsManager;
  }
  /**
   * Optional. Whether user has standard read-only access.
   *
   * @param bool $readOnly
   */
  public function setReadOnly($readOnly)
  {
    $this->readOnly = $readOnly;
  }
  /**
   * @return bool
   */
  public function getReadOnly()
  {
    return $this->readOnly;
  }
  /**
   * Whether user is a reporting manager. This role is equivalent to the
   * Performance and insights role in Merchant Center.
   *
   * @param bool $reportingManager
   */
  public function setReportingManager($reportingManager)
  {
    $this->reportingManager = $reportingManager;
  }
  /**
   * @return bool
   */
  public function getReportingManager()
  {
    return $this->reportingManager;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountUser::class, 'Google_Service_ShoppingContent_AccountUser');
