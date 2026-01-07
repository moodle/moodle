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

class ExtensionChainExtension extends \Google\Collection
{
  /**
   * Default value. Do not use.
   */
  public const REQUEST_BODY_SEND_MODE_BODY_SEND_MODE_UNSPECIFIED = 'BODY_SEND_MODE_UNSPECIFIED';
  /**
   * Calls to the extension are executed in the streamed mode. Subsequent chunks
   * will be sent only after the previous chunks have been processed. The
   * content of the body chunks is sent one way to the extension. Extension may
   * send modified chunks back. This is the default value if the processing mode
   * is not specified.
   */
  public const REQUEST_BODY_SEND_MODE_BODY_SEND_MODE_STREAMED = 'BODY_SEND_MODE_STREAMED';
  /**
   * Calls are executed in the full duplex mode. Subsequent chunks will be sent
   * for processing without waiting for the response for the previous chunk or
   * for the response for `REQUEST_HEADERS` event. Extension can freely modify
   * or chunk the body contents. If the extension doesn't send the body contents
   * back, the next extension in the chain or the upstream will receive an empty
   * body.
   */
  public const REQUEST_BODY_SEND_MODE_BODY_SEND_MODE_FULL_DUPLEX_STREAMED = 'BODY_SEND_MODE_FULL_DUPLEX_STREAMED';
  /**
   * Default value. Do not use.
   */
  public const RESPONSE_BODY_SEND_MODE_BODY_SEND_MODE_UNSPECIFIED = 'BODY_SEND_MODE_UNSPECIFIED';
  /**
   * Calls to the extension are executed in the streamed mode. Subsequent chunks
   * will be sent only after the previous chunks have been processed. The
   * content of the body chunks is sent one way to the extension. Extension may
   * send modified chunks back. This is the default value if the processing mode
   * is not specified.
   */
  public const RESPONSE_BODY_SEND_MODE_BODY_SEND_MODE_STREAMED = 'BODY_SEND_MODE_STREAMED';
  /**
   * Calls are executed in the full duplex mode. Subsequent chunks will be sent
   * for processing without waiting for the response for the previous chunk or
   * for the response for `REQUEST_HEADERS` event. Extension can freely modify
   * or chunk the body contents. If the extension doesn't send the body contents
   * back, the next extension in the chain or the upstream will receive an empty
   * body.
   */
  public const RESPONSE_BODY_SEND_MODE_BODY_SEND_MODE_FULL_DUPLEX_STREAMED = 'BODY_SEND_MODE_FULL_DUPLEX_STREAMED';
  protected $collection_key = 'supportedEvents';
  /**
   * Optional. The `:authority` header in the gRPC request sent from Envoy to
   * the extension service. Required for Callout extensions. This field is not
   * supported for plugin extensions. Setting it results in a validation error.
   *
   * @var string
   */
  public $authority;
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
   * client or backend). If omitted, all headers are sent. Each element is a
   * string indicating the header name.
   *
   * @var string[]
   */
  public $forwardHeaders;
  /**
   * Optional. The metadata provided here is included as part of the
   * `metadata_context` (of type `google.protobuf.Struct`) in the
   * `ProcessingRequest` message sent to the extension server. For
   * `AuthzExtension` resources, the metadata is available under the namespace
   * `com.google.authz_extension.`. For other types of extensions, the metadata
   * is available under the namespace `com.google....`. For example:
   * `com.google.lb_traffic_extension.lbtrafficextension1.chain1.ext1`. The
   * following variables are supported in the metadata: `{forwarding_rule_id}` -
   * substituted with the forwarding rule's fully qualified resource name. This
   * field must not be set for plugin extensions. Setting it results in a
   * validation error. You can set metadata at either the resource level or the
   * extension level. The extension level metadata is recommended because you
   * can pass a different set of metadata through each extension to the backend.
   * This field is subject to following limitations: * The total size of the
   * metadata must be less than 1KiB. * The total number of keys in the metadata
   * must be less than 16. * The length of each key must be less than 64
   * characters. * The length of each value must be less than 1024 characters. *
   * All values must be strings.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * Optional. The name for this extension. The name is logged as part of the
   * HTTP request logs. The name must conform with RFC-1034, is restricted to
   * lower-cased letters, numbers and hyphens, and can have a maximum length of
   * 63 characters. Additionally, the first character must be a letter and the
   * last a letter or a number. This field is required except for
   * AuthzExtension.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. When set to `TRUE`, enables `observability_mode` on the
   * `ext_proc` filter. This makes `ext_proc` calls asynchronous. Envoy doesn't
   * check for the response from `ext_proc` calls. For more information about
   * the filter, see: https://www.envoyproxy.io/docs/envoy/v1.32.3/api-
   * v3/extensions/filters/http/ext_proc/v3/ext_proc.proto#extensions-filters-
   * http-ext-proc-v3-externalprocessor This field is helpful when you want to
   * try out the extension in async log-only mode. Supported by regional
   * `LbTrafficExtension` and `LbRouteExtension` resources. Only `STREAMED`
   * (default) body processing mode is supported.
   *
   * @var bool
   */
  public $observabilityMode;
  /**
   * Optional. Configures the send mode for request body processing. The field
   * can only be set if `supported_events` includes `REQUEST_BODY`. If
   * `supported_events` includes `REQUEST_BODY`, but `request_body_send_mode` is
   * unset, the default value `STREAMED` is used. When this field is set to
   * `FULL_DUPLEX_STREAMED`, `supported_events` must include both `REQUEST_BODY`
   * and `REQUEST_TRAILERS`. This field can be set only for `LbTrafficExtension`
   * and `LbRouteExtension` resources, and only when the `service` field of the
   * extension points to a `BackendService`. Only `FULL_DUPLEX_STREAMED` mode is
   * supported for `LbRouteExtension` resources.
   *
   * @var string
   */
  public $requestBodySendMode;
  /**
   * Optional. Configures the send mode for response processing. If unspecified,
   * the default value `STREAMED` is used. The field can only be set if
   * `supported_events` includes `RESPONSE_BODY`. If `supported_events` includes
   * `RESPONSE_BODY`, but `response_body_send_mode` is unset, the default value
   * `STREAMED` is used. When this field is set to `FULL_DUPLEX_STREAMED`,
   * `supported_events` must include both `RESPONSE_BODY` and
   * `RESPONSE_TRAILERS`. This field can be set only for `LbTrafficExtension`
   * resources, and only when the `service` field of the extension points to a
   * `BackendService`.
   *
   * @var string
   */
  public $responseBodySendMode;
  /**
   * Required. The reference to the service that runs the extension. To
   * configure a callout extension, `service` must be a fully-qualified
   * reference to a [backend service](https://cloud.google.com/compute/docs/refe
   * rence/rest/v1/backendServices) in the format: `https://www.googleapis.com/c
   * ompute/v1/projects/{project}/regions/{region}/backendServices/{backendServi
   * ce}` or `https://www.googleapis.com/compute/v1/projects/{project}/global/ba
   * ckendServices/{backendService}`. To configure a plugin extension, `service`
   * must be a reference to a [`WasmPlugin`
   * resource](https://cloud.google.com/service-
   * extensions/docs/reference/rest/v1beta1/projects.locations.wasmPlugins) in
   * the format: `projects/{project}/locations/{location}/wasmPlugins/{plugin}`
   * or `//networkservices.googleapis.com/projects/{project}/locations/{location
   * }/wasmPlugins/{wasmPlugin}`. Plugin extensions are currently supported for
   * the `LbTrafficExtension`, the `LbRouteExtension`, and the `LbEdgeExtension`
   * resources.
   *
   * @var string
   */
  public $service;
  /**
   * Optional. A set of events during request or response processing for which
   * this extension is called. For the `LbTrafficExtension` resource, this field
   * is required. For the `LbRouteExtension` resource, this field is optional.
   * If unspecified, `REQUEST_HEADERS` event is assumed as supported. For the
   * `LbEdgeExtension` resource, this field is required and must only contain
   * `REQUEST_HEADERS` event. For the `AuthzExtension` resource, this field is
   * optional. `REQUEST_HEADERS` is the only supported event. If unspecified,
   * `REQUEST_HEADERS` event is assumed as supported.
   *
   * @var string[]
   */
  public $supportedEvents;
  /**
   * Optional. Specifies the timeout for each individual message on the stream.
   * The timeout must be between `10`-`10000` milliseconds. Required for callout
   * extensions. This field is not supported for plugin extensions. Setting it
   * results in a validation error.
   *
   * @var string
   */
  public $timeout;

  /**
   * Optional. The `:authority` header in the gRPC request sent from Envoy to
   * the extension service. Required for Callout extensions. This field is not
   * supported for plugin extensions. Setting it results in a validation error.
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
   * client or backend). If omitted, all headers are sent. Each element is a
   * string indicating the header name.
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
   * Optional. The metadata provided here is included as part of the
   * `metadata_context` (of type `google.protobuf.Struct`) in the
   * `ProcessingRequest` message sent to the extension server. For
   * `AuthzExtension` resources, the metadata is available under the namespace
   * `com.google.authz_extension.`. For other types of extensions, the metadata
   * is available under the namespace `com.google....`. For example:
   * `com.google.lb_traffic_extension.lbtrafficextension1.chain1.ext1`. The
   * following variables are supported in the metadata: `{forwarding_rule_id}` -
   * substituted with the forwarding rule's fully qualified resource name. This
   * field must not be set for plugin extensions. Setting it results in a
   * validation error. You can set metadata at either the resource level or the
   * extension level. The extension level metadata is recommended because you
   * can pass a different set of metadata through each extension to the backend.
   * This field is subject to following limitations: * The total size of the
   * metadata must be less than 1KiB. * The total number of keys in the metadata
   * must be less than 16. * The length of each key must be less than 64
   * characters. * The length of each value must be less than 1024 characters. *
   * All values must be strings.
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
   * Optional. The name for this extension. The name is logged as part of the
   * HTTP request logs. The name must conform with RFC-1034, is restricted to
   * lower-cased letters, numbers and hyphens, and can have a maximum length of
   * 63 characters. Additionally, the first character must be a letter and the
   * last a letter or a number. This field is required except for
   * AuthzExtension.
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
   * Optional. When set to `TRUE`, enables `observability_mode` on the
   * `ext_proc` filter. This makes `ext_proc` calls asynchronous. Envoy doesn't
   * check for the response from `ext_proc` calls. For more information about
   * the filter, see: https://www.envoyproxy.io/docs/envoy/v1.32.3/api-
   * v3/extensions/filters/http/ext_proc/v3/ext_proc.proto#extensions-filters-
   * http-ext-proc-v3-externalprocessor This field is helpful when you want to
   * try out the extension in async log-only mode. Supported by regional
   * `LbTrafficExtension` and `LbRouteExtension` resources. Only `STREAMED`
   * (default) body processing mode is supported.
   *
   * @param bool $observabilityMode
   */
  public function setObservabilityMode($observabilityMode)
  {
    $this->observabilityMode = $observabilityMode;
  }
  /**
   * @return bool
   */
  public function getObservabilityMode()
  {
    return $this->observabilityMode;
  }
  /**
   * Optional. Configures the send mode for request body processing. The field
   * can only be set if `supported_events` includes `REQUEST_BODY`. If
   * `supported_events` includes `REQUEST_BODY`, but `request_body_send_mode` is
   * unset, the default value `STREAMED` is used. When this field is set to
   * `FULL_DUPLEX_STREAMED`, `supported_events` must include both `REQUEST_BODY`
   * and `REQUEST_TRAILERS`. This field can be set only for `LbTrafficExtension`
   * and `LbRouteExtension` resources, and only when the `service` field of the
   * extension points to a `BackendService`. Only `FULL_DUPLEX_STREAMED` mode is
   * supported for `LbRouteExtension` resources.
   *
   * Accepted values: BODY_SEND_MODE_UNSPECIFIED, BODY_SEND_MODE_STREAMED,
   * BODY_SEND_MODE_FULL_DUPLEX_STREAMED
   *
   * @param self::REQUEST_BODY_SEND_MODE_* $requestBodySendMode
   */
  public function setRequestBodySendMode($requestBodySendMode)
  {
    $this->requestBodySendMode = $requestBodySendMode;
  }
  /**
   * @return self::REQUEST_BODY_SEND_MODE_*
   */
  public function getRequestBodySendMode()
  {
    return $this->requestBodySendMode;
  }
  /**
   * Optional. Configures the send mode for response processing. If unspecified,
   * the default value `STREAMED` is used. The field can only be set if
   * `supported_events` includes `RESPONSE_BODY`. If `supported_events` includes
   * `RESPONSE_BODY`, but `response_body_send_mode` is unset, the default value
   * `STREAMED` is used. When this field is set to `FULL_DUPLEX_STREAMED`,
   * `supported_events` must include both `RESPONSE_BODY` and
   * `RESPONSE_TRAILERS`. This field can be set only for `LbTrafficExtension`
   * resources, and only when the `service` field of the extension points to a
   * `BackendService`.
   *
   * Accepted values: BODY_SEND_MODE_UNSPECIFIED, BODY_SEND_MODE_STREAMED,
   * BODY_SEND_MODE_FULL_DUPLEX_STREAMED
   *
   * @param self::RESPONSE_BODY_SEND_MODE_* $responseBodySendMode
   */
  public function setResponseBodySendMode($responseBodySendMode)
  {
    $this->responseBodySendMode = $responseBodySendMode;
  }
  /**
   * @return self::RESPONSE_BODY_SEND_MODE_*
   */
  public function getResponseBodySendMode()
  {
    return $this->responseBodySendMode;
  }
  /**
   * Required. The reference to the service that runs the extension. To
   * configure a callout extension, `service` must be a fully-qualified
   * reference to a [backend service](https://cloud.google.com/compute/docs/refe
   * rence/rest/v1/backendServices) in the format: `https://www.googleapis.com/c
   * ompute/v1/projects/{project}/regions/{region}/backendServices/{backendServi
   * ce}` or `https://www.googleapis.com/compute/v1/projects/{project}/global/ba
   * ckendServices/{backendService}`. To configure a plugin extension, `service`
   * must be a reference to a [`WasmPlugin`
   * resource](https://cloud.google.com/service-
   * extensions/docs/reference/rest/v1beta1/projects.locations.wasmPlugins) in
   * the format: `projects/{project}/locations/{location}/wasmPlugins/{plugin}`
   * or `//networkservices.googleapis.com/projects/{project}/locations/{location
   * }/wasmPlugins/{wasmPlugin}`. Plugin extensions are currently supported for
   * the `LbTrafficExtension`, the `LbRouteExtension`, and the `LbEdgeExtension`
   * resources.
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
   * Optional. A set of events during request or response processing for which
   * this extension is called. For the `LbTrafficExtension` resource, this field
   * is required. For the `LbRouteExtension` resource, this field is optional.
   * If unspecified, `REQUEST_HEADERS` event is assumed as supported. For the
   * `LbEdgeExtension` resource, this field is required and must only contain
   * `REQUEST_HEADERS` event. For the `AuthzExtension` resource, this field is
   * optional. `REQUEST_HEADERS` is the only supported event. If unspecified,
   * `REQUEST_HEADERS` event is assumed as supported.
   *
   * @param string[] $supportedEvents
   */
  public function setSupportedEvents($supportedEvents)
  {
    $this->supportedEvents = $supportedEvents;
  }
  /**
   * @return string[]
   */
  public function getSupportedEvents()
  {
    return $this->supportedEvents;
  }
  /**
   * Optional. Specifies the timeout for each individual message on the stream.
   * The timeout must be between `10`-`10000` milliseconds. Required for callout
   * extensions. This field is not supported for plugin extensions. Setting it
   * results in a validation error.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExtensionChainExtension::class, 'Google_Service_NetworkServices_ExtensionChainExtension');
