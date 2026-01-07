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

class BareMetalAdminMachineDrainStatus extends \Google\Collection
{
  protected $collection_key = 'drainingMachines';
  protected $drainedMachinesType = BareMetalAdminDrainedMachine::class;
  protected $drainedMachinesDataType = 'array';
  protected $drainingMachinesType = BareMetalAdminDrainingMachine::class;
  protected $drainingMachinesDataType = 'array';

  /**
   * The list of drained machines.
   *
   * @param BareMetalAdminDrainedMachine[] $drainedMachines
   */
  public function setDrainedMachines($drainedMachines)
  {
    $this->drainedMachines = $drainedMachines;
  }
  /**
   * @return BareMetalAdminDrainedMachine[]
   */
  public function getDrainedMachines()
  {
    return $this->drainedMachines;
  }
  /**
   * The list of draning machines.
   *
   * @param BareMetalAdminDrainingMachine[] $drainingMachines
   */
  public function setDrainingMachines($drainingMachines)
  {
    $this->drainingMachines = $drainingMachines;
  }
  /**
   * @return BareMetalAdminDrainingMachine[]
   */
  public function getDrainingMachines()
  {
    return $this->drainingMachines;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalAdminMachineDrainStatus::class, 'Google_Service_GKEOnPrem_BareMetalAdminMachineDrainStatus');
