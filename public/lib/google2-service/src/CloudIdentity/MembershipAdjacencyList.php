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

class MembershipAdjacencyList extends \Google\Collection
{
  protected $collection_key = 'edges';
  protected $edgesType = Membership::class;
  protected $edgesDataType = 'array';
  /**
   * Resource name of the group that the members belong to.
   *
   * @var string
   */
  public $group;

  /**
   * Each edge contains information about the member that belongs to this group.
   * Note: Fields returned here will help identify the specific Membership
   * resource (e.g `name`, `preferred_member_key` and `role`), but may not be a
   * comprehensive list of all fields.
   *
   * @param Membership[] $edges
   */
  public function setEdges($edges)
  {
    $this->edges = $edges;
  }
  /**
   * @return Membership[]
   */
  public function getEdges()
  {
    return $this->edges;
  }
  /**
   * Resource name of the group that the members belong to.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MembershipAdjacencyList::class, 'Google_Service_CloudIdentity_MembershipAdjacencyList');
