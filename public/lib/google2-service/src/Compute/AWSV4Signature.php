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

namespace Google\Service\Compute;

class AWSV4Signature extends \Google\Model
{
  /**
   * The access key used for s3 bucket authentication. Required for updating or
   * creating a backend that uses AWS v4 signature authentication, but will not
   * be returned as part of the configuration when queried with a REST API GET
   * request.
   *
   * @InputOnly
   *
   * @var string
   */
  public $accessKey;
  /**
   * The identifier of an access key used for s3 bucket authentication.
   *
   * @var string
   */
  public $accessKeyId;
  /**
   * The optional version identifier for the access key. You can use this to
   * keep track of different iterations of your access key.
   *
   * @var string
   */
  public $accessKeyVersion;
  /**
   * The name of the cloud region of your origin. This is a free-form field with
   * the name of the region your cloud uses to host your origin.  For example,
   * "us-east-1" for AWS or "us-ashburn-1" for OCI.
   *
   * @var string
   */
  public $originRegion;

  /**
   * The access key used for s3 bucket authentication. Required for updating or
   * creating a backend that uses AWS v4 signature authentication, but will not
   * be returned as part of the configuration when queried with a REST API GET
   * request.
   *
   * @InputOnly
   *
   * @param string $accessKey
   */
  public function setAccessKey($accessKey)
  {
    $this->accessKey = $accessKey;
  }
  /**
   * @return string
   */
  public function getAccessKey()
  {
    return $this->accessKey;
  }
  /**
   * The identifier of an access key used for s3 bucket authentication.
   *
   * @param string $accessKeyId
   */
  public function setAccessKeyId($accessKeyId)
  {
    $this->accessKeyId = $accessKeyId;
  }
  /**
   * @return string
   */
  public function getAccessKeyId()
  {
    return $this->accessKeyId;
  }
  /**
   * The optional version identifier for the access key. You can use this to
   * keep track of different iterations of your access key.
   *
   * @param string $accessKeyVersion
   */
  public function setAccessKeyVersion($accessKeyVersion)
  {
    $this->accessKeyVersion = $accessKeyVersion;
  }
  /**
   * @return string
   */
  public function getAccessKeyVersion()
  {
    return $this->accessKeyVersion;
  }
  /**
   * The name of the cloud region of your origin. This is a free-form field with
   * the name of the region your cloud uses to host your origin.  For example,
   * "us-east-1" for AWS or "us-ashburn-1" for OCI.
   *
   * @param string $originRegion
   */
  public function setOriginRegion($originRegion)
  {
    $this->originRegion = $originRegion;
  }
  /**
   * @return string
   */
  public function getOriginRegion()
  {
    return $this->originRegion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AWSV4Signature::class, 'Google_Service_Compute_AWSV4Signature');
