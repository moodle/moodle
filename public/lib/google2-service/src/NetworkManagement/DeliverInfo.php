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

namespace Google\Service\NetworkManagement;

class DeliverInfo extends \Google\Model
{
  /**
   * Unspecified Google Service.
   */
  public const GOOGLE_SERVICE_TYPE_GOOGLE_SERVICE_TYPE_UNSPECIFIED = 'GOOGLE_SERVICE_TYPE_UNSPECIFIED';
  /**
   * Identity aware proxy. https://cloud.google.com/iap/docs/using-tcp-
   * forwarding
   */
  public const GOOGLE_SERVICE_TYPE_IAP = 'IAP';
  /**
   * One of two services sharing IP ranges: * Load Balancer proxy * Centralized
   * Health Check prober https://cloud.google.com/load-balancing/docs/firewall-
   * rules
   */
  public const GOOGLE_SERVICE_TYPE_GFE_PROXY_OR_HEALTH_CHECK_PROBER = 'GFE_PROXY_OR_HEALTH_CHECK_PROBER';
  /**
   * Connectivity from Cloud DNS to forwarding targets or alternate name servers
   * that use private routing.
   * https://cloud.google.com/dns/docs/zones/forwarding-zones#firewall-rules
   * https://cloud.google.com/dns/docs/policies#firewall-rules
   */
  public const GOOGLE_SERVICE_TYPE_CLOUD_DNS = 'CLOUD_DNS';
  /**
   * private.googleapis.com and restricted.googleapis.com
   */
  public const GOOGLE_SERVICE_TYPE_PRIVATE_GOOGLE_ACCESS = 'PRIVATE_GOOGLE_ACCESS';
  /**
   * Google API via Private Service Connect.
   * https://cloud.google.com/vpc/docs/configure-private-service-connect-apis
   * Google API via Serverless VPC Access.
   * https://cloud.google.com/vpc/docs/serverless-vpc-access
   */
  public const GOOGLE_SERVICE_TYPE_SERVERLESS_VPC_ACCESS = 'SERVERLESS_VPC_ACCESS';
  /**
   * Target not specified.
   */
  public const TARGET_TARGET_UNSPECIFIED = 'TARGET_UNSPECIFIED';
  /**
   * Target is a Compute Engine instance.
   */
  public const TARGET_INSTANCE = 'INSTANCE';
  /**
   * Target is the internet.
   */
  public const TARGET_INTERNET = 'INTERNET';
  /**
   * Target is a Google API.
   */
  public const TARGET_GOOGLE_API = 'GOOGLE_API';
  /**
   * Target is a Google Kubernetes Engine cluster master.
   */
  public const TARGET_GKE_MASTER = 'GKE_MASTER';
  /**
   * Target is a Cloud SQL instance.
   */
  public const TARGET_CLOUD_SQL_INSTANCE = 'CLOUD_SQL_INSTANCE';
  /**
   * Target is a published service that uses [Private Service
   * Connect](https://cloud.google.com/vpc/docs/configure-private-service-
   * connect-services).
   */
  public const TARGET_PSC_PUBLISHED_SERVICE = 'PSC_PUBLISHED_SERVICE';
  /**
   * Target is Google APIs that use [Private Service
   * Connect](https://cloud.google.com/vpc/docs/configure-private-service-
   * connect-apis).
   */
  public const TARGET_PSC_GOOGLE_API = 'PSC_GOOGLE_API';
  /**
   * Target is a VPC-SC that uses [Private Service
   * Connect](https://cloud.google.com/vpc/docs/configure-private-service-
   * connect-apis).
   */
  public const TARGET_PSC_VPC_SC = 'PSC_VPC_SC';
  /**
   * Target is a serverless network endpoint group.
   */
  public const TARGET_SERVERLESS_NEG = 'SERVERLESS_NEG';
  /**
   * Target is a Cloud Storage bucket.
   */
  public const TARGET_STORAGE_BUCKET = 'STORAGE_BUCKET';
  /**
   * Target is a private network. Used only for return traces.
   */
  public const TARGET_PRIVATE_NETWORK = 'PRIVATE_NETWORK';
  /**
   * Target is a Cloud Function. Used only for return traces.
   */
  public const TARGET_CLOUD_FUNCTION = 'CLOUD_FUNCTION';
  /**
   * Target is a App Engine service version. Used only for return traces.
   */
  public const TARGET_APP_ENGINE_VERSION = 'APP_ENGINE_VERSION';
  /**
   * Target is a Cloud Run revision. Used only for return traces.
   */
  public const TARGET_CLOUD_RUN_REVISION = 'CLOUD_RUN_REVISION';
  /**
   * Target is a Google-managed service. Used only for return traces.
   */
  public const TARGET_GOOGLE_MANAGED_SERVICE = 'GOOGLE_MANAGED_SERVICE';
  /**
   * Target is a Redis Instance.
   */
  public const TARGET_REDIS_INSTANCE = 'REDIS_INSTANCE';
  /**
   * Target is a Redis Cluster.
   */
  public const TARGET_REDIS_CLUSTER = 'REDIS_CLUSTER';
  /**
   * Recognized type of a Google Service the packet is delivered to (if
   * applicable).
   *
   * @var string
   */
  public $googleServiceType;
  /**
   * IP address of the target (if applicable).
   *
   * @var string
   */
  public $ipAddress;
  /**
   * PSC Google API target the packet is delivered to (if applicable).
   *
   * @var string
   */
  public $pscGoogleApiTarget;
  /**
   * URI of the resource that the packet is delivered to.
   *
   * @var string
   */
  public $resourceUri;
  /**
   * Name of the Cloud Storage Bucket the packet is delivered to (if
   * applicable).
   *
   * @var string
   */
  public $storageBucket;
  /**
   * Target type where the packet is delivered to.
   *
   * @var string
   */
  public $target;

