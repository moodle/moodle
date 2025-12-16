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

class TargetSslProxy extends \Google\Collection
{
  public const PROXY_HEADER_NONE = 'NONE';
  public const PROXY_HEADER_PROXY_V1 = 'PROXY_V1';
  protected $collection_key = 'sslCertificates';
  /**
   * URL of a certificate map that identifies a certificate map associated with
   * the given target proxy. This field can only be set for global target
   * proxies. If set, sslCertificates will be ignored.
   *
   *  Accepted format is//certificatemanager.googleapis.com/projects/{project}/l
   * ocations/{location}/certificateMaps/{resourceName}.
   *
   * @var string
   */
  public $certificateMap;
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
   *
   * @var string
   */
  public $creationTimestamp;
  /**
   * An optional description of this resource. Provide this property when you
   * create the resource.
   *
   * @var string
   */
  public $description;
  /**
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#targetSslProxy for target SSL proxies.
   *
   * @var string
   */
  public $kind;
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
  /**
   * Specifies the type of proxy header to append before sending data to the
   * backend, either NONE or PROXY_V1. The default is NONE.
   *
   * @var string
   */
  public $proxyHeader;
  /**
   * [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * URL to the BackendService resource.
   *
   * @var string
   */
  public $service;
  /**
   * URLs to SslCertificate resources that are used to authenticate connections
   * to Backends. At least one SSL certificate must be specified. Currently, you
   * may specify up to 15 SSL certificates. sslCertificates do not apply when
   * the load balancing scheme is set to INTERNAL_SELF_MANAGED.
   *
   * @var string[]
   */
  public $sslCertificates;
  /**
   * URL of SslPolicy resource that will be associated with the TargetSslProxy
   * resource. If not set, the TargetSslProxy resource will not have any SSL
   * policy configured.
   *
   * @var string
   */
  public $sslPolicy;

  /**
   * URL of a certificate map that identifies a certificate map associated with
   * the given target proxy. This field can only be set for global target
   * proxies. If set, sslCertificates will be ignored.
   *
   *  Accepted format is//certificatemanager.googleapis.com/projects/{project}/l
   * ocations/{location}/certificateMaps/{resourceName}.
   *
   * @param string $certificateMap
   */
  public function setCertificateMap($certificateMap)
  {
    $this->certificateMap = $certificateMap;
  }
  /**
   * @return string
   */
  public function getCertificateMap()
  {
    return $this->certificateMap;
  }
  /**
   * Output only. [Output Only] Creation timestamp inRFC3339 text format.
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
   * An optional description of this resource. Provide this property when you
   * create the resource.
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
   * [Output Only] The unique identifier for the resource. This identifier is
   * defined by the server.
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
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#targetSslProxy for target SSL proxies.
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
   * Specifies the type of proxy header to append before sending data to the
   * backend, either NONE or PROXY_V1. The default is NONE.
   *
   * Accepted values: NONE, PROXY_V1
   *
   * @param self::PROXY_HEADER_* $proxyHeader
   */
  public function setProxyHeader($proxyHeader)
  {
    $this->proxyHeader = $proxyHeader;
  }
  /**
   * @return self::PROXY_HEADER_*
   */
  public function getProxyHeader()
  {
    return $this->proxyHeader;
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
   * URL to the BackendService resource.
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
   * URLs to SslCertificate resources that are used to authenticate connections
   * to Backends. At least one SSL certificate must be specified. Currently, you
   * may specify up to 15 SSL certificates. sslCertificates do not apply when
   * the load balancing scheme is set to INTERNAL_SELF_MANAGED.
   *
   * @param string[] $sslCertificates
   */
  public function setSslCertificates($sslCertificates)
  {
    $this->sslCertificates = $sslCertificates;
  }
  /**
   * @return string[]
   */
  public function getSslCertificates()
  {
    return $this->sslCertificates;
  }
  /**
   * URL of SslPolicy resource that will be associated with the TargetSslProxy
   * resource. If not set, the TargetSslProxy resource will not have any SSL
   * policy configured.
   *
   * @param string $sslPolicy
   */
  public function setSslPolicy($sslPolicy)
  {
    $this->sslPolicy = $sslPolicy;
  }
  /**
   * @return string
   */
  public function getSslPolicy()
  {
    return $this->sslPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetSslProxy::class, 'Google_Service_Compute_TargetSslProxy');
