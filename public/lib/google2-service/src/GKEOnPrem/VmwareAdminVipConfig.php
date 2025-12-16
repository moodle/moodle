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

class VmwareAdminVipConfig extends \Google\Model
{
  /**
   * The VIP to configure the load balancer for add-ons.
   *
   * @var string
   */
  public $addonsVip;
  /**
   * The VIP which you previously set aside for the Kubernetes API of the admin
   * cluster.
   *
   * @var string
   */
  public $controlPlaneVip;

  /**
   * The VIP to configure the load balancer for add-ons.
   *
   * @param string $addonsVip
   */
  public function setAddonsVip($addonsVip)
  {
    $this->addonsVip = $addonsVip;
  }
  /**
   * @return string
   */
  public function getAddonsVip()
  {
    return $this->addonsVip;
  }
  /**
   * The VIP which you previously set aside for the Kubernetes API of the admin
   * cluster.
   *
   * @param string $controlPlaneVip
   */
  public function setControlPlaneVip($controlPlaneVip)
  {
    $this->controlPlaneVip = $controlPlaneVip;
  }
  /**
   * @return string
   */
  public function getControlPlaneVip()
  {
    return $this->controlPlaneVip;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareAdminVipConfig::class, 'Google_Service_GKEOnPrem_VmwareAdminVipConfig');
