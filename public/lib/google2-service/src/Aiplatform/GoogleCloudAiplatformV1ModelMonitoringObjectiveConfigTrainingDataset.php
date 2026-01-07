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

class GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigTrainingDataset extends \Google\Model
{
  protected $bigquerySourceType = GoogleCloudAiplatformV1BigQuerySource::class;
  protected $bigquerySourceDataType = '';
  /**
   * Data format of the dataset, only applicable if the input is from Google
   * Cloud Storage. The possible formats are: "tf-record" The source file is a
   * TFRecord file. "csv" The source file is a CSV file. "jsonl" The source file
   * is a JSONL file.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * The resource name of the Dataset used to train this Model.
   *
   * @var string
   */
  public $dataset;
  protected $gcsSourceType = GoogleCloudAiplatformV1GcsSource::class;
  protected $gcsSourceDataType = '';
  protected $loggingSamplingStrategyType = GoogleCloudAiplatformV1SamplingStrategy::class;
  protected $loggingSamplingStrategyDataType = '';
  /**
   * The target field name the model is to predict. This field will be excluded
   * when doing Predict and (or) Explain for the training data.
   *
   * @var string
   */
  public $targetField;

  /**
   * The BigQuery table of the unmanaged Dataset used to train this Model.
   *
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
   * Data format of the dataset, only applicable if the input is from Google
   * Cloud Storage. The possible formats are: "tf-record" The source file is a
   * TFRecord file. "csv" The source file is a CSV file. "jsonl" The source file
   * is a JSONL file.
   *
   * @param string $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return string
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * The resource name of the Dataset used to train this Model.
   *
   * @param string $dataset
   */
  public function setDataset($dataset)
  {
    $this->dataset = $dataset;
  }
  /**
   * @return string
   */
  public function getDataset()
  {
    return $this->dataset;
  }
  /**
   * The Google Cloud Storage uri of the unmanaged Dataset used to train this
   * Model.
   *
   * @param GoogleCloudAiplatformV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudAiplatformV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Strategy to sample data from Training Dataset. If not set, we process the
   * whole dataset.
   *
   * @param GoogleCloudAiplatformV1SamplingStrategy $loggingSamplingStrategy
   */
  public function setLoggingSamplingStrategy(GoogleCloudAiplatformV1SamplingStrategy $loggingSamplingStrategy)
  {
    $this->loggingSamplingStrategy = $loggingSamplingStrategy;
  }
  /**
   * @return GoogleCloudAiplatformV1SamplingStrategy
   */
  public function getLoggingSamplingStrategy()
  {
    return $this->loggingSamplingStrategy;
  }
  /**
   * The target field name the model is to predict. This field will be excluded
   * when doing Predict and (or) Explain for the training data.
   *
   * @param string $targetField
   */
  public function setTargetField($targetField)
  {
    $this->targetField = $targetField;
  }
  /**
   * @return string
   */
  public function getTargetField()
  {
    return $this->targetField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigTrainingDataset::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1ModelMonitoringObjectiveConfigTrainingDataset');
