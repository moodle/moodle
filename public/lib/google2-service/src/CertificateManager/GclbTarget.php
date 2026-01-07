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

namespace Google\Service\CertificateManager;

class GclbTarget extends \Google\Collection
{
  protected $collection_key = 'ipConfigs';
  protected $ipConfigsType = IpConfig::class;
  protected $ipConfigsDataType = 'array';
  /**
   * Output only. This field returns the resource name in the following format:
   * `//compute.googleapis.com/projects/global/targetHttpsProxies`.
   *
   * @var string
   */
  public $targetHttpsProxy;
  /**
   * Output only. This field returns the resource name in the following format:
   * `//compute.googleapis.com/projects/global/targetSslProxies`.
   *
   * @var string
   */
  public $targetSslProxy;

  /**
   * Output only. IP configurations for this Target Proxy where the Certificate
   * Map is serving.
   *
   * @param IpConfig[] $ipConfigs
   */
  public function setIpConfigs($ipConfigs)
  {
    $this->ipConfigs = $ipConfigs;
  }
  /**
   * @return IpConfig[]
   */
  public function getIpConfigs()
  {
    return $this->ipConfigs;
  }
  /**
   * Output only. This field returns the resource name in the following format:
   * `//compute.googleapis.com/projects/global/targetHttpsProxies`.
   *
   * @param string $targetHttpsProxy
   */
  public function setTargetHttpsProxy($targetHttpsProxy)
  {
    $this->targetHttpsProxy = $targetHttpsProxy;
  }
  /**
   * @return string
   */
  public function getTargetHttpsProxy()
  {
    return $this->targetHttpsProxy;
  }
  /**
   * Output only. This field returns the resource name in the following format:
   * `//compute.googleapis.com/projects/global/targetSslProxies`.
   *
   * @param string $targetSslProxy
   */
  public function setTargetSslProxy($targetSslProxy)
  {
    $this->targetSslProxy = $targetSslProxy;
  }
  /**
   * @return string
   */
  public function getTargetSslProxy()
  {
    return $this->targetSslProxy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GclbTarget::class, 'Google_Service_CertificateManager_GclbTarget');
