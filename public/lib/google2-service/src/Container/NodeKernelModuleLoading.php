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

class NodeKernelModuleLoading extends \Google\Model
{
  /**
   * Default behavior. GKE selects the image based on node type. For CPU and TPU
   * nodes, the image will not allow loading external kernel modules. For GPU
   * nodes, the image will allow loading any module, whether it is signed or
   * not.
   */
  public const POLICY_POLICY_UNSPECIFIED = 'POLICY_UNSPECIFIED';
  /**
   * Enforced signature verification: Node pools will use a Container-Optimized
   * OS image configured to allow loading of *Google-signed* external kernel
   * modules. Loadpin is enabled but configured to exclude modules, and kernel
   * module signature checking is enforced.
   */
  public const POLICY_ENFORCE_SIGNED_MODULES = 'ENFORCE_SIGNED_MODULES';
  /**
   * Mirrors existing DEFAULT behavior: For CPU and TPU nodes, the image will
   * not allow loading external kernel modules. For GPU nodes, the image will
   * allow loading any module, whether it is signed or not.
   */
  public const POLICY_DO_NOT_ENFORCE_SIGNED_MODULES = 'DO_NOT_ENFORCE_SIGNED_MODULES';
  /**
   * Set the node module loading policy for nodes in the node pool.
   *
   * @var string
   */
  public $policy;

  /**
   * Set the node module loading policy for nodes in the node pool.
   *
   * Accepted values: POLICY_UNSPECIFIED, ENFORCE_SIGNED_MODULES,
   * DO_NOT_ENFORCE_SIGNED_MODULES
   *
   * @param self::POLICY_* $policy
   */
  public function setPolicy($policy)
  {
    $this->policy = $policy;
  }
  /**
   * @return self::POLICY_*
   */
  public function getPolicy()
  {
    return $this->policy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NodeKernelModuleLoading::class, 'Google_Service_Container_NodeKernelModuleLoading');
