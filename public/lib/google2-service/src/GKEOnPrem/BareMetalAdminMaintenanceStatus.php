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

namespace Google\Service\GKEOnPrem;

class BareMetalAdminMaintenanceStatus extends \Google\Model
{
  protected $machineDrainStatusType = BareMetalAdminMachineDrainStatus::class;
  protected $machineDrainStatusDataType = '';

  /**
   * Represents the status of draining and drained machine nodes. This is used
   * to show the progress of cluster upgrade.
   *
   * @param BareMetalAdminMachineDrainStatus $machineDrainStatus
   */
  public function setMachineDrainStatus(BareMetalAdminMachineDrainStatus $machineDrainStatus)
  {
    $this->machineDrainStatus = $machineDrainStatus;
  }
  /**
   * @return BareMetalAdminMachineDrainStatus
   */
  public function getMachineDrainStatus()
  {
    return $this->machineDrainStatus;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalAdminMaintenanceStatus::class, 'Google_Service_GKEOnPrem_BareMetalAdminMaintenanceStatus');
