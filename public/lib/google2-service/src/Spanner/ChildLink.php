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

class ChildLink extends \Google\Model
{
  /**
   * The node to which the link points.
   *
   * @var int
   */
  public $childIndex;
  /**
   * The type of the link. For example, in Hash Joins this could be used to
   * distinguish between the build child and the probe child, or in the case of
   * the child being an output variable, to represent the tag associated with
   * the output variable.
   *
   * @var string
   */
  public $type;
  /**
   * Only present if the child node is SCALAR and corresponds to an output
   * variable of the parent node. The field carries the name of the output
   * variable. For example, a `TableScan` operator that reads rows from a table
   * will have child links to the `SCALAR` nodes representing the output
   * variables created for each column that is read by the operator. The
   * corresponding `variable` fields will be set to the variable names assigned
   * to the columns.
   *
   * @var string
   */
  public $variable;

  /**
   * The node to which the link points.
   *
   * @param int $childIndex
   */
  public function setChildIndex($childIndex)
  {
    $this->childIndex = $childIndex;
  }
  /**
   * @return int
   */
  public function getChildIndex()
  {
    return $this->childIndex;
  }
  /**
   * The type of the link. For example, in Hash Joins this could be used to
   * distinguish between the build child and the probe child, or in the case of
   * the child being an output variable, to represent the tag associated with
   * the output variable.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * Only present if the child node is SCALAR and corresponds to an output
   * variable of the parent node. The field carries the name of the output
   * variable. For example, a `TableScan` operator that reads rows from a table
   * will have child links to the `SCALAR` nodes representing the output
   * variables created for each column that is read by the operator. The
   * corresponding `variable` fields will be set to the variable names assigned
   * to the columns.
   *
   * @param string $variable
   */
  public function setVariable($variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return string
   */
  public function getVariable()
  {
    return $this->variable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChildLink::class, 'Google_Service_Spanner_ChildLink');
