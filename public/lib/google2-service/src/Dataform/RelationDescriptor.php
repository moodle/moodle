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

namespace Google\Service\Dataform;

class RelationDescriptor extends \Google\Collection
{
  protected $collection_key = 'columns';
  /**
   * A set of BigQuery labels that should be applied to the relation.
   *
   * @var string[]
   */
  public $bigqueryLabels;
  protected $columnsType = ColumnDescriptor::class;
  protected $columnsDataType = 'array';
  /**
   * A text description of the relation.
   *
   * @var string
   */
  public $description;

  /**
   * A set of BigQuery labels that should be applied to the relation.
   *
   * @param string[] $bigqueryLabels
   */
  public function setBigqueryLabels($bigqueryLabels)
  {
    $this->bigqueryLabels = $bigqueryLabels;
  }
  /**
   * @return string[]
   */
  public function getBigqueryLabels()
  {
    return $this->bigqueryLabels;
  }
  /**
   * A list of descriptions of columns within the relation.
   *
   * @param ColumnDescriptor[] $columns
   */
  public function setColumns($columns)
  {
    $this->columns = $columns;
  }
  /**
   * @return ColumnDescriptor[]
   */
  public function getColumns()
  {
    return $this->columns;
  }
  /**
   * A text description of the relation.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RelationDescriptor::class, 'Google_Service_Dataform_RelationDescriptor');
