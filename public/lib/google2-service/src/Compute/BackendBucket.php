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

class BackendBucket extends \Google\Collection
{
  /**
   * Automatically uses the best compression based on the Accept-Encoding header
   * sent by the client.
   */
  public const COMPRESSION_MODE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Disables compression. Existing compressed responses cached by Cloud CDN
   * will not be served to clients.
   */
  public const COMPRESSION_MODE_DISABLED = 'DISABLED';
  /**
   * Signifies that this will be used for internal Application Load Balancers.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_MANAGED = 'INTERNAL_MANAGED';
  protected $collection_key = 'usedBy';
  /**
   * Cloud Storage bucket name.
   *
   * @var string
   */
  public $bucketName;
  protected $cdnPolicyType = BackendBucketCdnPolicy::class;
  protected $cdnPolicyDataType = '';
  /**
   * Compress text responses using Brotli or gzip compression, based on the
   * client's Accept-Encoding header.
   *
   * @var string
   */
  public $compressionMode;
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * Headers that the Application Load Balancer should add to proxied responses.
   *
   * @var string[]
   */
  public $customResponseHeaders;
  /**
   * An optional textual description of the resource; provided by the client
   * when the resource is created.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The resource URL for the edge security policy associated with
   * this backend bucket.
   *
   * @var string
   */
  public $edgeSecurityPolicy;
  /**
   * If true, enable Cloud CDN for this BackendBucket.
   *
   * @var bool
   */
  public $enableCdn;
  /**
   * [Output Only] Unique identifier for the resource; defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Type of the resource.
   *
   * @var string
   */
  public $kind;
  /**
   * The value can only be INTERNAL_MANAGED for cross-region internal layer 7
   * load balancer.
   *
   * If loadBalancingScheme is not specified, the backend bucket can be used by
   * classic global external load balancers, or global application external load
   * balancers, or both.
   *
   * @var string
   */
  public $loadBalancingScheme;
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
   *
   * @var string
   */
  public $name;
  protected $paramsType = BackendBucketParams::class;
  protected $paramsDataType = '';
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  protected $usedByType = BackendBucketUsedBy::class;
  protected $usedByDataType = 'array';

  /**
   * Cloud Storage bucket name.
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
   * Cloud CDN configuration for this BackendBucket.
   *
   * @param BackendBucketCdnPolicy $cdnPolicy
   */
  public function setCdnPolicy(BackendBucketCdnPolicy $cdnPolicy)
  {
    $this->cdnPolicy = $cdnPolicy;
  }
  /**
   * @return BackendBucketCdnPolicy
   */
  public function getCdnPolicy()
  {
    return $this->cdnPolicy;
  }
  /**
   * Compress text responses using Brotli or gzip compression, based on the
   * client's Accept-Encoding header.
   *
   * Accepted values: AUTOMATIC, DISABLED
   *
   * @param self::COMPRESSION_MODE_* $compressionMode
   */
  public function setCompressionMode($compressionMode)
  {
    $this->compressionMode = $compressionMode;
  }
  /**
   * @return self::COMPRESSION_MODE_*
   */
  public function getCompressionMode()
  {
    return $this->compressionMode;
  }
  /**
   * [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @param string $creationTimestamp
   */
  public function setCreationTimestamp($creationTimestamp)
  {
    $this->creationTimestamp = $creationTimestamp;
  }
  /**
   * @return string
   */
  public function getCreationTimestamp()
  {
    return $this->creationTimestamp;
  }
  /**
   * Headers that the Application Load Balancer should add to proxied responses.
   *
   * @param string[] $customResponseHeaders
   */
  public function setCustomResponseHeaders($customResponseHeaders)
  {
    $this->customResponseHeaders = $customResponseHeaders;
  }
  /**
   * @return string[]
   */
  public function getCustomResponseHeaders()
  {
    return $this->customResponseHeaders;
  }
  /**
   * An optional textual description of the resource; provided by the client
   * when the resource is created.
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
   * [Output Only] The resource URL for the edge security policy associated with
   * this backend bucket.
   *
   * @param string $edgeSecurityPolicy
   */
  public function setEdgeSecurityPolicy($edgeSecurityPolicy)
  {
    $this->edgeSecurityPolicy = $edgeSecurityPolicy;
  }
  /**
   * @return string
   */
  public function getEdgeSecurityPolicy()
  {
    return $this->edgeSecurityPolicy;
  }
  /**
   * If true, enable Cloud CDN for this BackendBucket.
   *
   * @param bool $enableCdn
   */
  public function setEnableCdn($enableCdn)
  {
    $this->enableCdn = $enableCdn;
  }
  /**
   * @return bool
   */
  public function getEnableCdn()
  {
    return $this->enableCdn;
  }
  /**
   * [Output Only] Unique identifier for the resource; defined by the server.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Type of the resource.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The value can only be INTERNAL_MANAGED for cross-region internal layer 7
   * load balancer.
   *
   * If loadBalancingScheme is not specified, the backend bucket can be used by
   * classic global external load balancers, or global application external load
   * balancers, or both.
   *
   * Accepted values: INTERNAL_MANAGED
   *
   * @param self::LOAD_BALANCING_SCHEME_* $loadBalancingScheme
   */
  public function setLoadBalancingScheme($loadBalancingScheme)
  {
    $this->loadBalancingScheme = $loadBalancingScheme;
  }
  /**
   * @return self::LOAD_BALANCING_SCHEME_*
   */
  public function getLoadBalancingScheme()
  {
    return $this->loadBalancingScheme;
  }
  /**
   * Name of the resource. Provided by the client when the resource is created.
   * The name must be 1-63 characters long, and comply withRFC1035.
   * Specifically, the name must be 1-63 characters long and match the regular
   * expression `[a-z]([-a-z0-9]*[a-z0-9])?` which means the first character
   * must be a lowercase letter, and all following characters must be a dash,
   * lowercase letter, or digit, except the last character, which cannot be a
   * dash.
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
   * Input only. [Input Only] Additional params passed with the request, but not
   * persisted as part of resource payload.
   *
   * @param BackendBucketParams $params
   */
  public function setParams(BackendBucketParams $params)
  {
    $this->params = $params;
  }
  /**
   * @return BackendBucketParams
   */
  public function getParams()
  {
    return $this->params;
  }
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * Output only. [Output Only] List of resources referencing that backend
   * bucket.
   *
   * @param BackendBucketUsedBy[] $usedBy
   */
  public function setUsedBy($usedBy)
  {
    $this->usedBy = $usedBy;
  }
  /**
   * @return BackendBucketUsedBy[]
   */
  public function getUsedBy()
  {
    return $this->usedBy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BackendBucket::class, 'Google_Service_Compute_BackendBucket');
