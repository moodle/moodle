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

namespace Google\Service\NetworkServices;

class AuthzExtension extends \Google\Collection
{
  /**
   * Default value. Do not use.
   */
  public const LOAD_BALANCING_SCHEME_LOAD_BALANCING_SCHEME_UNSPECIFIED = 'LOAD_BALANCING_SCHEME_UNSPECIFIED';
  /**
   * Signifies that this is used for Internal HTTP(S) Load Balancing.
   */
  public const LOAD_BALANCING_SCHEME_INTERNAL_MANAGED = 'INTERNAL_MANAGED';
  /**
   * Signifies that this is used for External Managed HTTP(S) Load Balancing.
   */
  public const LOAD_BALANCING_SCHEME_EXTERNAL_MANAGED = 'EXTERNAL_MANAGED';
  /**
   * Not specified.
   */
  public const WIRE_FORMAT_WIRE_FORMAT_UNSPECIFIED = 'WIRE_FORMAT_UNSPECIFIED';
  /**
   * The extension service uses ext_proc gRPC API over a gRPC stream. This is
   * the default value if the wire format is not specified. The backend service
   * for the extension must use HTTP2 or H2C as the protocol. All
   * `supported_events` for a client request are sent as part of the same gRPC
   * stream.
   */
  public const WIRE_FORMAT_EXT_PROC_GRPC = 'EXT_PROC_GRPC';
  /**
   * The extension service uses Envoy's `ext_authz` gRPC API. The backend
   * service for the extension must use HTTP2 or H2C as the protocol.
   * `EXT_AUTHZ_GRPC` is only supported for regional `AuthzExtension` resources.
   */
  public const WIRE_FORMAT_EXT_AUTHZ_GRPC = 'EXT_AUTHZ_GRPC';
  protected $collection_key = 'forwardHeaders';
  /**
   * Required. The `:authority` header in the gRPC request sent from Envoy to
   * the extension service.
   *
   * @var string
   */
  public $authority;
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. A human-readable description of the resource.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Determines how the proxy behaves if the call to the extension
   * fails or times out. When set to `TRUE`, request or response processing
   * continues without error. Any subsequent extensions in the extension chain
   * are also executed. When set to `FALSE` or the default setting of `FALSE` is
   * used, one of the following happens: * If response headers have not been
   * delivered to the downstream client, a generic 500 error is returned to the
   * client. The error response can be tailored by configuring a custom error
   * response in the load balancer. * If response headers have been delivered,
   * then the HTTP stream to the downstream client is reset.
   *
   * @var bool
   */
  public $failOpen;
  /**
   * Optional. List of the HTTP headers to forward to the extension (from the
   * client). If omitted, all headers are sent. Each element is a string
   * indicating the header name.
   *
   * @var string[]
   */
  public $forwardHeaders;
  /**
   * Optional. Set of labels associated with the `AuthzExtension` resource. The
   * format must comply with [the requirements for
   * labels](/compute/docs/labeling-resources#requirements) for Google Cloud
   * resources.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. All backend services and forwarding rules referenced by this
   * extension must share the same load balancing scheme. Supported values:
   * `INTERNAL_MANAGED`, `EXTERNAL_MANAGED`. For more information, refer to
   * [Backend services overview](https://cloud.google.com/load-
   * balancing/docs/backend-service).
   *
   * @var string
   */
  public $loadBalancingScheme;
  /**
   * Optional. The metadata provided here is included as part of the
   * `metadata_context` (of type `google.protobuf.Struct`) in the
   * `ProcessingRequest` message sent to the extension server. The metadata is
   * available under the namespace `com.google.authz_extension.`. The following
   * variables are supported in the metadata Struct: `{forwarding_rule_id}` -
   * substituted with the forwarding rule's fully qualified resource name.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Required. Identifier. Name of the `AuthzExtension` resource in the
   * following format: `projects/{project}/locations/{location}/authzExtensions/
   * {authz_extension}`.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The reference to the service that runs the extension. To
   * configure a callout extension, `service` must be a fully-qualified
   * reference to a [backend service](https://cloud.google.com/compute/docs/refe
   * rence/rest/v1/backendServices) in the format: `https://www.googleapis.com/c
   * ompute/v1/projects/{project}/regions/{region}/backendServices/{backendServi
   * ce}` or `https://www.googleapis.com/compute/v1/projects/{project}/global/ba
   * ckendServices/{backendService}`.
   *
   * @var string
   */
  public $service;
  /**
   * Required. Specifies the timeout for each individual message on the stream.
   * The timeout must be between 10-10000 milliseconds.
   *
   * @var string
   */
  public $timeout;
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Optional. The format of communication supported by the callout extension.
   * This field is supported only for regional `AuthzExtension` resources. If
   * not specified, the default value `EXT_PROC_GRPC` is used. Global
   * `AuthzExtension` resources use the `EXT_PROC_GRPC` wire format.
   *
   * @var string
   */
  public $wireFormat;

