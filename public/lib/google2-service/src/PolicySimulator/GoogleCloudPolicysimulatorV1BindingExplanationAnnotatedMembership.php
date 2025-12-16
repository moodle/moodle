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

class GoogleCloudPolicysimulatorV1BindingExplanationAnnotatedMembership extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const MEMBERSHIP_MEMBERSHIP_UNSPECIFIED = 'MEMBERSHIP_UNSPECIFIED';
  /**
   * The binding includes the principal. The principal can be included directly
   * or indirectly. For example: * A principal is included directly if that
   * principal is listed in the binding. * A principal is included indirectly if
   * that principal is in a Google group or Google Workspace domain that is
   * listed in the binding.
   */
  public const MEMBERSHIP_MEMBERSHIP_INCLUDED = 'MEMBERSHIP_INCLUDED';
  /**
   * The binding does not include the principal.
   */
  public const MEMBERSHIP_MEMBERSHIP_NOT_INCLUDED = 'MEMBERSHIP_NOT_INCLUDED';
  /**
   * The user who created the Replay is not allowed to access the binding.
   */
  public const MEMBERSHIP_MEMBERSHIP_UNKNOWN_INFO_DENIED = 'MEMBERSHIP_UNKNOWN_INFO_DENIED';
  /**
   * The principal is an unsupported type. Only Google Accounts and service
   * accounts are supported.
   */
  public const MEMBERSHIP_MEMBERSHIP_UNKNOWN_UNSUPPORTED = 'MEMBERSHIP_UNKNOWN_UNSUPPORTED';
  /**
   * Default value. This value is unused.
   */
  public const RELEVANCE_HEURISTIC_RELEVANCE_UNSPECIFIED = 'HEURISTIC_RELEVANCE_UNSPECIFIED';
  /**
   * The data point has a limited effect on the result. Changing the data point
   * is unlikely to affect the overall determination.
   */
  public const RELEVANCE_NORMAL = 'NORMAL';
  /**
   * The data point has a strong effect on the result. Changing the data point
   * is likely to affect the overall determination.
   */
  public const RELEVANCE_HIGH = 'HIGH';
  /**
   * Indicates whether the binding includes the principal.
   *
   * @var string
   */
  public $membership;
  /**
   * The relevance of the principal's status to the overall determination for
   * the binding.
   *
   * @var string
   */
  public $relevance;

  /**
   * Indicates whether the binding includes the principal.
   *
   * Accepted values: MEMBERSHIP_UNSPECIFIED, MEMBERSHIP_INCLUDED,
   * MEMBERSHIP_NOT_INCLUDED, MEMBERSHIP_UNKNOWN_INFO_DENIED,
   * MEMBERSHIP_UNKNOWN_UNSUPPORTED
   *
   * @param self::MEMBERSHIP_* $membership
   */
  public function setMembership($membership)
  {
    $this->membership = $membership;
  }
  /**
   * @return self::MEMBERSHIP_*
   */
  public function getMembership()
  {
    return $this->membership;
  }
  /**
   * The relevance of the principal's status to the overall determination for
   * the binding.
   *
   * Accepted values: HEURISTIC_RELEVANCE_UNSPECIFIED, NORMAL, HIGH
   *
   * @param self::RELEVANCE_* $relevance
   */
  public function setRelevance($relevance)
  {
    $this->relevance = $relevance;
  }
  /**
   * @return self::RELEVANCE_*
   */
  public function getRelevance()
  {
    return $this->relevance;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudPolicysimulatorV1BindingExplanationAnnotatedMembership::class, 'Google_Service_PolicySimulator_GoogleCloudPolicysimulatorV1BindingExplanationAnnotatedMembership');
