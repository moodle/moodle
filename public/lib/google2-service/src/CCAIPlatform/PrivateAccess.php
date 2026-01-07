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

namespace Google\Service\CCAIPlatform;

class PrivateAccess extends \Google\Collection
{
  protected $collection_key = 'ingressSettings';
  protected $egressSettingsType = Component::class;
  protected $egressSettingsDataType = 'array';
  protected $ingressSettingsType = Component::class;
  protected $ingressSettingsDataType = 'array';
  protected $pscSettingType = PscSetting::class;
  protected $pscSettingDataType = '';

  /**
   * List of egress components that should not be accessed via the Internet. For
   * more information see go/ccaip-private-path-v2.
   *
   * @param Component[] $egressSettings
   */
  public function setEgressSettings($egressSettings)
  {
    $this->egressSettings = $egressSettings;
  }
  /**
   * @return Component[]
   */
  public function getEgressSettings()
  {
    return $this->egressSettings;
  }
  /**
   * List of ingress components that should not be accessed via the Internet.
   * For more information see go/ccaip-private-path-v2.
   *
   * @param Component[] $ingressSettings
   */
  public function setIngressSettings($ingressSettings)
  {
    $this->ingressSettings = $ingressSettings;
  }
  /**
   * @return Component[]
   */
  public function getIngressSettings()
  {
    return $this->ingressSettings;
  }
  /**
   * Private service connect settings.
   *
   * @param PscSetting $pscSetting
   */
  public function setPscSetting(PscSetting $pscSetting)
  {
    $this->pscSetting = $pscSetting;
  }
  /**
   * @return PscSetting
   */
  public function getPscSetting()
  {
    return $this->pscSetting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PrivateAccess::class, 'Google_Service_CCAIPlatform_PrivateAccess');
