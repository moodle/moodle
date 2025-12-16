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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1ShieldedVmConfig extends \Google\Model
{
  /**
   * Defines whether the instance has [Secure
   * Boot](https://cloud.google.com/compute/shielded-vm/docs/shielded-vm#secure-
   * boot) enabled. Secure Boot helps ensure that the system only runs authentic
   * software by verifying the digital signature of all boot components, and
   * halting the boot process if signature verification fails.
   *
   * @var bool
   */
  public $enableSecureBoot;

  /**
   * Defines whether the instance has [Secure
   * Boot](https://cloud.google.com/compute/shielded-vm/docs/shielded-vm#secure-
   * boot) enabled. Secure Boot helps ensure that the system only runs authentic
   * software by verifying the digital signature of all boot components, and
   * halting the boot process if signature verification fails.
   *
   * @param bool $enableSecureBoot
   */
  public function setEnableSecureBoot($enableSecureBoot)
  {
    $this->enableSecureBoot = $enableSecureBoot;
  }
  /**
   * @return bool
   */
  public function getEnableSecureBoot()
  {
    return $this->enableSecureBoot;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ShieldedVmConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ShieldedVmConfig');
