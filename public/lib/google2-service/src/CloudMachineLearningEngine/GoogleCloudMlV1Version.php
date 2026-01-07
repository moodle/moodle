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

class GoogleCloudMlV1Version extends \Google\Collection
{
  /**
   * Unspecified framework. Assigns a value based on the file suffix.
   */
  public const FRAMEWORK_FRAMEWORK_UNSPECIFIED = 'FRAMEWORK_UNSPECIFIED';
  /**
   * Tensorflow framework.
   */
  public const FRAMEWORK_TENSORFLOW = 'TENSORFLOW';
  /**
   * Scikit-learn framework.
   */
  public const FRAMEWORK_SCIKIT_LEARN = 'SCIKIT_LEARN';
  /**
   * XGBoost framework.
   */
  public const FRAMEWORK_XGBOOST = 'XGBOOST';
  /**
   * The version state is unspecified.
   */
  public const STATE_UNKNOWN = 'UNKNOWN';
  /**
   * The version is ready for prediction.
   */
  public const STATE_READY = 'READY';
  /**
   * The version is being created. New UpdateVersion and DeleteVersion requests
   * will fail if a version is in the CREATING state.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The version failed to be created, possibly cancelled. `error_message`
   * should contain the details of the failure.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The version is being deleted. New UpdateVersion and DeleteVersion requests
   * will fail if a version is in the DELETING state.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The version is being updated. New UpdateVersion and DeleteVersion requests
   * will fail if a version is in the UPDATING state.
   */
  public const STATE_UPDATING = 'UPDATING';
  protected $collection_key = 'packageUris';
  protected $acceleratorConfigType = GoogleCloudMlV1AcceleratorConfig::class;
  protected $acceleratorConfigDataType = '';
  protected $autoScalingType = GoogleCloudMlV1AutoScaling::class;
  protected $autoScalingDataType = '';
  protected $containerType = GoogleCloudMlV1ContainerSpec::class;
  protected $containerDataType = '';
  /**
   * Output only. The time the version was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * The Cloud Storage URI of a directory containing trained model artifacts to
   * be used to create the model version. See the [guide to deploying
   * models](/ai-platform/prediction/docs/deploying-models) for more
   * information. The total number of files under this directory must not exceed
   * 1000. During projects.models.versions.create, AI Platform Prediction copies
   * all files from the specified directory to a location managed by the
   * service. From then on, AI Platform Prediction uses these copies of the
   * model artifacts to serve predictions, not the original files in Cloud
   * Storage, so this location is useful only as a historical record. If you
   * specify container, then this field is optional. Otherwise, it is required.
   * Learn [how to use this field with a custom container](/ai-
   * platform/prediction/docs/custom-container-requirements#artifacts).
   *
   * @var string
   */
  public $deploymentUri;
  /**
   * Optional. The description specified for the version when it was created.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The details of a failure or a cancellation.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * `etag` is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a model from overwriting each other. It is strongly
   * suggested that systems make use of the `etag` in the read-modify-write
   * cycle to perform model updates in order to avoid race conditions: An `etag`
   * is returned in the response to `GetVersion`, and systems are expected to
   * put that etag in the request to `UpdateVersion` to ensure that their change
   * will be applied to the model as intended.
   *
   * @var string
   */
  public $etag;
  protected $explanationConfigType = GoogleCloudMlV1ExplanationConfig::class;
  protected $explanationConfigDataType = '';
  /**
   * Optional. The machine learning framework AI Platform uses to train this
   * version of the model. Valid values are `TENSORFLOW`, `SCIKIT_LEARN`,
   * `XGBOOST`. If you do not specify a framework, AI Platform will analyze
   * files in the deployment_uri to determine a framework. If you choose
   * `SCIKIT_LEARN` or `XGBOOST`, you must also set the runtime version of the
   * model to 1.4 or greater. Do **not** specify a framework if you're deploying
   * a [custom prediction routine](/ai-platform/prediction/docs/custom-
   * prediction-routines) or if you're using a [custom container](/ai-
   * platform/prediction/docs/use-custom-container).
   *
   * @var string
   */
  public $framework;
  /**
   * Output only. If true, this version will be used to handle prediction
   * requests that do not specify a version. You can change the default version
   * by calling projects.methods.versions.setDefault.
   *
   * @var bool
   */
  public $isDefault;
  /**
   * Optional. One or more labels that you can add, to organize your model
   * versions. Each label is a key-value pair, where both the key and the value
   * are arbitrary strings that you supply. For more information, see the
   * documentation on using labels. Note that this field is not updatable for
   * mls1* models.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The [AI Platform (Unified)
   * `Model`](https://cloud.google.com/ai-platform-
   * unified/docs/reference/rest/v1beta1/projects.locations.models) ID for the
   * last [model migration](https://cloud.google.com/ai-platform-
   * unified/docs/start/migrating-to-ai-platform-unified).
   *
   * @var string
   */
  public $lastMigrationModelId;
  /**
   * Output only. The last time this version was successfully [migrated to AI
   * Platform (Unified)](https://cloud.google.com/ai-platform-
   * unified/docs/start/migrating-to-ai-platform-unified).
   *
   * @var string
   */
  public $lastMigrationTime;
  /**
   * Output only. The time the version was last used for prediction.
   *
   * @var string
   */
  public $lastUseTime;
  /**
   * Optional. The type of machine on which to serve the model. Currently only
   * applies to online prediction service. To learn about valid values for this
   * field, read [Choosing a machine type for online prediction](/ai-
   * platform/prediction/docs/machine-types-online-prediction). If this field is
   * not specified and you are using a [regional endpoint](/ai-
   * platform/prediction/docs/regional-endpoints), then the machine type
   * defaults to `n1-standard-2`. If this field is not specified and you are
   * using the global endpoint (`ml.googleapis.com`), then the machine type
   * defaults to `mls1-c1-m2`.
   *
   * @var string
   */
  public $machineType;
  protected $manualScalingType = GoogleCloudMlV1ManualScaling::class;
  protected $manualScalingDataType = '';
  /**
   * Required. The name specified for the version when it was created. The
   * version name must be unique within the model it is created in.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Cloud Storage paths (`gs://…`) of packages for [custom prediction
   * routines](/ml-engine/docs/tensorflow/custom-prediction-routines) or
   * [scikit-learn pipelines with custom code](/ml-engine/docs/scikit/exporting-
   * for-prediction#custom-pipeline-code). For a custom prediction routine, one
   * of these packages must contain your Predictor class (see
   * [`predictionClass`](#Version.FIELDS.prediction_class)). Additionally,
   * include any dependencies used by your Predictor or scikit-learn pipeline
   * uses that are not already included in your selected [runtime version](/ml-
   * engine/docs/tensorflow/runtime-version-list). If you specify this field,
   * you must also set [`runtimeVersion`](#Version.FIELDS.runtime_version) to
   * 1.4 or greater.
   *
   * @var string[]
   */
  public $packageUris;
  /**
   * Optional. The fully qualified name (module_name.class_name) of a class that
   * implements the Predictor interface described in this reference field. The
   * module containing this class should be included in a package provided to
   * the [`packageUris` field](#Version.FIELDS.package_uris). Specify this field
   * if and only if you are deploying a [custom prediction routine (beta)](/ml-
   * engine/docs/tensorflow/custom-prediction-routines). If you specify this
   * field, you must set [`runtimeVersion`](#Version.FIELDS.runtime_version) to
   * 1.4 or greater and you must set `machineType` to a [legacy (MLS1) machine
   * type](/ml-engine/docs/machine-types-online-prediction). The following code
   * sample provides the Predictor interface: class Predictor(object): Interface
   * for constructing custom predictors. def predict(self, instances, **kwargs):
   * Performs custom prediction. Instances are the decoded values from the
   * request. They have already been deserialized from JSON. Args: instances: A
   * list of prediction input instances. **kwargs: A dictionary of keyword args
   * provided as additional fields on the predict request body. Returns: A list
   * of outputs containing the prediction results. This list must be JSON
   * serializable.  raise NotImplementedError() @classmethod def from_path(cls,
   * model_dir): Creates an instance of Predictor using the given path. Loading
   * of the predictor should be done in this method. Args: model_dir: The local
   * directory that contains the exported model file along with any additional
   * files uploaded when creating the version resource. Returns: An instance
   * implementing this Predictor class.  raise NotImplementedError() Learn more
   * about [the Predictor interface and custom prediction routines](/ml-
   * engine/docs/tensorflow/custom-prediction-routines).
   *
   * @var string
   */
  public $predictionClass;
  /**
   * Required. The version of Python used in prediction. The following Python
   * versions are available: * Python '3.7' is available when `runtime_version`
   * is set to '1.15' or later. * Python '3.5' is available when
   * `runtime_version` is set to a version from '1.4' to '1.14'. * Python '2.7'
   * is available when `runtime_version` is set to '1.15' or earlier. Read more
   * about the Python versions available for [each runtime version](/ml-
   * engine/docs/runtime-version-list).
   *
   * @var string
   */
  public $pythonVersion;
  protected $requestLoggingConfigType = GoogleCloudMlV1RequestLoggingConfig::class;
  protected $requestLoggingConfigDataType = '';
  protected $routesType = GoogleCloudMlV1RouteMap::class;
  protected $routesDataType = '';
  /**
   * Required. The AI Platform runtime version to use for this deployment. For
   * more information, see the [runtime version list](/ml-engine/docs/runtime-
   * version-list) and [how to manage runtime versions](/ml-
   * engine/docs/versioning).
   *
   * @var string
   */
  public $runtimeVersion;
  /**
   * Optional. Specifies the service account for resource access control. If you
   * specify this field, then you must also specify either the `containerSpec`
   * or the `predictionClass` field. Learn more about [using a custom service
   * account](/ai-platform/prediction/docs/custom-service-account).
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Output only. The state of a version.
   *
   * @var string
   */
  public $state;

