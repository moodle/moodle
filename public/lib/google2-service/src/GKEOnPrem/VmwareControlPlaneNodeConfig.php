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

class VmwareControlPlaneNodeConfig extends \Google\Model
{
  protected $autoResizeConfigType = VmwareAutoResizeConfig::class;
  protected $autoResizeConfigDataType = '';
  /**
   * The number of CPUs for each admin cluster node that serve as control planes
   * for this VMware user cluster. (default: 4 CPUs)
   *
   * @var string
   */
  public $cpus;
  /**
   * The megabytes of memory for each admin cluster node that serves as a
   * control plane for this VMware user cluster (default: 8192 MB memory).
   *
   * @var string
   */
  public $memory;
  /**
   * The number of control plane nodes for this VMware user cluster. (default: 1
   * replica).
   *
   * @var string
   */
  public $replicas;
  protected $vsphereConfigType = VmwareControlPlaneVsphereConfig::class;
  protected $vsphereConfigDataType = '';

  /**
   * AutoResizeConfig provides auto resizing configurations.
   *
   * @param VmwareAutoResizeConfig $autoResizeConfig
   */
  public function setAutoResizeConfig(VmwareAutoResizeConfig $autoResizeConfig)
  {
    $this->autoResizeConfig = $autoResizeConfig;
  }
  /**
   * @return VmwareAutoResizeConfig
   */
  public function getAutoResizeConfig()
  {
    return $this->autoResizeConfig;
  }
  /**
   * The number of CPUs for each admin cluster node that serve as control planes
   * for this VMware user cluster. (default: 4 CPUs)
   *
   * @param string $cpus
   */
  public function setCpus($cpus)
  {
    $this->cpus = $cpus;
  }
  /**
   * @return string
   */
  public function getCpus()
  {
    return $this->cpus;
  }
  /**
   * The megabytes of memory for each admin cluster node that serves as a
   * control plane for this VMware user cluster (default: 8192 MB memory).
   *
   * @param string $memory
   */
  public function setMemory($memory)
  {
    $this->memory = $memory;
  }
  /**
   * @return string
   */
  public function getMemory()
  {
    return $this->memory;
  }
  /**
   * The number of control plane nodes for this VMware user cluster. (default: 1
   * replica).
   *
   * @param string $replicas
   */
  public function setReplicas($replicas)
  {
    $this->replicas = $replicas;
  }
  /**
   * @return string
   */
  public function getReplicas()
  {
    return $this->replicas;
  }
  /**
   * Vsphere-specific config.
   *
   * @param VmwareControlPlaneVsphereConfig $vsphereConfig
   */
  public function setVsphereConfig(VmwareControlPlaneVsphereConfig $vsphereConfig)
  {
    $this->vsphereConfig = $vsphereConfig;
  }
  /**
   * @return VmwareControlPlaneVsphereConfig
   */
  public function getVsphereConfig()
  {
    return $this->vsphereConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareControlPlaneNodeConfig::class, 'Google_Service_GKEOnPrem_VmwareControlPlaneNodeConfig');
