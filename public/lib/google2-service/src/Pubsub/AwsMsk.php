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

class AwsMsk extends \Google\Model
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
   * Permission denied encountered while consuming data from Amazon MSK.
   */
  public const STATE_MSK_PERMISSION_DENIED = 'MSK_PERMISSION_DENIED';
  /**
   * Permission denied encountered while publishing to the topic.
   */
  public const STATE_PUBLISH_PERMISSION_DENIED = 'PUBLISH_PERMISSION_DENIED';
  /**
   * The provided MSK cluster wasn't found.
   */
  public const STATE_CLUSTER_NOT_FOUND = 'CLUSTER_NOT_FOUND';
  /**
   * The provided topic wasn't found.
   */
  public const STATE_TOPIC_NOT_FOUND = 'TOPIC_NOT_FOUND';
  /**
   * Required. AWS role ARN to be used for Federated Identity authentication
   * with Amazon MSK. Check the Pub/Sub docs for how to set up this role and the
   * required permissions that need to be attached to it.
   *
   * @var string
   */
  public $awsRoleArn;
  /**
   * Required. The Amazon Resource Name (ARN) that uniquely identifies the
   * cluster.
   *
   * @var string
   */
  public $clusterArn;
  /**
   * Required. The GCP service account to be used for Federated Identity
   * authentication with Amazon MSK (via a `AssumeRoleWithWebIdentity` call for
   * the provided role). The `aws_role_arn` must be set up with
   * `accounts.google.com:sub` equals to this service account number.
   *
   * @var string
   */
  public $gcpServiceAccount;
  /**
   * Output only. An output-only field that indicates the state of the Amazon
   * MSK ingestion source.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The name of the topic in the Amazon MSK cluster that Pub/Sub will
   * import from.
   *
   * @var string
   */
  public $topic;

  /**
   * Required. AWS role ARN to be used for Federated Identity authentication
   * with Amazon MSK. Check the Pub/Sub docs for how to set up this role and the
   * required permissions that need to be attached to it.
   *
   * @param string $awsRoleArn
   */
  public function setAwsRoleArn($awsRoleArn)
  {
    $this->awsRoleArn = $awsRoleArn;
  }
  /**
   * @return string
   */
  public function getAwsRoleArn()
  {
    return $this->awsRoleArn;
  }
  /**
   * Required. The Amazon Resource Name (ARN) that uniquely identifies the
   * cluster.
   *
   * @param string $clusterArn
   */
  public function setClusterArn($clusterArn)
  {
    $this->clusterArn = $clusterArn;
  }
  /**
   * @return string
   */
  public function getClusterArn()
  {
    return $this->clusterArn;
  }
  /**
   * Required. The GCP service account to be used for Federated Identity
   * authentication with Amazon MSK (via a `AssumeRoleWithWebIdentity` call for
   * the provided role). The `aws_role_arn` must be set up with
   * `accounts.google.com:sub` equals to this service account number.
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
   * Output only. An output-only field that indicates the state of the Amazon
   * MSK ingestion source.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, MSK_PERMISSION_DENIED,
   * PUBLISH_PERMISSION_DENIED, CLUSTER_NOT_FOUND, TOPIC_NOT_FOUND
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
   * Required. The name of the topic in the Amazon MSK cluster that Pub/Sub will
   * import from.
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
class_alias(AwsMsk::class, 'Google_Service_Pubsub_AwsMsk');
