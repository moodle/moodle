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

namespace Google\Service\Bigquery;

class StoredColumnsUsage extends \Google\Collection
{
  protected $collection_key = 'storedColumnsUnusedReasons';
  protected $baseTableType = TableReference::class;
  protected $baseTableDataType = '';
  /**
   * Specifies whether the query was accelerated with stored columns.
   *
   * @var bool
   */
  public $isQueryAccelerated;
  protected $storedColumnsUnusedReasonsType = StoredColumnsUnusedReason::class;
  protected $storedColumnsUnusedReasonsDataType = 'array';

  /**
   * Specifies the base table.
   *
   * @param TableReference $baseTable
   */
  public function setBaseTable(TableReference $baseTable)
  {
    $this->baseTable = $baseTable;
  }
  /**
   * @return TableReference
   */
  public function getBaseTable()
  {
    return $this->baseTable;
  }
  /**
   * Specifies whether the query was accelerated with stored columns.
   *
   * @param bool $isQueryAccelerated
   */
  public function setIsQueryAccelerated($isQueryAccelerated)
  {
    $this->isQueryAccelerated = $isQueryAccelerated;
  }
  /**
   * @return bool
   */
  public function getIsQueryAccelerated()
  {
    return $this->isQueryAccelerated;
  }
  /**
   * If stored columns were not used, explain why.
   *
   * @param StoredColumnsUnusedReason[] $storedColumnsUnusedReasons
   */
  public function setStoredColumnsUnusedReasons($storedColumnsUnusedReasons)
  {
    $this->storedColumnsUnusedReasons = $storedColumnsUnusedReasons;
  }
  /**
   * @return StoredColumnsUnusedReason[]
   */
  public function getStoredColumnsUnusedReasons()
  {
    return $this->storedColumnsUnusedReasons;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoredColumnsUsage::class, 'Google_Service_Bigquery_StoredColumnsUsage');
