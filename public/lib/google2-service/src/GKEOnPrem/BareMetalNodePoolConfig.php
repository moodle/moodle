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

class BareMetalNodePoolConfig extends \Google\Collection
{
  /**
   * No operating system runtime selected.
   */
  public const OPERATING_SYSTEM_OPERATING_SYSTEM_UNSPECIFIED = 'OPERATING_SYSTEM_UNSPECIFIED';
  /**
   * Linux operating system.
   */
  public const OPERATING_SYSTEM_LINUX = 'LINUX';
  protected $collection_key = 'taints';
  protected $kubeletConfigType = BareMetalKubeletConfig::class;
  protected $kubeletConfigDataType = '';
  /**
   * The labels assigned to nodes of this node pool. An object containing a list
   * of key/value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count":
   * "3" }.
   *
   * @var string[]
   */
  public $labels;
  protected $nodeConfigsType = BareMetalNodeConfig::class;
  protected $nodeConfigsDataType = 'array';
  /**
   * Specifies the nodes operating system (default: LINUX).
   *
   * @var string
   */
  public $operatingSystem;
  protected $taintsType = NodeTaint::class;
  protected $taintsDataType = 'array';

  /**
   * The modifiable kubelet configurations for the bare metal machines.
   *
   * @param BareMetalKubeletConfig $kubeletConfig
   */
  public function setKubeletConfig(BareMetalKubeletConfig $kubeletConfig)
  {
    $this->kubeletConfig = $kubeletConfig;
  }
  /**
   * @return BareMetalKubeletConfig
   */
  public function getKubeletConfig()
  {
    return $this->kubeletConfig;
  }
  /**
   * The labels assigned to nodes of this node pool. An object containing a list
   * of key/value pairs. Example: { "name": "wrench", "mass": "1.3kg", "count":
   * "3" }.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The list of machine addresses in the bare metal node pool.
   *
   * @param BareMetalNodeConfig[] $nodeConfigs
   */
  public function setNodeConfigs($nodeConfigs)
  {
    $this->nodeConfigs = $nodeConfigs;
  }
  /**
   * @return BareMetalNodeConfig[]
   */
  public function getNodeConfigs()
  {
    return $this->nodeConfigs;
  }
  /**
   * Specifies the nodes operating system (default: LINUX).
   *
   * Accepted values: OPERATING_SYSTEM_UNSPECIFIED, LINUX
   *
   * @param self::OPERATING_SYSTEM_* $operatingSystem
   */
  public function setOperatingSystem($operatingSystem)
  {
    $this->operatingSystem = $operatingSystem;
  }
  /**
   * @return self::OPERATING_SYSTEM_*
   */
  public function getOperatingSystem()
  {
    return $this->operatingSystem;
  }
  /**
   * The initial taints assigned to nodes of this node pool.
   *
   * @param NodeTaint[] $taints
   */
  public function setTaints($taints)
  {
    $this->taints = $taints;
  }
  /**
   * @return NodeTaint[]
   */
  public function getTaints()
  {
    return $this->taints;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalNodePoolConfig::class, 'Google_Service_GKEOnPrem_BareMetalNodePoolConfig');
