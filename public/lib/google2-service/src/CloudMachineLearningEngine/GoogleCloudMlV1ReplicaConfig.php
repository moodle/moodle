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

class GoogleCloudMlV1ReplicaConfig extends \Google\Collection
{
  protected $collection_key = 'containerCommand';
  protected $acceleratorConfigType = GoogleCloudMlV1AcceleratorConfig::class;
  protected $acceleratorConfigDataType = '';
  /**
   * Arguments to the entrypoint command. The following rules apply for
   * container_command and container_args: - If you do not supply command or
   * args: The defaults defined in the Docker image are used. - If you supply a
   * command but no args: The default EntryPoint and the default Cmd defined in
   * the Docker image are ignored. Your command is run without any arguments. -
   * If you supply only args: The default Entrypoint defined in the Docker image
   * is run with the args that you supplied. - If you supply a command and args:
   * The default Entrypoint and the default Cmd defined in the Docker image are
   * ignored. Your command is run with your args. It cannot be set if custom
   * container image is not provided. Note that this field and
   * [TrainingInput.args] are mutually exclusive, i.e., both cannot be set at
   * the same time.
   *
   * @var string[]
   */
  public $containerArgs;
  /**
   * The command with which the replica's custom container is run. If provided,
   * it will override default ENTRYPOINT of the docker image. If not provided,
   * the docker image's ENTRYPOINT is used. It cannot be set if custom container
   * image is not provided. Note that this field and [TrainingInput.args] are
   * mutually exclusive, i.e., both cannot be set at the same time.
   *
   * @var string[]
   */
  public $containerCommand;
  protected $diskConfigType = GoogleCloudMlV1DiskConfig::class;
  protected $diskConfigDataType = '';
  /**
   * The Docker image to run on the replica. This image must be in Container
   * Registry. Learn more about [configuring custom containers](/ai-
   * platform/training/docs/distributed-training-containers).
   *
   * @var string
   */
  public $imageUri;
  /**
   * The AI Platform runtime version that includes a TensorFlow version matching
   * the one used in the custom container. This field is required if the replica
   * is a TPU worker that uses a custom container. Otherwise, do not specify
   * this field. This must be a [runtime version that currently supports
   * training with TPUs](/ml-engine/docs/tensorflow/runtime-version-list#tpu-
   * support). Note that the version of TensorFlow included in a runtime version
   * may differ from the numbering of the runtime version itself, because it may
   * have a different [patch version](https://www.tensorflow.org/guide/version_c
   * ompat#semantic_versioning_20). In this field, you must specify the runtime
   * version (TensorFlow minor version). For example, if your custom container
   * runs TensorFlow `1.x.y`, specify `1.x`.
   *
   * @var string
   */
  public $tpuTfVersion;

  /**
   * Represents the type and number of accelerators used by the replica. [Learn
   * about restrictions on accelerator configurations for training.](/ai-
   * platform/training/docs/using-gpus#compute-engine-machine-types-with-gpu)
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
   * Arguments to the entrypoint command. The following rules apply for
   * container_command and container_args: - If you do not supply command or
   * args: The defaults defined in the Docker image are used. - If you supply a
   * command but no args: The default EntryPoint and the default Cmd defined in
   * the Docker image are ignored. Your command is run without any arguments. -
   * If you supply only args: The default Entrypoint defined in the Docker image
   * is run with the args that you supplied. - If you supply a command and args:
   * The default Entrypoint and the default Cmd defined in the Docker image are
   * ignored. Your command is run with your args. It cannot be set if custom
   * container image is not provided. Note that this field and
   * [TrainingInput.args] are mutually exclusive, i.e., both cannot be set at
   * the same time.
   *
   * @param string[] $containerArgs
   */
  public function setContainerArgs($containerArgs)
  {
    $this->containerArgs = $containerArgs;
  }
  /**
   * @return string[]
   */
  public function getContainerArgs()
  {
    return $this->containerArgs;
  }
  /**
   * The command with which the replica's custom container is run. If provided,
   * it will override default ENTRYPOINT of the docker image. If not provided,
   * the docker image's ENTRYPOINT is used. It cannot be set if custom container
   * image is not provided. Note that this field and [TrainingInput.args] are
   * mutually exclusive, i.e., both cannot be set at the same time.
   *
   * @param string[] $containerCommand
   */
  public function setContainerCommand($containerCommand)
  {
    $this->containerCommand = $containerCommand;
  }
  /**
   * @return string[]
   */
  public function getContainerCommand()
  {
    return $this->containerCommand;
  }
  /**
   * Represents the configuration of disk options.
   *
   * @param GoogleCloudMlV1DiskConfig $diskConfig
   */
  public function setDiskConfig(GoogleCloudMlV1DiskConfig $diskConfig)
  {
    $this->diskConfig = $diskConfig;
  }
  /**
   * @return GoogleCloudMlV1DiskConfig
   */
  public function getDiskConfig()
  {
    return $this->diskConfig;
  }
  /**
   * The Docker image to run on the replica. This image must be in Container
   * Registry. Learn more about [configuring custom containers](/ai-
   * platform/training/docs/distributed-training-containers).
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * The AI Platform runtime version that includes a TensorFlow version matching
   * the one used in the custom container. This field is required if the replica
   * is a TPU worker that uses a custom container. Otherwise, do not specify
   * this field. This must be a [runtime version that currently supports
   * training with TPUs](/ml-engine/docs/tensorflow/runtime-version-list#tpu-
   * support). Note that the version of TensorFlow included in a runtime version
   * may differ from the numbering of the runtime version itself, because it may
   * have a different [patch version](https://www.tensorflow.org/guide/version_c
   * ompat#semantic_versioning_20). In this field, you must specify the runtime
   * version (TensorFlow minor version). For example, if your custom container
   * runs TensorFlow `1.x.y`, specify `1.x`.
   *
   * @param string $tpuTfVersion
   */
  public function setTpuTfVersion($tpuTfVersion)
  {
    $this->tpuTfVersion = $tpuTfVersion;
  }
  /**
   * @return string
   */
  public function getTpuTfVersion()
  {
    return $this->tpuTfVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1ReplicaConfig::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1ReplicaConfig');
