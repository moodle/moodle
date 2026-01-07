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

class GooglePrivacyDlpV2BigQueryOptions extends \Google\Collection
{
  /**
   * No sampling.
   */
  public const SAMPLE_METHOD_SAMPLE_METHOD_UNSPECIFIED = 'SAMPLE_METHOD_UNSPECIFIED';
  /**
   * Scan groups of rows in the order BigQuery provides (default). Multiple
   * groups of rows may be scanned in parallel, so results may not appear in the
   * same order the rows are read.
   */
  public const SAMPLE_METHOD_TOP = 'TOP';
  /**
   * Randomly pick groups of rows to scan.
   */
  public const SAMPLE_METHOD_RANDOM_START = 'RANDOM_START';
  protected $collection_key = 'includedFields';
  protected $excludedFieldsType = GooglePrivacyDlpV2FieldId::class;
  protected $excludedFieldsDataType = 'array';
  protected $identifyingFieldsType = GooglePrivacyDlpV2FieldId::class;
  protected $identifyingFieldsDataType = 'array';
  protected $includedFieldsType = GooglePrivacyDlpV2FieldId::class;
  protected $includedFieldsDataType = 'array';
  /**
   * Max number of rows to scan. If the table has more rows than this value, the
   * rest of the rows are omitted. If not set, or if set to 0, all rows will be
   * scanned. Only one of rows_limit and rows_limit_percent can be specified.
   * Cannot be used in conjunction with TimespanConfig.
   *
   * @var string
   */
  public $rowsLimit;
  /**
   * Max percentage of rows to scan. The rest are omitted. The number of rows
   * scanned is rounded down. Must be between 0 and 100, inclusively. Both 0 and
   * 100 means no limit. Defaults to 0. Only one of rows_limit and
   * rows_limit_percent can be specified. Cannot be used in conjunction with
   * TimespanConfig. Caution: A [known
   * issue](https://cloud.google.com/sensitive-data-protection/docs/known-
   * issues#bq-sampling) is causing the `rowsLimitPercent` field to behave
   * unexpectedly. We recommend using `rowsLimit` instead.
   *
   * @var int
   */
  public $rowsLimitPercent;
  /**
   * How to sample the data.
   *
   * @var string
   */
  public $sampleMethod;
  protected $tableReferenceType = GooglePrivacyDlpV2BigQueryTable::class;
  protected $tableReferenceDataType = '';

  /**
   * References to fields excluded from scanning. This allows you to skip
   * inspection of entire columns which you know have no findings. When
   * inspecting a table, we recommend that you inspect all columns. Otherwise,
   * findings might be affected because hints from excluded columns will not be
   * used.
   *
   * @param GooglePrivacyDlpV2FieldId[] $excludedFields
   */
  public function setExcludedFields($excludedFields)
  {
    $this->excludedFields = $excludedFields;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId[]
   */
  public function getExcludedFields()
  {
    return $this->excludedFields;
  }
  /**
   * Table fields that may uniquely identify a row within the table. When
   * `actions.saveFindings.outputConfig.table` is specified, the values of
   * columns specified here are available in the output table under
   * `location.content_locations.record_location.record_key.id_values`. Nested
   * fields such as `person.birthdate.year` are allowed.
   *
   * @param GooglePrivacyDlpV2FieldId[] $identifyingFields
   */
  public function setIdentifyingFields($identifyingFields)
  {
    $this->identifyingFields = $identifyingFields;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId[]
   */
  public function getIdentifyingFields()
  {
    return $this->identifyingFields;
  }
  /**
   * Limit scanning only to these fields. When inspecting a table, we recommend
   * that you inspect all columns. Otherwise, findings might be affected because
   * hints from excluded columns will not be used.
   *
   * @param GooglePrivacyDlpV2FieldId[] $includedFields
   */
  public function setIncludedFields($includedFields)
  {
    $this->includedFields = $includedFields;
  }
  /**
   * @return GooglePrivacyDlpV2FieldId[]
   */
  public function getIncludedFields()
  {
    return $this->includedFields;
  }
  /**
   * Max number of rows to scan. If the table has more rows than this value, the
   * rest of the rows are omitted. If not set, or if set to 0, all rows will be
   * scanned. Only one of rows_limit and rows_limit_percent can be specified.
   * Cannot be used in conjunction with TimespanConfig.
   *
   * @param string $rowsLimit
   */
  public function setRowsLimit($rowsLimit)
  {
    $this->rowsLimit = $rowsLimit;
  }
  /**
   * @return string
   */
  public function getRowsLimit()
  {
    return $this->rowsLimit;
  }
  /**
   * Max percentage of rows to scan. The rest are omitted. The number of rows
   * scanned is rounded down. Must be between 0 and 100, inclusively. Both 0 and
   * 100 means no limit. Defaults to 0. Only one of rows_limit and
   * rows_limit_percent can be specified. Cannot be used in conjunction with
   * TimespanConfig. Caution: A [known
   * issue](https://cloud.google.com/sensitive-data-protection/docs/known-
   * issues#bq-sampling) is causing the `rowsLimitPercent` field to behave
   * unexpectedly. We recommend using `rowsLimit` instead.
   *
   * @param int $rowsLimitPercent
   */
  public function setRowsLimitPercent($rowsLimitPercent)
  {
    $this->rowsLimitPercent = $rowsLimitPercent;
  }
  /**
   * @return int
   */
  public function getRowsLimitPercent()
  {
    return $this->rowsLimitPercent;
  }
  /**
   * How to sample the data.
   *
   * Accepted values: SAMPLE_METHOD_UNSPECIFIED, TOP, RANDOM_START
   *
   * @param self::SAMPLE_METHOD_* $sampleMethod
   */
  public function setSampleMethod($sampleMethod)
  {
    $this->sampleMethod = $sampleMethod;
  }
  /**
   * @return self::SAMPLE_METHOD_*
   */
  public function getSampleMethod()
  {
    return $this->sampleMethod;
  }
  /**
   * Complete BigQuery table reference.
   *
   * @param GooglePrivacyDlpV2BigQueryTable $tableReference
   */
  public function setTableReference(GooglePrivacyDlpV2BigQueryTable $tableReference)
  {
    $this->tableReference = $tableReference;
  }
  /**
   * @return GooglePrivacyDlpV2BigQueryTable
   */
  public function getTableReference()
  {
    return $this->tableReference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2BigQueryOptions::class, 'Google_Service_DLP_GooglePrivacyDlpV2BigQueryOptions');
