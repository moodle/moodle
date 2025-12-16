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

class GoogleCloudAiplatformV1ImportFeatureValuesRequest extends \Google\Collection
{
  protected $collection_key = 'featureSpecs';
  protected $avroSourceType = GoogleCloudAiplatformV1AvroSource::class;
  protected $avroSourceDataType = '';
  protected $bigquerySourceType = GoogleCloudAiplatformV1BigQuerySource::class;
  protected $bigquerySourceDataType = '';
  protected $csvSourceType = GoogleCloudAiplatformV1CsvSource::class;
  protected $csvSourceDataType = '';
  /**
   * If true, API doesn't start ingestion analysis pipeline.
   *
   * @var bool
   */
  public $disableIngestionAnalysis;
  /**
   * If set, data will not be imported for online serving. This is typically
   * used for backfilling, where Feature generation timestamps are not in the
   * timestamp range needed for online serving.
   *
   * @var bool
   */
  public $disableOnlineServing;
  /**
   * Source column that holds entity IDs. If not provided, entity IDs are
   * extracted from the column named entity_id.
   *
   * @var string
   */
  public $entityIdField;
  protected $featureSpecsType = GoogleCloudAiplatformV1ImportFeatureValuesRequestFeatureSpec::class;
  protected $featureSpecsDataType = 'array';
  /**
   * Single Feature timestamp for all entities being imported. The timestamp
   * must not have higher than millisecond precision.
   *
   * @var string
   */
  public $featureTime;
  /**
   * Source column that holds the Feature timestamp for all Feature values in
   * each entity.
   *
   * @var string
   */
  public $featureTimeField;
  /**
   * Specifies the number of workers that are used to write data to the
   * Featurestore. Consider the online serving capacity that you require to
   * achieve the desired import throughput without interfering with online
   * serving. The value must be positive, and less than or equal to 100. If not
   * set, defaults to using 1 worker. The low count ensures minimal impact on
   * online serving performance.
   *
   * @var int
   */
  public $workerCount;

  /**
   * @param GoogleCloudAiplatformV1AvroSource $avroSource
   */
  public function setAvroSource(GoogleCloudAiplatformV1AvroSource $avroSource)
  {
    $this->avroSource = $avroSource;
  }
  /**
   * @return GoogleCloudAiplatformV1AvroSource
   */
  public function getAvroSource()
  {
    return $this->avroSource;
  }
  /**
   * @param GoogleCloudAiplatformV1BigQuerySource $bigquerySource
   */
  public function setBigquerySource(GoogleCloudAiplatformV1BigQuerySource $bigquerySource)
  {
    $this->bigquerySource = $bigquerySource;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQuerySource
   */
  public function getBigquerySource()
  {
    return $this->bigquerySource;
  }
  /**
   * @param GoogleCloudAiplatformV1CsvSource $csvSource
   */
  public function setCsvSource(GoogleCloudAiplatformV1CsvSource $csvSource)
  {
    $this->csvSource = $csvSource;
  }
  /**
   * @return GoogleCloudAiplatformV1CsvSource
   */
  public function getCsvSource()
  {
    return $this->csvSource;
  }
  /**
   * If true, API doesn't start ingestion analysis pipeline.
   *
   * @param bool $disableIngestionAnalysis
   */
  public function setDisableIngestionAnalysis($disableIngestionAnalysis)
  {
    $this->disableIngestionAnalysis = $disableIngestionAnalysis;
  }
  /**
   * @return bool
   */
  public function getDisableIngestionAnalysis()
  {
    return $this->disableIngestionAnalysis;
  }
  /**
   * If set, data will not be imported for online serving. This is typically
   * used for backfilling, where Feature generation timestamps are not in the
   * timestamp range needed for online serving.
   *
   * @param bool $disableOnlineServing
   */
  public function setDisableOnlineServing($disableOnlineServing)
  {
    $this->disableOnlineServing = $disableOnlineServing;
  }
  /**
   * @return bool
   */
  public function getDisableOnlineServing()
  {
    return $this->disableOnlineServing;
  }
  /**
   * Source column that holds entity IDs. If not provided, entity IDs are
   * extracted from the column named entity_id.
   *
   * @param string $entityIdField
   */
  public function setEntityIdField($entityIdField)
  {
    $this->entityIdField = $entityIdField;
  }
  /**
   * @return string
   */
  public function getEntityIdField()
  {
    return $this->entityIdField;
  }
  /**
   * Required. Specifications defining which Feature values to import from the
   * entity. The request fails if no feature_specs are provided, and having
   * multiple feature_specs for one Feature is not allowed.
   *
   * @param GoogleCloudAiplatformV1ImportFeatureValuesRequestFeatureSpec[] $featureSpecs
   */
  public function setFeatureSpecs($featureSpecs)
  {
    $this->featureSpecs = $featureSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1ImportFeatureValuesRequestFeatureSpec[]
   */
  public function getFeatureSpecs()
  {
    return $this->featureSpecs;
  }
  /**
   * Single Feature timestamp for all entities being imported. The timestamp
   * must not have higher than millisecond precision.
   *
   * @param string $featureTime
   */
  public function setFeatureTime($featureTime)
  {
    $this->featureTime = $featureTime;
  }
  /**
   * @return string
   */
  public function getFeatureTime()
  {
    return $this->featureTime;
  }
  /**
   * Source column that holds the Feature timestamp for all Feature values in
   * each entity.
   *
   * @param string $featureTimeField
   */
  public function setFeatureTimeField($featureTimeField)
  {
    $this->featureTimeField = $featureTimeField;
  }
  /**
   * @return string
   */
  public function getFeatureTimeField()
  {
    return $this->featureTimeField;
  }
  /**
   * Specifies the number of workers that are used to write data to the
   * Featurestore. Consider the online serving capacity that you require to
   * achieve the desired import throughput without interfering with online
   * serving. The value must be positive, and less than or equal to 100. If not
   * set, defaults to using 1 worker. The low count ensures minimal impact on
   * online serving performance.
   *
   * @param int $workerCount
   */
  public function setWorkerCount($workerCount)
  {
    $this->workerCount = $workerCount;
  }
  /**
   * @return int
   */
  public function getWorkerCount()
  {
    return $this->workerCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ImportFeatureValuesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ImportFeatureValuesRequest');
