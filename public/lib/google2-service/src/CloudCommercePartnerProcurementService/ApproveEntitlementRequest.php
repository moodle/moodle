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

namespace Google\Service\CloudCommercePartnerProcurementService;

class ApproveEntitlementRequest extends \Google\Model
{
  /**
   * Optional. The resource name of the entitlement that was migrated, with the
   * format `providers/{provider_id}/entitlements/{entitlement_id}`. Should only
   * be sent when resources have been migrated from entitlement_migrated to the
   * new entitlement. Optional.
   *
   * @var string
   */
  public $entitlementMigrated;
  /**
   * Set of properties that should be associated with the entitlement. Optional.
   *
   * @deprecated
   * @var string[]
   */
  public $properties;

  /**
   * Optional. The resource name of the entitlement that was migrated, with the
   * format `providers/{provider_id}/entitlements/{entitlement_id}`. Should only
   * be sent when resources have been migrated from entitlement_migrated to the
   * new entitlement. Optional.
   *
   * @param string $entitlementMigrated
   */
  public function setEntitlementMigrated($entitlementMigrated)
  {
    $this->entitlementMigrated = $entitlementMigrated;
  }
  /**
   * @return string
   */
  public function getEntitlementMigrated()
  {
    return $this->entitlementMigrated;
  }
  /**
   * Set of properties that should be associated with the entitlement. Optional.
   *
   * @deprecated
   * @param string[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApproveEntitlementRequest::class, 'Google_Service_CloudCommercePartnerProcurementService_ApproveEntitlementRequest');
