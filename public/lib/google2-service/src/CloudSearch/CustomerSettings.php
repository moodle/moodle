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

namespace Google\Service\CloudSearch;

class CustomerSettings extends \Google\Model
{
  protected $auditLoggingSettingsType = AuditLoggingSettings::class;
  protected $auditLoggingSettingsDataType = '';
  protected $vpcSettingsType = VPCSettings::class;
  protected $vpcSettingsDataType = '';

  /**
   * Audit Logging settings for the customer. If update_mask is empty then this
   * field will be updated based on UpdateCustomerSettings request.
   *
   * @param AuditLoggingSettings $auditLoggingSettings
   */
  public function setAuditLoggingSettings(AuditLoggingSettings $auditLoggingSettings)
  {
    $this->auditLoggingSettings = $auditLoggingSettings;
  }
  /**
   * @return AuditLoggingSettings
   */
  public function getAuditLoggingSettings()
  {
    return $this->auditLoggingSettings;
  }
  /**
   * VPC SC settings for the customer. If update_mask is empty then this field
   * will be updated based on UpdateCustomerSettings request.
   *
   * @param VPCSettings $vpcSettings
   */
  public function setVpcSettings(VPCSettings $vpcSettings)
  {
    $this->vpcSettings = $vpcSettings;
  }
  /**
   * @return VPCSettings
   */
  public function getVpcSettings()
  {
    return $this->vpcSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomerSettings::class, 'Google_Service_CloudSearch_CustomerSettings');
