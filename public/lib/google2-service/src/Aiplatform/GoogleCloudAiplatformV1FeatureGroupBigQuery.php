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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1FeatureGroupBigQuery extends \Google\Collection
{
  protected $collection_key = 'entityIdColumns';
  protected $bigQuerySourceType = GoogleCloudAiplatformV1BigQuerySource::class;
  protected $bigQuerySourceDataType = '';
  /**
   * Optional. If set, all feature values will be fetched from a single row per
   * unique entityId including nulls. If not set, will collapse all rows for
   * each unique entityId into a singe row with any non-null values if present,
   * if no non-null values are present will sync null. ex: If source has schema
   * `(entity_id, feature_timestamp, f0, f1)` and the following rows: `(e1,
   * 2020-01-01T10:00:00.123Z, 10, 15)` `(e1, 2020-02-01T10:00:00.123Z, 20,
   * null)` If dense is set, `(e1, 20, null)` is synced to online stores. If
   * dense is not set, `(e1, 20, 15)` is synced to online stores.
   *
   * @var bool
   */
  public $dense;
  /**
   * Optional. Columns to construct entity_id / row keys. If not provided
   * defaults to `entity_id`.
   *
   * @var string[]
   */
  public $entityIdColumns;
  /**
   * Optional. Set if the data source is not a time-series.
   *
   * @var bool
   */
  public $staticDataSource;
  protected $timeSeriesType = GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries::class;
  protected $timeSeriesDataType = '';

  /**
   * Required. Immutable. The BigQuery source URI that points to either a
   * BigQuery Table or View.
   *
   * @param GoogleCloudAiplatformV1BigQuerySource $bigQuerySource
   */
  public function setBigQuerySource(GoogleCloudAiplatformV1BigQuerySource $bigQuerySource)
  {
    $this->bigQuerySource = $bigQuerySource;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQuerySource
   */
  public function getBigQuerySource()
  {
    return $this->bigQuerySource;
  }
  /**
   * Optional. If set, all feature values will be fetched from a single row per
   * unique entityId including nulls. If not set, will collapse all rows for
   * each unique entityId into a singe row with any non-null values if present,
   * if no non-null values are present will sync null. ex: If source has schema
   * `(entity_id, feature_timestamp, f0, f1)` and the following rows: `(e1,
   * 2020-01-01T10:00:00.123Z, 10, 15)` `(e1, 2020-02-01T10:00:00.123Z, 20,
   * null)` If dense is set, `(e1, 20, null)` is synced to online stores. If
   * dense is not set, `(e1, 20, 15)` is synced to online stores.
   *
   * @param bool $dense
   */
  public function setDense($dense)
  {
    $this->dense = $dense;
  }
  /**
   * @return bool
   */
  public function getDense()
  {
    return $this->dense;
  }
  /**
   * Optional. Columns to construct entity_id / row keys. If not provided
   * defaults to `entity_id`.
   *
   * @param string[] $entityIdColumns
   */
  public function setEntityIdColumns($entityIdColumns)
  {
    $this->entityIdColumns = $entityIdColumns;
  }
  /**
   * @return string[]
   */
  public function getEntityIdColumns()
  {
    return $this->entityIdColumns;
  }
  /**
   * Optional. Set if the data source is not a time-series.
   *
   * @param bool $staticDataSource
   */
  public function setStaticDataSource($staticDataSource)
  {
    $this->staticDataSource = $staticDataSource;
  }
  /**
   * @return bool
   */
  public function getStaticDataSource()
  {
    return $this->staticDataSource;
  }
  /**
   * Optional. If the source is a time-series source, this can be set to control
   * how downstream sources (ex: FeatureView ) will treat time-series sources.
   * If not set, will treat the source as a time-series source with
   * `feature_timestamp` as timestamp column and no scan boundary.
   *
   * @param GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries $timeSeries
   */
  public function setTimeSeries(GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries $timeSeries)
  {
    $this->timeSeries = $timeSeries;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureGroupBigQueryTimeSeries
   */
  public function getTimeSeries()
  {
    return $this->timeSeries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureGroupBigQuery::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureGroupBigQuery');
