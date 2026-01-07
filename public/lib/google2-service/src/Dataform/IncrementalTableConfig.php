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

class IncrementalTableConfig extends \Google\Collection
{
  protected $collection_key = 'uniqueKeyParts';
  /**
   * SQL statements to be executed after inserting new rows into the relation.
   *
   * @var string[]
   */
  public $incrementalPostOperations;
  /**
   * SQL statements to be executed before inserting new rows into the relation.
   *
   * @var string[]
   */
  public $incrementalPreOperations;
  /**
   * The SELECT query which returns rows which should be inserted into the
   * relation if it already exists and is not being refreshed.
   *
   * @var string
   */
  public $incrementalSelectQuery;
  /**
   * Whether this table should be protected from being refreshed.
   *
   * @var bool
   */
  public $refreshDisabled;
  /**
   * A set of columns or SQL expressions used to define row uniqueness. If any
   * duplicates are discovered (as defined by `unique_key_parts`), only the
   * newly selected rows (as defined by `incremental_select_query`) will be
   * included in the relation.
   *
   * @var string[]
   */
  public $uniqueKeyParts;
  /**
   * A SQL expression conditional used to limit the set of existing rows
   * considered for a merge operation (see `unique_key_parts` for more
   * information).
   *
   * @var string
   */
  public $updatePartitionFilter;

  /**
   * SQL statements to be executed after inserting new rows into the relation.
   *
   * @param string[] $incrementalPostOperations
   */
  public function setIncrementalPostOperations($incrementalPostOperations)
  {
    $this->incrementalPostOperations = $incrementalPostOperations;
  }
  /**
   * @return string[]
   */
  public function getIncrementalPostOperations()
  {
    return $this->incrementalPostOperations;
  }
  /**
   * SQL statements to be executed before inserting new rows into the relation.
   *
   * @param string[] $incrementalPreOperations
   */
  public function setIncrementalPreOperations($incrementalPreOperations)
  {
    $this->incrementalPreOperations = $incrementalPreOperations;
  }
  /**
   * @return string[]
   */
  public function getIncrementalPreOperations()
  {
    return $this->incrementalPreOperations;
  }
  /**
   * The SELECT query which returns rows which should be inserted into the
   * relation if it already exists and is not being refreshed.
   *
   * @param string $incrementalSelectQuery
   */
  public function setIncrementalSelectQuery($incrementalSelectQuery)
  {
    $this->incrementalSelectQuery = $incrementalSelectQuery;
  }
  /**
   * @return string
   */
  public function getIncrementalSelectQuery()
  {
    return $this->incrementalSelectQuery;
  }
  /**
   * Whether this table should be protected from being refreshed.
   *
   * @param bool $refreshDisabled
   */
  public function setRefreshDisabled($refreshDisabled)
  {
    $this->refreshDisabled = $refreshDisabled;
  }
  /**
   * @return bool
   */
  public function getRefreshDisabled()
  {
    return $this->refreshDisabled;
  }
  /**
   * A set of columns or SQL expressions used to define row uniqueness. If any
   * duplicates are discovered (as defined by `unique_key_parts`), only the
   * newly selected rows (as defined by `incremental_select_query`) will be
   * included in the relation.
   *
   * @param string[] $uniqueKeyParts
   */
  public function setUniqueKeyParts($uniqueKeyParts)
  {
    $this->uniqueKeyParts = $uniqueKeyParts;
  }
  /**
   * @return string[]
   */
  public function getUniqueKeyParts()
  {
    return $this->uniqueKeyParts;
  }
  /**
   * A SQL expression conditional used to limit the set of existing rows
   * considered for a merge operation (see `unique_key_parts` for more
   * information).
   *
   * @param string $updatePartitionFilter
   */
  public function setUpdatePartitionFilter($updatePartitionFilter)
  {
    $this->updatePartitionFilter = $updatePartitionFilter;
  }
  /**
   * @return string
   */
  public function getUpdatePartitionFilter()
  {
    return $this->updatePartitionFilter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IncrementalTableConfig::class, 'Google_Service_Dataform_IncrementalTableConfig');