  /**
   * Recognized type of a Google Service the packet is delivered to (if
   * applicable).
   *
   * Accepted values: GOOGLE_SERVICE_TYPE_UNSPECIFIED, IAP,
   * GFE_PROXY_OR_HEALTH_CHECK_PROBER, CLOUD_DNS, PRIVATE_GOOGLE_ACCESS,
   * SERVERLESS_VPC_ACCESS
   *
   * @param self::GOOGLE_SERVICE_TYPE_* $googleServiceType
   */
  public function setGoogleServiceType($googleServiceType)
  {
    $this->googleServiceType = $googleServiceType;
  }
  /**
   * @return self::GOOGLE_SERVICE_TYPE_*
   */
  public function getGoogleServiceType()
  {
    return $this->googleServiceType;
  }
  /**
   * IP address of the target (if applicable).
   *
   * @param string $ipAddress
   */
  public function setIpAddress($ipAddress)
  {
    $this->ipAddress = $ipAddress;
  }
  /**
   * @return string
   */
  public function getIpAddress()
  {
    return $this->ipAddress;
  }
  /**
   * PSC Google API target the packet is delivered to (if applicable).
   *
   * @param string $pscGoogleApiTarget
   */
  public function setPscGoogleApiTarget($pscGoogleApiTarget)
  {
    $this->pscGoogleApiTarget = $pscGoogleApiTarget;
  }
  /**
   * @return string
   */
  public function getPscGoogleApiTarget()
  {
    return $this->pscGoogleApiTarget;
  }
  /**
   * URI of the resource that the packet is delivered to.
   *
   * @param string $resourceUri
   */
  public function setResourceUri($resourceUri)
  {
    $this->resourceUri = $resourceUri;
  }
  /**
   * @return string
   */
  public function getResourceUri()
  {
    return $this->resourceUri;
  }
  /**
   * Name of the Cloud Storage Bucket the packet is delivered to (if
   * applicable).
   *
   * @param string $storageBucket
   */
  public function setStorageBucket($storageBucket)
  {
    $this->storageBucket = $storageBucket;
  }
  /**
   * @return string
   */
  public function getStorageBucket()
  {
    return $this->storageBucket;
  }
  /**
   * Target type where the packet is delivered to.
   *
   * Accepted values: TARGET_UNSPECIFIED, INSTANCE, INTERNET, GOOGLE_API,
   * GKE_MASTER, CLOUD_SQL_INSTANCE, PSC_PUBLISHED_SERVICE, PSC_GOOGLE_API,
   * PSC_VPC_SC, SERVERLESS_NEG, STORAGE_BUCKET, PRIVATE_NETWORK,
   * CLOUD_FUNCTION, APP_ENGINE_VERSION, CLOUD_RUN_REVISION,
   * GOOGLE_MANAGED_SERVICE, REDIS_INSTANCE, REDIS_CLUSTER
   *
   * @param self::TARGET_* $target
   */
  public function setTarget($target)
  {
    $this->target = $target;
  }
  /**
   * @return self::TARGET_*
   */
  public function getTarget()
  {
    return $this->target;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeliverInfo::class, 'Google_Service_NetworkManagement_DeliverInfo');
