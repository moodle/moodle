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

class PolicyQuery extends \Google\Model
{
  /**
   * Immutable. The group that the query applies to. This field is only set if
   * there is a single value for group that satisfies all clauses of the query.
   * If no group applies, this will be the empty string.
   *
   * @var string
   */
  public $group;
  /**
   * Required. Immutable. Non-empty default. The OrgUnit the query applies to.
   * This field is only set if there is a single value for org_unit that
   * satisfies all clauses of the query.
   *
   * @var string
   */
  public $orgUnit;
  /**
   * Immutable. The CEL query that defines which entities the Policy applies to
   * (ex. a User entity). For details about CEL see
   * https://opensource.google.com/projects/cel. The OrgUnits the Policy applies
   * to are represented by a clause like so: entity.org_units.exists(org_unit,
   * org_unit.org_unit_id == orgUnitId('{orgUnitId}')) The Group the Policy
   * applies to are represented by a clause like so: entity.groups.exists(group,
   * group.group_id == groupId('{groupId}')) The Licenses the Policy applies to
   * are represented by a clause like so: entity.licenses.exists(license,
   * license in ['/product/{productId}/sku/{skuId}']) The above clauses can be
   * present in any combination, and used in conjunction with the &&, || and !
   * operators. The org_unit and group fields below are helper fields that
   * contain the corresponding value(s) as the query to make the query easier to
   * use.
   *
   * @var string
   */
  public $query;
  /**
   * Output only. The decimal sort order of this PolicyQuery. The value is
   * relative to all other policies with the same setting type for the customer.
   * (There are no duplicates within this set).
   *
   * @var 
   */
  public $sortOrder;

  /**
   * Immutable. The group that the query applies to. This field is only set if
   * there is a single value for group that satisfies all clauses of the query.
   * If no group applies, this will be the empty string.
   *
   * @param string $group
   */
  public function setGroup($group)
  {
    $this->group = $group;
  }
  /**
   * @return string
   */
  public function getGroup()
  {
    return $this->group;
  }
  /**
   * Required. Immutable. Non-empty default. The OrgUnit the query applies to.
   * This field is only set if there is a single value for org_unit that
   * satisfies all clauses of the query.
   *
   * @param string $orgUnit
   */
  public function setOrgUnit($orgUnit)
  {
    $this->orgUnit = $orgUnit;
  }
  /**
   * @return string
   */
  public function getOrgUnit()
  {
    return $this->orgUnit;
  }
  /**
   * Immutable. The CEL query that defines which entities the Policy applies to
   * (ex. a User entity). For details about CEL see
   * https://opensource.google.com/projects/cel. The OrgUnits the Policy applies
   * to are represented by a clause like so: entity.org_units.exists(org_unit,
   * org_unit.org_unit_id == orgUnitId('{orgUnitId}')) The Group the Policy
   * applies to are represented by a clause like so: entity.groups.exists(group,
   * group.group_id == groupId('{groupId}')) The Licenses the Policy applies to
   * are represented by a clause like so: entity.licenses.exists(license,
   * license in ['/product/{productId}/sku/{skuId}']) The above clauses can be
   * present in any combination, and used in conjunction with the &&, || and !
   * operators. The org_unit and group fields below are helper fields that
   * contain the corresponding value(s) as the query to make the query easier to
   * use.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;
  }
  public function getSortOrder()
  {
    return $this->sortOrder;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PolicyQuery::class, 'Google_Service_CloudIdentity_PolicyQuery');
