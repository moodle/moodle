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

namespace Google\Service\Spanner;

class ShortRepresentation extends \Google\Model
{
  /**
   * A string representation of the expression subtree rooted at this node.
   *
   * @var string
   */
  public $description;
  /**
   * A mapping of (subquery variable name) -> (subquery node id) for cases where
   * the `description` string of this node references a `SCALAR` subquery
   * contained in the expression subtree rooted at this node. The referenced
   * `SCALAR` subquery may not necessarily be a direct child of this node.
   *
   * @var int[]
   */
  public $subqueries;

  /**
   * A string representation of the expression subtree rooted at this node.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A mapping of (subquery variable name) -> (subquery node id) for cases where
   * the `description` string of this node references a `SCALAR` subquery
   * contained in the expression subtree rooted at this node. The referenced
   * `SCALAR` subquery may not necessarily be a direct child of this node.
   *
   * @param int[] $subqueries
   */
  public function setSubqueries($subqueries)
  {
    $this->subqueries = $subqueries;
  }
  /**
   * @return int[]
   */
  public function getSubqueries()
  {
    return $this->subqueries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ShortRepresentation::class, 'Google_Service_Spanner_ShortRepresentation');
