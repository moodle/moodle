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

class GooglePrivacyDlpV2ColumnDataProfile extends \Google\Collection
{
  /**
   * Invalid type.
   */
  public const COLUMN_TYPE_COLUMN_DATA_TYPE_UNSPECIFIED = 'COLUMN_DATA_TYPE_UNSPECIFIED';
  /**
   * Encoded as a string in decimal format.
   */
  public const COLUMN_TYPE_TYPE_INT64 = 'TYPE_INT64';
  /**
   * Encoded as a boolean "false" or "true".
   */
  public const COLUMN_TYPE_TYPE_BOOL = 'TYPE_BOOL';
  /**
   * Encoded as a number, or string "NaN", "Infinity" or "-Infinity".
   */
  public const COLUMN_TYPE_TYPE_FLOAT64 = 'TYPE_FLOAT64';
  /**
   * Encoded as a string value.
   */
  public const COLUMN_TYPE_TYPE_STRING = 'TYPE_STRING';
  /**
   * Encoded as a base64 string per RFC 4648, section 4.
   */
  public const COLUMN_TYPE_TYPE_BYTES = 'TYPE_BYTES';
  /**
   * Encoded as an RFC 3339 timestamp with mandatory "Z" time zone string:
   * 1985-04-12T23:20:50.52Z
   */
  public const COLUMN_TYPE_TYPE_TIMESTAMP = 'TYPE_TIMESTAMP';
  /**
   * Encoded as RFC 3339 full-date format string: 1985-04-12
   */
  public const COLUMN_TYPE_TYPE_DATE = 'TYPE_DATE';
  /**
   * Encoded as RFC 3339 partial-time format string: 23:20:50.52
   */
  public const COLUMN_TYPE_TYPE_TIME = 'TYPE_TIME';
  /**
   * Encoded as RFC 3339 full-date "T" partial-time: 1985-04-12T23:20:50.52
   */
  public const COLUMN_TYPE_TYPE_DATETIME = 'TYPE_DATETIME';
  /**
   * Encoded as WKT
   */
  public const COLUMN_TYPE_TYPE_GEOGRAPHY = 'TYPE_GEOGRAPHY';
  /**
   * Encoded as a decimal string.
   */
  public const COLUMN_TYPE_TYPE_NUMERIC = 'TYPE_NUMERIC';
  /**
   * Container of ordered fields, each with a type and field name.
   */
  public const COLUMN_TYPE_TYPE_RECORD = 'TYPE_RECORD';
  /**
   * Decimal type.
   */
  public const COLUMN_TYPE_TYPE_BIGNUMERIC = 'TYPE_BIGNUMERIC';
  /**
   * Json type.
   */
  public const COLUMN_TYPE_TYPE_JSON = 'TYPE_JSON';
  /**
   * Interval type.
   */
  public const COLUMN_TYPE_TYPE_INTERVAL = 'TYPE_INTERVAL';
  /**
   * `Range` type.
   */
  public const COLUMN_TYPE_TYPE_RANGE_DATE = 'TYPE_RANGE_DATE';
  /**
   * `Range` type.
   */
  public const COLUMN_TYPE_TYPE_RANGE_DATETIME = 'TYPE_RANGE_DATETIME';
  /**
   * `Range` type.
   */
  public const COLUMN_TYPE_TYPE_RANGE_TIMESTAMP = 'TYPE_RANGE_TIMESTAMP';
  /**
   * Unused.
   */
  public const ESTIMATED_NULL_PERCENTAGE_NULL_PERCENTAGE_LEVEL_UNSPECIFIED = 'NULL_PERCENTAGE_LEVEL_UNSPECIFIED';
  /**
   * Very few null entries.
   */
  public const ESTIMATED_NULL_PERCENTAGE_NULL_PERCENTAGE_VERY_LOW = 'NULL_PERCENTAGE_VERY_LOW';
  /**
   * Some null entries.
   */
  public const ESTIMATED_NULL_PERCENTAGE_NULL_PERCENTAGE_LOW = 'NULL_PERCENTAGE_LOW';
  /**
   * A few null entries.
   */
  public const ESTIMATED_NULL_PERCENTAGE_NULL_PERCENTAGE_MEDIUM = 'NULL_PERCENTAGE_MEDIUM';
  /**
   * A lot of null entries.
   */
  public const ESTIMATED_NULL_PERCENTAGE_NULL_PERCENTAGE_HIGH = 'NULL_PERCENTAGE_HIGH';
  /**
   * Some columns do not have estimated uniqueness. Possible reasons include
   * having too few values.
   */
  public const ESTIMATED_UNIQUENESS_SCORE_UNIQUENESS_SCORE_LEVEL_UNSPECIFIED = 'UNIQUENESS_SCORE_LEVEL_UNSPECIFIED';
  /**
   * Low uniqueness, possibly a boolean, enum or similiarly typed column.
   */
  public const ESTIMATED_UNIQUENESS_SCORE_UNIQUENESS_SCORE_LOW = 'UNIQUENESS_SCORE_LOW';
  /**
   * Medium uniqueness.
   */
  public const ESTIMATED_UNIQUENESS_SCORE_UNIQUENESS_SCORE_MEDIUM = 'UNIQUENESS_SCORE_MEDIUM';
  /**
   * High uniqueness, possibly a column of free text or unique identifiers.
   */
  public const ESTIMATED_UNIQUENESS_SCORE_UNIQUENESS_SCORE_HIGH = 'UNIQUENESS_SCORE_HIGH';
  /**
   * No policy tags.
   */
  public const POLICY_STATE_COLUMN_POLICY_STATE_UNSPECIFIED = 'COLUMN_POLICY_STATE_UNSPECIFIED';
  /**
   * Column has policy tag applied.
   */
  public const POLICY_STATE_COLUMN_POLICY_TAGGED = 'COLUMN_POLICY_TAGGED';
  /**
   * Unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The profile is currently running. Once a profile has finished it will
   * transition to DONE.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The profile is no longer generating. If profile_status.status.code is 0,
   * the profile succeeded, otherwise, it failed.
   */
  public const STATE_DONE = 'DONE';
  protected $collection_key = 'otherMatches';
  /**
   * The name of the column.
   *
   * @var string
   */
  public $column;
  protected $columnInfoTypeType = GooglePrivacyDlpV2InfoTypeSummary::class;
  protected $columnInfoTypeDataType = '';
  /**
   * The data type of a given column.
   *
   * @var string
   */
  public $columnType;
  protected $dataRiskLevelType = GooglePrivacyDlpV2DataRiskLevel::class;
  protected $dataRiskLevelDataType = '';
  /**
   * The BigQuery dataset ID, if the resource profiled is a BigQuery table.
   *
   * @var string
   */
  public $datasetId;
  /**
   * If supported, the location where the dataset's data is stored. See
   * https://cloud.google.com/bigquery/docs/locations for supported BigQuery
   * locations.
   *
   * @var string
   */
  public $datasetLocation;
  /**
   * The Google Cloud project ID that owns the profiled resource.
   *
   * @var string
   */
  public $datasetProjectId;
  /**
   * Approximate percentage of entries being null in the column.
   *
   * @var string
   */
  public $estimatedNullPercentage;
  /**
   * Approximate uniqueness of the column.
   *
   * @var string
   */
  public $estimatedUniquenessScore;
  /**
   * The likelihood that this column contains free-form text. A value close to 1
   * may indicate the column is likely to contain free-form or natural language
   * text. Range in 0-1.
   *
   * @var 
   */
  public $freeTextScore;
  /**
   * The name of the profile.
   *
   * @var string
   */
  public $name;
  protected $otherMatchesType = GooglePrivacyDlpV2OtherInfoTypeSummary::class;
  protected $otherMatchesDataType = 'array';
  /**
   * Indicates if a policy tag has been applied to the column.
   *
   * @var string
   */
  public $policyState;
  /**
   * The last time the profile was generated.
   *
   * @var string
   */
  public $profileLastGenerated;
  protected $profileStatusType = GooglePrivacyDlpV2ProfileStatus::class;
  protected $profileStatusDataType = '';
  protected $sensitivityScoreType = GooglePrivacyDlpV2SensitivityScore::class;
  protected $sensitivityScoreDataType = '';
  /**
   * State of a profile.
   *
   * @var string
   */
  public $state;
  /**
   * The resource name of the table data profile.
   *
   * @var string
   */
  public $tableDataProfile;
  /**
   * The resource name of the resource this column is within.
   *
   * @var string
   */
  public $tableFullResource;
  /**
   * The table ID.
   *
   * @var string
   */
  public $tableId;

