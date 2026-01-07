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

class InterconnectAttachmentGroupConfiguredAvailabilitySLA extends \Google\Collection
{
  public const EFFECTIVE_SLA_EFFECTIVE_SLA_UNSPECIFIED = 'EFFECTIVE_SLA_UNSPECIFIED';
  public const EFFECTIVE_SLA_NO_SLA = 'NO_SLA';
  public const EFFECTIVE_SLA_PRODUCTION_CRITICAL = 'PRODUCTION_CRITICAL';
  public const EFFECTIVE_SLA_PRODUCTION_NON_CRITICAL = 'PRODUCTION_NON_CRITICAL';
  protected $collection_key = 'intendedSlaBlockers';
  /**
   * @var string
   */
  public $effectiveSla;
  protected $intendedSlaBlockersType = InterconnectAttachmentGroupConfiguredAvailabilitySLAIntendedSlaBlockers::class;
  protected $intendedSlaBlockersDataType = 'array';

  /**
   * @param self::EFFECTIVE_SLA_* $effectiveSla
   */
  public function setEffectiveSla($effectiveSla)
  {
    $this->effectiveSla = $effectiveSla;
  }
  /**
   * @return self::EFFECTIVE_SLA_*
   */
  public function getEffectiveSla()
  {
    return $this->effectiveSla;
  }
  /**
   * @param InterconnectAttachmentGroupConfiguredAvailabilitySLAIntendedSlaBlockers[] $intendedSlaBlockers
   */
  public function setIntendedSlaBlockers($intendedSlaBlockers)
  {
    $this->intendedSlaBlockers = $intendedSlaBlockers;
  }
  /**
   * @return InterconnectAttachmentGroupConfiguredAvailabilitySLAIntendedSlaBlockers[]
   */
  public function getIntendedSlaBlockers()
  {
    return $this->intendedSlaBlockers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentGroupConfiguredAvailabilitySLA::class, 'Google_Service_Compute_InterconnectAttachmentGroupConfiguredAvailabilitySLA');
