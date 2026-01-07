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

class GooglePrivacyDlpV2TransformationDetailsStorageConfig extends \Google\Model
{
  protected $tableType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $tableDataType = '';

  /**
   * The BigQuery table in which to store the output. This may be an existing
   * table or in a new table in an existing dataset. If table_id is not set a
   * new one will be generated for you with the following format:
   * dlp_googleapis_transformation_details_yyyy_mm_dd_[dlp_job_id]. Pacific time
   * zone will be used for generating the date details.
   *
   * @param GooglePrivacyDlpV2BigQueryTable $table
   */
  public function setTable(GooglePrivacyDlpV2BigQueryTable $table)
  {
    $this->table = $table;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTable
   */
  public function getTable()
  {
    return $this->table;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransformationDetailsStorageConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransformationDetailsStorageConfig');
