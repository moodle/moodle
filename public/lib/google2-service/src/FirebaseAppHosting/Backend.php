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

class Backend extends \Google\Collection
{
  /**
   * Unspecified. Will return an error if used.
   */
  public const SERVING_LOCALITY_SERVING_LOCALITY_UNSPECIFIED = 'SERVING_LOCALITY_UNSPECIFIED';
  /**
   * In this mode, App Hosting serves your backend's content from your chosen
   * parent region. App Hosting only maintains data and serving infrastructure
   * in that chosen region and does not replicate your data to other regions.
   */
  public const SERVING_LOCALITY_REGIONAL_STRICT = 'REGIONAL_STRICT';
  /**
   * In this mode, App Hosting serves your backend's content from multiple
   * points-of-presence (POP) across the globe. App Hosting replicates your
   * backend's configuration and cached data to these POPs and uses a global CDN
   * to further decrease response latency. App Hosting-maintained Cloud
   * Resources on your project, such as Cloud Run services, Cloud Build build,
   * and Artifact Registry Images are still confined to your backend's parent
   * region. Responses cached by the CDN may be stored in the POPs for the
   * duration of the cache's TTL.
   */
  public const SERVING_LOCALITY_GLOBAL_ACCESS = 'GLOBAL_ACCESS';
  protected $collection_key = 'managedResources';
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Optional. The [ID of a Web
   * App](https://firebase.google.com/docs/reference/firebase-
   * management/rest/v1beta1/projects.webApps#WebApp.FIELDS.app_id) associated
   * with the backend.
   *
   * @var string
   */
  public $appId;
  protected $codebaseType = Codebase::class;
  protected $codebaseDataType = '';
  /**
   * Output only. Time at which the backend was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Time at which the backend was deleted.
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
   * Optional. The environment name of the backend, used to load environment
   * variables from environment specific configuration.
   *
   * @var string
   */
  public $environment;
  /**
   * Output only. Server-computed checksum based on other values; may be sent on
   * update or delete to ensure operation is done on expected resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects.
   *
   * @var string[]
   */
  public $labels;
  protected $managedResourcesType = ManagedResource::class;
  protected $managedResourcesDataType = 'array';
  /**
   * Optional. Deprecated: Use `environment` instead.
   *
   * @deprecated
   * @var string
   */
  public $mode;
  /**
   * Identifier. The resource name of the backend. Format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. A field that, if true, indicates that the system is working to
   * make adjustments to the backend during a LRO.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Optional. A field that, if true, indicates that incoming request logs are
   * disabled for this backend. Incoming request logs are enabled by default.
   *
   * @var bool
   */
  public $requestLogsDisabled;
  /**
   * Required. The name of the service account used for Cloud Build and Cloud
   * Run. Should have the role roles/firebaseapphosting.computeRunner or
   * equivalent permissions.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Required. Immutable. Specifies how App Hosting will serve the content for
   * this backend. It will either be contained to a single region
   * (REGIONAL_STRICT) or allowed to use App Hosting's global-replicated serving
   * infrastructure (GLOBAL_ACCESS).
   *
   * @var string
   */
  public $servingLocality;
  /**
   * Output only. System-assigned, unique identifier.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. Time at which the backend was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The primary URI to communicate with the backend.
   *
   * @var string
   */
  public $uri;

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
   * Optional. The [ID of a Web
   * App](https://firebase.google.com/docs/reference/firebase-
   * management/rest/v1beta1/projects.webApps#WebApp.FIELDS.app_id) associated
   * with the backend.
   *
   * @param string $appId
   */
  public function setAppId($appId)
  {
    $this->appId = $appId;
  }
  /**
   * @return string
   */
  public function getAppId()
  {
    return $this->appId;
  }
  /**
   * Optional. If specified, the connection to an external source repository to
   * watch for event-driven updates to the backend.
   *
   * @param Codebase $codebase
   */
  public function setCodebase(Codebase $codebase)
  {
    $this->codebase = $codebase;
  }
  /**
   * @return Codebase
   */
  public function getCodebase()
  {
    return $this->codebase;
  }
  /**
   * Output only. Time at which the backend was created.
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
   * Output only. Time at which the backend was deleted.
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
   * Optional. The environment name of the backend, used to load environment
   * variables from environment specific configuration.
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
   * Output only. A list of the resources managed by this backend.
   *
   * @param ManagedResource[] $managedResources
   */
  public function setManagedResources($managedResources)
  {
    $this->managedResources = $managedResources;
  }
  /**
   * @return ManagedResource[]
   */
  public function getManagedResources()
  {
    return $this->managedResources;
  }
  /**
   * Optional. Deprecated: Use `environment` instead.
   *
   * @deprecated
   * @param string $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Identifier. The resource name of the backend. Format:
   * `projects/{project}/locations/{locationId}/backends/{backendId}`.
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
   * Output only. A field that, if true, indicates that the system is working to
   * make adjustments to the backend during a LRO.
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
   * Optional. A field that, if true, indicates that incoming request logs are
   * disabled for this backend. Incoming request logs are enabled by default.
   *
   * @param bool $requestLogsDisabled
   */
  public function setRequestLogsDisabled($requestLogsDisabled)
  {
    $this->requestLogsDisabled = $requestLogsDisabled;
  }
  /**
   * @return bool
   */
  public function getRequestLogsDisabled()
  {
    return $this->requestLogsDisabled;
  }
  /**
   * Required. The name of the service account used for Cloud Build and Cloud
   * Run. Should have the role roles/firebaseapphosting.computeRunner or
   * equivalent permissions.
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
   * Required. Immutable. Specifies how App Hosting will serve the content for
   * this backend. It will either be contained to a single region
   * (REGIONAL_STRICT) or allowed to use App Hosting's global-replicated serving
   * infrastructure (GLOBAL_ACCESS).
   *
   * Accepted values: SERVING_LOCALITY_UNSPECIFIED, REGIONAL_STRICT,
   * GLOBAL_ACCESS
   *
   * @param self::SERVING_LOCALITY_* $servingLocality
   */
  public function setServingLocality($servingLocality)
  {
    $this->servingLocality = $servingLocality;
  }
  /**
   * @return self::SERVING_LOCALITY_*
   */
  public function getServingLocality()
  {
    return $this->servingLocality;
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
   * Output only. Time at which the backend was last updated.
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
  /**
   * Output only. The primary URI to communicate with the backend.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Backend::class, 'Google_Service_FirebaseAppHosting_Backend');
