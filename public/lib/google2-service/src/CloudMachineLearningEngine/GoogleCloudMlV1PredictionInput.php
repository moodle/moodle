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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1PredictionInput extends \Google\Collection
{
  /**
   * Unspecified format.
   */
  public const DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Each line of the file is a JSON dictionary representing one record.
   */
  public const DATA_FORMAT_JSON = 'JSON';
  /**
   * Deprecated. Use JSON instead.
   */
  public const DATA_FORMAT_TEXT = 'TEXT';
  /**
   * The source file is a TFRecord file. Currently available only for input
   * data.
   */
  public const DATA_FORMAT_TF_RECORD = 'TF_RECORD';
  /**
   * The source file is a GZIP-compressed TFRecord file. Currently available
   * only for input data.
   */
  public const DATA_FORMAT_TF_RECORD_GZIP = 'TF_RECORD_GZIP';
  /**
   * Values are comma-separated rows, with keys in a separate file. Currently
   * available only for output data.
   */
  public const DATA_FORMAT_CSV = 'CSV';
  /**
   * Unspecified format.
   */
  public const OUTPUT_DATA_FORMAT_DATA_FORMAT_UNSPECIFIED = 'DATA_FORMAT_UNSPECIFIED';
  /**
   * Each line of the file is a JSON dictionary representing one record.
   */
  public const OUTPUT_DATA_FORMAT_JSON = 'JSON';
  /**
   * Deprecated. Use JSON instead.
   */
  public const OUTPUT_DATA_FORMAT_TEXT = 'TEXT';
  /**
   * The source file is a TFRecord file. Currently available only for input
   * data.
   */
  public const OUTPUT_DATA_FORMAT_TF_RECORD = 'TF_RECORD';
  /**
   * The source file is a GZIP-compressed TFRecord file. Currently available
   * only for input data.
   */
  public const OUTPUT_DATA_FORMAT_TF_RECORD_GZIP = 'TF_RECORD_GZIP';
  /**
   * Values are comma-separated rows, with keys in a separate file. Currently
   * available only for output data.
   */
  public const OUTPUT_DATA_FORMAT_CSV = 'CSV';
  protected $collection_key = 'inputPaths';
  /**
   * Optional. Number of records per batch, defaults to 64. The service will
   * buffer batch_size number of records in memory before invoking one
   * Tensorflow prediction call internally. So take the record size and memory
   * available into consideration when setting this parameter.
   *
   * @var string
   */
  public $batchSize;
  /**
   * Required. The format of the input data files.
   *
   * @var string
   */
  public $dataFormat;
  /**
   * Required. The Cloud Storage location of the input data files. May contain
   * wildcards.
   *
   * @var string[]
   */
  public $inputPaths;
  /**
   * Optional. The maximum number of workers to be used for parallel processing.
   * Defaults to 10 if not specified.
   *
   * @var string
   */
  public $maxWorkerCount;
  /**
   * Use this field if you want to use the default version for the specified
   * model. The string must use the following format:
   * `"projects/YOUR_PROJECT/models/YOUR_MODEL"`
   *
   * @var string
   */
  public $modelName;
  /**
   * Optional. Format of the output data files, defaults to JSON.
   *
   * @var string
   */
  public $outputDataFormat;
  /**
   * Required. The output Google Cloud Storage location.
   *
   * @var string
   */
  public $outputPath;
  /**
   * Required. The Google Compute Engine region to run the prediction job in.
   * See the available regions for AI Platform services.
   *
   * @var string
   */
  public $region;
  /**
   * Optional. The AI Platform runtime version to use for this batch prediction.
   * If not set, AI Platform will pick the runtime version used during the
   * CreateVersion request for this model version, or choose the latest stable
   * version when model version information is not available such as when the
   * model is specified by uri.
   *
   * @var string
   */
  public $runtimeVersion;
  /**
   * Optional. The name of the signature defined in the SavedModel to use for
   * this job. Please refer to
   * [SavedModel](https://tensorflow.github.io/serving/serving_basic.html) for
   * information about how to use signatures. Defaults to [DEFAULT_SERVING_SIGNA
   * TURE_DEF_KEY](https://www.tensorflow.org/api_docs/python/tf/saved_model/sig
   * nature_constants) , which is "serving_default".
   *
   * @var string
   */
  public $signatureName;
  /**
   * Use this field if you want to specify a Google Cloud Storage path for the
   * model to use.
   *
   * @var string
   */
  public $uri;
  /**
   * Use this field if you want to specify a version of the model to use. The
   * string is formatted the same way as `model_version`, with the addition of
   * the version information:
   * `"projects/YOUR_PROJECT/models/YOUR_MODEL/versions/YOUR_VERSION"`
   *
   * @var string
   */
  public $versionName;

  /**
   * Optional. Number of records per batch, defaults to 64. The service will
   * buffer batch_size number of records in memory before invoking one
   * Tensorflow prediction call internally. So take the record size and memory
   * available into consideration when setting this parameter.
   *
   * @param string $batchSize
   */
  public function setBatchSize($batchSize)
  {
    $this->batchSize = $batchSize;
  }
  /**
   * @return string
   */
  public function getBatchSize()
  {
    return $this->batchSize;
  }
  /**
   * Required. The format of the input data files.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, JSON, TEXT, TF_RECORD,
   * TF_RECORD_GZIP, CSV
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * Required. The Cloud Storage location of the input data files. May contain
   * wildcards.
   *
   * @param string[] $inputPaths
   */
  public function setInputPaths($inputPaths)
  {
    $this->inputPaths = $inputPaths;
  }
  /**
   * @return string[]
   */
  public function getInputPaths()
  {
    return $this->inputPaths;
  }
  /**
   * Optional. The maximum number of workers to be used for parallel processing.
   * Defaults to 10 if not specified.
   *
   * @param string $maxWorkerCount
   */
  public function setMaxWorkerCount($maxWorkerCount)
  {
    $this->maxWorkerCount = $maxWorkerCount;
  }
  /**
   * @return string
   */
  public function getMaxWorkerCount()
  {
    return $this->maxWorkerCount;
  }
  /**
   * Use this field if you want to use the default version for the specified
   * model. The string must use the following format:
   * `"projects/YOUR_PROJECT/models/YOUR_MODEL"`
   *
   * @param string $modelName
   */
  public function setModelName($modelName)
  {
    $this->modelName = $modelName;
  }
  /**
   * @return string
   */
  public function getModelName()
  {
    return $this->modelName;
  }
  /**
   * Optional. Format of the output data files, defaults to JSON.
   *
   * Accepted values: DATA_FORMAT_UNSPECIFIED, JSON, TEXT, TF_RECORD,
   * TF_RECORD_GZIP, CSV
   *
   * @param self::OUTPUT_DATA_FORMAT_* $outputDataFormat
   */
  public function setOutputDataFormat($outputDataFormat)
  {
    $this->outputDataFormat = $outputDataFormat;
  }
  /**
   * @return self::OUTPUT_DATA_FORMAT_*
   */
  public function getOutputDataFormat()
  {
    return $this->outputDataFormat;
  }
  /**
   * Required. The output Google Cloud Storage location.
   *
   * @param string $outputPath
   */
  public function setOutputPath($outputPath)
  {
    $this->outputPath = $outputPath;
  }
  /**
   * @return string
   */
  public function getOutputPath()
  {
    return $this->outputPath;
  }
  /**
   * Required. The Google Compute Engine region to run the prediction job in.
   * See the available regions for AI Platform services.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
  /**
   * Optional. The AI Platform runtime version to use for this batch prediction.
   * If not set, AI Platform will pick the runtime version used during the
   * CreateVersion request for this model version, or choose the latest stable
   * version when model version information is not available such as when the
   * model is specified by uri.
   *
   * @param string $runtimeVersion
   */
  public function setRuntimeVersion($runtimeVersion)
  {
    $this->runtimeVersion = $runtimeVersion;
  }
  /**
   * @return string
   */
  public function getRuntimeVersion()
  {
    return $this->runtimeVersion;
  }
  /**
   * Optional. The name of the signature defined in the SavedModel to use for
   * this job. Please refer to
   * [SavedModel](https://tensorflow.github.io/serving/serving_basic.html) for
   * information about how to use signatures. Defaults to [DEFAULT_SERVING_SIGNA
   * TURE_DEF_KEY](https://www.tensorflow.org/api_docs/python/tf/saved_model/sig
   * nature_constants) , which is "serving_default".
   *
   * @param string $signatureName
   */
  public function setSignatureName($signatureName)
  {
    $this->signatureName = $signatureName;
  }
  /**
   * @return string
   */
  public function getSignatureName()
  {
    return $this->signatureName;
  }
  /**
   * Use this field if you want to specify a Google Cloud Storage path for the
   * model to use.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
  /**
   * Use this field if you want to specify a version of the model to use. The
   * string is formatted the same way as `model_version`, with the addition of
   * the version information:
   * `"projects/YOUR_PROJECT/models/YOUR_MODEL/versions/YOUR_VERSION"`
   *
   * @param string $versionName
   */
  public function setVersionName($versionName)
  {
    $this->versionName = $versionName;
  }
  /**
   * @return string
   */
  public function getVersionName()
  {
    return $this->versionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1PredictionInput::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1PredictionInput');
