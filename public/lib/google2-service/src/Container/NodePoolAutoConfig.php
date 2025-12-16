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

namespace Google\Service\Container;

class NodePoolAutoConfig extends \Google\Model
{
  protected $linuxNodeConfigType = LinuxNodeConfig::class;
  protected $linuxNodeConfigDataType = '';
  protected $networkTagsType = NetworkTags::class;
  protected $networkTagsDataType = '';
  protected $nodeKubeletConfigType = NodeKubeletConfig::class;
  protected $nodeKubeletConfigDataType = '';
  protected $resourceManagerTagsType = ResourceManagerTags::class;
  protected $resourceManagerTagsDataType = '';

  /**
   * Output only. Configuration options for Linux nodes.
   *
   * @param LinuxNodeConfig $linuxNodeConfig
   */
  public function setLinuxNodeConfig(LinuxNodeConfig $linuxNodeConfig)
  {
    $this->linuxNodeConfig = $linuxNodeConfig;
  }
  /**
   * @return LinuxNodeConfig
   */
  public function getLinuxNodeConfig()
  {
    return $this->linuxNodeConfig;
  }
  /**
   * The list of instance tags applied to all nodes. Tags are used to identify
   * valid sources or targets for network firewalls and are specified by the
   * client during cluster creation. Each tag within the list must comply with
   * RFC1035.
   *
   * @param NetworkTags $networkTags
   */
  public function setNetworkTags(NetworkTags $networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return NetworkTags
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * NodeKubeletConfig controls the defaults for autoprovisioned node-pools.
   * Currently only `insecure_kubelet_readonly_port_enabled` can be set here.
   *
   * @param NodeKubeletConfig $nodeKubeletConfig
   */
  public function setNodeKubeletConfig(NodeKubeletConfig $nodeKubeletConfig)
  {
    $this->nodeKubeletConfig = $nodeKubeletConfig;
  }
  /**
   * @return NodeKubeletConfig
   */
  public function getNodeKubeletConfig()
  {
    return $this->nodeKubeletConfig;
  }
  /**
   * Resource manager tag keys and values to be attached to the nodes for
   * managing Compute Engine firewalls using Network Firewall Policies.
   *
   * @param ResourceManagerTags $resourceManagerTags
   */
  public function setResourceManagerTags(ResourceManagerTags $resourceManagerTags)
  {
    $this->resourceManagerTags = $resourceManagerTags;
  }
  /**
   * @return ResourceManagerTags
   */
  public function getResourceManagerTags()
  {
    return $this->resourceManagerTags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodePoolAutoConfig::class, 'Google_Service_Container_NodePoolAutoConfig');
