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

namespace Google\Service\CloudDeploy;

class RuntimeConfig extends \Google\Model
{
  protected $cloudRunType = CloudRunConfig::class;
  protected $cloudRunDataType = '';
  protected $kubernetesType = KubernetesConfig::class;
  protected $kubernetesDataType = '';

  /**
   * Optional. Cloud Run runtime configuration.
   *
   * @param CloudRunConfig $cloudRun
   */
  public function setCloudRun(CloudRunConfig $cloudRun)
  {
    $this->cloudRun = $cloudRun;
  }
  /**
   * @return CloudRunConfig
   */
  public function getCloudRun()
  {
    return $this->cloudRun;
  }
  /**
   * Optional. Kubernetes runtime configuration.
   *
   * @param KubernetesConfig $kubernetes
   */
  public function setKubernetes(KubernetesConfig $kubernetes)
  {
    $this->kubernetes = $kubernetes;
  }
  /**
   * @return KubernetesConfig
   */
  public function getKubernetes()
  {
    return $this->kubernetes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RuntimeConfig::class, 'Google_Service_CloudDeploy_RuntimeConfig');
