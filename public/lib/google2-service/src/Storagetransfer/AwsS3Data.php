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

namespace Google\Service\Storagetransfer;

class AwsS3Data extends \Google\Model
{
  protected $awsAccessKeyType = AwsAccessKey::class;
  protected $awsAccessKeyDataType = '';
  /**
   * Required. S3 Bucket name (see [Creating a
   * bucket](https://docs.aws.amazon.com/AmazonS3/latest/dev/create-bucket-get-
   * location-example.html)).
   *
   * @var string
   */
  public $bucketName;
  /**
   * Optional. The CloudFront distribution domain name pointing to this bucket,
   * to use when fetching. See [Transfer from S3 via
   * CloudFront](https://cloud.google.com/storage-transfer/docs/s3-cloudfront)
   * for more information. Format: `https://{id}.cloudfront.net` or any valid
   * custom domain. Must begin with `https://`.
   *
   * @var string
   */
  public $cloudfrontDomain;
  /**
   * Optional. The Resource name of a secret in Secret Manager. AWS credentials
   * must be stored in Secret Manager in JSON format: { "access_key_id":
   * "ACCESS_KEY_ID", "secret_access_key": "SECRET_ACCESS_KEY" }
   * GoogleServiceAccount must be granted `roles/secretmanager.secretAccessor`
   * for the resource. See [Configure access to a source: Amazon S3]
   * (https://cloud.google.com/storage-transfer/docs/source-
   * amazon-s3#secret_manager) for more information. If `credentials_secret` is
   * specified, do not specify role_arn or aws_access_key. Format:
   * `projects/{project_number}/secrets/{secret_name}`
   *
   * @var string
   */
  public $credentialsSecret;
  /**
   * Egress bytes over a Google-managed private network. This network is shared
   * between other users of Storage Transfer Service.
   *
   * @var bool
   */
  public $managedPrivateNetwork;
  /**
   * Root path to transfer objects. Must be an empty string or full path name
   * that ends with a '/'. This field is treated as an object prefix. As such,
   * it should generally not begin with a '/'.
   *
   * @var string
   */
  public $path;
  /**
   * Service Directory Service to be used as the endpoint for transfers from a
   * custom VPC. Format: `projects/{project_id}/locations/{location}/namespaces/
   * {namespace}/services/{service}`
   *
   * @var string
   */
  public $privateNetworkService;
  /**
   * The Amazon Resource Name (ARN) of the role to support temporary credentials
   * via `AssumeRoleWithWebIdentity`. For more information about ARNs, see [IAM
   * ARNs](https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_identifier
   * s.html#identifiers-arns). When a role ARN is provided, Transfer Service
   * fetches temporary credentials for the session using a
   * `AssumeRoleWithWebIdentity` call for the provided role using the
   * GoogleServiceAccount for this project.
   *
   * @var string
   */
  public $roleArn;

  /**
   * Input only. AWS access key used to sign the API requests to the AWS S3
   * bucket. Permissions on the bucket must be granted to the access ID of the
   * AWS access key. For information on our data retention policy for user
   * credentials, see [User credentials](/storage-transfer/docs/data-
   * retention#user-credentials).
   *
   * @param AwsAccessKey $awsAccessKey
   */
  public function setAwsAccessKey(AwsAccessKey $awsAccessKey)
  {
    $this->awsAccessKey = $awsAccessKey;
  }
  /**
   * @return AwsAccessKey
   */
  public function getAwsAccessKey()
  {
    return $this->awsAccessKey;
  }
  /**
   * Required. S3 Bucket name (see [Creating a
   * bucket](https://docs.aws.amazon.com/AmazonS3/latest/dev/create-bucket-get-
   * location-example.html)).
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * Optional. The CloudFront distribution domain name pointing to this bucket,
   * to use when fetching. See [Transfer from S3 via
   * CloudFront](https://cloud.google.com/storage-transfer/docs/s3-cloudfront)
   * for more information. Format: `https://{id}.cloudfront.net` or any valid
   * custom domain. Must begin with `https://`.
   *
   * @param string $cloudfrontDomain
   */
  public function setCloudfrontDomain($cloudfrontDomain)
  {
    $this->cloudfrontDomain = $cloudfrontDomain;
  }
  /**
   * @return string
   */
  public function getCloudfrontDomain()
  {
    return $this->cloudfrontDomain;
  }
  /**
   * Optional. The Resource name of a secret in Secret Manager. AWS credentials
   * must be stored in Secret Manager in JSON format: { "access_key_id":
   * "ACCESS_KEY_ID", "secret_access_key": "SECRET_ACCESS_KEY" }
   * GoogleServiceAccount must be granted `roles/secretmanager.secretAccessor`
   * for the resource. See [Configure access to a source: Amazon S3]
   * (https://cloud.google.com/storage-transfer/docs/source-
   * amazon-s3#secret_manager) for more information. If `credentials_secret` is
   * specified, do not specify role_arn or aws_access_key. Format:
   * `projects/{project_number}/secrets/{secret_name}`
   *
   * @param string $credentialsSecret
   */
  public function setCredentialsSecret($credentialsSecret)
  {
    $this->credentialsSecret = $credentialsSecret;
  }
  /**
   * @return string
   */
  public function getCredentialsSecret()
  {
    return $this->credentialsSecret;
  }
  /**
   * Egress bytes over a Google-managed private network. This network is shared
   * between other users of Storage Transfer Service.
   *
   * @param bool $managedPrivateNetwork
   */
  public function setManagedPrivateNetwork($managedPrivateNetwork)
  {
    $this->managedPrivateNetwork = $managedPrivateNetwork;
  }
  /**
   * @return bool
   */
  public function getManagedPrivateNetwork()
  {
    return $this->managedPrivateNetwork;
  }
  /**
   * Root path to transfer objects. Must be an empty string or full path name
   * that ends with a '/'. This field is treated as an object prefix. As such,
   * it should generally not begin with a '/'.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Service Directory Service to be used as the endpoint for transfers from a
   * custom VPC. Format: `projects/{project_id}/locations/{location}/namespaces/
   * {namespace}/services/{service}`
   *
   * @param string $privateNetworkService
   */
  public function setPrivateNetworkService($privateNetworkService)
  {
    $this->privateNetworkService = $privateNetworkService;
  }
  /**
   * @return string
   */
  public function getPrivateNetworkService()
  {
    return $this->privateNetworkService;
  }
  /**
   * The Amazon Resource Name (ARN) of the role to support temporary credentials
   * via `AssumeRoleWithWebIdentity`. For more information about ARNs, see [IAM
   * ARNs](https://docs.aws.amazon.com/IAM/latest/UserGuide/reference_identifier
   * s.html#identifiers-arns). When a role ARN is provided, Transfer Service
   * fetches temporary credentials for the session using a
   * `AssumeRoleWithWebIdentity` call for the provided role using the
   * GoogleServiceAccount for this project.
   *
   * @param string $roleArn
   */
  public function setRoleArn($roleArn)
  {
    $this->roleArn = $roleArn;
  }
  /**
   * @return string
   */
  public function getRoleArn()
  {
    return $this->roleArn;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsS3Data::class, 'Google_Service_Storagetransfer_AwsS3Data');
