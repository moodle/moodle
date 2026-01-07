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

class GoogleCloudAiplatformV1FeatureValueDestination extends \Google\Model
{
  protected $bigqueryDestinationType = GoogleCloudAiplatformV1BigQueryDestination::class;
  protected $bigqueryDestinationDataType = '';
  protected $csvDestinationType = GoogleCloudAiplatformV1CsvDestination::class;
  protected $csvDestinationDataType = '';
  protected $tfrecordDestinationType = GoogleCloudAiplatformV1TFRecordDestination::class;
  protected $tfrecordDestinationDataType = '';

  /**
   * Output in BigQuery format. BigQueryDestination.output_uri in
   * FeatureValueDestination.bigquery_destination must refer to a table.
   *
   * @param GoogleCloudAiplatformV1BigQueryDestination $bigqueryDestination
   */
  public function setBigqueryDestination(GoogleCloudAiplatformV1BigQueryDestination $bigqueryDestination)
  {
    $this->bigqueryDestination = $bigqueryDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1BigQueryDestination
   */
  public function getBigqueryDestination()
  {
    return $this->bigqueryDestination;
  }
  /**
   * Output in CSV format. Array Feature value types are not allowed in CSV
   * format.
   *
   * @param GoogleCloudAiplatformV1CsvDestination $csvDestination
   */
  public function setCsvDestination(GoogleCloudAiplatformV1CsvDestination $csvDestination)
  {
    $this->csvDestination = $csvDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1CsvDestination
   */
  public function getCsvDestination()
  {
    return $this->csvDestination;
  }
  /**
   * Output in TFRecord format. Below are the mapping from Feature value type in
   * Featurestore to Feature value type in TFRecord: Value type in Featurestore
   * | Value type in TFRecord DOUBLE, DOUBLE_ARRAY | FLOAT_LIST INT64,
   * INT64_ARRAY | INT64_LIST STRING, STRING_ARRAY, BYTES | BYTES_LIST true ->
   * byte_string("true"), false -> byte_string("false") BOOL, BOOL_ARRAY (true,
   * false) | BYTES_LIST
   *
   * @param GoogleCloudAiplatformV1TFRecordDestination $tfrecordDestination
   */
  public function setTfrecordDestination(GoogleCloudAiplatformV1TFRecordDestination $tfrecordDestination)
  {
    $this->tfrecordDestination = $tfrecordDestination;
  }
  /**
   * @return GoogleCloudAiplatformV1TFRecordDestination
   */
  public function getTfrecordDestination()
  {
    return $this->tfrecordDestination;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureValueDestination::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureValueDestination');
