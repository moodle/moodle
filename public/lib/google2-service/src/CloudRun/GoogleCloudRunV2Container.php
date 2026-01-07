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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2Container extends \Google\Collection
{
  protected $collection_key = 'volumeMounts';
  /**
   * Arguments to the entrypoint. The docker image's CMD is used if this is not
   * provided.
   *
   * @var string[]
   */
  public $args;
  /**
   * Base image for this container. Only supported for services. If set, it
   * indicates that the service is enrolled into automatic base image update.
   *
   * @var string
   */
  public $baseImageUri;
  protected $buildInfoType = GoogleCloudRunV2BuildInfo::class;
  protected $buildInfoDataType = '';
  /**
   * Entrypoint array. Not executed within a shell. The docker image's
   * ENTRYPOINT is used if this is not provided.
   *
   * @var string[]
   */
  public $command;
  /**
   * Names of the containers that must start before this container.
   *
   * @var string[]
   */
  public $dependsOn;
  protected $envType = GoogleCloudRunV2EnvVar::class;
  protected $envDataType = 'array';
  /**
   * Required. Name of the container image in Dockerhub, Google Artifact
   * Registry, or Google Container Registry. If the host is not provided,
   * Dockerhub is assumed.
   *
   * @var string
   */
  public $image;
  protected $livenessProbeType = GoogleCloudRunV2Probe::class;
  protected $livenessProbeDataType = '';
  /**
   * Name of the container specified as a DNS_LABEL (RFC 1123).
   *
   * @var string
   */
  public $name;
  protected $portsType = GoogleCloudRunV2ContainerPort::class;
  protected $portsDataType = 'array';
  protected $resourcesType = GoogleCloudRunV2ResourceRequirements::class;
  protected $resourcesDataType = '';
  protected $sourceCodeType = GoogleCloudRunV2SourceCode::class;
  protected $sourceCodeDataType = '';
  protected $startupProbeType = GoogleCloudRunV2Probe::class;
  protected $startupProbeDataType = '';
  protected $volumeMountsType = GoogleCloudRunV2VolumeMount::class;
  protected $volumeMountsDataType = 'array';
  /**
   * Container's working directory. If not specified, the container runtime's
   * default will be used, which might be configured in the container image.
   *
   * @var string
   */
  public $workingDir;

  /**
   * Arguments to the entrypoint. The docker image's CMD is used if this is not
   * provided.
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
   * Base image for this container. Only supported for services. If set, it
   * indicates that the service is enrolled into automatic base image update.
   *
   * @param string $baseImageUri
   */
  public function setBaseImageUri($baseImageUri)
  {
    $this->baseImageUri = $baseImageUri;
  }
  /**
   * @return string
   */
  public function getBaseImageUri()
  {
    return $this->baseImageUri;
  }
  /**
   * Output only. The build info of the container image.
   *
   * @param GoogleCloudRunV2BuildInfo $buildInfo
   */
  public function setBuildInfo(GoogleCloudRunV2BuildInfo $buildInfo)
  {
    $this->buildInfo = $buildInfo;
  }
  /**
   * @return GoogleCloudRunV2BuildInfo
   */
  public function getBuildInfo()
  {
    return $this->buildInfo;
  }
  /**
   * Entrypoint array. Not executed within a shell. The docker image's
   * ENTRYPOINT is used if this is not provided.
   *
   * @param string[] $command
   */
  public function setCommand($command)
  {
    $this->command = $command;
  }
  /**
   * @return string[]
   */
  public function getCommand()
  {
    return $this->command;
  }
  /**
   * Names of the containers that must start before this container.
   *
   * @param string[] $dependsOn
   */
  public function setDependsOn($dependsOn)
  {
    $this->dependsOn = $dependsOn;
  }
  /**
   * @return string[]
   */
  public function getDependsOn()
  {
    return $this->dependsOn;
  }
  /**
   * List of environment variables to set in the container.
   *
   * @param GoogleCloudRunV2EnvVar[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return GoogleCloudRunV2EnvVar[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Required. Name of the container image in Dockerhub, Google Artifact
   * Registry, or Google Container Registry. If the host is not provided,
   * Dockerhub is assumed.
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Periodic probe of container liveness. Container will be restarted if the
   * probe fails.
   *
   * @param GoogleCloudRunV2Probe $livenessProbe
   */
  public function setLivenessProbe(GoogleCloudRunV2Probe $livenessProbe)
  {
    $this->livenessProbe = $livenessProbe;
  }
  /**
   * @return GoogleCloudRunV2Probe
   */
  public function getLivenessProbe()
  {
    return $this->livenessProbe;
  }
  /**
   * Name of the container specified as a DNS_LABEL (RFC 1123).
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
   * List of ports to expose from the container. Only a single port can be
   * specified. The specified ports must be listening on all interfaces
   * (0.0.0.0) within the container to be accessible. If omitted, a port number
   * will be chosen and passed to the container through the PORT environment
   * variable for the container to listen on.
   *
   * @param GoogleCloudRunV2ContainerPort[] $ports
   */
  public function setPorts($ports)
  {
    $this->ports = $ports;
  }
  /**
   * @return GoogleCloudRunV2ContainerPort[]
   */
  public function getPorts()
  {
    return $this->ports;
  }
  /**
   * Compute Resource requirements by this container.
   *
   * @param GoogleCloudRunV2ResourceRequirements $resources
   */
  public function setResources(GoogleCloudRunV2ResourceRequirements $resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return GoogleCloudRunV2ResourceRequirements
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Optional. Location of the source.
   *
   * @param GoogleCloudRunV2SourceCode $sourceCode
   */
  public function setSourceCode(GoogleCloudRunV2SourceCode $sourceCode)
  {
    $this->sourceCode = $sourceCode;
  }
  /**
   * @return GoogleCloudRunV2SourceCode
   */
  public function getSourceCode()
  {
    return $this->sourceCode;
  }
  /**
   * Startup probe of application within the container. All other probes are
   * disabled if a startup probe is provided, until it succeeds. Container will
   * not be added to service endpoints if the probe fails.
   *
   * @param GoogleCloudRunV2Probe $startupProbe
   */
  public function setStartupProbe(GoogleCloudRunV2Probe $startupProbe)
  {
    $this->startupProbe = $startupProbe;
  }
  /**
   * @return GoogleCloudRunV2Probe
   */
  public function getStartupProbe()
  {
    return $this->startupProbe;
  }
  /**
   * Volume to mount into the container's filesystem.
   *
   * @param GoogleCloudRunV2VolumeMount[] $volumeMounts
   */
  public function setVolumeMounts($volumeMounts)
  {
    $this->volumeMounts = $volumeMounts;
  }
  /**
   * @return GoogleCloudRunV2VolumeMount[]
   */
  public function getVolumeMounts()
  {
    return $this->volumeMounts;
  }
  /**
   * Container's working directory. If not specified, the container runtime's
   * default will be used, which might be configured in the container image.
   *
   * @param string $workingDir
   */
  public function setWorkingDir($workingDir)
  {
    $this->workingDir = $workingDir;
  }
  /**
   * @return string
   */
  public function getWorkingDir()
  {
    return $this->workingDir;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2Container::class, 'Google_Service_CloudRun_GoogleCloudRunV2Container');
