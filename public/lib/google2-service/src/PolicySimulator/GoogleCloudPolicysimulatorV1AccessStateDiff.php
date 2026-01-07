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

namespace Google\Service\PolicySimulator;

class GoogleCloudPolicysimulatorV1AccessStateDiff extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const ACCESS_CHANGE_ACCESS_CHANGE_TYPE_UNSPECIFIED = 'ACCESS_CHANGE_TYPE_UNSPECIFIED';
  /**
   * The principal's access did not change. This includes the case where both
   * baseline and simulated are UNKNOWN, but the unknown information is
   * equivalent.
   */
  public const ACCESS_CHANGE_NO_CHANGE = 'NO_CHANGE';
  /**
   * The principal's access under both the current policies and the proposed
   * policies is `UNKNOWN`, but the unknown information differs between them.
   */
  public const ACCESS_CHANGE_UNKNOWN_CHANGE = 'UNKNOWN_CHANGE';
  /**
   * The principal had access under the current policies (`GRANTED`), but will
   * no longer have access after the proposed changes (`NOT_GRANTED`).
   */
  public const ACCESS_CHANGE_ACCESS_REVOKED = 'ACCESS_REVOKED';
  /**
   * The principal did not have access under the current policies
   * (`NOT_GRANTED`), but will have access after the proposed changes
   * (`GRANTED`).
   */
  public const ACCESS_CHANGE_ACCESS_GAINED = 'ACCESS_GAINED';
  /**
   * This result can occur for the following reasons: * The principal had access
   * under the current policies (`GRANTED`), but their access after the proposed
   * changes is `UNKNOWN`. * The principal's access under the current policies
   * is `UNKNOWN`, but they will not have access after the proposed changes
   * (`NOT_GRANTED`).
   */
  public const ACCESS_CHANGE_ACCESS_MAYBE_REVOKED = 'ACCESS_MAYBE_REVOKED';
  /**
   * This result can occur for the following reasons: * The principal did not
   * have access under the current policies (`NOT_GRANTED`), but their access
   * after the proposed changes is `UNKNOWN`. * The principal's access under the
   * current policies is `UNKNOWN`, but they will have access after the proposed
   * changes (`GRANTED`).
   */
  public const ACCESS_CHANGE_ACCESS_MAYBE_GAINED = 'ACCESS_MAYBE_GAINED';
  /**
   * How the principal's access, specified in the AccessState field, changed
   * between the current (baseline) policies and proposed (simulated) policies.
   *
   * @var string
   */
  public $accessChange;
  protected $baselineType = GoogleCloudPolicysimulatorV1ExplainedAccess::class;
  protected $baselineDataType = '';
  protected $simulatedType = GoogleCloudPolicysimulatorV1ExplainedAccess::class;
  protected $simulatedDataType = '';

  /**
   * How the principal's access, specified in the AccessState field, changed
   * between the current (baseline) policies and proposed (simulated) policies.
   *
   * Accepted values: ACCESS_CHANGE_TYPE_UNSPECIFIED, NO_CHANGE, UNKNOWN_CHANGE,
   * ACCESS_REVOKED, ACCESS_GAINED, ACCESS_MAYBE_REVOKED, ACCESS_MAYBE_GAINED
   *
   * @param self::ACCESS_CHANGE_* $accessChange
   */
  public function setAccessChange($accessChange)
  {
    $this->accessChange = $accessChange;
  }
  /**
   * @return self::ACCESS_CHANGE_*
   */
  public function getAccessChange()
  {
    return $this->accessChange;
  }
  /**
   * The results of evaluating the access tuple under the current (baseline)
   * policies. If the AccessState couldn't be fully evaluated, this field
   * explains why.
   *
   * @param GoogleCloudPolicysimulatorV1ExplainedAccess $baseline
   */
  public function setBaseline(GoogleCloudPolicysimulatorV1ExplainedAccess $baseline)
  {
    $this->baseline = $baseline;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ExplainedAccess
   */
  public function getBaseline()
  {
    return $this->baseline;
  }
  /**
   * The results of evaluating the access tuple under the proposed (simulated)
   * policies. If the AccessState couldn't be fully evaluated, this field
   * explains why.
   *
   * @param GoogleCloudPolicysimulatorV1ExplainedAccess $simulated
   */
  public function setSimulated(GoogleCloudPolicysimulatorV1ExplainedAccess $simulated)
  {
    $this->simulated = $simulated;
  }
  /**
   * @return GoogleCloudPolicysimulatorV1ExplainedAccess
   */
  public function getSimulated()
  {
    return $this->simulated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1AccessStateDiff::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1AccessStateDiff');
