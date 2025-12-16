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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class OnPremDomainSIDDetails extends \Google\Model
{
  /**
   * SID Filtering is in unspecified state.
   */
  public const SID_FILTERING_STATE_SID_FILTERING_STATE_UNSPECIFIED = 'SID_FILTERING_STATE_UNSPECIFIED';
  /**
   * SID Filtering is Enabled.
   */
  public const SID_FILTERING_STATE_ENABLED = 'ENABLED';
  /**
   * SID Filtering is Disabled.
   */
  public const SID_FILTERING_STATE_DISABLED = 'DISABLED';
  /**
   * FQDN of the on-prem domain being migrated.
   *
   * @var string
   */
  public $name;
  /**
   * Current SID filtering state.
   *
   * @var string
   */
  public $sidFilteringState;

  /**
   * FQDN of the on-prem domain being migrated.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Current SID filtering state.
   *
   * Accepted values: SID_FILTERING_STATE_UNSPECIFIED, ENABLED, DISABLED
   *
   * @param self::SID_FILTERING_STATE_* $sidFilteringState
   */
  public function setSidFilteringState($sidFilteringState)
  {
    $this->sidFilteringState = $sidFilteringState;
  }
  /**
   * @return self::SID_FILTERING_STATE_*
   */
  public function getSidFilteringState()
  {
    return $this->sidFilteringState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OnPremDomainSIDDetails::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_OnPremDomainSIDDetails');
