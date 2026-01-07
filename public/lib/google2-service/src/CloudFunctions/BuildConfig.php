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

namespace Google\Service\CloudFunctions;

class BuildConfig extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const DOCKER_REGISTRY_DOCKER_REGISTRY_UNSPECIFIED = 'DOCKER_REGISTRY_UNSPECIFIED';
  /**
   * Docker images will be stored in multi-regional Container Registry
   * repositories named `gcf`.
   */
  public const DOCKER_REGISTRY_CONTAINER_REGISTRY = 'CONTAINER_REGISTRY';
  /**
   * Docker images will be stored in regional Artifact Registry repositories. By
   * default, GCF will create and use repositories named `gcf-artifacts` in
   * every region in which a function is deployed. But the repository to use can
   * also be specified by the user using the `docker_repository` field.
   */
  public const DOCKER_REGISTRY_ARTIFACT_REGISTRY = 'ARTIFACT_REGISTRY';
  protected $automaticUpdatePolicyType = AutomaticUpdatePolicy::class;
  protected $automaticUpdatePolicyDataType = '';
  /**
   * Output only. The Cloud Build name of the latest successful deployment of
   * the function.
   *
   * @var string
   */
  public $build;
  /**
   * Docker Registry to use for this deployment. This configuration is only
   * applicable to 1st Gen functions, 2nd Gen functions can only use Artifact
   * Registry. Deprecated: as of March 2025, `CONTAINER_REGISTRY` option is no
   * longer available in response to Container Registry's deprecation:
   * https://cloud.google.com/artifact-registry/docs/transition/transition-from-
   * gcr Please use Artifact Registry instead, which is the default choice. If
   * unspecified, it defaults to `ARTIFACT_REGISTRY`. If `docker_repository`
   * field is specified, this field should either be left unspecified or set to
   * `ARTIFACT_REGISTRY`.
   *
   * @deprecated
   * @var string
   */
  public $dockerRegistry;
  /**
   * Repository in Artifact Registry to which the function docker image will be
   * pushed after it is built by Cloud Build. If specified by user, it is
   * created and managed by user with a customer managed encryption key.
   * Otherwise, GCF will create and use a repository named 'gcf-artifacts' for
   * every deployed region. It must match the pattern
   * `projects/{project}/locations/{location}/repositories/{repository}`.
   * Repository format must be 'DOCKER'.
   *
   * @var string
   */
  public $dockerRepository;
  /**
   * The name of the function (as defined in source code) that will be executed.
   * Defaults to the resource name suffix, if not specified. For backward
   * compatibility, if function with given name is not found, then the system
   * will try to use function named "function". For Node.js this is name of a
   * function exported by the module specified in `source_location`.
   *
   * @var string
   */
  public $entryPoint;
  /**
   * User-provided build-time environment variables for the function
   *
   * @var string[]
   */
  public $environmentVariables;
  protected $onDeployUpdatePolicyType = OnDeployUpdatePolicy::class;
  protected $onDeployUpdatePolicyDataType = '';
  /**
   * The runtime in which to run the function. Required when deploying a new
   * function, optional when updating an existing function. For a complete list
   * of possible choices, see the [`gcloud` command reference](https://cloud.goo
   * gle.com/sdk/gcloud/reference/functions/deploy#--runtime).
   *
   * @var string
   */
  public $runtime;
  /**
   * Service account to be used for building the container. The format of this
   * field is `projects/{projectId}/serviceAccounts/{serviceAccountEmail}`.
   *
   * @var string
   */
  public $serviceAccount;
  protected $sourceType = Source::class;
  protected $sourceDataType = '';
  protected $sourceProvenanceType = SourceProvenance::class;
  protected $sourceProvenanceDataType = '';
  /**
   * An identifier for Firebase function sources. Disclaimer: This field is only
   * supported for Firebase function deployments.
   *
   * @var string
   */
  public $sourceToken;
  /**
   * Name of the Cloud Build Custom Worker Pool that should be used to build the
   * function. The format of this field is
   * `projects/{project}/locations/{region}/workerPools/{workerPool}` where
   * {project} and {region} are the project id and region respectively where the
   * worker pool is defined and {workerPool} is the short name of the worker
   * pool. If the project id is not the same as the function, then the Cloud
   * Functions Service Agent (service-@gcf-admin-robot.iam.gserviceaccount.com)
   * must be granted the role Cloud Build Custom Workers Builder
   * (roles/cloudbuild.customworkers.builder) in the project.
   *
   * @var string
   */
  public $workerPool;

  /**
   * @param AutomaticUpdatePolicy $automaticUpdatePolicy
   */
  public function setAutomaticUpdatePolicy(AutomaticUpdatePolicy $automaticUpdatePolicy)
  {
    $this->automaticUpdatePolicy = $automaticUpdatePolicy;
  }
  /**
   * @return AutomaticUpdatePolicy
   */
  public function getAutomaticUpdatePolicy()
  {
    return $this->automaticUpdatePolicy;
  }
  /**
   * Output only. The Cloud Build name of the latest successful deployment of
   * the function.
   *
   * @param string $build
   */
  public function setBuild($build)
  {
    $this->build = $build;
  }
  /**
   * @return string
   */
  public function getBuild()
  {
    return $this->build;
  }
  /**
   * Docker Registry to use for this deployment. This configuration is only
   * applicable to 1st Gen functions, 2nd Gen functions can only use Artifact
   * Registry. Deprecated: as of March 2025, `CONTAINER_REGISTRY` option is no
   * longer available in response to Container Registry's deprecation:
   * https://cloud.google.com/artifact-registry/docs/transition/transition-from-
   * gcr Please use Artifact Registry instead, which is the default choice. If
   * unspecified, it defaults to `ARTIFACT_REGISTRY`. If `docker_repository`
   * field is specified, this field should either be left unspecified or set to
   * `ARTIFACT_REGISTRY`.
   *
   * Accepted values: DOCKER_REGISTRY_UNSPECIFIED, CONTAINER_REGISTRY,
   * ARTIFACT_REGISTRY
   *
   * @deprecated
   * @param self::DOCKER_REGISTRY_* $dockerRegistry
   */
  public function setDockerRegistry($dockerRegistry)
  {
    $this->dockerRegistry = $dockerRegistry;
  }
  /**
   * @deprecated
   * @return self::DOCKER_REGISTRY_*
   */
  public function getDockerRegistry()
  {
    return $this->dockerRegistry;
  }
  /**
   * Repository in Artifact Registry to which the function docker image will be
   * pushed after it is built by Cloud Build. If specified by user, it is
   * created and managed by user with a customer managed encryption key.
   * Otherwise, GCF will create and use a repository named 'gcf-artifacts' for
   * every deployed region. It must match the pattern
   * `projects/{project}/locations/{location}/repositories/{repository}`.
   * Repository format must be 'DOCKER'.
   *
   * @param string $dockerRepository
   */
  public function setDockerRepository($dockerRepository)
  {
    $this->dockerRepository = $dockerRepository;
  }
  /**
   * @return string
   */
  public function getDockerRepository()
  {
    return $this->dockerRepository;
  }
  /**
   * The name of the function (as defined in source code) that will be executed.
   * Defaults to the resource name suffix, if not specified. For backward
   * compatibility, if function with given name is not found, then the system
   * will try to use function named "function". For Node.js this is name of a
   * function exported by the module specified in `source_location`.
   *
   * @param string $entryPoint
   */
  public function setEntryPoint($entryPoint)
  {
    $this->entryPoint = $entryPoint;
  }
  /**
   * @return string
   */
  public function getEntryPoint()
  {
    return $this->entryPoint;
  }
  /**
   * User-provided build-time environment variables for the function
   *
   * @param string[] $environmentVariables
   */
  public function setEnvironmentVariables($environmentVariables)
  {
    $this->environmentVariables = $environmentVariables;
  }
  /**
   * @return string[]
   */
  public function getEnvironmentVariables()
  {
    return $this->environmentVariables;
  }
  /**
   * @param OnDeployUpdatePolicy $onDeployUpdatePolicy
   */
  public function setOnDeployUpdatePolicy(OnDeployUpdatePolicy $onDeployUpdatePolicy)
  {
    $this->onDeployUpdatePolicy = $onDeployUpdatePolicy;
  }
  /**
   * @return OnDeployUpdatePolicy
   */
  public function getOnDeployUpdatePolicy()
  {
    return $this->onDeployUpdatePolicy;
  }
  /**
   * The runtime in which to run the function. Required when deploying a new
   * function, optional when updating an existing function. For a complete list
   * of possible choices, see the [`gcloud` command reference](https://cloud.goo
   * gle.com/sdk/gcloud/reference/functions/deploy#--runtime).
   *
   * @param string $runtime
   */
  public function setRuntime($runtime)
  {
    $this->runtime = $runtime;
  }
  /**
   * @return string
   */
  public function getRuntime()
  {
    return $this->runtime;
  }
  /**
   * Service account to be used for building the container. The format of this
   * field is `projects/{projectId}/serviceAccounts/{serviceAccountEmail}`.
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
   * The location of the function source code.
   *
   * @param Source $source
   */
  public function setSource(Source $source)
  {
    $this->source = $source;
  }
  /**
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. A permanent fixed identifier for source.
   *
   * @param SourceProvenance $sourceProvenance
   */
  public function setSourceProvenance(SourceProvenance $sourceProvenance)
  {
    $this->sourceProvenance = $sourceProvenance;
  }
  /**
   * @return SourceProvenance
   */
  public function getSourceProvenance()
  {
    return $this->sourceProvenance;
  }
  /**
   * An identifier for Firebase function sources. Disclaimer: This field is only
   * supported for Firebase function deployments.
   *
   * @param string $sourceToken
   */
  public function setSourceToken($sourceToken)
  {
    $this->sourceToken = $sourceToken;
  }
  /**
   * @return string
   */
  public function getSourceToken()
  {
    return $this->sourceToken;
  }
  /**
   * Name of the Cloud Build Custom Worker Pool that should be used to build the
   * function. The format of this field is
   * `projects/{project}/locations/{region}/workerPools/{workerPool}` where
   * {project} and {region} are the project id and region respectively where the
   * worker pool is defined and {workerPool} is the short name of the worker
   * pool. If the project id is not the same as the function, then the Cloud
   * Functions Service Agent (service-@gcf-admin-robot.iam.gserviceaccount.com)
   * must be granted the role Cloud Build Custom Workers Builder
   * (roles/cloudbuild.customworkers.builder) in the project.
   *
   * @param string $workerPool
   */
  public function setWorkerPool($workerPool)
  {
    $this->workerPool = $workerPool;
  }
  /**
   * @return string
   */
  public function getWorkerPool()
  {
    return $this->workerPool;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuildConfig::class, 'Google_Service_CloudFunctions_BuildConfig');
