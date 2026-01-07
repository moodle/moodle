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

namespace Google\Service\BigtableAdmin;

class DataBoostIsolationReadOnly extends \Google\Model
{
  /**
   * Unspecified value.
   */
  public const COMPUTE_BILLING_OWNER_COMPUTE_BILLING_OWNER_UNSPECIFIED = 'COMPUTE_BILLING_OWNER_UNSPECIFIED';
  /**
   * The host Cloud Project containing the targeted Bigtable Instance / Table
   * pays for compute.
   */
  public const COMPUTE_BILLING_OWNER_HOST_PAYS = 'HOST_PAYS';
  /**
   * The Compute Billing Owner for this Data Boost App Profile.
   *
   * @var string
   */
  public $computeBillingOwner;

  /**
   * The Compute Billing Owner for this Data Boost App Profile.
   *
   * Accepted values: COMPUTE_BILLING_OWNER_UNSPECIFIED, HOST_PAYS
   *
   * @param self::COMPUTE_BILLING_OWNER_* $computeBillingOwner
   */
  public function setComputeBillingOwner($computeBillingOwner)
  {
    $this->computeBillingOwner = $computeBillingOwner;
  }
  /**
   * @return self::COMPUTE_BILLING_OWNER_*
   */
  public function getComputeBillingOwner()
  {
    return $this->computeBillingOwner;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataBoostIsolationReadOnly::class, 'Google_Service_BigtableAdmin_DataBoostIsolationReadOnly');
