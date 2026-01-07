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

namespace Google\Service\BigQueryDataTransfer;

class DataSource extends \Google\Collection
{
  /**
   * Type unspecified.
   */
  public const AUTHORIZATION_TYPE_AUTHORIZATION_TYPE_UNSPECIFIED = 'AUTHORIZATION_TYPE_UNSPECIFIED';
  /**
   * Use OAuth 2 authorization codes that can be exchanged for a refresh token
   * on the backend.
   */
  public const AUTHORIZATION_TYPE_AUTHORIZATION_CODE = 'AUTHORIZATION_CODE';
  /**
   * Return an authorization code for a given Google+ page that can then be
   * exchanged for a refresh token on the backend.
   */
  public const AUTHORIZATION_TYPE_GOOGLE_PLUS_AUTHORIZATION_CODE = 'GOOGLE_PLUS_AUTHORIZATION_CODE';
  /**
   * Use First Party OAuth.
   */
  public const AUTHORIZATION_TYPE_FIRST_PARTY_OAUTH = 'FIRST_PARTY_OAUTH';
  /**
   * The data source won't support data auto refresh, which is default value.
   */
  public const DATA_REFRESH_TYPE_DATA_REFRESH_TYPE_UNSPECIFIED = 'DATA_REFRESH_TYPE_UNSPECIFIED';
  /**
   * The data source supports data auto refresh, and runs will be scheduled for
   * the past few days. Does not allow custom values to be set for each transfer
   * config.
   */
  public const DATA_REFRESH_TYPE_SLIDING_WINDOW = 'SLIDING_WINDOW';
  /**
   * The data source supports data auto refresh, and runs will be scheduled for
   * the past few days. Allows custom values to be set for each transfer config.
   */
  public const DATA_REFRESH_TYPE_CUSTOM_SLIDING_WINDOW = 'CUSTOM_SLIDING_WINDOW';
  /**
   * Invalid or Unknown transfer type placeholder.
   */
  public const TRANSFER_TYPE_TRANSFER_TYPE_UNSPECIFIED = 'TRANSFER_TYPE_UNSPECIFIED';
  /**
   * Batch data transfer.
   */
  public const TRANSFER_TYPE_BATCH = 'BATCH';
  /**
   * Streaming data transfer. Streaming data source currently doesn't support
   * multiple transfer configs per project.
   */
  public const TRANSFER_TYPE_STREAMING = 'STREAMING';
  protected $collection_key = 'scopes';
  /**
   * Indicates the type of authorization.
   *
   * @var string
   */
  public $authorizationType;
  /**
   * Data source client id which should be used to receive refresh token.
   *
   * @var string
   */
  public $clientId;
  /**
   * Specifies whether the data source supports automatic data refresh for the
   * past few days, and how it's supported. For some data sources, data might
   * not be complete until a few days later, so it's useful to refresh data
   * automatically.
   *
   * @var string
   */
  public $dataRefreshType;
  /**
   * Data source id.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * Default data refresh window on days. Only meaningful when
   * `data_refresh_type` = `SLIDING_WINDOW`.
   *
   * @var int
   */
  public $defaultDataRefreshWindowDays;
  /**
   * Default data transfer schedule. Examples of valid schedules include:
   * `1st,3rd monday of month 15:30`, `every wed,fri of jan,jun 13:15`, and
   * `first sunday of quarter 00:00`.
   *
   * @var string
   */
  public $defaultSchedule;
  /**
   * User friendly data source description string.
   *
   * @var string
   */
  public $description;
  /**
   * User friendly data source name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Url for the help document for this data source.
   *
   * @var string
   */
  public $helpUrl;
  /**
   * Disables backfilling and manual run scheduling for the data source.
   *
   * @var bool
   */
  public $manualRunsDisabled;
  /**
   * The minimum interval for scheduler to schedule runs.
   *
   * @var string
   */
  public $minimumScheduleInterval;
  /**
   * Output only. Data source resource name.
   *
   * @var string
   */
  public $name;
  protected $parametersType = DataSourceParameter::class;
  protected $parametersDataType = 'array';
  /**
   * Api auth scopes for which refresh token needs to be obtained. These are
   * scopes needed by a data source to prepare data and ingest them into
   * BigQuery, e.g., https://www.googleapis.com/auth/bigquery
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Specifies whether the data source supports a user defined schedule, or
   * operates on the default schedule. When set to `true`, user can override
   * default schedule.
   *
   * @var bool
   */
  public $supportsCustomSchedule;
  /**
   * Deprecated. This field has no effect.
   *
   * @deprecated
   * @var bool
   */
  public $supportsMultipleTransfers;
  /**
   * Deprecated. This field has no effect.
   *
   * @deprecated
   * @var string
   */
  public $transferType;
  /**
   * The number of seconds to wait for an update from the data source before the
   * Data Transfer Service marks the transfer as FAILED.
   *
   * @var int
   */
  public $updateDeadlineSeconds;

