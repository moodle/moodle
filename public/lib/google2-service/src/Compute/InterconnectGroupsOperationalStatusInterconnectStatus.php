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

namespace Google\Service\Compute;

class InterconnectGroupsOperationalStatusInterconnectStatus extends \Google\Model
{
  public const IS_ACTIVE_ACTIVE = 'ACTIVE';
  public const IS_ACTIVE_INACTIVE = 'INACTIVE';
  public const IS_ACTIVE_IS_ACTIVE_UNSPECIFIED = 'IS_ACTIVE_UNSPECIFIED';
  /**
   * Output only. Whether the Interconnect is enabled.
   *
   * @var bool
   */
  public $adminEnabled;
  protected $diagnosticsType = InterconnectDiagnostics::class;
  protected $diagnosticsDataType = '';
  /**
   * Output only. The URL of the Interconnect being described.
   *
   * @var string
   */
  public $interconnect;
  /**
   * Output only. Whether this interconnect is participating in the redundant
   * configuration.
   *
   * @var string
   */
  public $isActive;

  /**
   * Output only. Whether the Interconnect is enabled.
   *
   * @param bool $adminEnabled
   */
  public function setAdminEnabled($adminEnabled)
  {
    $this->adminEnabled = $adminEnabled;
  }
  /**
   * @return bool
   */
  public function getAdminEnabled()
  {
    return $this->adminEnabled;
  }
  /**
   * Output only. The diagnostics of the Interconnect, as returned by the
   * existing get-diagnostics method.
   *
   * @param InterconnectDiagnostics $diagnostics
   */
  public function setDiagnostics(InterconnectDiagnostics $diagnostics)
  {
    $this->diagnostics = $diagnostics;
  }
  /**
   * @return InterconnectDiagnostics
   */
  public function getDiagnostics()
  {
    return $this->diagnostics;
  }
  /**
   * Output only. The URL of the Interconnect being described.
   *
   * @param string $interconnect
   */
  public function setInterconnect($interconnect)
  {
    $this->interconnect = $interconnect;
  }
  /**
   * @return string
   */
  public function getInterconnect()
  {
    return $this->interconnect;
  }
  /**
   * Output only. Whether this interconnect is participating in the redundant
   * configuration.
   *
   * Accepted values: ACTIVE, INACTIVE, IS_ACTIVE_UNSPECIFIED
   *
   * @param self::IS_ACTIVE_* $isActive
   */
  public function setIsActive($isActive)
  {
    $this->isActive = $isActive;
  }
  /**
   * @return self::IS_ACTIVE_*
   */
  public function getIsActive()
  {
    return $this->isActive;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectGroupsOperationalStatusInterconnectStatus::class, 'Google_Service_Compute_InterconnectGroupsOperationalStatusInterconnectStatus');
