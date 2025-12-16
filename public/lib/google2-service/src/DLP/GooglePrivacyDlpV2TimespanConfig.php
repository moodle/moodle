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

class GooglePrivacyDlpV2TimespanConfig extends \Google\Model
{
  /**
   * When the job is started by a JobTrigger we will automatically figure out a
   * valid start_time to avoid scanning files that have not been modified since
   * the last time the JobTrigger executed. This will be based on the time of
   * the execution of the last run of the JobTrigger or the timespan end_time
   * used in the last run of the JobTrigger. **For BigQuery** Inspect jobs
   * triggered by automatic population will scan data that is at least three
   * hours old when the job starts. This is because streaming buffer rows are
   * not read during inspection and reading up to the current timestamp will
   * result in skipped rows. See the [known
   * issue](https://cloud.google.com/sensitive-data-protection/docs/known-
   * issues#recently-streamed-data) related to this operation.
   *
   * @var bool
   */
  public $enableAutoPopulationOfTimespanConfig;
  /**
   * Exclude files, tables, or rows newer than this value. If not set, no upper
   * time limit is applied.
   *
   * @var string
   */
  public $endTime;
  /**
   * Exclude files, tables, or rows older than this value. If not set, no lower
   * time limit is applied.
   *
   * @var string
   */
  public $startTime;
  protected $timestampFieldType = GooglePrivacyDlpV2FieldId::class;
  protected $timestampFieldDataType = '';

  /**
   * When the job is started by a JobTrigger we will automatically figure out a
   * valid start_time to avoid scanning files that have not been modified since
   * the last time the JobTrigger executed. This will be based on the time of
   * the execution of the last run of the JobTrigger or the timespan end_time
   * used in the last run of the JobTrigger. **For BigQuery** Inspect jobs
   * triggered by automatic population will scan data that is at least three
   * hours old when the job starts. This is because streaming buffer rows are
   * not read during inspection and reading up to the current timestamp will
   * result in skipped rows. See the [known
   * issue](https://cloud.google.com/sensitive-data-protection/docs/known-
   * issues#recently-streamed-data) related to this operation.
   *
   * @param bool $enableAutoPopulationOfTimespanConfig
   */
  public function setEnableAutoPopulationOfTimespanConfig($enableAutoPopulationOfTimespanConfig)
  {
    $this->enableAutoPopulationOfTimespanConfig = $enableAutoPopulationOfTimespanConfig;
  }
  /**
   * @return bool
   */
  public function getEnableAutoPopulationOfTimespanConfig()
  {
    return $this->enableAutoPopulationOfTimespanConfig;
  }
  /**
   * Exclude files, tables, or rows newer than this value. If not set, no upper
   * time limit is applied.
   *
   * @param string $endTime
   */
  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }
  /**
   * @return string
   */
  public function getEndTime()
  {
    return $this->endTime;
  }
  /**
   * Exclude files, tables, or rows older than this value. If not set, no lower
   * time limit is applied.
   *
   * @param string $startTime
   */
  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }
  /**
   * @return string
   */
  public function getStartTime()
  {
    return $this->startTime;
  }
  /**
   * Specification of the field containing the timestamp of scanned items. Used
   * for data sources like Datastore and BigQuery. **For BigQuery** If this
   * value is not specified and the table was modified between the given start
   * and end times, the entire table will be scanned. If this value is
   * specified, then rows are filtered based on the given start and end times.
   * Rows with a `NULL` value in the provided BigQuery column are skipped. Valid
   * data types of the provided BigQuery column are: `INTEGER`, `DATE`,
   * `TIMESTAMP`, and `DATETIME`. If your BigQuery table is [partitioned at
   * ingestion time](https://cloud.google.com/bigquery/docs/partitioned-
   * tables#ingestion_time), you can use any of the following pseudo-columns as
   * your timestamp field. When used with Cloud DLP, these pseudo-column names
   * are case sensitive. - `_PARTITIONTIME` - `_PARTITIONDATE` -
   * `_PARTITION_LOAD_TIME` **For Datastore** If this value is specified, then
   * entities are filtered based on the given start and end times. If an entity
   * does not contain the provided timestamp property or contains empty or
   * invalid values, then it is included. Valid data types of the provided
   * timestamp property are: `TIMESTAMP`. See the [known
   * issue](https://cloud.google.com/sensitive-data-protection/docs/known-
   * issues#bq-timespan) related to this operation.
   *
   * @param GooglePrivacyDlpV2FieldId $timestampField
   */
  public function setTimestampField(GooglePrivacyDlpV2FieldId $timestampField)
  {
    $this->timestampField = $timestampField;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId
   */
  public function getTimestampField()
  {
    return $this->timestampField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TimespanConfig::class, 'Google_Service_DLP_GooglePrivacyDlpV2TimespanConfig');
