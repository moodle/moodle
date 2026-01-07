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

class ServiceConfig extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const INGRESS_SETTINGS_INGRESS_SETTINGS_UNSPECIFIED = 'INGRESS_SETTINGS_UNSPECIFIED';
  /**
   * Allow HTTP traffic from public and private sources.
   */
  public const INGRESS_SETTINGS_ALLOW_ALL = 'ALLOW_ALL';
  /**
   * Allow HTTP traffic from only private VPC sources.
   */
  public const INGRESS_SETTINGS_ALLOW_INTERNAL_ONLY = 'ALLOW_INTERNAL_ONLY';
  /**
   * Allow HTTP traffic from private VPC sources and through GCLB.
   */
  public const INGRESS_SETTINGS_ALLOW_INTERNAL_AND_GCLB = 'ALLOW_INTERNAL_AND_GCLB';
  /**
   * Unspecified.
   */
  public const SECURITY_LEVEL_SECURITY_LEVEL_UNSPECIFIED = 'SECURITY_LEVEL_UNSPECIFIED';
  /**
   * Requests for a URL that match this handler that do not use HTTPS are
   * automatically redirected to the HTTPS URL with the same path. Query
   * parameters are reserved for the redirect.
   */
  public const SECURITY_LEVEL_SECURE_ALWAYS = 'SECURE_ALWAYS';
  /**
   * Both HTTP and HTTPS requests with URLs that match the handler succeed
   * without redirects. The application can examine the request to determine
   * which protocol was used and respond accordingly.
   */
  public const SECURITY_LEVEL_SECURE_OPTIONAL = 'SECURE_OPTIONAL';
  /**
   * Unspecified.
   */
  public const VPC_CONNECTOR_EGRESS_SETTINGS_VPC_CONNECTOR_EGRESS_SETTINGS_UNSPECIFIED = 'VPC_CONNECTOR_EGRESS_SETTINGS_UNSPECIFIED';
  /**
   * Use the VPC Access Connector only for private IP space from RFC1918.
   */
  public const VPC_CONNECTOR_EGRESS_SETTINGS_PRIVATE_RANGES_ONLY = 'PRIVATE_RANGES_ONLY';
  /**
   * Force the use of VPC Access Connector for all egress traffic from the
   * function.
   */
  public const VPC_CONNECTOR_EGRESS_SETTINGS_ALL_TRAFFIC = 'ALL_TRAFFIC';
  protected $collection_key = 'secretVolumes';
  /**
   * Whether 100% of traffic is routed to the latest revision. On CreateFunction
   * and UpdateFunction, when set to true, the revision being deployed will
   * serve 100% of traffic, ignoring any traffic split settings, if any. On
   * GetFunction, true will be returned if the latest revision is serving 100%
   * of traffic.
   *
   * @var bool
   */
  public $allTrafficOnLatestRevision;
  /**
   * The number of CPUs used in a single container instance. Default value is
   * calculated from available memory. Supports the same values as Cloud Run,
   * see https://cloud.google.com/run/docs/reference/rest/v1/Container#resourcer
   * equirements Example: "1" indicates 1 vCPU
   *
   * @var string
   */
  public $availableCpu;
  /**
   * The amount of memory available for a function. Defaults to 256M. Supported
   * units are k, M, G, Mi, Gi. If no unit is supplied the value is interpreted
   * as bytes. See https://github.com/kubernetes/kubernetes/blob/master/staging/
   * src/k8s.io/apimachinery/pkg/api/resource/quantity.go a full description.
   *
   * @var string
   */
  public $availableMemory;
  /**
   * Optional. The binary authorization policy to be checked when deploying the
   * Cloud Run service.
   *
   * @var string
   */
  public $binaryAuthorizationPolicy;
  /**
   * Environment variables that shall be available during function execution.
   *
   * @var string[]
   */
  public $environmentVariables;
  /**
   * The ingress settings for the function, controlling what traffic can reach
   * it.
   *
   * @var string
   */
  public $ingressSettings;
  /**
   * The limit on the maximum number of function instances that may coexist at a
   * given time. In some cases, such as rapid traffic surges, Cloud Functions
   * may, for a short period of time, create more instances than the specified
   * max instances limit. If your function cannot tolerate this temporary
   * behavior, you may want to factor in a safety margin and set a lower max
   * instances value than your function can tolerate. See the [Max
   * Instances](https://cloud.google.com/functions/docs/max-instances) Guide for
   * more details.
   *
   * @var int
   */
  public $maxInstanceCount;
  /**
   * Sets the maximum number of concurrent requests that each instance can
   * receive. Defaults to 1.
   *
   * @var int
   */
  public $maxInstanceRequestConcurrency;
  /**
   * The limit on the minimum number of function instances that may coexist at a
   * given time. Function instances are kept in idle state for a short period
   * after they finished executing the request to reduce cold start time for
   * subsequent requests. Setting a minimum instance count will ensure that the
   * given number of instances are kept running in idle state always. This can
   * help with cold start times when jump in incoming request count occurs after
   * the idle instance would have been stopped in the default case.
   *
   * @var int
   */
  public $minInstanceCount;
  /**
   * Output only. The name of service revision.
   *
   * @var string
   */
  public $revision;
  protected $secretEnvironmentVariablesType = SecretEnvVar::class;
  protected $secretEnvironmentVariablesDataType = 'array';
  protected $secretVolumesType = SecretVolume::class;
  protected $secretVolumesDataType = 'array';
  /**
   * Security level configure whether the function only accepts https. This
   * configuration is only applicable to 1st Gen functions with Http trigger. By
   * default https is optional for 1st Gen functions; 2nd Gen functions are
   * https ONLY.
   *
   * @var string
   */
  public $securityLevel;
  /**
   * Output only. Name of the service associated with a Function. The format of
   * this field is `projects/{project}/locations/{region}/services/{service}`
   *
   * @var string
   */
  public $service;
  /**
   * The email of the service's service account. If empty, defaults to
   * `{project_number}-compute@developer.gserviceaccount.com`.
   *
   * @var string
   */
  public $serviceAccountEmail;
  /**
   * The function execution timeout. Execution is considered failed and can be
   * terminated if the function is not completed at the end of the timeout
   * period. Defaults to 60 seconds.
   *
   * @var int
   */
  public $timeoutSeconds;
  /**
   * Output only. URI of the Service deployed.
   *
   * @var string
   */
  public $uri;
  /**
   * The Serverless VPC Access connector that this cloud function can connect
   * to. The format of this field is `projects/locations/connectors`.
   *
   * @var string
   */
  public $vpcConnector;
  /**
   * The egress settings for the connector, controlling what traffic is diverted
   * through it.
   *
   * @var string
   */
  public $vpcConnectorEgressSettings;

  /**
   * Whether 100% of traffic is routed to the latest revision. On CreateFunction
   * and UpdateFunction, when set to true, the revision being deployed will
   * serve 100% of traffic, ignoring any traffic split settings, if any. On
   * GetFunction, true will be returned if the latest revision is serving 100%
   * of traffic.
   *
   * @param bool $allTrafficOnLatestRevision
   */
  public function setAllTrafficOnLatestRevision($allTrafficOnLatestRevision)
  {
    $this->allTrafficOnLatestRevision = $allTrafficOnLatestRevision;
  }
  /**
   * @return bool
   */
  public function getAllTrafficOnLatestRevision()
  {
    return $this->allTrafficOnLatestRevision;
  }
  /**
   * The number of CPUs used in a single container instance. Default value is
   * calculated from available memory. Supports the same values as Cloud Run,
   * see https://cloud.google.com/run/docs/reference/rest/v1/Container#resourcer
   * equirements Example: "1" indicates 1 vCPU
   *
   * @param string $availableCpu
   */
  public function setAvailableCpu($availableCpu)
  {
    $this->availableCpu = $availableCpu;
  }
  /**
   * @return string
   */
  public function getAvailableCpu()
  {
    return $this->availableCpu;
  }
  /**
   * The amount of memory available for a function. Defaults to 256M. Supported
   * units are k, M, G, Mi, Gi. If no unit is supplied the value is interpreted
   * as bytes. See https://github.com/kubernetes/kubernetes/blob/master/staging/
   * src/k8s.io/apimachinery/pkg/api/resource/quantity.go a full description.
   *
   * @param string $availableMemory
   */
  public function setAvailableMemory($availableMemory)
  {
    $this->availableMemory = $availableMemory;
  }
  /**
   * @return string
   */
  public function getAvailableMemory()
  {
    return $this->availableMemory;
  }
  /**
   * Optional. The binary authorization policy to be checked when deploying the
   * Cloud Run service.
   *
   * @param string $binaryAuthorizationPolicy
   */
  public function setBinaryAuthorizationPolicy($binaryAuthorizationPolicy)
  {
    $this->binaryAuthorizationPolicy = $binaryAuthorizationPolicy;
  }
  /**
   * @return string
   */
  public function getBinaryAuthorizationPolicy()
  {
    return $this->binaryAuthorizationPolicy;
  }
  /**
   * Environment variables that shall be available during function execution.
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
   * The ingress settings for the function, controlling what traffic can reach
   * it.
   *
   * Accepted values: INGRESS_SETTINGS_UNSPECIFIED, ALLOW_ALL,
   * ALLOW_INTERNAL_ONLY, ALLOW_INTERNAL_AND_GCLB
   *
   * @param self::INGRESS_SETTINGS_* $ingressSettings
   */
  public function setIngressSettings($ingressSettings)
  {
    $this->ingressSettings = $ingressSettings;
  }
  /**
   * @return self::INGRESS_SETTINGS_*
   */
  public function getIngressSettings()
  {
    return $this->ingressSettings;
  }
  /**
   * The limit on the maximum number of function instances that may coexist at a
   * given time. In some cases, such as rapid traffic surges, Cloud Functions
   * may, for a short period of time, create more instances than the specified
   * max instances limit. If your function cannot tolerate this temporary
   * behavior, you may want to factor in a safety margin and set a lower max
   * instances value than your function can tolerate. See the [Max
   * Instances](https://cloud.google.com/functions/docs/max-instances) Guide for
   * more details.
   *
   * @param int $maxInstanceCount
   */
  public function setMaxInstanceCount($maxInstanceCount)
  {
    $this->maxInstanceCount = $maxInstanceCount;
  }
  /**
   * @return int
   */
  public function getMaxInstanceCount()
  {
    return $this->maxInstanceCount;
  }
  /**
   * Sets the maximum number of concurrent requests that each instance can
   * receive. Defaults to 1.
   *
   * @param int $maxInstanceRequestConcurrency
   */
  public function setMaxInstanceRequestConcurrency($maxInstanceRequestConcurrency)
  {
    $this->maxInstanceRequestConcurrency = $maxInstanceRequestConcurrency;
  }
  /**
   * @return int
   */
  public function getMaxInstanceRequestConcurrency()
  {
    return $this->maxInstanceRequestConcurrency;
  }
  /**
   * The limit on the minimum number of function instances that may coexist at a
   * given time. Function instances are kept in idle state for a short period
   * after they finished executing the request to reduce cold start time for
   * subsequent requests. Setting a minimum instance count will ensure that the
   * given number of instances are kept running in idle state always. This can
   * help with cold start times when jump in incoming request count occurs after
   * the idle instance would have been stopped in the default case.
   *
   * @param int $minInstanceCount
   */
  public function setMinInstanceCount($minInstanceCount)
  {
    $this->minInstanceCount = $minInstanceCount;
  }
  /**
   * @return int
   */
  public function getMinInstanceCount()
  {
    return $this->minInstanceCount;
  }
  /**
   * Output only. The name of service revision.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * Secret environment variables configuration.
   *
   * @param SecretEnvVar[] $secretEnvironmentVariables
   */
  public function setSecretEnvironmentVariables($secretEnvironmentVariables)
  {
    $this->secretEnvironmentVariables = $secretEnvironmentVariables;
  }
  /**
   * @return SecretEnvVar[]
   */
  public function getSecretEnvironmentVariables()
  {
    return $this->secretEnvironmentVariables;
  }
  /**
   * Secret volumes configuration.
   *
   * @param SecretVolume[] $secretVolumes
   */
  public function setSecretVolumes($secretVolumes)
  {
    $this->secretVolumes = $secretVolumes;
  }
  /**
   * @return SecretVolume[]
   */
  public function getSecretVolumes()
  {
    return $this->secretVolumes;
  }
  /**
   * Security level configure whether the function only accepts https. This
   * configuration is only applicable to 1st Gen functions with Http trigger. By
   * default https is optional for 1st Gen functions; 2nd Gen functions are
   * https ONLY.
   *
   * Accepted values: SECURITY_LEVEL_UNSPECIFIED, SECURE_ALWAYS, SECURE_OPTIONAL
   *
   * @param self::SECURITY_LEVEL_* $securityLevel
   */
  public function setSecurityLevel($securityLevel)
  {
    $this->securityLevel = $securityLevel;
  }
  /**
   * @return self::SECURITY_LEVEL_*
   */
  public function getSecurityLevel()
  {
    return $this->securityLevel;
  }
  /**
   * Output only. Name of the service associated with a Function. The format of
   * this field is `projects/{project}/locations/{region}/services/{service}`
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * The email of the service's service account. If empty, defaults to
   * `{project_number}-compute@developer.gserviceaccount.com`.
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
  /**
   * The function execution timeout. Execution is considered failed and can be
   * terminated if the function is not completed at the end of the timeout
   * period. Defaults to 60 seconds.
   *
   * @param int $timeoutSeconds
   */
  public function setTimeoutSeconds($timeoutSeconds)
  {
    $this->timeoutSeconds = $timeoutSeconds;
  }
  /**
   * @return int
   */
  public function getTimeoutSeconds()
  {
    return $this->timeoutSeconds;
  }
  /**
   * Output only. URI of the Service deployed.
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
   * The Serverless VPC Access connector that this cloud function can connect
   * to. The format of this field is `projects/locations/connectors`.
   *
   * @param string $vpcConnector
   */
  public function setVpcConnector($vpcConnector)
  {
    $this->vpcConnector = $vpcConnector;
  }
  /**
   * @return string
   */
  public function getVpcConnector()
  {
    return $this->vpcConnector;
  }
  /**
   * The egress settings for the connector, controlling what traffic is diverted
   * through it.
   *
   * Accepted values: VPC_CONNECTOR_EGRESS_SETTINGS_UNSPECIFIED,
   * PRIVATE_RANGES_ONLY, ALL_TRAFFIC
   *
   * @param self::VPC_CONNECTOR_EGRESS_SETTINGS_* $vpcConnectorEgressSettings
   */
  public function setVpcConnectorEgressSettings($vpcConnectorEgressSettings)
  {
    $this->vpcConnectorEgressSettings = $vpcConnectorEgressSettings;
  }
  /**
   * @return self::VPC_CONNECTOR_EGRESS_SETTINGS_*
   */
  public function getVpcConnectorEgressSettings()
  {
    return $this->vpcConnectorEgressSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceConfig::class, 'Google_Service_CloudFunctions_ServiceConfig');
