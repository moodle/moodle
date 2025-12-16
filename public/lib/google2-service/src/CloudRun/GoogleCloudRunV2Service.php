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

class GoogleCloudRunV2Service extends \Google\Collection
{
  /**
   * Unspecified
   */
  public const INGRESS_INGRESS_TRAFFIC_UNSPECIFIED = 'INGRESS_TRAFFIC_UNSPECIFIED';
  /**
   * All inbound traffic is allowed.
   */
  public const INGRESS_INGRESS_TRAFFIC_ALL = 'INGRESS_TRAFFIC_ALL';
  /**
   * Only internal traffic is allowed.
   */
  public const INGRESS_INGRESS_TRAFFIC_INTERNAL_ONLY = 'INGRESS_TRAFFIC_INTERNAL_ONLY';
  /**
   * Both internal and Google Cloud Load Balancer traffic is allowed.
   */
  public const INGRESS_INGRESS_TRAFFIC_INTERNAL_LOAD_BALANCER = 'INGRESS_TRAFFIC_INTERNAL_LOAD_BALANCER';
  /**
   * No ingress traffic is allowed.
   */
  public const INGRESS_INGRESS_TRAFFIC_NONE = 'INGRESS_TRAFFIC_NONE';
  /**
   * Do not use this default value.
   */
  public const LAUNCH_STAGE_LAUNCH_STAGE_UNSPECIFIED = 'LAUNCH_STAGE_UNSPECIFIED';
  /**
   * The feature is not yet implemented. Users can not use it.
   */
  public const LAUNCH_STAGE_UNIMPLEMENTED = 'UNIMPLEMENTED';
  /**
   * Prelaunch features are hidden from users and are only visible internally.
   */
  public const LAUNCH_STAGE_PRELAUNCH = 'PRELAUNCH';
  /**
   * Early Access features are limited to a closed group of testers. To use
   * these features, you must sign up in advance and sign a Trusted Tester
   * agreement (which includes confidentiality provisions). These features may
   * be unstable, changed in backward-incompatible ways, and are not guaranteed
   * to be released.
   */
  public const LAUNCH_STAGE_EARLY_ACCESS = 'EARLY_ACCESS';
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
  public const LAUNCH_STAGE_ALPHA = 'ALPHA';
  /**
   * Beta is the point at which we are ready to open a release for any customer
   * to use. There are no SLA or technical support obligations in a Beta
   * release. Products will be complete from a feature perspective, but may have
   * some open outstanding issues. Beta releases are suitable for limited
   * production use cases.
   */
  public const LAUNCH_STAGE_BETA = 'BETA';
  /**
   * GA features are open to all developers and are considered stable and fully
   * qualified for production use.
   */
  public const LAUNCH_STAGE_GA = 'GA';
  /**
   * Deprecated features are scheduled to be shut down and removed. For more
   * information, see the "Deprecation Policy" section of our [Terms of
   * Service](https://cloud.google.com/terms/) and the [Google Cloud Platform
   * Subject to the Deprecation
   * Policy](https://cloud.google.com/terms/deprecation) documentation.
   */
  public const LAUNCH_STAGE_DEPRECATED = 'DEPRECATED';
  protected $collection_key = 'urls';
  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects. Cloud Run API v2 does not support
   * annotations with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected in new resources. All system annotations in v1 now have a
   * corresponding field in v2 Service. This field follows Kubernetes
   * annotations' namespacing, limits, and rules.
   *
   * @var string[]
   */
  public $annotations;
  protected $binaryAuthorizationType = GoogleCloudRunV2BinaryAuthorization::class;
  protected $binaryAuthorizationDataType = '';
  protected $buildConfigType = GoogleCloudRunV2BuildConfig::class;
  protected $buildConfigDataType = '';
  /**
   * Arbitrary identifier for the API client.
   *
   * @var string
   */
  public $client;
  /**
   * Arbitrary version identifier for the API client.
   *
   * @var string
   */
  public $clientVersion;
  protected $conditionsType = GoogleCloudRunV2Condition::class;
  protected $conditionsDataType = 'array';
  /**
   * Output only. The creation time.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Email address of the authenticated creator.
   *
   * @var string
   */
  public $creator;
  /**
   * One or more custom audiences that you want this service to support. Specify
   * each custom audience as the full URL in a string. The custom audiences are
   * encoded in the token and used to authenticate requests. For more
   * information, see https://cloud.google.com/run/docs/configuring/custom-
   * audiences.
   *
   * @var string[]
   */
  public $customAudiences;
  /**
   * Optional. Disables public resolution of the default URI of this service.
   *
   * @var bool
   */
  public $defaultUriDisabled;
  /**
   * Output only. The deletion time. It is only populated as a response to a
   * Delete request.
   *
   * @var string
   */
  public $deleteTime;
  /**
   * User-provided description of the Service. This field currently has a
   * 512-character limit.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. A system-generated fingerprint for this version of the resource.
   * May be used to detect modification conflict during updates.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. For a deleted resource, the time after which it will be
   * permanently deleted.
   *
   * @var string
   */
  public $expireTime;
  /**
   * Output only. A number that monotonically increases every time the user
   * modifies the desired state. Please note that unlike v1, this is an int64
   * value. As with most Google APIs, its JSON representation will be a `string`
   * instead of an `integer`.
   *
   * @var string
   */
  public $generation;
  /**
   * Optional. IAP settings on the Service.
   *
   * @var bool
   */
  public $iapEnabled;
  /**
   * Optional. Provides the ingress settings for this Service. On output,
   * returns the currently observed ingress settings, or
   * INGRESS_TRAFFIC_UNSPECIFIED if no revision is active.
   *
   * @var string
   */
  public $ingress;
  /**
   * Optional. Disables IAM permission check for run.routes.invoke for callers
   * of this service. For more information, visit
   * https://cloud.google.com/run/docs/securing/managing-access#invoker_check.
   *
   * @var bool
   */
  public $invokerIamDisabled;
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 Service.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Email address of the last authenticated modifier.
   *
   * @var string
   */
  public $lastModifier;
  /**
   * Output only. Name of the last created revision. See comments in
   * `reconciling` for additional information on reconciliation process in Cloud
   * Run.
   *
   * @var string
   */
  public $latestCreatedRevision;
  /**
   * Output only. Name of the latest revision that is serving traffic. See
   * comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run.
   *
   * @var string
   */
  public $latestReadyRevision;
  /**
   * Optional. The launch stage as defined by [Google Cloud Platform Launch
   * Stages](https://cloud.google.com/terms/launch-stages). Cloud Run supports
   * `ALPHA`, `BETA`, and `GA`. If no value is specified, GA is assumed. Set the
   * launch stage to a preview stage on input to allow use of preview features
   * in that stage. On read (or output), describes whether the resource uses
   * preview features. For example, if ALPHA is provided as input, but only BETA
   * and GA-level features are used, this field will be BETA on output.
   *
   * @var string
   */
  public $launchStage;
  protected $multiRegionSettingsType = GoogleCloudRunV2MultiRegionSettings::class;
  protected $multiRegionSettingsDataType = '';
  /**
   * Identifier. The fully qualified name of this Service. In
   * CreateServiceRequest, this field is ignored, and instead composed from
   * CreateServiceRequest.parent and CreateServiceRequest.service_id. Format:
   * projects/{project}/locations/{location}/services/{service_id}
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The generation of this Service currently serving traffic. See
   * comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run. Please note that unlike v1, this is an int64 value.
   * As with most Google APIs, its JSON representation will be a `string`
   * instead of an `integer`.
   *
   * @var string
   */
  public $observedGeneration;
  /**
   * Output only. Returns true if the Service is currently being acted upon by
   * the system to bring it into the desired state. When a new Service is
   * created, or an existing one is updated, Cloud Run will asynchronously
   * perform all necessary steps to bring the Service to the desired serving
   * state. This process is called reconciliation. While reconciliation is in
   * process, `observed_generation`, `latest_ready_revision`,
   * `traffic_statuses`, and `uri` will have transient values that might
   * mismatch the intended state: Once reconciliation is over (and this field is
   * false), there are two possible outcomes: reconciliation succeeded and the
   * serving state matches the Service, or there was an error, and
   * reconciliation failed. This state can be found in
   * `terminal_condition.state`. If reconciliation succeeded, the following
   * fields will match: `traffic` and `traffic_statuses`, `observed_generation`
   * and `generation`, `latest_ready_revision` and `latest_created_revision`. If
   * reconciliation failed, `traffic_statuses`, `observed_generation`, and
   * `latest_ready_revision` will have the state of the last serving revision,
   * or empty for newly created Services. Additional information on the failure
   * can be found in `terminal_condition` and `conditions`.
   *
   * @var bool
   */
  public $reconciling;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  protected $scalingType = GoogleCloudRunV2ServiceScaling::class;
  protected $scalingDataType = '';
  protected $templateType = GoogleCloudRunV2RevisionTemplate::class;
  protected $templateDataType = '';
  protected $terminalConditionType = GoogleCloudRunV2Condition::class;
  protected $terminalConditionDataType = '';
  /**
   * Output only. True if Cloud Run Threat Detection monitoring is enabled for
   * the parent project of this Service.
   *
   * @var bool
   */
  public $threatDetectionEnabled;
  protected $trafficType = GoogleCloudRunV2TrafficTarget::class;
  protected $trafficDataType = 'array';
  protected $trafficStatusesType = GoogleCloudRunV2TrafficTargetStatus::class;
  protected $trafficStatusesDataType = 'array';
  /**
   * Output only. Server assigned unique identifier for the trigger. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The last-modified time.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The main URI in which this Service is serving traffic.
   *
   * @var string
   */
  public $uri;
  /**
   * Output only. All URLs serving traffic for this Service.
   *
   * @var string[]
   */
  public $urls;

