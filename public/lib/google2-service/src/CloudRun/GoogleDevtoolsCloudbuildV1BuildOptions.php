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

class GoogleDevtoolsCloudbuildV1BuildOptions extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const DEFAULT_LOGS_BUCKET_BEHAVIOR_DEFAULT_LOGS_BUCKET_BEHAVIOR_UNSPECIFIED = 'DEFAULT_LOGS_BUCKET_BEHAVIOR_UNSPECIFIED';
  /**
   * Bucket is located in user-owned project in the same region as the build.
   * The builder service account must have access to create and write to Cloud
   * Storage buckets in the build project.
   */
  public const DEFAULT_LOGS_BUCKET_BEHAVIOR_REGIONAL_USER_OWNED_BUCKET = 'REGIONAL_USER_OWNED_BUCKET';
  /**
   * Bucket is located in a Google-owned project and is not regionalized.
   */
  public const DEFAULT_LOGS_BUCKET_BEHAVIOR_LEGACY_BUCKET = 'LEGACY_BUCKET';
  /**
   * Service may automatically determine build log streaming behavior.
   */
  public const LOG_STREAMING_OPTION_STREAM_DEFAULT = 'STREAM_DEFAULT';
  /**
   * Build logs should be streamed to Cloud Storage.
   */
  public const LOG_STREAMING_OPTION_STREAM_ON = 'STREAM_ON';
  /**
   * Build logs should not be streamed to Cloud Storage; they will be written
   * when the build is completed.
   */
  public const LOG_STREAMING_OPTION_STREAM_OFF = 'STREAM_OFF';
  /**
   * The service determines the logging mode. The default is `LEGACY`. Do not
   * rely on the default logging behavior as it may change in the future.
   */
  public const LOGGING_LOGGING_UNSPECIFIED = 'LOGGING_UNSPECIFIED';
  /**
   * Build logs are stored in Cloud Logging and Cloud Storage.
   */
  public const LOGGING_LEGACY = 'LEGACY';
  /**
   * Build logs are stored in Cloud Storage.
   */
  public const LOGGING_GCS_ONLY = 'GCS_ONLY';
  /**
   * This option is the same as CLOUD_LOGGING_ONLY.
   *
   * @deprecated
   */
  public const LOGGING_STACKDRIVER_ONLY = 'STACKDRIVER_ONLY';
  /**
   * Build logs are stored in Cloud Logging. Selecting this option will not
   * allow [logs
   * streaming](https://cloud.google.com/sdk/gcloud/reference/builds/log).
   */
  public const LOGGING_CLOUD_LOGGING_ONLY = 'CLOUD_LOGGING_ONLY';
  /**
   * Turn off all logging. No build logs will be captured.
   */
  public const LOGGING_NONE = 'NONE';
  /**
   * Standard machine type.
   */
  public const MACHINE_TYPE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Highcpu machine with 8 CPUs.
   *
   * @deprecated
   */
  public const MACHINE_TYPE_N1_HIGHCPU_8 = 'N1_HIGHCPU_8';
  /**
   * Highcpu machine with 32 CPUs.
   *
   * @deprecated
   */
  public const MACHINE_TYPE_N1_HIGHCPU_32 = 'N1_HIGHCPU_32';
  /**
   * Highcpu e2 machine with 8 CPUs.
   */
  public const MACHINE_TYPE_E2_HIGHCPU_8 = 'E2_HIGHCPU_8';
  /**
   * Highcpu e2 machine with 32 CPUs.
   */
  public const MACHINE_TYPE_E2_HIGHCPU_32 = 'E2_HIGHCPU_32';
  /**
   * E2 machine with 1 CPU.
   */
  public const MACHINE_TYPE_E2_MEDIUM = 'E2_MEDIUM';
  /**
   * Not a verifiable build (the default).
   */
  public const REQUESTED_VERIFY_OPTION_NOT_VERIFIED = 'NOT_VERIFIED';
  /**
   * Build must be verified.
   */
  public const REQUESTED_VERIFY_OPTION_VERIFIED = 'VERIFIED';
  /**
   * Fails the build if error in substitutions checks, like missing a
   * substitution in the template or in the map.
   */
  public const SUBSTITUTION_OPTION_MUST_MATCH = 'MUST_MATCH';
  /**
   * Do not fail the build if error in substitutions checks.
   */
  public const SUBSTITUTION_OPTION_ALLOW_LOOSE = 'ALLOW_LOOSE';
  protected $collection_key = 'volumes';
  /**
   * Option to include built-in and custom substitutions as env variables for
   * all build steps.
   *
   * @var bool
   */
  public $automapSubstitutions;
  /**
   * Optional. Option to specify how default logs buckets are setup.
   *
   * @var string
   */
  public $defaultLogsBucketBehavior;
  /**
   * Requested disk size for the VM that runs the build. Note that this is *NOT*
   * "disk free"; some of the space will be used by the operating system and
   * build utilities. Also note that this is the minimum disk size that will be
   * allocated for the build -- the build may run with a larger disk than
   * requested. At present, the maximum disk size is 4000GB; builds that request
   * more than the maximum are rejected with an error.
   *
   * @var string
   */
  public $diskSizeGb;
  /**
   * Option to specify whether or not to apply bash style string operations to
   * the substitutions. NOTE: this is always enabled for triggered builds and
   * cannot be overridden in the build configuration file.
   *
   * @var bool
   */
  public $dynamicSubstitutions;
  /**
   * Optional. Option to specify whether structured logging is enabled. If true,
   * JSON-formatted logs are parsed as structured logs.
   *
   * @var bool
   */
  public $enableStructuredLogging;
  /**
   * A list of global environment variable definitions that will exist for all
   * build steps in this build. If a variable is defined in both globally and in
   * a build step, the variable will use the build step value. The elements are
   * of the form "KEY=VALUE" for the environment variable "KEY" being given the
   * value "VALUE".
   *
   * @var string[]
   */
  public $env;
  /**
   * Option to define build log streaming behavior to Cloud Storage.
   *
   * @var string
   */
  public $logStreamingOption;
  /**
   * Option to specify the logging mode, which determines if and where build
   * logs are stored.
   *
   * @var string
   */
  public $logging;
  /**
   * Compute Engine machine type on which to run the build.
   *
   * @var string
   */
  public $machineType;
  protected $poolType = GoogleDevtoolsCloudbuildV1PoolOption::class;
  protected $poolDataType = '';
  /**
   * Optional. Option to specify the Pub/Sub topic to receive build status
   * updates.
   *
   * @var string
   */
  public $pubsubTopic;
  /**
   * Requested verifiability options.
   *
   * @var string
   */
  public $requestedVerifyOption;
  /**
   * A list of global environment variables, which are encrypted using a Cloud
   * Key Management Service crypto key. These values must be specified in the
   * build's `Secret`. These variables will be available to all build steps in
   * this build.
   *
   * @var string[]
   */
  public $secretEnv;
  /**
   * Requested hash for SourceProvenance.
   *
   * @var string[]
   */
  public $sourceProvenanceHash;
  /**
   * Option to specify behavior when there is an error in the substitution
   * checks. NOTE: this is always set to ALLOW_LOOSE for triggered builds and
   * cannot be overridden in the build configuration file.
   *
   * @var string
   */
  public $substitutionOption;
  protected $volumesType = GoogleDevtoolsCloudbuildV1Volume::class;
  protected $volumesDataType = 'array';
  /**
   * This field deprecated; please use `pool.name` instead.
   *
   * @deprecated
   * @var string
   */
  public $workerPool;

  /**
   * Option to include built-in and custom substitutions as env variables for
   * all build steps.
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
   * Optional. Option to specify how default logs buckets are setup.
   *
   * Accepted values: DEFAULT_LOGS_BUCKET_BEHAVIOR_UNSPECIFIED,
   * REGIONAL_USER_OWNED_BUCKET, LEGACY_BUCKET
   *
   * @param self::DEFAULT_LOGS_BUCKET_BEHAVIOR_* $defaultLogsBucketBehavior
   */
  public function setDefaultLogsBucketBehavior($defaultLogsBucketBehavior)
  {
    $this->defaultLogsBucketBehavior = $defaultLogsBucketBehavior;
  }
  /**
   * @return self::DEFAULT_LOGS_BUCKET_BEHAVIOR_*
   */
  public function getDefaultLogsBucketBehavior()
  {
    return $this->defaultLogsBucketBehavior;
  }
  /**
   * Requested disk size for the VM that runs the build. Note that this is *NOT*
   * "disk free"; some of the space will be used by the operating system and
   * build utilities. Also note that this is the minimum disk size that will be
   * allocated for the build -- the build may run with a larger disk than
   * requested. At present, the maximum disk size is 4000GB; builds that request
   * more than the maximum are rejected with an error.
   *
   * @param string $diskSizeGb
   */
  public function setDiskSizeGb($diskSizeGb)
  {
    $this->diskSizeGb = $diskSizeGb;
  }
  /**
   * @return string
   */
  public function getDiskSizeGb()
  {
    return $this->diskSizeGb;
  }
  /**
   * Option to specify whether or not to apply bash style string operations to
   * the substitutions. NOTE: this is always enabled for triggered builds and
   * cannot be overridden in the build configuration file.
   *
   * @param bool $dynamicSubstitutions
   */
  public function setDynamicSubstitutions($dynamicSubstitutions)
  {
    $this->dynamicSubstitutions = $dynamicSubstitutions;
  }
  /**
   * @return bool
   */
  public function getDynamicSubstitutions()
  {
    return $this->dynamicSubstitutions;
  }
  /**
   * Optional. Option to specify whether structured logging is enabled. If true,
   * JSON-formatted logs are parsed as structured logs.
   *
   * @param bool $enableStructuredLogging
   */
  public function setEnableStructuredLogging($enableStructuredLogging)
  {
    $this->enableStructuredLogging = $enableStructuredLogging;
  }
  /**
   * @return bool
   */
  public function getEnableStructuredLogging()
  {
    return $this->enableStructuredLogging;
  }
  /**
   * A list of global environment variable definitions that will exist for all
   * build steps in this build. If a variable is defined in both globally and in
   * a build step, the variable will use the build step value. The elements are
   * of the form "KEY=VALUE" for the environment variable "KEY" being given the
   * value "VALUE".
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
   * Option to define build log streaming behavior to Cloud Storage.
   *
   * Accepted values: STREAM_DEFAULT, STREAM_ON, STREAM_OFF
   *
   * @param self::LOG_STREAMING_OPTION_* $logStreamingOption
   */
  public function setLogStreamingOption($logStreamingOption)
  {
    $this->logStreamingOption = $logStreamingOption;
  }
  /**
   * @return self::LOG_STREAMING_OPTION_*
   */
  public function getLogStreamingOption()
  {
    return $this->logStreamingOption;
  }
  /**
   * Option to specify the logging mode, which determines if and where build
   * logs are stored.
   *
   * Accepted values: LOGGING_UNSPECIFIED, LEGACY, GCS_ONLY, STACKDRIVER_ONLY,
   * CLOUD_LOGGING_ONLY, NONE
   *
   * @param self::LOGGING_* $logging
   */
  public function setLogging($logging)
  {
    $this->logging = $logging;
  }
  /**
   * @return self::LOGGING_*
   */
  public function getLogging()
  {
    return $this->logging;
  }
  /**
   * Compute Engine machine type on which to run the build.
   *
   * Accepted values: UNSPECIFIED, N1_HIGHCPU_8, N1_HIGHCPU_32, E2_HIGHCPU_8,
   * E2_HIGHCPU_32, E2_MEDIUM
   *
   * @param self::MACHINE_TYPE_* $machineType
   */
  public function setMachineType($machineType)
  {
    $this->machineType = $machineType;
  }
  /**
   * @return self::MACHINE_TYPE_*
   */
  public function getMachineType()
  {
    return $this->machineType;
  }
  /**
   * Optional. Specification for execution on a `WorkerPool`. See [running
   * builds in a private pool](https://cloud.google.com/build/docs/private-
   * pools/run-builds-in-private-pool) for more information.
   *
   * @param GoogleDevtoolsCloudbuildV1PoolOption $pool
   */
  public function setPool(GoogleDevtoolsCloudbuildV1PoolOption $pool)
  {
    $this->pool = $pool;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1PoolOption
   */
  public function getPool()
  {
    return $this->pool;
  }
  /**
   * Optional. Option to specify the Pub/Sub topic to receive build status
   * updates.
   *
   * @param string $pubsubTopic
   */
  public function setPubsubTopic($pubsubTopic)
  {
    $this->pubsubTopic = $pubsubTopic;
  }
  /**
   * @return string
   */
  public function getPubsubTopic()
  {
    return $this->pubsubTopic;
  }
  /**
   * Requested verifiability options.
   *
   * Accepted values: NOT_VERIFIED, VERIFIED
   *
   * @param self::REQUESTED_VERIFY_OPTION_* $requestedVerifyOption
   */
  public function setRequestedVerifyOption($requestedVerifyOption)
  {
    $this->requestedVerifyOption = $requestedVerifyOption;
  }
  /**
   * @return self::REQUESTED_VERIFY_OPTION_*
   */
  public function getRequestedVerifyOption()
  {
    return $this->requestedVerifyOption;
  }
  /**
   * A list of global environment variables, which are encrypted using a Cloud
   * Key Management Service crypto key. These values must be specified in the
   * build's `Secret`. These variables will be available to all build steps in
   * this build.
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
   * Requested hash for SourceProvenance.
   *
   * @param string[] $sourceProvenanceHash
   */
  public function setSourceProvenanceHash($sourceProvenanceHash)
  {
    $this->sourceProvenanceHash = $sourceProvenanceHash;
  }
  /**
   * @return string[]
   */
  public function getSourceProvenanceHash()
  {
    return $this->sourceProvenanceHash;
  }
  /**
   * Option to specify behavior when there is an error in the substitution
   * checks. NOTE: this is always set to ALLOW_LOOSE for triggered builds and
   * cannot be overridden in the build configuration file.
   *
   * Accepted values: MUST_MATCH, ALLOW_LOOSE
   *
   * @param self::SUBSTITUTION_OPTION_* $substitutionOption
   */
  public function setSubstitutionOption($substitutionOption)
  {
    $this->substitutionOption = $substitutionOption;
  }
  /**
   * @return self::SUBSTITUTION_OPTION_*
   */
  public function getSubstitutionOption()
  {
    return $this->substitutionOption;
  }
  /**
   * Global list of volumes to mount for ALL build steps Each volume is created
   * as an empty volume prior to starting the build process. Upon completion of
   * the build, volumes and their contents are discarded. Global volume names
   * and paths cannot conflict with the volumes defined a build step. Using a
   * global volume in a build with only one step is not valid as it is
   * indicative of a build request with an incorrect configuration.
   *
   * @param GoogleDevtoolsCloudbuildV1Volume[] $volumes
   */
  public function setVolumes($volumes)
  {
    $this->volumes = $volumes;
  }
  /**
   * @return GoogleDevtoolsCloudbuildV1Volume[]
   */
  public function getVolumes()
  {
    return $this->volumes;
  }
  /**
   * This field deprecated; please use `pool.name` instead.
   *
   * @deprecated
   * @param string $workerPool
   */
  public function setWorkerPool($workerPool)
  {
    $this->workerPool = $workerPool;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getWorkerPool()
  {
    return $this->workerPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsCloudbuildV1BuildOptions::class, 'Google_Service_CloudRun_GoogleDevtoolsCloudbuildV1BuildOptions');
