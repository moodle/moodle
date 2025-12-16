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

class VmwareNodeConfig extends \Google\Collection
{
  protected $collection_key = 'taints';
  /**
   * VMware disk size to be used during creation.
   *
   * @var string
   */
  public $bootDiskSizeGb;
  /**
   * The number of CPUs for each node in the node pool.
   *
   * @var string
   */
  public $cpus;
  /**
   * Allow node pool traffic to be load balanced. Only works for clusters with
   * MetalLB load balancers.
   *
   * @var bool
   */
  public $enableLoadBalancer;
  /**
   * The OS image name in vCenter, only valid when using Windows.
   *
   * @var string
   */
  public $image;
  /**
   * Required. The OS image to be used for each node in a node pool. Currently
   * `cos`, `cos_cgv2`, `ubuntu`, `ubuntu_cgv2`, `ubuntu_containerd` and
   * `windows` are supported.
   *
   * @var string
   */
  public $imageType;
  /**
   * The map of Kubernetes labels (key/value pairs) to be applied to each node.
   * These will added in addition to any default label(s) that Kubernetes may
   * apply to the node. In case of conflict in label keys, the applied set may
   * differ depending on the Kubernetes version -- it's best to assume the
   * behavior is undefined and conflicts should be avoided. For more
   * information, including usage and the valid values, see:
   * https://kubernetes.io/docs/concepts/overview/working-with-objects/labels/
   *
   * @var string[]
   */
  public $labels;
  /**
   * The megabytes of memory for each node in the node pool.
   *
   * @var string
   */
  public $memoryMb;
  /**
   * The number of nodes in the node pool.
   *
   * @var string
   */
  public $replicas;
  protected $taintsType = NodeTaint::class;
  protected $taintsDataType = 'array';
  protected $vsphereConfigType = VmwareVsphereConfig::class;
  protected $vsphereConfigDataType = '';

  /**
   * VMware disk size to be used during creation.
   *
   * @param string $bootDiskSizeGb
   */
  public function setBootDiskSizeGb($bootDiskSizeGb)
  {
    $this->bootDiskSizeGb = $bootDiskSizeGb;
  }
  /**
   * @return string
   */
  public function getBootDiskSizeGb()
  {
    return $this->bootDiskSizeGb;
  }
  /**
   * The number of CPUs for each node in the node pool.
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
   * Allow node pool traffic to be load balanced. Only works for clusters with
   * MetalLB load balancers.
   *
   * @param bool $enableLoadBalancer
   */
  public function setEnableLoadBalancer($enableLoadBalancer)
  {
    $this->enableLoadBalancer = $enableLoadBalancer;
  }
  /**
   * @return bool
   */
  public function getEnableLoadBalancer()
  {
    return $this->enableLoadBalancer;
  }
  /**
   * The OS image name in vCenter, only valid when using Windows.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Required. The OS image to be used for each node in a node pool. Currently
   * `cos`, `cos_cgv2`, `ubuntu`, `ubuntu_cgv2`, `ubuntu_containerd` and
   * `windows` are supported.
   *
   * @param string $imageType
   */
  public function setImageType($imageType)
  {
    $this->imageType = $imageType;
  }
  /**
   * @return string
   */
  public function getImageType()
  {
    return $this->imageType;
  }
  /**
   * The map of Kubernetes labels (key/value pairs) to be applied to each node.
   * These will added in addition to any default label(s) that Kubernetes may
   * apply to the node. In case of conflict in label keys, the applied set may
   * differ depending on the Kubernetes version -- it's best to assume the
   * behavior is undefined and conflicts should be avoided. For more
   * information, including usage and the valid values, see:
   * https://kubernetes.io/docs/concepts/overview/working-with-objects/labels/
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
   * The megabytes of memory for each node in the node pool.
   *
   * @param string $memoryMb
   */
  public function setMemoryMb($memoryMb)
  {
    $this->memoryMb = $memoryMb;
  }
  /**
   * @return string
   */
  public function getMemoryMb()
  {
    return $this->memoryMb;
  }
  /**
   * The number of nodes in the node pool.
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
  /**
   * Specifies the vSphere config for node pool.
   *
   * @param VmwareVsphereConfig $vsphereConfig
   */
  public function setVsphereConfig(VmwareVsphereConfig $vsphereConfig)
  {
    $this->vsphereConfig = $vsphereConfig;
  }
  /**
   * @return VmwareVsphereConfig
   */
  public function getVsphereConfig()
  {
    return $this->vsphereConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VmwareNodeConfig::class, 'Google_Service_GKEOnPrem_VmwareNodeConfig');
