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

namespace Google\Service\ContainerAnalysis;

class BuildStep extends \Google\Collection
{
  /**
   * Status of the build is unknown.
   */
  public const STATUS_STATUS_UNKNOWN = 'STATUS_UNKNOWN';
  /**
   * Build has been created and is pending execution and queuing. It has not
   * been queued.
   */
  public const STATUS_PENDING = 'PENDING';
  /**
   * Build has been received and is being queued.
   */
  public const STATUS_QUEUING = 'QUEUING';
  /**
   * Build or step is queued; work has not yet begun.
   */
  public const STATUS_QUEUED = 'QUEUED';
  /**
   * Build or step is being executed.
   */
  public const STATUS_WORKING = 'WORKING';
  /**
   * Build or step finished successfully.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * Build or step failed to complete successfully.
   */
  public const STATUS_FAILURE = 'FAILURE';
  /**
   * Build or step failed due to an internal cause.
   */
  public const STATUS_INTERNAL_ERROR = 'INTERNAL_ERROR';
  /**
   * Build or step took longer than was allowed.
   */
  public const STATUS_TIMEOUT = 'TIMEOUT';
  /**
   * Build or step was canceled by a user.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  /**
   * Build was enqueued for longer than the value of `queue_ttl`.
   */
  public const STATUS_EXPIRED = 'EXPIRED';
  protected $collection_key = 'waitFor';
  /**
   * Allow this build step to fail without failing the entire build if and only
   * if the exit code is one of the specified codes. If allow_failure is also
   * specified, this field will take precedence.
   *
   * @var int[]
   */
  public $allowExitCodes;
  /**
   * Allow this build step to fail without failing the entire build. If false,
   * the entire build will fail if this step fails. Otherwise, the build will
   * succeed, but this step will still have a failure status. Error information
   * will be reported in the failure_detail field.
   *
   * @var bool
   */
  public $allowFailure;
  /**
   * A list of arguments that will be presented to the step when it is started.
   * If the image used to run the step's container has an entrypoint, the `args`
   * are used as arguments to that entrypoint. If the image does not define an
   * entrypoint, the first element in args is used as the entrypoint, and the
   * remainder will be used as arguments.
   *
   * @var string[]
   */
  public $args;
  /**
   * Option to include built-in and custom substitutions as env variables for
   * this build step. This option will override the global option in
   * BuildOption.
   *
   * @var bool
   */
  public $automapSubstitutions;
  /**
   * Working directory to use when running this step's container. If this value
   * is a relative path, it is relative to the build's working directory. If
   * this value is absolute, it may be outside the build's working directory, in
   * which case the contents of the path may not be persisted across build step
   * executions, unless a `volume` for that path is specified. If the build
   * specifies a `RepoSource` with `dir` and a step with a `dir`, which
   * specifies an absolute path, the `RepoSource` `dir` is ignored for the
   * step's execution.
   *
   * @var string
   */
  public $dir;
  /**
   * Entrypoint to be used instead of the build step image's default entrypoint.
   * If unset, the image's default entrypoint is used.
   *
   * @var string
   */
  public $entrypoint;
  /**
   * A list of environment variable definitions to be used when running a step.
   * The elements are of the form "KEY=VALUE" for the environment variable "KEY"
   * being given the value "VALUE".
   *
   * @var string[]
   */
  public $env;
  /**
   * Output only. Return code from running the step.
   *
   * @var int
   */
  public $exitCode;
  /**
   * Unique identifier for this build step, used in `wait_for` to reference this
   * build step as a dependency.
   *
   * @var string
   */
  public $id;
  /**
   * Required. The name of the container image that will run this particular
   * build step. If the image is available in the host's Docker daemon's cache,
   * it will be run directly. If not, the host will attempt to pull the image
   * first, using the builder service account's credentials if necessary. The
   * Docker daemon's cache will already have the latest versions of all of the
   * officially supported build steps
   * ([https://github.com/GoogleCloudPlatform/cloud-
   * builders](https://github.com/GoogleCloudPlatform/cloud-builders)). The
   * Docker daemon will also have cached many of the layers for some popular
   * images, like "ubuntu", "debian", but they will be refreshed at the time you
   * attempt to use them. If you built an image in a previous build step, it
   * will be stored in the host's Docker daemon's cache and is available to use
   * as the name for a later build step.
   *
   * @var string
   */
  public $name;
  protected $pullTimingType = TimeSpan::class;
  protected $pullTimingDataType = '';
  /**
   * Remote configuration for the build step.
   *
   * @var string
   */
  public $remoteConfig;
  protected $resultsType = StepResult::class;
  protected $resultsDataType = 'array';
  /**
   * A shell script to be executed in the step. When script is provided, the
   * user cannot specify the entrypoint or args.
   *
   * @var string
   */
  public $script;
  /**
   * A list of environment variables which are encrypted using a Cloud Key
   * Management Service crypto key. These values must be specified in the
   * build's `Secret`.
   *
   * @var string[]
   */
  public $secretEnv;
  /**
   * Output only. Status of the build step. At this time, build step status is
   * only updated on build completion; step status is not updated in real-time
   * as the build progresses.
   *
   * @var string
   */
  public $status;
  /**
   * Time limit for executing this build step. If not defined, the step has no
   * time limit and will be allowed to continue to run until either it completes
   * or the build itself times out.
   *
   * @var string
   */
  public $timeout;
  protected $timingType = TimeSpan::class;
  protected $timingDataType = '';
  protected $volumesType = Volume::class;
  protected $volumesDataType = 'array';
  /**
   * The ID(s) of the step(s) that this build step depends on. This build step
   * will not start until all the build steps in `wait_for` have completed
   * successfully. If `wait_for` is empty, this build step will start when all
   * previous build steps in the `Build.Steps` list have completed successfully.
   *
   * @var string[]
   */
  public $waitFor;

