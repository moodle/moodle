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

namespace Google\Service\Looker;

class ControlledEgressConfig extends \Google\Collection
{
  protected $collection_key = 'webProxyIps';
  /**
   * Optional. List of fully qualified domain names to be added to the allowlist
   * for outbound traffic.
   *
   * @var string[]
   */
  public $egressFqdns;
  /**
   * Optional. Whether marketplace is enabled.
   *
   * @var bool
   */
  public $marketplaceEnabled;
  /**
   * Output only. The list of IP addresses used by Secure Web Proxy for outbound
   * traffic.
   *
   * @var string[]
   */
  public $webProxyIps;

  /**
   * Optional. List of fully qualified domain names to be added to the allowlist
   * for outbound traffic.
   *
   * @param string[] $egressFqdns
   */
  public function setEgressFqdns($egressFqdns)
  {
    $this->egressFqdns = $egressFqdns;
  }
  /**
   * @return string[]
   */
  public function getEgressFqdns()
  {
    return $this->egressFqdns;
  }
  /**
   * Optional. Whether marketplace is enabled.
   *
   * @param bool $marketplaceEnabled
   */
  public function setMarketplaceEnabled($marketplaceEnabled)
  {
    $this->marketplaceEnabled = $marketplaceEnabled;
  }
  /**
   * @return bool
   */
  public function getMarketplaceEnabled()
  {
    return $this->marketplaceEnabled;
  }
  /**
   * Output only. The list of IP addresses used by Secure Web Proxy for outbound
   * traffic.
   *
   * @param string[] $webProxyIps
   */
  public function setWebProxyIps($webProxyIps)
  {
    $this->webProxyIps = $webProxyIps;
  }
  /**
   * @return string[]
   */
  public function getWebProxyIps()
  {
    return $this->webProxyIps;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ControlledEgressConfig::class, 'Google_Service_Looker_ControlledEgressConfig');