  /**
   * Optional. Accelerator config for using GPUs for online prediction (beta).
   * Only specify this field if you have specified a Compute Engine (N1) machine
   * type in the `machineType` field. Learn more about [using GPUs for online
   * prediction](/ml-engine/docs/machine-types-online-prediction#gpus).
   *
   * @param GoogleCloudMlV1AcceleratorConfig $acceleratorConfig
   */
  public function setAcceleratorConfig(GoogleCloudMlV1AcceleratorConfig $acceleratorConfig)
  {
    $this->acceleratorConfig = $acceleratorConfig;
  }
  /**
   * @return GoogleCloudMlV1AcceleratorConfig
   */
  public function getAcceleratorConfig()
  {
    return $this->acceleratorConfig;
  }
  /**
   * Automatically scale the number of nodes used to serve the model in response
   * to increases and decreases in traffic. Care should be taken to ramp up
   * traffic according to the model's ability to scale or you will start seeing
   * increases in latency and 429 response codes.
   *
   * @param GoogleCloudMlV1AutoScaling $autoScaling
   */
  public function setAutoScaling(GoogleCloudMlV1AutoScaling $autoScaling)
  {
    $this->autoScaling = $autoScaling;
  }
  /**
   * @return GoogleCloudMlV1AutoScaling
   */
  public function getAutoScaling()
  {
    return $this->autoScaling;
  }
  /**
   * Optional. Specifies a custom container to use for serving predictions. If
   * you specify this field, then `machineType` is required. If you specify this
   * field, then `deploymentUri` is optional. If you specify this field, then
   * you must not specify `runtimeVersion`, `packageUris`, `framework`,
   * `pythonVersion`, or `predictionClass`.
   *
   * @param GoogleCloudMlV1ContainerSpec $container
   */
  public function setContainer(GoogleCloudMlV1ContainerSpec $container)
  {
    $this->container = $container;
  }
  /**
   * @return GoogleCloudMlV1ContainerSpec
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * Output only. The time the version was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * The Cloud Storage URI of a directory containing trained model artifacts to
   * be used to create the model version. See the [guide to deploying
   * models](/ai-platform/prediction/docs/deploying-models) for more
   * information. The total number of files under this directory must not exceed
   * 1000. During projects.models.versions.create, AI Platform Prediction copies
   * all files from the specified directory to a location managed by the
   * service. From then on, AI Platform Prediction uses these copies of the
   * model artifacts to serve predictions, not the original files in Cloud
   * Storage, so this location is useful only as a historical record. If you
   * specify container, then this field is optional. Otherwise, it is required.
   * Learn [how to use this field with a custom container](/ai-
   * platform/prediction/docs/custom-container-requirements#artifacts).
   *
   * @param string $deploymentUri
   */
  public function setDeploymentUri($deploymentUri)
  {
    $this->deploymentUri = $deploymentUri;
  }
  /**
   * @return string
   */
  public function getDeploymentUri()
  {
    return $this->deploymentUri;
  }
  /**
   * Optional. The description specified for the version when it was created.
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
   * Output only. The details of a failure or a cancellation.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * `etag` is used for optimistic concurrency control as a way to help prevent
   * simultaneous updates of a model from overwriting each other. It is strongly
   * suggested that systems make use of the `etag` in the read-modify-write
   * cycle to perform model updates in order to avoid race conditions: An `etag`
   * is returned in the response to `GetVersion`, and systems are expected to
   * put that etag in the request to `UpdateVersion` to ensure that their change
   * will be applied to the model as intended.
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
   * Optional. Configures explainability features on the model's version. Some
   * explanation features require additional metadata to be loaded as part of
   * the model payload.
   *
   * @param GoogleCloudMlV1ExplanationConfig $explanationConfig
   */
  public function setExplanationConfig(GoogleCloudMlV1ExplanationConfig $explanationConfig)
  {
    $this->explanationConfig = $explanationConfig;
  }
  /**
   * @return GoogleCloudMlV1ExplanationConfig
   */
  public function getExplanationConfig()
  {
    return $this->explanationConfig;
  }
  /**
   * Optional. The machine learning framework AI Platform uses to train this
   * version of the model. Valid values are `TENSORFLOW`, `SCIKIT_LEARN`,
   * `XGBOOST`. If you do not specify a framework, AI Platform will analyze
   * files in the deployment_uri to determine a framework. If you choose
   * `SCIKIT_LEARN` or `XGBOOST`, you must also set the runtime version of the
   * model to 1.4 or greater. Do **not** specify a framework if you're deploying
   * a [custom prediction routine](/ai-platform/prediction/docs/custom-
   * prediction-routines) or if you're using a [custom container](/ai-
   * platform/prediction/docs/use-custom-container).
   *
   * Accepted values: FRAMEWORK_UNSPECIFIED, TENSORFLOW, SCIKIT_LEARN, XGBOOST
   *
   * @param self::FRAMEWORK_* $framework
   */
  public function setFramework($framework)
  {
    $this->framework = $framework;
  }
  /**
   * @return self::FRAMEWORK_*
   */
  public function getFramework()
  {
    return $this->framework;
  }
  /**
   * Output only. If true, this version will be used to handle prediction
   * requests that do not specify a version. You can change the default version
   * by calling projects.methods.versions.setDefault.
   *
   * @param bool $isDefault
   */
  public function setIsDefault($isDefault)
  {
    $this->isDefault = $isDefault;
  }
  /**
   * @return bool
   */
  public function getIsDefault()
  {
    return $this->isDefault;
  }
  /**
   * Optional. One or more labels that you can add, to organize your model
   * versions. Each label is a key-value pair, where both the key and the value
   * are arbitrary strings that you supply. For more information, see the
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
   * Output only. The [AI Platform (Unified)
   * `Model`](https://cloud.google.com/ai-platform-
   * unified/docs/reference/rest/v1beta1/projects.locations.models) ID for the
   * last [model migration](https://cloud.google.com/ai-platform-
   * unified/docs/start/migrating-to-ai-platform-unified).
   *
   * @param string $lastMigrationModelId
   */
  public function setLastMigrationModelId($lastMigrationModelId)
  {
    $this->lastMigrationModelId = $lastMigrationModelId;
  }
  /**
   * @return string
   */
  public function getLastMigrationModelId()
  {
    return $this->lastMigrationModelId;
  }
  /**
   * Output only. The last time this version was successfully [migrated to AI
   * Platform (Unified)](https://cloud.google.com/ai-platform-
   * unified/docs/start/migrating-to-ai-platform-unified).
   *
   * @param string $lastMigrationTime
   */
  public function setLastMigrationTime($lastMigrationTime)
  {
    $this->lastMigrationTime = $lastMigrationTime;
  }
  /**
   * @return string
   */
  public function getLastMigrationTime()
  {
    return $this->lastMigrationTime;
  }
  /**
   * Output only. The time the version was last used for prediction.
   *
   * @param string $lastUseTime
   */
  public function setLastUseTime($lastUseTime)
  {
    $this->lastUseTime = $lastUseTime;
  }
  /**
   * @return string
   */
  public function getLastUseTime()
  {
    return $this->lastUseTime;
  }
  /**
   * Optional. The type of machine on which to serve the model. Currently only
   * applies to online prediction service. To learn about valid values for this
   * field, read [Choosing a machine type for online prediction](/ai-
   * platform/prediction/docs/machine-types-online-prediction). If this field is
   * not specified and you are using a [regional endpoint](/ai-
   * platform/prediction/docs/regional-endpoints), then the machine type
   * defaults to `n1-standard-2`. If this field is not specified and you are
   * using the global endpoint (`ml.googleapis.com`), then the machine type
   * defaults to `mls1-c1-m2`.
   *
   * @param string $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return string
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Manually select the number of nodes to use for serving the model. You
   * should generally use `auto_scaling` with an appropriate `min_nodes`
   * instead, but this option is available if you want more predictable billing.
   * Beware that latency and error rates will increase if the traffic exceeds
   * that capability of the system to serve it based on the selected number of
   * nodes.
   *
   * @param GoogleCloudMlV1ManualScaling $manualScaling
   */
  public function setManualScaling(GoogleCloudMlV1ManualScaling $manualScaling)
  {
    $this->manualScaling = $manualScaling;
  }
  /**
   * @return GoogleCloudMlV1ManualScaling
   */
  public function getManualScaling()
  {
    return $this->manualScaling;
  }
  /**
   * Required. The name specified for the version when it was created. The
   * version name must be unique within the model it is created in.
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
   * Optional. Cloud Storage paths (`gs://…`) of packages for [custom prediction
   * routines](/ml-engine/docs/tensorflow/custom-prediction-routines) or
   * [scikit-learn pipelines with custom code](/ml-engine/docs/scikit/exporting-
   * for-prediction#custom-pipeline-code). For a custom prediction routine, one
   * of these packages must contain your Predictor class (see
   * [`predictionClass`](#Version.FIELDS.prediction_class)). Additionally,
   * include any dependencies used by your Predictor or scikit-learn pipeline
   * uses that are not already included in your selected [runtime version](/ml-
   * engine/docs/tensorflow/runtime-version-list). If you specify this field,
   * you must also set [`runtimeVersion`](#Version.FIELDS.runtime_version) to
   * 1.4 or greater.
   *
   * @param string[] $packageUris
   */
  public function setPackageUris($packageUris)
  {
    $this->packageUris = $packageUris;
  }
  /**
   * @return string[]
   */
  public function getPackageUris()
  {
    return $this->packageUris;
  }
  /**
   * Optional. The fully qualified name (module_name.class_name) of a class that
   * implements the Predictor interface described in this reference field. The
   * module containing this class should be included in a package provided to
   * the [`packageUris` field](#Version.FIELDS.package_uris). Specify this field
   * if and only if you are deploying a [custom prediction routine (beta)](/ml-
   * engine/docs/tensorflow/custom-prediction-routines). If you specify this
   * field, you must set [`runtimeVersion`](#Version.FIELDS.runtime_version) to
   * 1.4 or greater and you must set `machineType` to a [legacy (MLS1) machine
   * type](/ml-engine/docs/machine-types-online-prediction). The following code
   * sample provides the Predictor interface: class Predictor(object): Interface
   * for constructing custom predictors. def predict(self, instances, **kwargs):
   * Performs custom prediction. Instances are the decoded values from the
   * request. They have already been deserialized from JSON. Args: instances: A
   * list of prediction input instances. **kwargs: A dictionary of keyword args
   * provided as additional fields on the predict request body. Returns: A list
   * of outputs containing the prediction results. This list must be JSON
   * serializable.  raise NotImplementedError() @classmethod def from_path(cls,
   * model_dir): Creates an instance of Predictor using the given path. Loading
   * of the predictor should be done in this method. Args: model_dir: The local
   * directory that contains the exported model file along with any additional
   * files uploaded when creating the version resource. Returns: An instance
   * implementing this Predictor class.  raise NotImplementedError() Learn more
   * about [the Predictor interface and custom prediction routines](/ml-
   * engine/docs/tensorflow/custom-prediction-routines).
   *
   * @param string $predictionClass
   */
  public function setPredictionClass($predictionClass)
  {
    $this->predictionClass = $predictionClass;
  }
  /**
   * @return string
   */
  public function getPredictionClass()
  {
    return $this->predictionClass;
  }
  /**
   * Required. The version of Python used in prediction. The following Python
   * versions are available: * Python '3.7' is available when `runtime_version`
   * is set to '1.15' or later. * Python '3.5' is available when
   * `runtime_version` is set to a version from '1.4' to '1.14'. * Python '2.7'
   * is available when `runtime_version` is set to '1.15' or earlier. Read more
   * about the Python versions available for [each runtime version](/ml-
   * engine/docs/runtime-version-list).
   *
   * @param string $pythonVersion
   */
  public function setPythonVersion($pythonVersion)
  {
    $this->pythonVersion = $pythonVersion;
  }
  /**
   * @return string
   */
  public function getPythonVersion()
  {
    return $this->pythonVersion;
  }
  /**
   * Optional. *Only* specify this field in a projects.models.versions.patch
   * request. Specifying it in a projects.models.versions.create request has no
   * effect. Configures the request-response pair logging on predictions from
   * this Version.
   *
   * @param GoogleCloudMlV1RequestLoggingConfig $requestLoggingConfig
   */
  public function setRequestLoggingConfig(GoogleCloudMlV1RequestLoggingConfig $requestLoggingConfig)
  {
    $this->requestLoggingConfig = $requestLoggingConfig;
  }
  /**
   * @return GoogleCloudMlV1RequestLoggingConfig
   */
  public function getRequestLoggingConfig()
  {
    return $this->requestLoggingConfig;
  }
  /**
   * Optional. Specifies paths on a custom container's HTTP server where AI
   * Platform Prediction sends certain requests. If you specify this field, then
   * you must also specify the `container` field. If you specify the `container`
   * field and do not specify this field, it defaults to the following: ```json
   * { "predict": "/v1/models/MODEL/versions/VERSION:predict", "health":
   * "/v1/models/MODEL/versions/VERSION" } ``` See RouteMap for more details
   * about these default values.
   *
   * @param GoogleCloudMlV1RouteMap $routes
   */
  public function setRoutes(GoogleCloudMlV1RouteMap $routes)
  {
    $this->routes = $routes;
  }
  /**
   * @return GoogleCloudMlV1RouteMap
   */
  public function getRoutes()
  {
    return $this->routes;
  }
  /**
   * Required. The AI Platform runtime version to use for this deployment. For
   * more information, see the [runtime version list](/ml-engine/docs/runtime-
   * version-list) and [how to manage runtime versions](/ml-
   * engine/docs/versioning).
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
   * Optional. Specifies the service account for resource access control. If you
   * specify this field, then you must also specify either the `containerSpec`
   * or the `predictionClass` field. Learn more about [using a custom service
   * account](/ai-platform/prediction/docs/custom-service-account).
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Output only. The state of a version.
   *
   * Accepted values: UNKNOWN, READY, CREATING, FAILED, DELETING, UPDATING
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1Version::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Version');
