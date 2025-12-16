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

class DynamicGroupQuery extends \Google\Model
{
  /**
   * Default value (not valid)
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * For queries on User
   */
  public const RESOURCE_TYPE_USER = 'USER';
  /**
   * Query that determines the memberships of the dynamic group. Examples: All
   * users with at least one `organizations.department` of engineering.
   * `user.organizations.exists(org, org.department=='engineering')` All users
   * with at least one location that has `area` of `foo` and `building_id` of
   * `bar`. `user.locations.exists(loc, loc.area=='foo' &&
   * loc.building_id=='bar')` All users with any variation of the name John Doe
   * (case-insensitive queries add `equalsIgnoreCase()` to the value being
   * queried). `user.name.value.equalsIgnoreCase('jOhn DoE')`
   *
   * @var string
   */
  public $query;
  /**
   * Resource type for the Dynamic Group Query
   *
   * @var string
   */
  public $resourceType;

  /**
   * Query that determines the memberships of the dynamic group. Examples: All
   * users with at least one `organizations.department` of engineering.
   * `user.organizations.exists(org, org.department=='engineering')` All users
   * with at least one location that has `area` of `foo` and `building_id` of
   * `bar`. `user.locations.exists(loc, loc.area=='foo' &&
   * loc.building_id=='bar')` All users with any variation of the name John Doe
   * (case-insensitive queries add `equalsIgnoreCase()` to the value being
   * queried). `user.name.value.equalsIgnoreCase('jOhn DoE')`
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
  /**
   * Resource type for the Dynamic Group Query
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, USER
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicGroupQuery::class, 'Google_Service_CloudIdentity_DynamicGroupQuery');
