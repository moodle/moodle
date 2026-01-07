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

namespace Google\Service\Pubsub;

class ConfluentCloud extends \Google\Model
{
  /**
   * Default value. This value is unused.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * Ingestion is active.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * Permission denied encountered while consuming data from Confluent Cloud.
   */
  public const STATE_CONFLUENT_CLOUD_PERMISSION_DENIED = 'CONFLUENT_CLOUD_PERMISSION_DENIED';
  /**
   * Permission denied encountered while publishing to the topic.
   */
  public const STATE_PUBLISH_PERMISSION_DENIED = 'PUBLISH_PERMISSION_DENIED';
  /**
   * The provided bootstrap server address is unreachable.
   */
  public const STATE_UNREACHABLE_BOOTSTRAP_SERVER = 'UNREACHABLE_BOOTSTRAP_SERVER';
  /**
   * The provided cluster wasn't found.
   */
  public const STATE_CLUSTER_NOT_FOUND = 'CLUSTER_NOT_FOUND';
  /**
   * The provided topic wasn't found.
   */
  public const STATE_TOPIC_NOT_FOUND = 'TOPIC_NOT_FOUND';
  /**
   * Required. The address of the bootstrap server. The format is url:port.
   *
   * @var string
   */
  public $bootstrapServer;
  /**
   * Required. The id of the cluster.
   *
   * @var string
   */
  public $clusterId;
  /**
   * Required. The GCP service account to be used for Federated Identity
   * authentication with `identity_pool_id`.
   *
   * @var string
   */
  public $gcpServiceAccount;
  /**
   * Required. The id of the identity pool to be used for Federated Identity
   * authentication with Confluent Cloud. See
   * https://docs.confluent.io/cloud/current/security/authenticate/workload-
   * identities/identity-providers/oauth/identity-pools.html#add-oauth-identity-
   * pools.
   *
   * @var string
   */
  public $identityPoolId;
  /**
   * Output only. An output-only field that indicates the state of the Confluent
   * Cloud ingestion source.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The name of the topic in the Confluent Cloud cluster that Pub/Sub
   * will import from.
   *
   * @var string
   */
  public $topic;

  /**
   * Required. The address of the bootstrap server. The format is url:port.
   *
   * @param string $bootstrapServer
   */
  public function setBootstrapServer($bootstrapServer)
  {
    $this->bootstrapServer = $bootstrapServer;
  }
  /**
   * @return string
   */
  public function getBootstrapServer()
  {
    return $this->bootstrapServer;
  }
  /**
   * Required. The id of the cluster.
   *
   * @param string $clusterId
   */
  public function setClusterId($clusterId)
  {
    $this->clusterId = $clusterId;
  }
  /**
   * @return string
   */
  public function getClusterId()
  {
    return $this->clusterId;
  }
  /**
   * Required. The GCP service account to be used for Federated Identity
   * authentication with `identity_pool_id`.
   *
   * @param string $gcpServiceAccount
   */
  public function setGcpServiceAccount($gcpServiceAccount)
  {
    $this->gcpServiceAccount = $gcpServiceAccount;
  }
  /**
   * @return string
   */
  public function getGcpServiceAccount()
  {
    return $this->gcpServiceAccount;
  }
  /**
   * Required. The id of the identity pool to be used for Federated Identity
   * authentication with Confluent Cloud. See
   * https://docs.confluent.io/cloud/current/security/authenticate/workload-
   * identities/identity-providers/oauth/identity-pools.html#add-oauth-identity-
   * pools.
   *
   * @param string $identityPoolId
   */
  public function setIdentityPoolId($identityPoolId)
  {
    $this->identityPoolId = $identityPoolId;
  }
  /**
   * @return string
   */
  public function getIdentityPoolId()
  {
    return $this->identityPoolId;
  }
  /**
   * Output only. An output-only field that indicates the state of the Confluent
   * Cloud ingestion source.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE,
   * CONFLUENT_CLOUD_PERMISSION_DENIED, PUBLISH_PERMISSION_DENIED,
   * UNREACHABLE_BOOTSTRAP_SERVER, CLUSTER_NOT_FOUND, TOPIC_NOT_FOUND
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
   * Required. The name of the topic in the Confluent Cloud cluster that Pub/Sub
   * will import from.
   *
   * @param string $topic
   */
  public function setTopic($topic)
  {
    $this->topic = $topic;
  }
  /**
   * @return string
   */
  public function getTopic()
  {
    return $this->topic;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfluentCloud::class, 'Google_Service_Pubsub_ConfluentCloud');
