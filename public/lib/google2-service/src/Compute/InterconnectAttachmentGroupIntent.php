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

class InterconnectAttachmentGroupIntent extends \Google\Model
{
  public const AVAILABILITY_SLA_AVAILABILITY_SLA_UNSPECIFIED = 'AVAILABILITY_SLA_UNSPECIFIED';
  public const AVAILABILITY_SLA_NO_SLA = 'NO_SLA';
  public const AVAILABILITY_SLA_PRODUCTION_CRITICAL = 'PRODUCTION_CRITICAL';
  public const AVAILABILITY_SLA_PRODUCTION_NON_CRITICAL = 'PRODUCTION_NON_CRITICAL';
  /**
   * @var string
   */
  public $availabilitySla;

  /**
   * @param self::AVAILABILITY_SLA_* $availabilitySla
   */
  public function setAvailabilitySla($availabilitySla)
  {
    $this->availabilitySla = $availabilitySla;
  }
  /**
   * @return self::AVAILABILITY_SLA_*
   */
  public function getAvailabilitySla()
  {
    return $this->availabilitySla;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectAttachmentGroupIntent::class, 'Google_Service_Compute_InterconnectAttachmentGroupIntent');
