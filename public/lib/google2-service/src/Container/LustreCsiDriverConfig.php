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

class LustreCsiDriverConfig extends \Google\Model
{
  /**
   * If set to true, the Lustre CSI driver will install Lustre kernel modules
   * using port 6988. This serves as a workaround for a port conflict with the
   * gke-metadata-server. This field is required ONLY under the following
   * conditions: 1. The GKE node version is older than 1.33.2-gke.4655000. 2.
   * You're connecting to a Lustre instance that has the 'gke-support-enabled'
   * flag. Deprecated: This flag is no longer required as of GKE node version
   * 1.33.2-gke.4655000, unless you are connecting to a Lustre instance that has
   * the `gke-support-enabled` flag.
   *
   * @deprecated
   * @var bool
   */
  public $enableLegacyLustrePort;
  /**
   * Whether the Lustre CSI driver is enabled for this cluster.
   *
   * @var bool
   */
  public $enabled;

  /**
   * If set to true, the Lustre CSI driver will install Lustre kernel modules
   * using port 6988. This serves as a workaround for a port conflict with the
   * gke-metadata-server. This field is required ONLY under the following
   * conditions: 1. The GKE node version is older than 1.33.2-gke.4655000. 2.
   * You're connecting to a Lustre instance that has the 'gke-support-enabled'
   * flag. Deprecated: This flag is no longer required as of GKE node version
   * 1.33.2-gke.4655000, unless you are connecting to a Lustre instance that has
   * the `gke-support-enabled` flag.
   *
   * @deprecated
   * @param bool $enableLegacyLustrePort
   */
  public function setEnableLegacyLustrePort($enableLegacyLustrePort)
  {
    $this->enableLegacyLustrePort = $enableLegacyLustrePort;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getEnableLegacyLustrePort()
  {
    return $this->enableLegacyLustrePort;
  }
  /**
   * Whether the Lustre CSI driver is enabled for this cluster.
   *
   * @param bool $enabled
   */
  public function setEnabled($enabled)
  {
    $this->enabled = $enabled;
  }
  /**
   * @return bool
   */
  public function getEnabled()
  {
    return $this->enabled;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LustreCsiDriverConfig::class, 'Google_Service_Container_LustreCsiDriverConfig');