  /**
   * Indicates the type of authorization.
   *
   * Accepted values: AUTHORIZATION_TYPE_UNSPECIFIED, AUTHORIZATION_CODE,
   * GOOGLE_PLUS_AUTHORIZATION_CODE, FIRST_PARTY_OAUTH
   *
   * @param self::AUTHORIZATION_TYPE_* $authorizationType
   */
  public function setAuthorizationType($authorizationType)
  {
    $this->authorizationType = $authorizationType;
  }
  /**
   * @return self::AUTHORIZATION_TYPE_*
   */
  public function getAuthorizationType()
  {
    return $this->authorizationType;
  }
  /**
   * Data source client id which should be used to receive refresh token.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * Specifies whether the data source supports automatic data refresh for the
   * past few days, and how it's supported. For some data sources, data might
   * not be complete until a few days later, so it's useful to refresh data
   * automatically.
   *
   * Accepted values: DATA_REFRESH_TYPE_UNSPECIFIED, SLIDING_WINDOW,
   * CUSTOM_SLIDING_WINDOW
   *
   * @param self::DATA_REFRESH_TYPE_* $dataRefreshType
   */
  public function setDataRefreshType($dataRefreshType)
  {
    $this->dataRefreshType = $dataRefreshType;
  }
  /**
   * @return self::DATA_REFRESH_TYPE_*
   */
  public function getDataRefreshType()
  {
    return $this->dataRefreshType;
  }
  /**
   * Data source id.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * Default data refresh window on days. Only meaningful when
   * `data_refresh_type` = `SLIDING_WINDOW`.
   *
   * @param int $defaultDataRefreshWindowDays
   */
  public function setDefaultDataRefreshWindowDays($defaultDataRefreshWindowDays)
  {
    $this->defaultDataRefreshWindowDays = $defaultDataRefreshWindowDays;
  }
  /**
   * @return int
   */
  public function getDefaultDataRefreshWindowDays()
  {
    return $this->defaultDataRefreshWindowDays;
  }
  /**
   * Default data transfer schedule. Examples of valid schedules include:
   * `1st,3rd monday of month 15:30`, `every wed,fri of jan,jun 13:15`, and
   * `first sunday of quarter 00:00`.
   *
   * @param string $defaultSchedule
   */
  public function setDefaultSchedule($defaultSchedule)
  {
    $this->defaultSchedule = $defaultSchedule;
  }
  /**
   * @return string
   */
  public function getDefaultSchedule()
  {
    return $this->defaultSchedule;
  }
  /**
   * User friendly data source description string.
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
  /**
   * User friendly data source name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Url for the help document for this data source.
   *
   * @param string $helpUrl
   */
  public function setHelpUrl($helpUrl)
  {
    $this->helpUrl = $helpUrl;
  }
  /**
   * @return string
   */
  public function getHelpUrl()
  {
    return $this->helpUrl;
  }
  /**
   * Disables backfilling and manual run scheduling for the data source.
   *
   * @param bool $manualRunsDisabled
   */
  public function setManualRunsDisabled($manualRunsDisabled)
  {
    $this->manualRunsDisabled = $manualRunsDisabled;
  }
  /**
   * @return bool
   */
  public function getManualRunsDisabled()
  {
    return $this->manualRunsDisabled;
  }
  /**
   * The minimum interval for scheduler to schedule runs.
   *
   * @param string $minimumScheduleInterval
   */
  public function setMinimumScheduleInterval($minimumScheduleInterval)
  {
    $this->minimumScheduleInterval = $minimumScheduleInterval;
  }
  /**
   * @return string
   */
  public function getMinimumScheduleInterval()
  {
    return $this->minimumScheduleInterval;
  }
  /**
   * Output only. Data source resource name.
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
   * Data source parameters.
   *
   * @param DataSourceParameter[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return DataSourceParameter[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Api auth scopes for which refresh token needs to be obtained. These are
   * scopes needed by a data source to prepare data and ingest them into
   * BigQuery, e.g., https://www.googleapis.com/auth/bigquery
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * Specifies whether the data source supports a user defined schedule, or
   * operates on the default schedule. When set to `true`, user can override
   * default schedule.
   *
   * @param bool $supportsCustomSchedule
   */
  public function setSupportsCustomSchedule($supportsCustomSchedule)
  {
    $this->supportsCustomSchedule = $supportsCustomSchedule;
  }
  /**
   * @return bool
   */
  public function getSupportsCustomSchedule()
  {
    return $this->supportsCustomSchedule;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * @deprecated
   * @param bool $supportsMultipleTransfers
   */
  public function setSupportsMultipleTransfers($supportsMultipleTransfers)
  {
    $this->supportsMultipleTransfers = $supportsMultipleTransfers;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getSupportsMultipleTransfers()
  {
    return $this->supportsMultipleTransfers;
  }
  /**
   * Deprecated. This field has no effect.
   *
   * Accepted values: TRANSFER_TYPE_UNSPECIFIED, BATCH, STREAMING
   *
   * @deprecated
   * @param self::TRANSFER_TYPE_* $transferType
   */
  public function setTransferType($transferType)
  {
    $this->transferType = $transferType;
  }
  /**
   * @deprecated
   * @return self::TRANSFER_TYPE_*
   */
  public function getTransferType()
  {
    return $this->transferType;
  }
  /**
   * The number of seconds to wait for an update from the data source before the
   * Data Transfer Service marks the transfer as FAILED.
   *
   * @param int $updateDeadlineSeconds
   */
  public function setUpdateDeadlineSeconds($updateDeadlineSeconds)
  {
    $this->updateDeadlineSeconds = $updateDeadlineSeconds;
  }
  /**
   * @return int
   */
  public function getUpdateDeadlineSeconds()
  {
    return $this->updateDeadlineSeconds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSource::class, 'Google_Service_BigQueryDataTransfer_DataSource');
