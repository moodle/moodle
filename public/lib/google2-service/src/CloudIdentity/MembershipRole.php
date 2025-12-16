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

namespace Google\Service\CloudIdentity;

class MembershipRole extends \Google\Model
{
  protected $expiryDetailType = ExpiryDetail::class;
  protected $expiryDetailDataType = '';
  /**
   * The name of the `MembershipRole`. Must be one of `OWNER`, `MANAGER`,
   * `MEMBER`.
   *
   * @var string
   */
  public $name;
  protected $restrictionEvaluationsType = RestrictionEvaluations::class;
  protected $restrictionEvaluationsDataType = '';

  /**
   * The expiry details of the `MembershipRole`. Expiry details are only
   * supported for `MEMBER` `MembershipRoles`. May be set if `name` is `MEMBER`.
   * Must not be set if `name` is any other value.
   *
   * @param ExpiryDetail $expiryDetail
   */
  public function setExpiryDetail(ExpiryDetail $expiryDetail)
  {
    $this->expiryDetail = $expiryDetail;
  }
  /**
   * @return ExpiryDetail
   */
  public function getExpiryDetail()
  {
    return $this->expiryDetail;
  }
  /**
   * The name of the `MembershipRole`. Must be one of `OWNER`, `MANAGER`,
   * `MEMBER`.
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
   * Evaluations of restrictions applied to parent group on this membership.
   *
   * @param RestrictionEvaluations $restrictionEvaluations
   */
  public function setRestrictionEvaluations(RestrictionEvaluations $restrictionEvaluations)
  {
    $this->restrictionEvaluations = $restrictionEvaluations;
  }
  /**
   * @return RestrictionEvaluations
   */
  public function getRestrictionEvaluations()
  {
    return $this->restrictionEvaluations;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MembershipRole::class, 'Google_Service_CloudIdentity_MembershipRole');