  /**
   * Required. The `:authority` header in the gRPC request sent from Envoy to
   * the extension service.
   *
   * @param string $authority
   */
  public function setAuthority($authority)
  {
    $this->authority = $authority;
  }
  /**
   * @return string
   */
  public function getAuthority()
  {
    return $this->authority;
  }
  /**
   * Output only. The timestamp when the resource was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. A human-readable description of the resource.
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
   * Optional. Determines how the proxy behaves if the call to the extension
   * fails or times out. When set to `TRUE`, request or response processing
   * continues without error. Any subsequent extensions in the extension chain
   * are also executed. When set to `FALSE` or the default setting of `FALSE` is
   * used, one of the following happens: * If response headers have not been
   * delivered to the downstream client, a generic 500 error is returned to the
   * client. The error response can be tailored by configuring a custom error
   * response in the load balancer. * If response headers have been delivered,
   * then the HTTP stream to the downstream client is reset.
   *
   * @param bool $failOpen
   */
  public function setFailOpen($failOpen)
  {
    $this->failOpen = $failOpen;
  }
  /**
   * @return bool
   */
  public function getFailOpen()
  {
    return $this->failOpen;
  }
  /**
   * Optional. List of the HTTP headers to forward to the extension (from the
   * client). If omitted, all headers are sent. Each element is a string
   * indicating the header name.
   *
   * @param string[] $forwardHeaders
   */
  public function setForwardHeaders($forwardHeaders)
  {
    $this->forwardHeaders = $forwardHeaders;
  }
  /**
   * @return string[]
   */
  public function getForwardHeaders()
  {
    return $this->forwardHeaders;
  }
  /**
   * Optional. Set of labels associated with the `AuthzExtension` resource. The
   * format must comply with [the requirements for
   * labels](/compute/docs/labeling-resources#requirements) for Google Cloud
   * resources.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Required. All backend services and forwarding rules referenced by this
   * extension must share the same load balancing scheme. Supported values:
   * `INTERNAL_MANAGED`, `EXTERNAL_MANAGED`. For more information, refer to
   * [Backend services overview](https://cloud.google.com/load-
   * balancing/docs/backend-service).
   *
   * Accepted values: LOAD_BALANCING_SCHEME_UNSPECIFIED, INTERNAL_MANAGED,
   * EXTERNAL_MANAGED
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
   * Optional. The metadata provided here is included as part of the
   * `metadata_context` (of type `google.protobuf.Struct`) in the
   * `ProcessingRequest` message sent to the extension server. The metadata is
   * available under the namespace `com.google.authz_extension.`. The following
   * variables are supported in the metadata Struct: `{forwarding_rule_id}` -
   * substituted with the forwarding rule's fully qualified resource name.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Required. Identifier. Name of the `AuthzExtension` resource in the
   * following format: `projects/{project}/locations/{location}/authzExtensions/
   * {authz_extension}`.
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
   * Required. The reference to the service that runs the extension. To
   * configure a callout extension, `service` must be a fully-qualified
   * reference to a [backend service](https://cloud.google.com/compute/docs/refe
   * rence/rest/v1/backendServices) in the format: `https://www.googleapis.com/c
   * ompute/v1/projects/{project}/regions/{region}/backendServices/{backendServi
   * ce}` or `https://www.googleapis.com/compute/v1/projects/{project}/global/ba
   * ckendServices/{backendService}`.
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
   * Required. Specifies the timeout for each individual message on the stream.
   * The timeout must be between 10-10000 milliseconds.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
  /**
   * Output only. The timestamp when the resource was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Optional. The format of communication supported by the callout extension.
   * This field is supported only for regional `AuthzExtension` resources. If
   * not specified, the default value `EXT_PROC_GRPC` is used. Global
   * `AuthzExtension` resources use the `EXT_PROC_GRPC` wire format.
   *
   * Accepted values: WIRE_FORMAT_UNSPECIFIED, EXT_PROC_GRPC, EXT_AUTHZ_GRPC
   *
   * @param self::WIRE_FORMAT_* $wireFormat
   */
  public function setWireFormat($wireFormat)
  {
    $this->wireFormat = $wireFormat;
  }
  /**
   * @return self::WIRE_FORMAT_*
   */
  public function getWireFormat()
  {
    return $this->wireFormat;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuthzExtension::class, 'Google_Service_NetworkServices_AuthzExtension');
