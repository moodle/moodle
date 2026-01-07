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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2DiscoveryBigQueryFilter extends \Google\Model
{
  protected $otherTablesType = GooglePrivacyDlpV2AllOtherBigQueryTables::class;
  protected $otherTablesDataType = '';
  protected $tableReferenceType = GooglePrivacyDlpV2TableReference::class;
  protected $tableReferenceDataType = '';
  protected $tablesType = GooglePrivacyDlpV2BigQueryTableCollection::class;
  protected $tablesDataType = '';

  /**
   * Catch-all. This should always be the last filter in the list because
   * anything above it will apply first. Should only appear once in a
   * configuration. If none is specified, a default one will be added
   * automatically.
   *
   * @param GooglePrivacyDlpV2AllOtherBigQueryTables $otherTables
   */
  public function setOtherTables(GooglePrivacyDlpV2AllOtherBigQueryTables $otherTables)
  {
    $this->otherTables = $otherTables;
  }
  /**
   * @return GooglePrivacyDlpV2AllOtherBigQueryTables
   */
  public function getOtherTables()
  {
    return $this->otherTables;
  }
  /**
   * The table to scan. Discovery configurations including this can only include
   * one DiscoveryTarget (the DiscoveryTarget with this TableReference).
   *
   * @param GooglePrivacyDlpV2TableReference $tableReference
   */
  public function setTableReference(GooglePrivacyDlpV2TableReference $tableReference)
  {
    $this->tableReference = $tableReference;
  }
  /**
   * @return GooglePrivacyDlpV2TableReference
   */
  public function getTableReference()
  {
    return $this->tableReference;
  }
  /**
   * A specific set of tables for this filter to apply to. A table collection
   * must be specified in only one filter per config. If a table id or dataset
   * is empty, Cloud DLP assumes all tables in that collection must be profiled.
   * Must specify a project ID.
   *
   * @param GooglePrivacyDlpV2BigQueryTableCollection $tables
   */
  public function setTables(GooglePrivacyDlpV2BigQueryTableCollection $tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTableCollection
   */
  public function getTables()
  {
    return $this->tables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DiscoveryBigQueryFilter::class, 'Google_Service_DLP_GooglePrivacyDlpV2DiscoveryBigQueryFilter');
