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

namespace Google\Service\VMMigrationService;

class MachineImageParametersOverrides extends \Google\Model
{
  /**
   * Optional. The machine type to create the MachineImage with. If empty, the
   * service will choose a relevant machine type based on the information from
   * the source image. For more information about machine types, please refer to
   * https://cloud.google.com/compute/docs/machine-resource.
   *
   * @var string
   */
  public $machineType;

  /**
   * Optional. The machine type to create the MachineImage with. If empty, the
   * service will choose a relevant machine type based on the information from
   * the source image. For more information about machine types, please refer to
   * https://cloud.google.com/compute/docs/machine-resource.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MachineImageParametersOverrides::class, 'Google_Service_VMMigrationService_MachineImageParametersOverrides');