  /**
   * The name of the column.
   *
   * @param string $column
   */
  public function setColumn($column)
  {
    $this->column = $column;
  }
  /**
   * @return string
   */
  public function getColumn()
  {
    return $this->column;
  }
  /**
   * If it's been determined this column can be identified as a single type,
   * this will be set. Otherwise the column either has unidentifiable content or
   * mixed types.
   *
   * @param GooglePrivacyDlpV2InfoTypeSummary $columnInfoType
   */
  public function setColumnInfoType(GooglePrivacyDlpV2InfoTypeSummary $columnInfoType)
  {
    $this->columnInfoType = $columnInfoType;
  }
  /**
   * @return GooglePrivacyDlpV2InfoTypeSummary
   */
  public function getColumnInfoType()
  {
    return $this->columnInfoType;
  }
  /**
   * The data type of a given column.
   *
   * Accepted values: COLUMN_DATA_TYPE_UNSPECIFIED, TYPE_INT64, TYPE_BOOL,
   * TYPE_FLOAT64, TYPE_STRING, TYPE_BYTES, TYPE_TIMESTAMP, TYPE_DATE,
   * TYPE_TIME, TYPE_DATETIME, TYPE_GEOGRAPHY, TYPE_NUMERIC, TYPE_RECORD,
   * TYPE_BIGNUMERIC, TYPE_JSON, TYPE_INTERVAL, TYPE_RANGE_DATE,
   * TYPE_RANGE_DATETIME, TYPE_RANGE_TIMESTAMP
   *
   * @param self::COLUMN_TYPE_* $columnType
   */
  public function setColumnType($columnType)
  {
    $this->columnType = $columnType;
  }
  /**
   * @return self::COLUMN_TYPE_*
   */
  public function getColumnType()
  {
    return $this->columnType;
  }
  /**
   * The data risk level for this column.
   *
   * @param GooglePrivacyDlpV2DataRiskLevel $dataRiskLevel
   */
  public function setDataRiskLevel(GooglePrivacyDlpV2DataRiskLevel $dataRiskLevel)
  {
    $this->dataRiskLevel = $dataRiskLevel;
  }
  /**
   * @return GooglePrivacyDlpV2DataRiskLevel
   */
  public function getDataRiskLevel()
  {
    return $this->dataRiskLevel;
  }
  /**
   * The BigQuery dataset ID, if the resource profiled is a BigQuery table.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * If supported, the location where the dataset's data is stored. See
   * https://cloud.google.com/bigquery/docs/locations for supported BigQuery
   * locations.
   *
   * @param string $datasetLocation
   */
  public function setDatasetLocation($datasetLocation)
  {
    $this->datasetLocation = $datasetLocation;
  }
  /**
   * @return string
   */
  public function getDatasetLocation()
  {
    return $this->datasetLocation;
  }
  /**
   * The Google Cloud project ID that owns the profiled resource.
   *
   * @param string $datasetProjectId
   */
  public function setDatasetProjectId($datasetProjectId)
  {
    $this->datasetProjectId = $datasetProjectId;
  }
  /**
   * @return string
   */
  public function getDatasetProjectId()
  {
    return $this->datasetProjectId;
  }
  /**
   * Approximate percentage of entries being null in the column.
   *
   * Accepted values: NULL_PERCENTAGE_LEVEL_UNSPECIFIED,
   * NULL_PERCENTAGE_VERY_LOW, NULL_PERCENTAGE_LOW, NULL_PERCENTAGE_MEDIUM,
   * NULL_PERCENTAGE_HIGH
   *
   * @param self::ESTIMATED_NULL_PERCENTAGE_* $estimatedNullPercentage
   */
  public function setEstimatedNullPercentage($estimatedNullPercentage)
  {
    $this->estimatedNullPercentage = $estimatedNullPercentage;
  }
  /**
   * @return self::ESTIMATED_NULL_PERCENTAGE_*
   */
  public function getEstimatedNullPercentage()
  {
    return $this->estimatedNullPercentage;
  }
  /**
   * Approximate uniqueness of the column.
   *
   * Accepted values: UNIQUENESS_SCORE_LEVEL_UNSPECIFIED, UNIQUENESS_SCORE_LOW,
   * UNIQUENESS_SCORE_MEDIUM, UNIQUENESS_SCORE_HIGH
   *
   * @param self::ESTIMATED_UNIQUENESS_SCORE_* $estimatedUniquenessScore
   */
  public function setEstimatedUniquenessScore($estimatedUniquenessScore)
  {
    $this->estimatedUniquenessScore = $estimatedUniquenessScore;
  }
  /**
   * @return self::ESTIMATED_UNIQUENESS_SCORE_*
   */
  public function getEstimatedUniquenessScore()
  {
    return $this->estimatedUniquenessScore;
  }
  public function setFreeTextScore($freeTextScore)
  {
    $this->freeTextScore = $freeTextScore;
  }
  public function getFreeTextScore()
  {
    return $this->freeTextScore;
  }
  /**
   * The name of the profile.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Other types found within this column. List will be unordered.
   *
   * @param GooglePrivacyDlpV2OtherInfoTypeSummary[] $otherMatches
   */
  public function setOtherMatches($otherMatches)
  {
    $this->otherMatches = $otherMatches;
  }
  /**
   * @return GooglePrivacyDlpV2OtherInfoTypeSummary[]
   */
  public function getOtherMatches()
  {
    return $this->otherMatches;
  }
  /**
   * Indicates if a policy tag has been applied to the column.
   *
   * Accepted values: COLUMN_POLICY_STATE_UNSPECIFIED, COLUMN_POLICY_TAGGED
   *
   * @param self::POLICY_STATE_* $policyState
   */
  public function setPolicyState($policyState)
  {
    $this->policyState = $policyState;
  }
  /**
   * @return self::POLICY_STATE_*
   */
  public function getPolicyState()
  {
    return $this->policyState;
  }
  /**
   * The last time the profile was generated.
   *
   * @param string $profileLastGenerated
   */
  public function setProfileLastGenerated($profileLastGenerated)
  {
    $this->profileLastGenerated = $profileLastGenerated;
  }
  /**
   * @return string
   */
  public function getProfileLastGenerated()
  {
    return $this->profileLastGenerated;
  }
  /**
   * Success or error status from the most recent profile generation attempt.
   * May be empty if the profile is still being generated.
   *
   * @param GooglePrivacyDlpV2ProfileStatus $profileStatus
   */
  public function setProfileStatus(GooglePrivacyDlpV2ProfileStatus $profileStatus)
  {
    $this->profileStatus = $profileStatus;
  }
  /**
   * @return GooglePrivacyDlpV2ProfileStatus
   */
  public function getProfileStatus()
  {
    return $this->profileStatus;
  }
  /**
   * The sensitivity of this column.
   *
   * @param GooglePrivacyDlpV2SensitivityScore $sensitivityScore
   */
  public function setSensitivityScore(GooglePrivacyDlpV2SensitivityScore $sensitivityScore)
  {
    $this->sensitivityScore = $sensitivityScore;
  }
  /**
   * @return GooglePrivacyDlpV2SensitivityScore
   */
  public function getSensitivityScore()
  {
    return $this->sensitivityScore;
  }
  /**
   * State of a profile.
   *
   * Accepted values: STATE_UNSPECIFIED, RUNNING, DONE
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The resource name of the table data profile.
   *
   * @param string $tableDataProfile
   */
  public function setTableDataProfile($tableDataProfile)
  {
    $this->tableDataProfile = $tableDataProfile;
  }
  /**
   * @return string
   */
  public function getTableDataProfile()
  {
    return $this->tableDataProfile;
  }
  /**
   * The resource name of the resource this column is within.
   *
   * @param string $tableFullResource
   */
  public function setTableFullResource($tableFullResource)
  {
    $this->tableFullResource = $tableFullResource;
  }
  /**
   * @return string
   */
  public function getTableFullResource()
  {
    return $this->tableFullResource;
  }
  /**
   * The table ID.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2ColumnDataProfile::class, 'Google_Service_DLP_GooglePrivacyDlpV2ColumnDataProfile');
