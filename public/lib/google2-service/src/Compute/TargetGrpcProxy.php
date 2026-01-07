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

class TargetGrpcProxy extends \Google\Model
{
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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a TargetGrpcProxy. An up-to-date fingerprint must be provided in
   * order to patch/update the TargetGrpcProxy; otherwise, the request will fail
   * with error 412 conditionNotMet. To see the latest fingerprint, make a get()
   * request to retrieve the TargetGrpcProxy.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. [Output Only] Type of the resource.
   * Alwayscompute#targetGrpcProxy for target grpc proxies.
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
   * Output only. [Output Only] Server-defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;
  /**
   * Output only. [Output Only] Server-defined URL with id for the resource.
   *
   * @var string
   */
  public $selfLinkWithId;
  /**
   * URL to the UrlMap resource that defines the mapping from URL to the
   * BackendService. The protocol field in the BackendService must be set to
   * GRPC.
   *
   * @var string
   */
  public $urlMap;
  /**
   * If true, indicates that the BackendServices referenced by the urlMap may be
   * accessed by gRPC applications without using a sidecar proxy. This will
   * enable configuration checks on urlMap and its referenced BackendServices to
   * not allow unsupported features. A gRPC application must use "xds:" scheme
   * in the target URI of the service it is connecting to. If false, indicates
   * that the BackendServices referenced by the urlMap will be accessed by gRPC
   * applications via a sidecar proxy. In this case, a gRPC application must not
   * use "xds:" scheme in the target URI of the service it is connecting to
   *
   * @var bool
   */
  public $validateForProxyless;

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
   * Fingerprint of this resource. A hash of the contents stored in this object.
   * This field is used in optimistic locking. This field will be ignored when
   * inserting a TargetGrpcProxy. An up-to-date fingerprint must be provided in
   * order to patch/update the TargetGrpcProxy; otherwise, the request will fail
   * with error 412 conditionNotMet. To see the latest fingerprint, make a get()
   * request to retrieve the TargetGrpcProxy.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Output only. [Output Only] The unique identifier for the resource type. The
   * server generates this identifier.
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
   * Alwayscompute#targetGrpcProxy for target grpc proxies.
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
   * Output only. [Output Only] Server-defined URL for the resource.
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
   * Output only. [Output Only] Server-defined URL with id for the resource.
   *
   * @param string $selfLinkWithId
   */
  public function setSelfLinkWithId($selfLinkWithId)
  {
    $this->selfLinkWithId = $selfLinkWithId;
  }
  /**
   * @return string
   */
  public function getSelfLinkWithId()
  {
    return $this->selfLinkWithId;
  }
  /**
   * URL to the UrlMap resource that defines the mapping from URL to the
   * BackendService. The protocol field in the BackendService must be set to
   * GRPC.
   *
   * @param string $urlMap
   */
  public function setUrlMap($urlMap)
  {
    $this->urlMap = $urlMap;
  }
  /**
   * @return string
   */
  public function getUrlMap()
  {
    return $this->urlMap;
  }
  /**
   * If true, indicates that the BackendServices referenced by the urlMap may be
   * accessed by gRPC applications without using a sidecar proxy. This will
   * enable configuration checks on urlMap and its referenced BackendServices to
   * not allow unsupported features. A gRPC application must use "xds:" scheme
   * in the target URI of the service it is connecting to. If false, indicates
   * that the BackendServices referenced by the urlMap will be accessed by gRPC
   * applications via a sidecar proxy. In this case, a gRPC application must not
   * use "xds:" scheme in the target URI of the service it is connecting to
   *
   * @param bool $validateForProxyless
   */
  public function setValidateForProxyless($validateForProxyless)
  {
    $this->validateForProxyless = $validateForProxyless;
  }
  /**
   * @return bool
   */
  public function getValidateForProxyless()
  {
    return $this->validateForProxyless;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetGrpcProxy::class, 'Google_Service_Compute_TargetGrpcProxy');
