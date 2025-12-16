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

class GoogleCloudAiplatformV1BatchReadFeatureValuesRequest extends \Google\Collection
{
  protected $collection_key = 'passThroughFields';
  protected $bigqueryReadInstancesType = GoogleCloudAiplatformV1BigQuerySource::class;
  protected $bigqueryReadInstancesDataType = '';
  protected $csvReadInstancesType = GoogleCloudAiplatformV1CsvSource::class;
  protected $csvReadInstancesDataType = '';
  protected $destinationType = GoogleCloudAiplatformV1FeatureValueDestination::class;
  protected $destinationDataType = '';
  protected $entityTypeSpecsType = GoogleCloudAiplatformV1BatchReadFeatureValuesRequestEntityTypeSpec::class;
  protected $entityTypeSpecsDataType = 'array';
  protected $passThroughFieldsType = GoogleCloudAiplatformV1BatchReadFeatureValuesRequestPassThroughField::class;
  protected $passThroughFieldsDataType = 'array';
  /**
   * Optional. Excludes Feature values with feature generation timestamp before
   * this timestamp. If not set, retrieve oldest values kept in Feature Store.
   * Timestamp, if present, must not have higher than millisecond precision.
   *
   * @var string
   */
  public $startTime;

  /**
   * Similar to csv_read_instances, but from BigQuery source.
   *
   * @param GoogleCloudAiplatformV1BigQuerySource $bigqueryReadInstances
   */
  public function setBigqueryReadInstances(GoogleCloudAiplatformV1BigQuerySource $bigqueryReadInstances)
  {
    $this->bigqueryReadInstances = $bigqueryReadInstances;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQuerySource
   */
  public function getBigqueryReadInstances()
  {
    return $this->bigqueryReadInstances;
  }
  /**
   * Each read instance consists of exactly one read timestamp and one or more
   * entity IDs identifying entities of the corresponding EntityTypes whose
   * Features are requested. Each output instance contains Feature values of
   * requested entities concatenated together as of the read time. An example
   * read instance may be `foo_entity_id, bar_entity_id,
   * 2020-01-01T10:00:00.123Z`. An example output instance may be
   * `foo_entity_id, bar_entity_id, 2020-01-01T10:00:00.123Z,
   * foo_entity_feature1_value, bar_entity_feature2_value`. Timestamp in each
   * read instance must be millisecond-aligned. `csv_read_instances` are read
   * instances stored in a plain-text CSV file. The header should be:
   * [ENTITY_TYPE_ID1], [ENTITY_TYPE_ID2], ..., timestamp The columns can be in
   * any order. Values in the timestamp column must use the RFC 3339 format,
   * e.g. `2012-07-30T10:43:17.123Z`.
   *
   * @param GoogleCloudAiplatformV1CsvSource $csvReadInstances
   */
  public function setCsvReadInstances(GoogleCloudAiplatformV1CsvSource $csvReadInstances)
  {
    $this->csvReadInstances = $csvReadInstances;
  }
  /**
   * @return GoogleCloudAiplatformV1CsvSource
   */
  public function getCsvReadInstances()
  {
    return $this->csvReadInstances;
  }
  /**
   * Required. Specifies output location and format.
   *
   * @param GoogleCloudAiplatformV1FeatureValueDestination $destination
   */
  public function setDestination(GoogleCloudAiplatformV1FeatureValueDestination $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureValueDestination
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * Required. Specifies EntityType grouping Features to read values of and
   * settings.
   *
   * @param GoogleCloudAiplatformV1BatchReadFeatureValuesRequestEntityTypeSpec[] $entityTypeSpecs
   */
  public function setEntityTypeSpecs($entityTypeSpecs)
  {
    $this->entityTypeSpecs = $entityTypeSpecs;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchReadFeatureValuesRequestEntityTypeSpec[]
   */
  public function getEntityTypeSpecs()
  {
    return $this->entityTypeSpecs;
  }
  /**
   * When not empty, the specified fields in the *_read_instances source will be
   * joined as-is in the output, in addition to those fields from the
   * Featurestore Entity. For BigQuery source, the type of the pass-through
   * values will be automatically inferred. For CSV source, the pass-through
   * values will be passed as opaque bytes.
   *
   * @param GoogleCloudAiplatformV1BatchReadFeatureValuesRequestPassThroughField[] $passThroughFields
   */
  public function setPassThroughFields($passThroughFields)
  {
    $this->passThroughFields = $passThroughFields;
  }
  /**
   * @return GoogleCloudAiplatformV1BatchReadFeatureValuesRequestPassThroughField[]
   */
  public function getPassThroughFields()
  {
    return $this->passThroughFields;
  }
  /**
   * Optional. Excludes Feature values with feature generation timestamp before
   * this timestamp. If not set, retrieve oldest values kept in Feature Store.
   * Timestamp, if present, must not have higher than millisecond precision.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1BatchReadFeatureValuesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1BatchReadFeatureValuesRequest');
