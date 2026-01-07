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

class GoogleCloudRunV2SubmitBuildRequest extends \Google\Collection
{
  /**
   * Do not use this default value.
   */
  public const RELEASE_TRACK_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const RELEASE_TRACK_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const RELEASE_TRACK_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const RELEASE_TRACK_EARLY_ACCESS = 'EARLY_ACCESS';
  /**
   * Alpha is a limited availability test for releases before they are cleared
   * for widespread use. By Alpha, all significant design issues are resolved
   * and we are in the process of verifying functionality. Alpha customers need
   * to apply for access, agree to applicable terms, and have their projects
   * allowlisted. Alpha releases don't have to be feature complete, no SLAs are
   * provided, and there are no technical support obligations, but they will be
   * far enough along that customers can actually use them in test environments
   * or for limited-use tests -- just like they would in normal production
   * cases.
   */
  public const RELEASE_TRACK_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const RELEASE_TRACK_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const RELEASE_TRACK_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const RELEASE_TRACK_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'tags';
  protected $buildpackBuildType = GoogleCloudRunV2BuildpacksBuild::class;
  protected $buildpackBuildDataType = '';
  /**
   * Optional. The client that initiated the build request.
   *
   * @var string
   */
  public $client;
  protected $dockerBuildType = GoogleCloudRunV2DockerBuild::class;
  protected $dockerBuildDataType = '';
  /**
   * Required. Artifact Registry URI to store the built image.
   *
   * @var string
   */
  public $imageUri;
  /**
   * Optional. The machine type from default pool to use for the build. If left
   * blank, cloudbuild will use a sensible default. Currently only E2_HIGHCPU_8
   * is supported. If worker_pool is set, this field will be ignored.
   *
   * @var string
   */
  public $machineType;
  /**
   * Optional. The release track of the client that initiated the build request.
   *
   * @var string
   */
  public $releaseTrack;
  /**
   * Optional. The service account to use for the build. If not set, the default
   * Cloud Build service account for the project will be used.
   *
   * @var string
   */
  public $serviceAccount;
  protected $storageSourceType = GoogleCloudRunV2StorageSource::class;
  protected $storageSourceDataType = '';
  /**
   * Optional. Additional tags to annotate the build.
   *
   * @var string[]
   */
  public $tags;
  /**
   * Optional. Name of the Cloud Build Custom Worker Pool that should be used to
   * build the function. The format of this field is
   * `projects/{project}/locations/{region}/workerPools/{workerPool}` where
   * `{project}` and `{region}` are the project id and region respectively where
   * the worker pool is defined and `{workerPool}` is the short name of the
   * worker pool.
   *
   * @var string
   */
  public $workerPool;

  /**
   * Build the source using Buildpacks.
   *
   * @param GoogleCloudRunV2BuildpacksBuild $buildpackBuild
   */
  public function setBuildpackBuild(GoogleCloudRunV2BuildpacksBuild $buildpackBuild)
  {
    $this->buildpackBuild = $buildpackBuild;
  }
  /**
   * @return GoogleCloudRunV2BuildpacksBuild
   */
  public function getBuildpackBuild()
  {
    return $this->buildpackBuild;
  }
  /**
   * Optional. The client that initiated the build request.
   *
   * @param string $client
   */
  public function setClient($client)
  {
    $this->client = $client;
  }
  /**
   * @return string
   */
  public function getClient()
  {
    return $this->client;
  }
  /**
   * Build the source using Docker. This means the source has a Dockerfile.
   *
   * @param GoogleCloudRunV2DockerBuild $dockerBuild
   */
  public function setDockerBuild(GoogleCloudRunV2DockerBuild $dockerBuild)
  {
    $this->dockerBuild = $dockerBuild;
  }
  /**
   * @return GoogleCloudRunV2DockerBuild
   */
  public function getDockerBuild()
  {
    return $this->dockerBuild;
  }
  /**
   * Required. Artifact Registry URI to store the built image.
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
   * Optional. The machine type from default pool to use for the build. If left
   * blank, cloudbuild will use a sensible default. Currently only E2_HIGHCPU_8
   * is supported. If worker_pool is set, this field will be ignored.
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
   * Optional. The release track of the client that initiated the build request.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::RELEASE_TRACK_* $releaseTrack
   */
  public function setReleaseTrack($releaseTrack)
  {
    $this->releaseTrack = $releaseTrack;
  }
  /**
   * @return self::RELEASE_TRACK_*
   */
  public function getReleaseTrack()
  {
    return $this->releaseTrack;
  }
  /**
   * Optional. The service account to use for the build. If not set, the default
   * Cloud Build service account for the project will be used.
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
   * Required. Source for the build.
   *
   * @param GoogleCloudRunV2StorageSource $storageSource
   */
  public function setStorageSource(GoogleCloudRunV2StorageSource $storageSource)
  {
    $this->storageSource = $storageSource;
  }
  /**
   * @return GoogleCloudRunV2StorageSource
   */
  public function getStorageSource()
  {
    return $this->storageSource;
  }
  /**
   * Optional. Additional tags to annotate the build.
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * Optional. Name of the Cloud Build Custom Worker Pool that should be used to
   * build the function. The format of this field is
   * `projects/{project}/locations/{region}/workerPools/{workerPool}` where
   * `{project}` and `{region}` are the project id and region respectively where
   * the worker pool is defined and `{workerPool}` is the short name of the
   * worker pool.
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
class_alias(GoogleCloudRunV2SubmitBuildRequest::class, 'Google_Service_CloudRun_GoogleCloudRunV2SubmitBuildRequest');
