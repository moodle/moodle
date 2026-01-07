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

namespace Google\Service\CloudSupport;

class Escalation extends \Google\Model
{
  /**
   * The escalation reason is in an unknown state or has not been specified.
   */
  public const REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * The case is taking too long to resolve.
   */
  public const REASON_RESOLUTION_TIME = 'RESOLUTION_TIME';
  /**
   * The support agent does not have the expertise required to successfully
   * resolve the issue.
   */
  public const REASON_TECHNICAL_EXPERTISE = 'TECHNICAL_EXPERTISE';
  /**
   * The issue is having a significant business impact.
   */
  public const REASON_BUSINESS_IMPACT = 'BUSINESS_IMPACT';
  /**
   * Required. A free text description to accompany the `reason` field above.
   * Provides additional context on why the case is being escalated.
   *
   * @var string
   */
  public $justification;
  /**
   * Required. The reason why the Case is being escalated.
   *
   * @var string
   */
  public $reason;

  /**
   * Required. A free text description to accompany the `reason` field above.
   * Provides additional context on why the case is being escalated.
   *
   * @param string $justification
   */
  public function setJustification($justification)
  {
    $this->justification = $justification;
  }
  /**
   * @return string
   */
  public function getJustification()
  {
    return $this->justification;
  }
  /**
   * Required. The reason why the Case is being escalated.
   *
   * Accepted values: REASON_UNSPECIFIED, RESOLUTION_TIME, TECHNICAL_EXPERTISE,
   * BUSINESS_IMPACT
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Escalation::class, 'Google_Service_CloudSupport_Escalation');