  /**
   * Optional. Unstructured key value map that may be set by external tools to
   * store and arbitrary metadata. They are not queryable and should be
   * preserved when modifying objects. Cloud Run API v2 does not support
   * annotations with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected in new resources. All system annotations in v1 now have a
   * corresponding field in v2 Service. This field follows Kubernetes
   * annotations' namespacing, limits, and rules.
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
   * Optional. Settings for the Binary Authorization feature.
   *
   * @param GoogleCloudRunV2BinaryAuthorization $binaryAuthorization
   */
  public function setBinaryAuthorization(GoogleCloudRunV2BinaryAuthorization $binaryAuthorization)
  {
    $this->binaryAuthorization = $binaryAuthorization;
  }
  /**
   * @return GoogleCloudRunV2BinaryAuthorization
   */
  public function getBinaryAuthorization()
  {
    return $this->binaryAuthorization;
  }
  /**
   * Optional. Configuration for building a Cloud Run function.
   *
   * @param GoogleCloudRunV2BuildConfig $buildConfig
   */
  public function setBuildConfig(GoogleCloudRunV2BuildConfig $buildConfig)
  {
    $this->buildConfig = $buildConfig;
  }
  /**
   * @return GoogleCloudRunV2BuildConfig
   */
  public function getBuildConfig()
  {
    return $this->buildConfig;
  }
  /**
   * Arbitrary identifier for the API client.
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
   * Arbitrary version identifier for the API client.
   *
   * @param string $clientVersion
   */
  public function setClientVersion($clientVersion)
  {
    $this->clientVersion = $clientVersion;
  }
  /**
   * @return string
   */
  public function getClientVersion()
  {
    return $this->clientVersion;
  }
  /**
   * Output only. The Conditions of all other associated sub-resources. They
   * contain additional diagnostics information in case the Service does not
   * reach its Serving state. See comments in `reconciling` for additional
   * information on reconciliation process in Cloud Run.
   *
   * @param GoogleCloudRunV2Condition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return GoogleCloudRunV2Condition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Output only. The creation time.
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
   * Output only. Email address of the authenticated creator.
   *
   * @param string $creator
   */
  public function setCreator($creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return string
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * One or more custom audiences that you want this service to support. Specify
   * each custom audience as the full URL in a string. The custom audiences are
   * encoded in the token and used to authenticate requests. For more
   * information, see https://cloud.google.com/run/docs/configuring/custom-
   * audiences.
   *
   * @param string[] $customAudiences
   */
  public function setCustomAudiences($customAudiences)
  {
    $this->customAudiences = $customAudiences;
  }
  /**
   * @return string[]
   */
  public function getCustomAudiences()
  {
    return $this->customAudiences;
  }
  /**
   * Optional. Disables public resolution of the default URI of this service.
   *
   * @param bool $defaultUriDisabled
   */
  public function setDefaultUriDisabled($defaultUriDisabled)
  {
    $this->defaultUriDisabled = $defaultUriDisabled;
  }
  /**
   * @return bool
   */
  public function getDefaultUriDisabled()
  {
    return $this->defaultUriDisabled;
  }
  /**
   * Output only. The deletion time. It is only populated as a response to a
   * Delete request.
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
   * User-provided description of the Service. This field currently has a
   * 512-character limit.
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
   * Optional. A system-generated fingerprint for this version of the resource.
   * May be used to detect modification conflict during updates.
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
   * Output only. For a deleted resource, the time after which it will be
   * permanently deleted.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * Output only. A number that monotonically increases every time the user
   * modifies the desired state. Please note that unlike v1, this is an int64
   * value. As with most Google APIs, its JSON representation will be a `string`
   * instead of an `integer`.
   *
   * @param string $generation
   */
  public function setGeneration($generation)
  {
    $this->generation = $generation;
  }
  /**
   * @return string
   */
  public function getGeneration()
  {
    return $this->generation;
  }
  /**
   * Optional. IAP settings on the Service.
   *
   * @param bool $iapEnabled
   */
  public function setIapEnabled($iapEnabled)
  {
    $this->iapEnabled = $iapEnabled;
  }
  /**
   * @return bool
   */
  public function getIapEnabled()
  {
    return $this->iapEnabled;
  }
  /**
   * Optional. Provides the ingress settings for this Service. On output,
   * returns the currently observed ingress settings, or
   * INGRESS_TRAFFIC_UNSPECIFIED if no revision is active.
   *
   * Accepted values: INGRESS_TRAFFIC_UNSPECIFIED, INGRESS_TRAFFIC_ALL,
   * INGRESS_TRAFFIC_INTERNAL_ONLY, INGRESS_TRAFFIC_INTERNAL_LOAD_BALANCER,
   * INGRESS_TRAFFIC_NONE
   *
   * @param self::INGRESS_* $ingress
   */
  public function setIngress($ingress)
  {
    $this->ingress = $ingress;
  }
  /**
   * @return self::INGRESS_*
   */
  public function getIngress()
  {
    return $this->ingress;
  }
  /**
   * Optional. Disables IAM permission check for run.routes.invoke for callers
   * of this service. For more information, visit
   * https://cloud.google.com/run/docs/securing/managing-access#invoker_check.
   *
   * @param bool $invokerIamDisabled
   */
  public function setInvokerIamDisabled($invokerIamDisabled)
  {
    $this->invokerIamDisabled = $invokerIamDisabled;
  }
  /**
   * @return bool
   */
  public function getInvokerIamDisabled()
  {
    return $this->invokerIamDisabled;
  }
  /**
   * Optional. Unstructured key value map that can be used to organize and
   * categorize objects. User-provided labels are shared with Google's billing
   * system, so they can be used to filter, or break down billing charges by
   * team, component, environment, state, etc. For more information, visit
   * https://cloud.google.com/resource-manager/docs/creating-managing-labels or
   * https://cloud.google.com/run/docs/configuring/labels. Cloud Run API v2 does
   * not support labels with `run.googleapis.com`, `cloud.googleapis.com`,
   * `serving.knative.dev`, or `autoscaling.knative.dev` namespaces, and they
   * will be rejected. All system labels in v1 now have a corresponding field in
   * v2 Service.
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
   * Output only. Email address of the last authenticated modifier.
   *
   * @param string $lastModifier
   */
  public function setLastModifier($lastModifier)
  {
    $this->lastModifier = $lastModifier;
  }
  /**
   * @return string
   */
  public function getLastModifier()
  {
    return $this->lastModifier;
  }
  /**
   * Output only. Name of the last created revision. See comments in
   * `reconciling` for additional information on reconciliation process in Cloud
   * Run.
   *
   * @param string $latestCreatedRevision
   */
  public function setLatestCreatedRevision($latestCreatedRevision)
  {
    $this->latestCreatedRevision = $latestCreatedRevision;
  }
  /**
   * @return string
   */
  public function getLatestCreatedRevision()
  {
    return $this->latestCreatedRevision;
  }
  /**
   * Output only. Name of the latest revision that is serving traffic. See
   * comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run.
   *
   * @param string $latestReadyRevision
   */
  public function setLatestReadyRevision($latestReadyRevision)
  {
    $this->latestReadyRevision = $latestReadyRevision;
  }
  /**
   * @return string
   */
  public function getLatestReadyRevision()
  {
    return $this->latestReadyRevision;
  }
  /**
   * Optional. The launch stage as defined by [Google Cloud Platform Launch
   * Stages](https://cloud.google.com/terms/launch-stages). Cloud Run supports
   * `ALPHA`, `BETA`, and `GA`. If no value is specified, GA is assumed. Set the
   * launch stage to a preview stage on input to allow use of preview features
   * in that stage. On read (or output), describes whether the resource uses
   * preview features. For example, if ALPHA is provided as input, but only BETA
   * and GA-level features are used, this field will be BETA on output.
   *
   * Accepted values: LAUNCH_STAGE_UNSPECIFIED, UNIMPLEMENTED, PRELAUNCH,
   * EARLY_ACCESS, ALPHA, BETA, GA, DEPRECATED
   *
   * @param self::LAUNCH_STAGE_* $launchStage
   */
  public function setLaunchStage($launchStage)
  {
    $this->launchStage = $launchStage;
  }
  /**
   * @return self::LAUNCH_STAGE_*
   */
  public function getLaunchStage()
  {
    return $this->launchStage;
  }
  /**
   * Optional. Settings for multi-region deployment.
   *
   * @param GoogleCloudRunV2MultiRegionSettings $multiRegionSettings
   */
  public function setMultiRegionSettings(GoogleCloudRunV2MultiRegionSettings $multiRegionSettings)
  {
    $this->multiRegionSettings = $multiRegionSettings;
  }
  /**
   * @return GoogleCloudRunV2MultiRegionSettings
   */
  public function getMultiRegionSettings()
  {
    return $this->multiRegionSettings;
  }
  /**
   * Identifier. The fully qualified name of this Service. In
   * CreateServiceRequest, this field is ignored, and instead composed from
   * CreateServiceRequest.parent and CreateServiceRequest.service_id. Format:
   * projects/{project}/locations/{location}/services/{service_id}
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
   * Output only. The generation of this Service currently serving traffic. See
   * comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run. Please note that unlike v1, this is an int64 value.
   * As with most Google APIs, its JSON representation will be a `string`
   * instead of an `integer`.
   *
   * @param string $observedGeneration
   */
  public function setObservedGeneration($observedGeneration)
  {
    $this->observedGeneration = $observedGeneration;
  }
  /**
   * @return string
   */
  public function getObservedGeneration()
  {
    return $this->observedGeneration;
  }
  /**
   * Output only. Returns true if the Service is currently being acted upon by
   * the system to bring it into the desired state. When a new Service is
   * created, or an existing one is updated, Cloud Run will asynchronously
   * perform all necessary steps to bring the Service to the desired serving
   * state. This process is called reconciliation. While reconciliation is in
   * process, `observed_generation`, `latest_ready_revision`,
   * `traffic_statuses`, and `uri` will have transient values that might
   * mismatch the intended state: Once reconciliation is over (and this field is
   * false), there are two possible outcomes: reconciliation succeeded and the
   * serving state matches the Service, or there was an error, and
   * reconciliation failed. This state can be found in
   * `terminal_condition.state`. If reconciliation succeeded, the following
   * fields will match: `traffic` and `traffic_statuses`, `observed_generation`
   * and `generation`, `latest_ready_revision` and `latest_created_revision`. If
   * reconciliation failed, `traffic_statuses`, `observed_generation`, and
   * `latest_ready_revision` will have the state of the last serving revision,
   * or empty for newly created Services. Additional information on the failure
   * can be found in `terminal_condition` and `conditions`.
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
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Optional. Specifies service-level scaling settings
   *
   * @param GoogleCloudRunV2ServiceScaling $scaling
   */
  public function setScaling(GoogleCloudRunV2ServiceScaling $scaling)
  {
    $this->scaling = $scaling;
  }
  /**
   * @return GoogleCloudRunV2ServiceScaling
   */
  public function getScaling()
  {
    return $this->scaling;
  }
  /**
   * Required. The template used to create revisions for this Service.
   *
   * @param GoogleCloudRunV2RevisionTemplate $template
   */
  public function setTemplate(GoogleCloudRunV2RevisionTemplate $template)
  {
    $this->template = $template;
  }
  /**
   * @return GoogleCloudRunV2RevisionTemplate
   */
  public function getTemplate()
  {
    return $this->template;
  }
  /**
   * Output only. The Condition of this Service, containing its readiness
   * status, and detailed error information in case it did not reach a serving
   * state. See comments in `reconciling` for additional information on
   * reconciliation process in Cloud Run.
   *
   * @param GoogleCloudRunV2Condition $terminalCondition
   */
  public function setTerminalCondition(GoogleCloudRunV2Condition $terminalCondition)
  {
    $this->terminalCondition = $terminalCondition;
  }
  /**
   * @return GoogleCloudRunV2Condition
   */
  public function getTerminalCondition()
  {
    return $this->terminalCondition;
  }
  /**
   * Output only. True if Cloud Run Threat Detection monitoring is enabled for
   * the parent project of this Service.
   *
   * @param bool $threatDetectionEnabled
   */
  public function setThreatDetectionEnabled($threatDetectionEnabled)
  {
    $this->threatDetectionEnabled = $threatDetectionEnabled;
  }
  /**
   * @return bool
   */
  public function getThreatDetectionEnabled()
  {
    return $this->threatDetectionEnabled;
  }
  /**
   * Optional. Specifies how to distribute traffic over a collection of
   * Revisions belonging to the Service. If traffic is empty or not provided,
   * defaults to 100% traffic to the latest `Ready` Revision.
   *
   * @param GoogleCloudRunV2TrafficTarget[] $traffic
   */
  public function setTraffic($traffic)
  {
    $this->traffic = $traffic;
  }
  /**
   * @return GoogleCloudRunV2TrafficTarget[]
   */
  public function getTraffic()
  {
    return $this->traffic;
  }
  /**
   * Output only. Detailed status information for corresponding traffic targets.
   * See comments in `reconciling` for additional information on reconciliation
   * process in Cloud Run.
   *
   * @param GoogleCloudRunV2TrafficTargetStatus[] $trafficStatuses
   */
  public function setTrafficStatuses($trafficStatuses)
  {
    $this->trafficStatuses = $trafficStatuses;
  }
  /**
   * @return GoogleCloudRunV2TrafficTargetStatus[]
   */
  public function getTrafficStatuses()
  {
    return $this->trafficStatuses;
  }
  /**
   * Output only. Server assigned unique identifier for the trigger. The value
   * is a UUID4 string and guaranteed to remain unchanged until the resource is
   * deleted.
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
   * Output only. The last-modified time.
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
   * Output only. The main URI in which this Service is serving traffic.
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
  /**
   * Output only. All URLs serving traffic for this Service.
   *
   * @param string[] $urls
   */
  public function setUrls($urls)
  {
    $this->urls = $urls;
  }
  /**
   * @return string[]
   */
  public function getUrls()
  {
    return $this->urls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2Service::class, 'Google_Service_CloudRun_GoogleCloudRunV2Service');