  /**
   * Allow this build step to fail without failing the entire build if and only
   * if the exit code is one of the specified codes. If allow_failure is also
   * specified, this field will take precedence.
   *
   * @param int[] $allowExitCodes
   */
  public function setAllowExitCodes($allowExitCodes)
  {
    $this->allowExitCodes = $allowExitCodes;
  }
  /**
   * @return int[]
   */
  public function getAllowExitCodes()
  {
    return $this->allowExitCodes;
  }
  /**
   * Allow this build step to fail without failing the entire build. If false,
   * the entire build will fail if this step fails. Otherwise, the build will
   * succeed, but this step will still have a failure status. Error information
   * will be reported in the failure_detail field.
   *
   * @param bool $allowFailure
   */
  public function setAllowFailure($allowFailure)
  {
    $this->allowFailure = $allowFailure;
  }
  /**
   * @return bool
   */
  public function getAllowFailure()
  {
    return $this->allowFailure;
  }
  /**
   * A list of arguments that will be presented to the step when it is started.
   * If the image used to run the step's container has an entrypoint, the `args`
   * are used as arguments to that entrypoint. If the image does not define an
   * entrypoint, the first element in args is used as the entrypoint, and the
   * remainder will be used as arguments.
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
   * Option to include built-in and custom substitutions as env variables for
   * this build step. This option will override the global option in
   * BuildOption.
   *
   * @param bool $automapSubstitutions
   */
  public function setAutomapSubstitutions($automapSubstitutions)
  {
    $this->automapSubstitutions = $automapSubstitutions;
  }
  /**
   * @return bool
   */
  public function getAutomapSubstitutions()
  {
    return $this->automapSubstitutions;
  }
  /**
   * Working directory to use when running this step's container. If this value
   * is a relative path, it is relative to the build's working directory. If
   * this value is absolute, it may be outside the build's working directory, in
   * which case the contents of the path may not be persisted across build step
   * executions, unless a `volume` for that path is specified. If the build
   * specifies a `RepoSource` with `dir` and a step with a `dir`, which
   * specifies an absolute path, the `RepoSource` `dir` is ignored for the
   * step's execution.
   *
   * @param string $dir
   */
  public function setDir($dir)
  {
    $this->dir = $dir;
  }
  /**
   * @return string
   */
  public function getDir()
  {
    return $this->dir;
  }
  /**
   * Entrypoint to be used instead of the build step image's default entrypoint.
   * If unset, the image's default entrypoint is used.
   *
   * @param string $entrypoint
   */
  public function setEntrypoint($entrypoint)
  {
    $this->entrypoint = $entrypoint;
  }
  /**
   * @return string
   */
  public function getEntrypoint()
  {
    return $this->entrypoint;
  }
  /**
   * A list of environment variable definitions to be used when running a step.
   * The elements are of the form "KEY=VALUE" for the environment variable "KEY"
   * being given the value "VALUE".
   *
   * @param string[] $env
   */
  public function setEnv($env)
  {
    $this->env = $env;
  }
  /**
   * @return string[]
   */
  public function getEnv()
  {
    return $this->env;
  }
  /**
   * Output only. Return code from running the step.
   *
   * @param int $exitCode
   */
  public function setExitCode($exitCode)
  {
    $this->exitCode = $exitCode;
  }
  /**
   * @return int
   */
  public function getExitCode()
  {
    return $this->exitCode;
  }
  /**
   * Unique identifier for this build step, used in `wait_for` to reference this
   * build step as a dependency.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The name of the container image that will run this particular
   * build step. If the image is available in the host's Docker daemon's cache,
   * it will be run directly. If not, the host will attempt to pull the image
   * first, using the builder service account's credentials if necessary. The
   * Docker daemon's cache will already have the latest versions of all of the
   * officially supported build steps
   * ([https://github.com/GoogleCloudPlatform/cloud-
   * builders](https://github.com/GoogleCloudPlatform/cloud-builders)). The
   * Docker daemon will also have cached many of the layers for some popular
   * images, like "ubuntu", "debian", but they will be refreshed at the time you
   * attempt to use them. If you built an image in a previous build step, it
   * will be stored in the host's Docker daemon's cache and is available to use
   * as the name for a later build step.
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
   * Output only. Stores timing information for pulling this build step's
   * builder image only.
   *
   * @param TimeSpan $pullTiming
   */
  public function setPullTiming(TimeSpan $pullTiming)
  {
    $this->pullTiming = $pullTiming;
  }
  /**
   * @return TimeSpan
   */
  public function getPullTiming()
  {
    return $this->pullTiming;
  }
  /**
   * Remote configuration for the build step.
   *
   * @param string $remoteConfig
   */
  public function setRemoteConfig($remoteConfig)
  {
    $this->remoteConfig = $remoteConfig;
  }
  /**
   * @return string
   */
  public function getRemoteConfig()
  {
    return $this->remoteConfig;
  }
  /**
   * @param StepResult[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return StepResult[]
   */
  public function getResults()
  {
    return $this->results;
  }
  /**
   * A shell script to be executed in the step. When script is provided, the
   * user cannot specify the entrypoint or args.
   *
   * @param string $script
   */
  public function setScript($script)
  {
    $this->script = $script;
  }
  /**
   * @return string
   */
  public function getScript()
  {
    return $this->script;
  }
  /**
   * A list of environment variables which are encrypted using a Cloud Key
   * Management Service crypto key. These values must be specified in the
   * build's `Secret`.
   *
   * @param string[] $secretEnv
   */
  public function setSecretEnv($secretEnv)
  {
    $this->secretEnv = $secretEnv;
  }
  /**
   * @return string[]
   */
  public function getSecretEnv()
  {
    return $this->secretEnv;
  }
  /**
   * Output only. Status of the build step. At this time, build step status is
   * only updated on build completion; step status is not updated in real-time
   * as the build progresses.
   *
   * Accepted values: STATUS_UNKNOWN, PENDING, QUEUING, QUEUED, WORKING,
   * SUCCESS, FAILURE, INTERNAL_ERROR, TIMEOUT, CANCELLED, EXPIRED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Time limit for executing this build step. If not defined, the step has no
   * time limit and will be allowed to continue to run until either it completes
   * or the build itself times out.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Output only. Stores timing information for executing this build step.
   *
   * @param TimeSpan $timing
   */
  public function setTiming(TimeSpan $timing)
  {
    $this->timing = $timing;
  }
  /**
   * @return TimeSpan
   */
  public function getTiming()
  {
    return $this->timing;
  }
  /**
   * List of volumes to mount into the build step. Each volume is created as an
   * empty volume prior to execution of the build step. Upon completion of the
   * build, volumes and their contents are discarded. Using a named volume in
   * only one step is not valid as it is indicative of a build request with an
   * incorrect configuration.
   *
   * @param Volume[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return Volume[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
  /**
   * The ID(s) of the step(s) that this build step depends on. This build step
   * will not start until all the build steps in `wait_for` have completed
   * successfully. If `wait_for` is empty, this build step will start when all
   * previous build steps in the `Build.Steps` list have completed successfully.
   *
   * @param string[] $waitFor
   */
  public function setWaitFor($waitFor)
  {
    $this->waitFor = $waitFor;
  }
  /**
   * @return string[]
   */
  public function getWaitFor()
  {
    return $this->waitFor;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildStep::class, 'Google_Service_ContainerAnalysis_BuildStep');
