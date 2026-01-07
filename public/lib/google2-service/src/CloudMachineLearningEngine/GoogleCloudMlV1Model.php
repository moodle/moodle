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

class GoogleCloudMlV1Model extends \Google\Collection
{
  protected $collection_key = 'regions';
  protected $defaultVersionType = GoogleCloudMlV1Version::class;
  protected $defaultVersionDataType = '';
  /**
   * Optional. The description specified for the model when it was created.
   *
   * @var string
   */
  public $description;
  /**
   * `etag` is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a model from overwriting each other. It is strongly
   * suggested that systems make use of the `etag` in the read-modify-write
   * cycle to perform model updates in order to avoid race conditions: An `etag`
   * is returned in the response to `GetModel`, and systems are expected to put
   * that etag in the request to `UpdateModel` to ensure that their change will
   * be applied to the model as intended.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. One or more labels that you can add, to organize your models.
   * Each label is a key-value pair, where both the key and the value are
   * arbitrary strings that you supply. For more information, see the
   * documentation on using labels. Note that this field is not updatable for
   * mls1* models.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The name specified for the model when it was created. The model
   * name must be unique within the project it is created in.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. If true, online prediction nodes send `stderr` and `stdout`
   * streams to Cloud Logging. These can be more verbose than the standard
   * access logs (see `onlinePredictionLogging`) and can incur higher cost.
   * However, they are helpful for debugging. Note that [logs may incur a
   * cost](/stackdriver/pricing), especially if your project receives prediction
   * requests at a high QPS. Estimate your costs before enabling this option.
   * Default is false.
   *
   * @var bool
   */
  public $onlinePredictionConsoleLogging;
  /**
   * Optional. If true, online prediction access logs are sent to Cloud Logging.
   * These logs are like standard server access logs, containing information
   * like timestamp and latency for each request. Note that [logs may incur a
   * cost](/stackdriver/pricing), especially if your project receives prediction
   * requests at a high queries per second rate (QPS). Estimate your costs
   * before enabling this option. Default is false.
   *
   * @var bool
   */
  public $onlinePredictionLogging;
  /**
   * Optional. The list of regions where the model is going to be deployed. Only
   * one region per model is supported. Defaults to 'us-central1' if nothing is
   * set. See the available regions for AI Platform services. Note: * No matter
   * where a model is deployed, it can always be accessed by users from
   * anywhere, both for online and batch prediction. * The region for a batch
   * prediction job is set by the region field when submitting the batch
   * prediction job and does not take its value from this field.
   *
   * @var string[]
   */
  public $regions;

  /**
   * Output only. The default version of the model. This version will be used to
   * handle prediction requests that do not specify a version. You can change
   * the default version by calling projects.models.versions.setDefault.
   *
   * @param GoogleCloudMlV1Version $defaultVersion
   */
  public function setDefaultVersion(GoogleCloudMlV1Version $defaultVersion)
  {
    $this->defaultVersion = $defaultVersion;
  }
  /**
   * @return GoogleCloudMlV1Version
   */
  public function getDefaultVersion()
  {
    return $this->defaultVersion;
  }
  /**
   * Optional. The description specified for the model when it was created.
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
   * `etag` is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a model from overwriting each other. It is strongly
   * suggested that systems make use of the `etag` in the read-modify-write
   * cycle to perform model updates in order to avoid race conditions: An `etag`
   * is returned in the response to `GetModel`, and systems are expected to put
   * that etag in the request to `UpdateModel` to ensure that their change will
   * be applied to the model as intended.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. One or more labels that you can add, to organize your models.
   * Each label is a key-value pair, where both the key and the value are
   * arbitrary strings that you supply. For more information, see the
   * documentation on using labels. Note that this field is not updatable for
   * mls1* models.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. The name specified for the model when it was created. The model
   * name must be unique within the project it is created in.
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
   * Optional. If true, online prediction nodes send `stderr` and `stdout`
   * streams to Cloud Logging. These can be more verbose than the standard
   * access logs (see `onlinePredictionLogging`) and can incur higher cost.
   * However, they are helpful for debugging. Note that [logs may incur a
   * cost](/stackdriver/pricing), especially if your project receives prediction
   * requests at a high QPS. Estimate your costs before enabling this option.
   * Default is false.
   *
   * @param bool $onlinePredictionConsoleLogging
   */
  public function setOnlinePredictionConsoleLogging($onlinePredictionConsoleLogging)
  {
    $this->onlinePredictionConsoleLogging = $onlinePredictionConsoleLogging;
  }
  /**
   * @return bool
   */
  public function getOnlinePredictionConsoleLogging()
  {
    return $this->onlinePredictionConsoleLogging;
  }
  /**
   * Optional. If true, online prediction access logs are sent to Cloud Logging.
   * These logs are like standard server access logs, containing information
   * like timestamp and latency for each request. Note that [logs may incur a
   * cost](/stackdriver/pricing), especially if your project receives prediction
   * requests at a high queries per second rate (QPS). Estimate your costs
   * before enabling this option. Default is false.
   *
   * @param bool $onlinePredictionLogging
   */
  public function setOnlinePredictionLogging($onlinePredictionLogging)
  {
    $this->onlinePredictionLogging = $onlinePredictionLogging;
  }
  /**
   * @return bool
   */
  public function getOnlinePredictionLogging()
  {
    return $this->onlinePredictionLogging;
  }
  /**
   * Optional. The list of regions where the model is going to be deployed. Only
   * one region per model is supported. Defaults to 'us-central1' if nothing is
   * set. See the available regions for AI Platform services. Note: * No matter
   * where a model is deployed, it can always be accessed by users from
   * anywhere, both for online and batch prediction. * The region for a batch
   * prediction job is set by the region field when submitting the batch
   * prediction job and does not take its value from this field.
   *
   * @param string[] $regions
   */
  public function setRegions($regions)
  {
    $this->regions = $regions;
  }
  /**
   * @return string[]
   */
  public function getRegions()
  {
    return $this->regions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1Model::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Model');
