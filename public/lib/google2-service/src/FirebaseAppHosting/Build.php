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

namespace Google\Service\FirebaseAppHosting;

class Build extends \Google\Collection
{
  /**
   * The build is in an unknown state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The build is building.
   */
  public const STATE_BUILDING = 'BUILDING';
  /**
   * The build has completed and is awaiting the next step. This may move to
   * DEPLOYING once App Hosting starts to set up infrastructure.
   */
  public const STATE_BUILT = 'BUILT';
  /**
   * The infrastructure for this build is being set up.
   */
  public const STATE_DEPLOYING = 'DEPLOYING';
  /**
   * The infrastructure for this build is ready. The build may or may not be
   * serving traffic - see `Backend.traffic` for the current state, or
   * `Backend.traffic_statuses` for the desired state.
   */
  public const STATE_READY = 'READY';
  /**
   * The build has failed.
   */
  public const STATE_FAILED = 'FAILED';
  protected $collection_key = 'errors';
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The location of the [Cloud Build
   * logs](https://cloud.google.com/build/docs/view-build-results) for the build
   * process.
   *
   * @var string
   */
  public $buildLogsUri;
  protected $configType = Config::class;
  protected $configDataType = '';
  /**
   * Output only. Time at which the build was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time at which the build was deleted.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * Optional. Human-readable name. 63 character limit.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The environment name of the backend when this build was
   * created.
   *
   * @var string
   */
  public $environment;
  protected $errorsType = Error::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. The Artifact Registry [container
   * image](https://cloud.google.com/artifact-registry/docs/reference/rest/v1/pr
   * ojects.locations.repositories.dockerImages) URI, used by the Cloud Run [`re
   * vision`](https://cloud.google.com/run/docs/reference/rest/v2/projects.locat
   * ions.services.revisions) for this build.
   *
   * @var string
   */
  public $image;
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the build. Format: `projects/{project}/loc
   * ations/{locationId}/backends/{backendId}/builds/{buildId}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A field that, if true, indicates that the build has an ongoing
   * LRO.
   *
   * @var bool
   */
  public $reconciling;
  protected $sourceType = BuildSource::class;
  protected $sourceDataType = '';
  /**
   * Output only. The state of the build.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time at which the build was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The location of the [Cloud Build
   * logs](https://cloud.google.com/build/docs/view-build-results) for the build
   * process.
   *
   * @param string $buildLogsUri
   */
  public function setBuildLogsUri($buildLogsUri)
  {
    $this->buildLogsUri = $buildLogsUri;
  }
  /**
   * @return string
   */
  public function getBuildLogsUri()
  {
    return $this->buildLogsUri;
  }
  /**
   * Optional. Additional configuration of the service.
   *
   * @param Config $config
   */
  public function setConfig(Config $config)
  {
    $this->config = $config;
  }
  /**
   * @return Config
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. Time at which the build was created.
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
   * Output only. Time at which the build was deleted.
   *
   * @param string $deleteTime
   */
  public function setDeleteTime($deleteTime)
  {
    $this->deleteTime = $deleteTime;
  }
  /**
   * @return string
   */
  public function getDeleteTime()
  {
    return $this->deleteTime;
  }
  /**
   * Optional. Human-readable name. 63 character limit.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The environment name of the backend when this build was
   * created.
   *
   * @param string $environment
   */
  public function setEnvironment($environment)
  {
    $this->environment = $environment;
  }
  /**
   * @return string
   */
  public function getEnvironment()
  {
    return $this->environment;
  }
  /**
   * Output only. A list of all errors that occurred during an App Hosting
   * build.
   *
   * @param Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
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
   * Output only. The Artifact Registry [container
   * image](https://cloud.google.com/artifact-registry/docs/reference/rest/v1/pr
   * ojects.locations.repositories.dockerImages) URI, used by the Cloud Run [`re
   * vision`](https://cloud.google.com/run/docs/reference/rest/v2/projects.locat
   * ions.services.revisions) for this build.
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
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
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
   * Identifier. The resource name of the build. Format: `projects/{project}/loc
   * ations/{locationId}/backends/{backendId}/builds/{buildId}`.
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
   * Output only. A field that, if true, indicates that the build has an ongoing
   * LRO.
   *
   * @param bool $reconciling
   */
  public function setReconciling($reconciling)
  {
    $this->reconciling = $reconciling;
  }
  /**
   * @return bool
   */
  public function getReconciling()
  {
    return $this->reconciling;
  }
  /**
   * Required. Immutable. The source for the build.
   *
   * @param BuildSource $source
   */
  public function setSource(BuildSource $source)
  {
    $this->source = $source;
  }
  /**
   * @return BuildSource
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * Output only. The state of the build.
   *
   * Accepted values: STATE_UNSPECIFIED, BUILDING, BUILT, DEPLOYING, READY,
   * FAILED
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
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. Time at which the build was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Build::class, 'Google_Service_FirebaseAppHosting_Build');
