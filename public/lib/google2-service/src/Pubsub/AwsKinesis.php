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

class AwsKinesis extends \Google\Model
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
   * Permission denied encountered while consuming data from Kinesis. This can
   * happen if: - The provided `aws_role_arn` does not exist or does not have
   * the appropriate permissions attached. - The provided `aws_role_arn` is not
   * set up properly for Identity Federation using `gcp_service_account`. - The
   * Pub/Sub SA is not granted the `iam.serviceAccounts.getOpenIdToken`
   * permission on `gcp_service_account`.
   */
  public const STATE_KINESIS_PERMISSION_DENIED = 'KINESIS_PERMISSION_DENIED';
  /**
   * Permission denied encountered while publishing to the topic. This can
   * happen if the Pub/Sub SA has not been granted the [appropriate publish
   * permissions](https://cloud.google.com/pubsub/docs/access-
   * control#pubsub.publisher)
   */
  public const STATE_PUBLISH_PERMISSION_DENIED = 'PUBLISH_PERMISSION_DENIED';
  /**
   * The Kinesis stream does not exist.
   */
  public const STATE_STREAM_NOT_FOUND = 'STREAM_NOT_FOUND';
  /**
   * The Kinesis consumer does not exist.
   */
  public const STATE_CONSUMER_NOT_FOUND = 'CONSUMER_NOT_FOUND';
  /**
   * Required. AWS role ARN to be used for Federated Identity authentication
   * with Kinesis. Check the Pub/Sub docs for how to set up this role and the
   * required permissions that need to be attached to it.
   *
   * @var string
   */
  public $awsRoleArn;
  /**
   * Required. The Kinesis consumer ARN to used for ingestion in Enhanced Fan-
   * Out mode. The consumer must be already created and ready to be used.
   *
   * @var string
   */
  public $consumerArn;
  /**
   * Required. The GCP service account to be used for Federated Identity
   * authentication with Kinesis (via a `AssumeRoleWithWebIdentity` call for the
   * provided role). The `aws_role_arn` must be set up with
   * `accounts.google.com:sub` equals to this service account number.
   *
   * @var string
   */
  public $gcpServiceAccount;
  /**
   * Output only. An output-only field that indicates the state of the Kinesis
   * ingestion source.
   *
   * @var string
   */
  public $state;
  /**
   * Required. The Kinesis stream ARN to ingest data from.
   *
   * @var string
   */
  public $streamArn;

  /**
   * Required. AWS role ARN to be used for Federated Identity authentication
   * with Kinesis. Check the Pub/Sub docs for how to set up this role and the
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
   * Required. The Kinesis consumer ARN to used for ingestion in Enhanced Fan-
   * Out mode. The consumer must be already created and ready to be used.
   *
   * @param string $consumerArn
   */
  public function setConsumerArn($consumerArn)
  {
    $this->consumerArn = $consumerArn;
  }
  /**
   * @return string
   */
  public function getConsumerArn()
  {
    return $this->consumerArn;
  }
  /**
   * Required. The GCP service account to be used for Federated Identity
   * authentication with Kinesis (via a `AssumeRoleWithWebIdentity` call for the
   * provided role). The `aws_role_arn` must be set up with
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
   * Output only. An output-only field that indicates the state of the Kinesis
   * ingestion source.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, KINESIS_PERMISSION_DENIED,
   * PUBLISH_PERMISSION_DENIED, STREAM_NOT_FOUND, CONSUMER_NOT_FOUND
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
   * Required. The Kinesis stream ARN to ingest data from.
   *
   * @param string $streamArn
   */
  public function setStreamArn($streamArn)
  {
    $this->streamArn = $streamArn;
  }
  /**
   * @return string
   */
  public function getStreamArn()
  {
    return $this->streamArn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsKinesis::class, 'Google_Service_Pubsub_AwsKinesis');
