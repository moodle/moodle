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

class GooglePrivacyDlpV2Export extends \Google\Model
{
  protected $profileTableType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $profileTableDataType = '';
  protected $sampleFindingsTableType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $sampleFindingsTableDataType = '';

  /**
   * Store all profiles to BigQuery. * The system will create a new dataset and
   * table for you if none are are provided. The dataset will be named
   * `sensitive_data_protection_discovery` and table will be named
   * `discovery_profiles`. This table will be placed in the same project as the
   * container project running the scan. After the first profile is generated
   * and the dataset and table are created, the discovery scan configuration
   * will be updated with the dataset and table names. * See [Analyze data
   * profiles stored in BigQuery](https://cloud.google.com/sensitive-data-
   * protection/docs/analyze-data-profiles). * See [Sample queries for your
   * BigQuery table](https://cloud.google.com/sensitive-data-
   * protection/docs/analyze-data-profiles#sample_sql_queries). * Data is
   * inserted using [streaming
   * insert](https://cloud.google.com/blog/products/bigquery/life-of-a-bigquery-
   * streaming-insert) and so data may be in the buffer for a period of time
   * after the profile has finished. * The Pub/Sub notification is sent before
   * the streaming buffer is guaranteed to be written, so data may not be
   * instantly visible to queries by the time your topic receives the Pub/Sub
   * notification. * The best practice is to use the same table for an entire
   * organization so that you can take advantage of the [provided Looker
   * reports](https://cloud.google.com/sensitive-data-protection/docs/analyze-
   * data-profiles#use_a_premade_report). If you use VPC Service Controls to
   * define security perimeters, then you must use a separate table for each
   * boundary.
   *
   * @param GooglePrivacyDlpV2BigQueryTable $profileTable
   */
  public function setProfileTable(GooglePrivacyDlpV2BigQueryTable $profileTable)
  {
    $this->profileTable = $profileTable;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTable
   */
  public function getProfileTable()
  {
    return $this->profileTable;
  }
  /**
   * Store sample data profile findings in an existing table or a new table in
   * an existing dataset. Each regeneration will result in new rows in BigQuery.
   * Data is inserted using [streaming
   * insert](https://cloud.google.com/blog/products/bigquery/life-of-a-bigquery-
   * streaming-insert) and so data may be in the buffer for a period of time
   * after the profile has finished.
   *
   * @param GooglePrivacyDlpV2BigQueryTable $sampleFindingsTable
   */
  public function setSampleFindingsTable(GooglePrivacyDlpV2BigQueryTable $sampleFindingsTable)
  {
    $this->sampleFindingsTable = $sampleFindingsTable;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTable
   */
  public function getSampleFindingsTable()
  {
    return $this->sampleFindingsTable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Export::class, 'Google_Service_DLP_GooglePrivacyDlpV2Export');
