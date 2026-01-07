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

namespace Google\Service\Dataproc;

class ExecutionConfig extends \Google\Collection
{
  protected $collection_key = 'networkTags';
  protected $authenticationConfigType = AuthenticationConfig::class;
  protected $authenticationConfigDataType = '';
  /**
   * Optional. Applies to sessions only. The duration to keep the session alive
   * while it's idling. Exceeding this threshold causes the session to
   * terminate. This field cannot be set on a batch workload. Minimum value is
   * 10 minutes; maximum value is 14 days (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   * Defaults to 1 hour if not set. If both ttl and idle_ttl are specified for
   * an interactive session, the conditions are treated as OR conditions: the
   * workload will be terminated when it has been idle for idle_ttl or when ttl
   * has been exceeded, whichever occurs first.
   *
   * @var string
   */
  public $idleTtl;
  /**
   * Optional. The Cloud KMS key to use for encryption.
   *
   * @var string
   */
  public $kmsKey;
  /**
   * Optional. Tags used for network traffic control.
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * Optional. Network URI to connect workload to.
   *
   * @var string
   */
  public $networkUri;
  /**
   * Optional. Service account that used to execute workload.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Optional. A Cloud Storage bucket used to stage workload dependencies,
   * config files, and store workload output and other ephemeral data, such as
   * Spark history files. If you do not specify a staging bucket, Cloud Dataproc
   * will determine a Cloud Storage location according to the region where your
   * workload is running, and then create and manage project-level, per-location
   * staging and temporary buckets. This field requires a Cloud Storage bucket
   * name, not a gs://... URI to a Cloud Storage bucket.
   *
   * @var string
   */
  public $stagingBucket;
  /**
   * Optional. Subnetwork URI to connect workload to.
   *
   * @var string
   */
  public $subnetworkUri;
  /**
   * Optional. The duration after which the workload will be terminated,
   * specified as the JSON representation for Duration
   * (https://protobuf.dev/programming-guides/proto3/#json). When the workload
   * exceeds this duration, it will be unconditionally terminated without
   * waiting for ongoing work to finish. If ttl is not specified for a batch
   * workload, the workload will be allowed to run until it exits naturally (or
   * run forever without exiting). If ttl is not specified for an interactive
   * session, it defaults to 24 hours. If ttl is not specified for a batch that
   * uses 2.1+ runtime version, it defaults to 4 hours. Minimum value is 10
   * minutes; maximum value is 14 days. If both ttl and idle_ttl are specified
   * (for an interactive session), the conditions are treated as OR conditions:
   * the workload will be terminated when it has been idle for idle_ttl or when
   * ttl has been exceeded, whichever occurs first.
   *
   * @var string
   */
  public $ttl;

  /**
   * Optional. Authentication configuration used to set the default identity for
   * the workload execution. The config specifies the type of identity (service
   * account or user) that will be used by workloads to access resources on the
   * project(s).
   *
   * @param AuthenticationConfig $authenticationConfig
   */
  public function setAuthenticationConfig(AuthenticationConfig $authenticationConfig)
  {
    $this->authenticationConfig = $authenticationConfig;
  }
  /**
   * @return AuthenticationConfig
   */
  public function getAuthenticationConfig()
  {
    return $this->authenticationConfig;
  }
  /**
   * Optional. Applies to sessions only. The duration to keep the session alive
   * while it's idling. Exceeding this threshold causes the session to
   * terminate. This field cannot be set on a batch workload. Minimum value is
   * 10 minutes; maximum value is 14 days (see JSON representation of Duration
   * (https://developers.google.com/protocol-buffers/docs/proto3#json)).
   * Defaults to 1 hour if not set. If both ttl and idle_ttl are specified for
   * an interactive session, the conditions are treated as OR conditions: the
   * workload will be terminated when it has been idle for idle_ttl or when ttl
   * has been exceeded, whichever occurs first.
   *
   * @param string $idleTtl
   */
  public function setIdleTtl($idleTtl)
  {
    $this->idleTtl = $idleTtl;
  }
  /**
   * @return string
   */
  public function getIdleTtl()
  {
    return $this->idleTtl;
  }
  /**
   * Optional. The Cloud KMS key to use for encryption.
   *
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * Optional. Tags used for network traffic control.
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * Optional. Network URI to connect workload to.
   *
   * @param string $networkUri
   */
  public function setNetworkUri($networkUri)
  {
    $this->networkUri = $networkUri;
  }
  /**
   * @return string
   */
  public function getNetworkUri()
  {
    return $this->networkUri;
  }
  /**
   * Optional. Service account that used to execute workload.
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
   * Optional. A Cloud Storage bucket used to stage workload dependencies,
   * config files, and store workload output and other ephemeral data, such as
   * Spark history files. If you do not specify a staging bucket, Cloud Dataproc
   * will determine a Cloud Storage location according to the region where your
   * workload is running, and then create and manage project-level, per-location
   * staging and temporary buckets. This field requires a Cloud Storage bucket
   * name, not a gs://... URI to a Cloud Storage bucket.
   *
   * @param string $stagingBucket
   */
  public function setStagingBucket($stagingBucket)
  {
    $this->stagingBucket = $stagingBucket;
  }
  /**
   * @return string
   */
  public function getStagingBucket()
  {
    return $this->stagingBucket;
  }
  /**
   * Optional. Subnetwork URI to connect workload to.
   *
   * @param string $subnetworkUri
   */
  public function setSubnetworkUri($subnetworkUri)
  {
    $this->subnetworkUri = $subnetworkUri;
  }
  /**
   * @return string
   */
  public function getSubnetworkUri()
  {
    return $this->subnetworkUri;
  }
  /**
   * Optional. The duration after which the workload will be terminated,
   * specified as the JSON representation for Duration
   * (https://protobuf.dev/programming-guides/proto3/#json). When the workload
   * exceeds this duration, it will be unconditionally terminated without
   * waiting for ongoing work to finish. If ttl is not specified for a batch
   * workload, the workload will be allowed to run until it exits naturally (or
   * run forever without exiting). If ttl is not specified for an interactive
   * session, it defaults to 24 hours. If ttl is not specified for a batch that
   * uses 2.1+ runtime version, it defaults to 4 hours. Minimum value is 10
   * minutes; maximum value is 14 days. If both ttl and idle_ttl are specified
   * (for an interactive session), the conditions are treated as OR conditions:
   * the workload will be terminated when it has been idle for idle_ttl or when
   * ttl has been exceeded, whichever occurs first.
   *
   * @param string $ttl
   */
  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }
  /**
   * @return string
   */
  public function getTtl()
  {
    return $this->ttl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecutionConfig::class, 'Google_Service_Dataproc_ExecutionConfig');
