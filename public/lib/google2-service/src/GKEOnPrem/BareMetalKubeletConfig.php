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

class BareMetalKubeletConfig extends \Google\Model
{
  /**
   * The maximum size of bursty pulls, temporarily allows pulls to burst to this
   * number, while still not exceeding registry_pull_qps. The value must not be
   * a negative number. Updating this field may impact scalability by changing
   * the amount of traffic produced by image pulls. Defaults to 10.
   *
   * @var int
   */
  public $registryBurst;
  /**
   * The limit of registry pulls per second. Setting this value to 0 means no
   * limit. Updating this field may impact scalability by changing the amount of
   * traffic produced by image pulls. Defaults to 5.
   *
   * @var int
   */
  public $registryPullQps;
  /**
   * Prevents the Kubelet from pulling multiple images at a time. We recommend
   * *not* changing the default value on nodes that run docker daemon with
   * version < 1.9 or an Another Union File System (Aufs) storage backend. Issue
   * https://github.com/kubernetes/kubernetes/issues/10959 has more details.
   *
   * @var bool
   */
  public $serializeImagePullsDisabled;

  /**
   * The maximum size of bursty pulls, temporarily allows pulls to burst to this
   * number, while still not exceeding registry_pull_qps. The value must not be
   * a negative number. Updating this field may impact scalability by changing
   * the amount of traffic produced by image pulls. Defaults to 10.
   *
   * @param int $registryBurst
   */
  public function setRegistryBurst($registryBurst)
  {
    $this->registryBurst = $registryBurst;
  }
  /**
   * @return int
   */
  public function getRegistryBurst()
  {
    return $this->registryBurst;
  }
  /**
   * The limit of registry pulls per second. Setting this value to 0 means no
   * limit. Updating this field may impact scalability by changing the amount of
   * traffic produced by image pulls. Defaults to 5.
   *
   * @param int $registryPullQps
   */
  public function setRegistryPullQps($registryPullQps)
  {
    $this->registryPullQps = $registryPullQps;
  }
  /**
   * @return int
   */
  public function getRegistryPullQps()
  {
    return $this->registryPullQps;
  }
  /**
   * Prevents the Kubelet from pulling multiple images at a time. We recommend
   * *not* changing the default value on nodes that run docker daemon with
   * version < 1.9 or an Another Union File System (Aufs) storage backend. Issue
   * https://github.com/kubernetes/kubernetes/issues/10959 has more details.
   *
   * @param bool $serializeImagePullsDisabled
   */
  public function setSerializeImagePullsDisabled($serializeImagePullsDisabled)
  {
    $this->serializeImagePullsDisabled = $serializeImagePullsDisabled;
  }
  /**
   * @return bool
   */
  public function getSerializeImagePullsDisabled()
  {
    return $this->serializeImagePullsDisabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BareMetalKubeletConfig::class, 'Google_Service_GKEOnPrem_BareMetalKubeletConfig');
