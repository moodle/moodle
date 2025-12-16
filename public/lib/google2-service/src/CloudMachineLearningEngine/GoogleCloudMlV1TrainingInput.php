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

class GoogleCloudMlV1TrainingInput extends \Google\Collection
{
  /**
   * A single worker instance. This tier is suitable for learning how to use
   * Cloud ML, and for experimenting with new models using small datasets.
   */
  public const SCALE_TIER_BASIC = 'BASIC';
  /**
   * Many workers and a few parameter servers.
   */
  public const SCALE_TIER_STANDARD_1 = 'STANDARD_1';
  /**
   * A large number of workers with many parameter servers.
   */
  public const SCALE_TIER_PREMIUM_1 = 'PREMIUM_1';
  /**
   * A single worker instance [with a GPU](/ai-platform/training/docs/using-
   * gpus).
   */
  public const SCALE_TIER_BASIC_GPU = 'BASIC_GPU';
  /**
   * A single worker instance with a [Cloud TPU](/ml-
   * engine/docs/tensorflow/using-tpus).
   */
  public const SCALE_TIER_BASIC_TPU = 'BASIC_TPU';
  /**
   * The CUSTOM tier is not a set tier, but rather enables you to use your own
   * cluster specification. When you use this tier, set values to configure your
   * processing cluster according to these guidelines: * You _must_ set
   * `TrainingInput.masterType` to specify the type of machine to use for your
   * master node. This is the only required setting. * You _may_ set
   * `TrainingInput.workerCount` to specify the number of workers to use. If you
   * specify one or more workers, you _must_ also set `TrainingInput.workerType`
   * to specify the type of machine to use for your worker nodes. * You _may_
   * set `TrainingInput.parameterServerCount` to specify the number of parameter
   * servers to use. If you specify one or more parameter servers, you _must_
   * also set `TrainingInput.parameterServerType` to specify the type of machine
   * to use for your parameter servers. Note that all of your workers must use
   * the same machine type, which can be different from your parameter server
   * type and master type. Your parameter servers must likewise use the same
   * machine type, which can be different from your worker type and master type.
   */
  public const SCALE_TIER_CUSTOM = 'CUSTOM';
  protected $collection_key = 'packageUris';
  /**
   * Optional. Command-line arguments passed to the training application when it
   * starts. If your job uses a custom container, then the arguments are passed
   * to the container's `ENTRYPOINT` command.
   *
   * @var string[]
   */
  public $args;
  /**
   * Optional. Whether you want AI Platform Training to enable [interactive
   * shell access](https://cloud.google.com/ai-platform/training/docs/monitor-
   * debug-interactive-shell) to training containers. If set to `true`, you can
   * access interactive shells at the URIs given by
   * TrainingOutput.web_access_uris or HyperparameterOutput.web_access_uris
   * (within TrainingOutput.trials).
   *
   * @var bool
   */
  public $enableWebAccess;
  protected $encryptionConfigType = GoogleCloudMlV1EncryptionConfig::class;
  protected $encryptionConfigDataType = '';
  protected $evaluatorConfigType = GoogleCloudMlV1ReplicaConfig::class;
  protected $evaluatorConfigDataType = '';
  /**
   * Optional. The number of evaluator replicas to use for the training job.
   * Each replica in the cluster will be of the type specified in
   * `evaluator_type`. This value can only be used when `scale_tier` is set to
   * `CUSTOM`. If you set this value, you must also set `evaluator_type`. The
   * default value is zero.
   *
   * @var string
   */
  public $evaluatorCount;
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's evaluator nodes. The supported values are the same as those described
   * in the entry for `masterType`. This value must be consistent with the
   * category of machine type that `masterType` uses. In other words, both must
   * be Compute Engine machine types or both must be legacy machine types. This
   * value must be present when `scaleTier` is set to `CUSTOM` and
   * `evaluatorCount` is greater than zero.
   *
   * @var string
   */
  public $evaluatorType;
  protected $hyperparametersType = GoogleCloudMlV1HyperparameterSpec::class;
  protected $hyperparametersDataType = '';
  /**
   * Optional. A Google Cloud Storage path in which to store training outputs
   * and other data needed for training. This path is passed to your TensorFlow
   * program as the '--job-dir' command-line argument. The benefit of specifying
   * this field is that Cloud ML validates the path for use in training.
   *
   * @var string
   */
  public $jobDir;
  protected $masterConfigType = GoogleCloudMlV1ReplicaConfig::class;
  protected $masterConfigDataType = '';
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's master worker. You must specify this field when `scaleTier` is set to
   * `CUSTOM`. You can use certain Compute Engine machine types directly in this
   * field. See the [list of compatible Compute Engine machine types](/ai-
   * platform/training/docs/machine-types#compute-engine-machine-types).
   * Alternatively, you can use the certain legacy machine types in this field.
   * See the [list of legacy machine types](/ai-platform/training/docs/machine-
   * types#legacy-machine-types). Finally, if you want to use a TPU for
   * training, specify `cloud_tpu` in this field. Learn more about the [special
   * configuration options for training with TPUs](/ai-
   * platform/training/docs/using-tpus#configuring_a_custom_tpu_machine).
   *
   * @var string
   */
  public $masterType;
  /**
   * Optional. The full name of the [Compute Engine network](/vpc/docs/vpc) to
   * which the Job is peered. For example,
   * `projects/12345/global/networks/myVPC`. The format of this field is
   * `projects/{project}/global/networks/{network}`, where {project} is a
   * project number (like `12345`) and {network} is network name. Private
   * services access must already be configured for the network. If left
   * unspecified, the Job is not peered with any network. [Learn about using VPC
   * Network Peering.](/ai-platform/training/docs/vpc-peering).
   *
   * @var string
   */
  public $network;
  /**
   * Required. The Google Cloud Storage location of the packages with the
   * training program and any additional dependencies. The maximum number of
   * package URIs is 100.
   *
   * @var string[]
   */
  public $packageUris;
  protected $parameterServerConfigType = GoogleCloudMlV1ReplicaConfig::class;
  protected $parameterServerConfigDataType = '';
  /**
   * Optional. The number of parameter server replicas to use for the training
   * job. Each replica in the cluster will be of the type specified in
   * `parameter_server_type`. This value can only be used when `scale_tier` is
   * set to `CUSTOM`. If you set this value, you must also set
   * `parameter_server_type`. The default value is zero.
   *
   * @var string
   */
  public $parameterServerCount;
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's parameter server. The supported values are the same as those
   * described in the entry for `master_type`. This value must be consistent
   * with the category of machine type that `masterType` uses. In other words,
   * both must be Compute Engine machine types or both must be legacy machine
   * types. This value must be present when `scaleTier` is set to `CUSTOM` and
   * `parameter_server_count` is greater than zero.
   *
   * @var string
   */
  public $parameterServerType;
  /**
   * Required. The Python module name to run after installing the packages.
   *
   * @var string
   */
  public $pythonModule;
  /**
   * Optional. The version of Python used in training. You must either specify
   * this field or specify `masterConfig.imageUri`. The following Python
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
  /**
   * Required. The region to run the training job in. See the [available
   * regions](/ai-platform/training/docs/regions) for AI Platform Training.
   *
   * @var string
   */
  public $region;
  /**
   * Optional. The AI Platform runtime version to use for training. You must
   * either specify this field or specify `masterConfig.imageUri`. For more
   * information, see the [runtime version list](/ai-
   * platform/training/docs/runtime-version-list) and learn [how to manage
   * runtime versions](/ai-platform/training/docs/versioning).
   *
   * @var string
   */
  public $runtimeVersion;
  /**
   * Required. Specifies the machine types, the number of replicas for workers
   * and parameter servers.
   *
   * @var string
   */
  public $scaleTier;
  protected $schedulingType = GoogleCloudMlV1Scheduling::class;
  protected $schedulingDataType = '';
  /**
   * Optional. The email address of a service account to use when running the
   * training appplication. You must have the `iam.serviceAccounts.actAs`
   * permission for the specified service account. In addition, the AI Platform
   * Training Google-managed service account must have the
   * `roles/iam.serviceAccountAdmin` role for the specified service account.
   * [Learn more about configuring a service account.](/ai-
   * platform/training/docs/custom-service-account) If not specified, the AI
   * Platform Training Google-managed service account is used by default.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. Use `chief` instead of `master` in the `TF_CONFIG` environment
   * variable when training with a custom container. Defaults to `false`. [Learn
   * more about this field.](/ai-platform/training/docs/distributed-training-
   * details#chief-versus-master) This field has no effect for training jobs
   * that don't use a custom container.
   *
   * @var bool
   */
  public $useChiefInTfConfig;
  protected $workerConfigType = GoogleCloudMlV1ReplicaConfig::class;
  protected $workerConfigDataType = '';
  /**
   * Optional. The number of worker replicas to use for the training job. Each
   * replica in the cluster will be of the type specified in `worker_type`. This
   * value can only be used when `scale_tier` is set to `CUSTOM`. If you set
   * this value, you must also set `worker_type`. The default value is zero.
   *
   * @var string
   */
  public $workerCount;
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's worker nodes. The supported values are the same as those described in
   * the entry for `masterType`. This value must be consistent with the category
   * of machine type that `masterType` uses. In other words, both must be
   * Compute Engine machine types or both must be legacy machine types. If you
   * use `cloud_tpu` for this value, see special instructions for [configuring a
   * custom TPU machine](/ml-engine/docs/tensorflow/using-
   * tpus#configuring_a_custom_tpu_machine). This value must be present when
   * `scaleTier` is set to `CUSTOM` and `workerCount` is greater than zero.
   *
   * @var string
   */
  public $workerType;

  /**
   * Optional. Command-line arguments passed to the training application when it
   * starts. If your job uses a custom container, then the arguments are passed
   * to the container's `ENTRYPOINT` command.
   *
   * @param string[] $args
   */
  public function setArgs($args)
  {
    $this->args = $args;
  }
  /**
   * @return string[]
   */
  public function getArgs()
  {
    return $this->args;
  }
  /**
   * Optional. Whether you want AI Platform Training to enable [interactive
   * shell access](https://cloud.google.com/ai-platform/training/docs/monitor-
   * debug-interactive-shell) to training containers. If set to `true`, you can
   * access interactive shells at the URIs given by
   * TrainingOutput.web_access_uris or HyperparameterOutput.web_access_uris
   * (within TrainingOutput.trials).
   *
   * @param bool $enableWebAccess
   */
  public function setEnableWebAccess($enableWebAccess)
  {
    $this->enableWebAccess = $enableWebAccess;
  }
  /**
   * @return bool
   */
  public function getEnableWebAccess()
  {
    return $this->enableWebAccess;
  }
  /**
   * Optional. Options for using customer-managed encryption keys (CMEK) to
   * protect resources created by a training job, instead of using Google's
   * default encryption. If this is set, then all resources created by the
   * training job will be encrypted with the customer-managed encryption key
   * that you specify. [Learn how and when to use CMEK with AI Platform
   * Training](/ai-platform/training/docs/cmek).
   *
   * @param GoogleCloudMlV1EncryptionConfig $encryptionConfig
   */
  public function setEncryptionConfig(GoogleCloudMlV1EncryptionConfig $encryptionConfig)
  {
    $this->encryptionConfig = $encryptionConfig;
  }
  /**
   * @return GoogleCloudMlV1EncryptionConfig
   */
  public function getEncryptionConfig()
  {
    return $this->encryptionConfig;
  }
  /**
   * Optional. The configuration for evaluators. You should only set
   * `evaluatorConfig.acceleratorConfig` if `evaluatorType` is set to a Compute
   * Engine machine type. [Learn about restrictions on accelerator
   * configurations for training.](/ai-platform/training/docs/using-
   * gpus#compute-engine-machine-types-with-gpu) Set `evaluatorConfig.imageUri`
   * only if you build a custom image for your evaluator. If
   * `evaluatorConfig.imageUri` has not been set, AI Platform uses the value of
   * `masterConfig.imageUri`. Learn more about [configuring custom
   * containers](/ai-platform/training/docs/distributed-training-containers).
   *
   * @param GoogleCloudMlV1ReplicaConfig $evaluatorConfig
   */
  public function setEvaluatorConfig(GoogleCloudMlV1ReplicaConfig $evaluatorConfig)
  {
    $this->evaluatorConfig = $evaluatorConfig;
  }
  /**
   * @return GoogleCloudMlV1ReplicaConfig
   */
  public function getEvaluatorConfig()
  {
    return $this->evaluatorConfig;
  }
  /**
   * Optional. The number of evaluator replicas to use for the training job.
   * Each replica in the cluster will be of the type specified in
   * `evaluator_type`. This value can only be used when `scale_tier` is set to
   * `CUSTOM`. If you set this value, you must also set `evaluator_type`. The
   * default value is zero.
   *
   * @param string $evaluatorCount
   */
  public function setEvaluatorCount($evaluatorCount)
  {
    $this->evaluatorCount = $evaluatorCount;
  }
  /**
   * @return string
   */
  public function getEvaluatorCount()
  {
    return $this->evaluatorCount;
  }
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's evaluator nodes. The supported values are the same as those described
   * in the entry for `masterType`. This value must be consistent with the
   * category of machine type that `masterType` uses. In other words, both must
   * be Compute Engine machine types or both must be legacy machine types. This
   * value must be present when `scaleTier` is set to `CUSTOM` and
   * `evaluatorCount` is greater than zero.
   *
   * @param string $evaluatorType
   */
  public function setEvaluatorType($evaluatorType)
  {
    $this->evaluatorType = $evaluatorType;
  }
  /**
   * @return string
   */
  public function getEvaluatorType()
  {
    return $this->evaluatorType;
  }
  /**
   * Optional. The set of Hyperparameters to tune.
   *
   * @param GoogleCloudMlV1HyperparameterSpec $hyperparameters
   */
  public function setHyperparameters(GoogleCloudMlV1HyperparameterSpec $hyperparameters)
  {
    $this->hyperparameters = $hyperparameters;
  }
  /**
   * @return GoogleCloudMlV1HyperparameterSpec
   */
  public function getHyperparameters()
  {
    return $this->hyperparameters;
  }
  /**
   * Optional. A Google Cloud Storage path in which to store training outputs
   * and other data needed for training. This path is passed to your TensorFlow
   * program as the '--job-dir' command-line argument. The benefit of specifying
   * this field is that Cloud ML validates the path for use in training.
   *
   * @param string $jobDir
   */
  public function setJobDir($jobDir)
  {
    $this->jobDir = $jobDir;
  }
  /**
   * @return string
   */
  public function getJobDir()
  {
    return $this->jobDir;
  }
  /**
   * Optional. The configuration for your master worker. You should only set
   * `masterConfig.acceleratorConfig` if `masterType` is set to a Compute Engine
   * machine type. Learn about [restrictions on accelerator configurations for
   * training.](/ai-platform/training/docs/using-gpus#compute-engine-machine-
   * types-with-gpu) Set `masterConfig.imageUri` only if you build a custom
   * image. Only one of `masterConfig.imageUri` and `runtimeVersion` should be
   * set. Learn more about [configuring custom containers](/ai-
   * platform/training/docs/distributed-training-containers).
   *
   * @param GoogleCloudMlV1ReplicaConfig $masterConfig
   */
  public function setMasterConfig(GoogleCloudMlV1ReplicaConfig $masterConfig)
  {
    $this->masterConfig = $masterConfig;
  }
  /**
   * @return GoogleCloudMlV1ReplicaConfig
   */
  public function getMasterConfig()
  {
    return $this->masterConfig;
  }
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's master worker. You must specify this field when `scaleTier` is set to
   * `CUSTOM`. You can use certain Compute Engine machine types directly in this
   * field. See the [list of compatible Compute Engine machine types](/ai-
   * platform/training/docs/machine-types#compute-engine-machine-types).
   * Alternatively, you can use the certain legacy machine types in this field.
   * See the [list of legacy machine types](/ai-platform/training/docs/machine-
   * types#legacy-machine-types). Finally, if you want to use a TPU for
   * training, specify `cloud_tpu` in this field. Learn more about the [special
   * configuration options for training with TPUs](/ai-
   * platform/training/docs/using-tpus#configuring_a_custom_tpu_machine).
   *
   * @param string $masterType
   */
  public function setMasterType($masterType)
  {
    $this->masterType = $masterType;
  }
  /**
   * @return string
   */
  public function getMasterType()
  {
    return $this->masterType;
  }
  /**
   * Optional. The full name of the [Compute Engine network](/vpc/docs/vpc) to
   * which the Job is peered. For example,
   * `projects/12345/global/networks/myVPC`. The format of this field is
   * `projects/{project}/global/networks/{network}`, where {project} is a
   * project number (like `12345`) and {network} is network name. Private
   * services access must already be configured for the network. If left
   * unspecified, the Job is not peered with any network. [Learn about using VPC
   * Network Peering.](/ai-platform/training/docs/vpc-peering).
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Required. The Google Cloud Storage location of the packages with the
   * training program and any additional dependencies. The maximum number of
   * package URIs is 100.
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
   * Optional. The configuration for parameter servers. You should only set
   * `parameterServerConfig.acceleratorConfig` if `parameterServerType` is set
   * to a Compute Engine machine type. [Learn about restrictions on accelerator
   * configurations for training.](/ai-platform/training/docs/using-
   * gpus#compute-engine-machine-types-with-gpu) Set
   * `parameterServerConfig.imageUri` only if you build a custom image for your
   * parameter server. If `parameterServerConfig.imageUri` has not been set, AI
   * Platform uses the value of `masterConfig.imageUri`. Learn more about
   * [configuring custom containers](/ai-platform/training/docs/distributed-
   * training-containers).
   *
   * @param GoogleCloudMlV1ReplicaConfig $parameterServerConfig
   */
  public function setParameterServerConfig(GoogleCloudMlV1ReplicaConfig $parameterServerConfig)
  {
    $this->parameterServerConfig = $parameterServerConfig;
  }
  /**
   * @return GoogleCloudMlV1ReplicaConfig
   */
  public function getParameterServerConfig()
  {
    return $this->parameterServerConfig;
  }
  /**
   * Optional. The number of parameter server replicas to use for the training
   * job. Each replica in the cluster will be of the type specified in
   * `parameter_server_type`. This value can only be used when `scale_tier` is
   * set to `CUSTOM`. If you set this value, you must also set
   * `parameter_server_type`. The default value is zero.
   *
   * @param string $parameterServerCount
   */
  public function setParameterServerCount($parameterServerCount)
  {
    $this->parameterServerCount = $parameterServerCount;
  }
  /**
   * @return string
   */
  public function getParameterServerCount()
  {
    return $this->parameterServerCount;
  }
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's parameter server. The supported values are the same as those
   * described in the entry for `master_type`. This value must be consistent
   * with the category of machine type that `masterType` uses. In other words,
   * both must be Compute Engine machine types or both must be legacy machine
   * types. This value must be present when `scaleTier` is set to `CUSTOM` and
   * `parameter_server_count` is greater than zero.
   *
   * @param string $parameterServerType
   */
  public function setParameterServerType($parameterServerType)
  {
    $this->parameterServerType = $parameterServerType;
  }
  /**
   * @return string
   */
  public function getParameterServerType()
  {
    return $this->parameterServerType;
  }
  /**
   * Required. The Python module name to run after installing the packages.
   *
   * @param string $pythonModule
   */
  public function setPythonModule($pythonModule)
  {
    $this->pythonModule = $pythonModule;
  }
  /**
   * @return string
   */
  public function getPythonModule()
  {
    return $this->pythonModule;
  }
  /**
   * Optional. The version of Python used in training. You must either specify
   * this field or specify `masterConfig.imageUri`. The following Python
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
   * Required. The region to run the training job in. See the [available
   * regions](/ai-platform/training/docs/regions) for AI Platform Training.
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
   * Optional. The AI Platform runtime version to use for training. You must
   * either specify this field or specify `masterConfig.imageUri`. For more
   * information, see the [runtime version list](/ai-
   * platform/training/docs/runtime-version-list) and learn [how to manage
   * runtime versions](/ai-platform/training/docs/versioning).
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
   * Required. Specifies the machine types, the number of replicas for workers
   * and parameter servers.
   *
   * Accepted values: BASIC, STANDARD_1, PREMIUM_1, BASIC_GPU, BASIC_TPU, CUSTOM
   *
   * @param self::SCALE_TIER_* $scaleTier
   */
  public function setScaleTier($scaleTier)
  {
    $this->scaleTier = $scaleTier;
  }
  /**
   * @return self::SCALE_TIER_*
   */
  public function getScaleTier()
  {
    return $this->scaleTier;
  }
  /**
   * Optional. Scheduling options for a training job.
   *
   * @param GoogleCloudMlV1Scheduling $scheduling
   */
  public function setScheduling(GoogleCloudMlV1Scheduling $scheduling)
  {
    $this->scheduling = $scheduling;
  }
  /**
   * @return GoogleCloudMlV1Scheduling
   */
  public function getScheduling()
  {
    return $this->scheduling;
  }
  /**
   * Optional. The email address of a service account to use when running the
   * training appplication. You must have the `iam.serviceAccounts.actAs`
   * permission for the specified service account. In addition, the AI Platform
   * Training Google-managed service account must have the
   * `roles/iam.serviceAccountAdmin` role for the specified service account.
   * [Learn more about configuring a service account.](/ai-
   * platform/training/docs/custom-service-account) If not specified, the AI
   * Platform Training Google-managed service account is used by default.
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
   * Optional. Use `chief` instead of `master` in the `TF_CONFIG` environment
   * variable when training with a custom container. Defaults to `false`. [Learn
   * more about this field.](/ai-platform/training/docs/distributed-training-
   * details#chief-versus-master) This field has no effect for training jobs
   * that don't use a custom container.
   *
   * @param bool $useChiefInTfConfig
   */
  public function setUseChiefInTfConfig($useChiefInTfConfig)
  {
    $this->useChiefInTfConfig = $useChiefInTfConfig;
  }
  /**
   * @return bool
   */
  public function getUseChiefInTfConfig()
  {
    return $this->useChiefInTfConfig;
  }
  /**
   * Optional. The configuration for workers. You should only set
   * `workerConfig.acceleratorConfig` if `workerType` is set to a Compute Engine
   * machine type. [Learn about restrictions on accelerator configurations for
   * training.](/ai-platform/training/docs/using-gpus#compute-engine-machine-
   * types-with-gpu) Set `workerConfig.imageUri` only if you build a custom
   * image for your worker. If `workerConfig.imageUri` has not been set, AI
   * Platform uses the value of `masterConfig.imageUri`. Learn more about
   * [configuring custom containers](/ai-platform/training/docs/distributed-
   * training-containers).
   *
   * @param GoogleCloudMlV1ReplicaConfig $workerConfig
   */
  public function setWorkerConfig(GoogleCloudMlV1ReplicaConfig $workerConfig)
  {
    $this->workerConfig = $workerConfig;
  }
  /**
   * @return GoogleCloudMlV1ReplicaConfig
   */
  public function getWorkerConfig()
  {
    return $this->workerConfig;
  }
  /**
   * Optional. The number of worker replicas to use for the training job. Each
   * replica in the cluster will be of the type specified in `worker_type`. This
   * value can only be used when `scale_tier` is set to `CUSTOM`. If you set
   * this value, you must also set `worker_type`. The default value is zero.
   *
   * @param string $workerCount
   */
  public function setWorkerCount($workerCount)
  {
    $this->workerCount = $workerCount;
  }
  /**
   * @return string
   */
  public function getWorkerCount()
  {
    return $this->workerCount;
  }
  /**
   * Optional. Specifies the type of virtual machine to use for your training
   * job's worker nodes. The supported values are the same as those described in
   * the entry for `masterType`. This value must be consistent with the category
   * of machine type that `masterType` uses. In other words, both must be
   * Compute Engine machine types or both must be legacy machine types. If you
   * use `cloud_tpu` for this value, see special instructions for [configuring a
   * custom TPU machine](/ml-engine/docs/tensorflow/using-
   * tpus#configuring_a_custom_tpu_machine). This value must be present when
   * `scaleTier` is set to `CUSTOM` and `workerCount` is greater than zero.
   *
   * @param string $workerType
   */
  public function setWorkerType($workerType)
  {
    $this->workerType = $workerType;
  }
  /**
   * @return string
   */
  public function getWorkerType()
  {
    return $this->workerType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1TrainingInput::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1TrainingInput');
