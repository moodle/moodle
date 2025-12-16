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

namespace Google\Service\Eventarc;

class GoogleCloudEventarcV1PipelineDestinationHttpEndpoint extends \Google\Model
{
  /**
   * Optional. The CEL expression used to modify how the destination-bound HTTP
   * request is constructed. If a binding expression is not specified here, the
   * message is treated as a CloudEvent and is mapped to the HTTP request
   * according to the CloudEvent HTTP Protocol Binding Binary Content Mode
   * (https://github.com/cloudevents/spec/blob/main/cloudevents/bindings/http-
   * protocol-binding.md#31-binary-content-mode). In this representation, all
   * fields except the `data` and `datacontenttype` field on the message are
   * mapped to HTTP request headers with a prefix of `ce-`. To construct the
   * HTTP request payload and the value of the content-type HTTP header, the
   * payload format is defined as follows: 1) Use the output_payload_format_type
   * on the Pipeline.Destination if it is set, else: 2) Use the
   * input_payload_format_type on the Pipeline if it is set, else: 3) Treat the
   * payload as opaque binary data. The `data` field of the message is converted
   * to the payload format or left as-is for case 3) and then attached as the
   * payload of the HTTP request. The `content-type` header on the HTTP request
   * is set to the payload format type or left empty for case 3). However, if a
   * mediation has updated the `datacontenttype` field on the message so that it
   * is not the same as the payload format type but it is still a prefix of the
   * payload format type, then the `content-type` header on the HTTP request is
   * set to this `datacontenttype` value. For example, if the `datacontenttype`
   * is "application/json" and the payload format type is "application/json;
   * charset=utf-8", then the `content-type` header on the HTTP request is set
   * to "application/json; charset=utf-8". If a non-empty binding expression is
   * specified then this expression is used to modify the default CloudEvent
   * HTTP Protocol Binding Binary Content representation. The result of the CEL
   * expression must be a map of key/value pairs which is used as follows: - If
   * a map named `headers` exists on the result of the expression, then its
   * key/value pairs are directly mapped to the HTTP request headers. The
   * headers values are constructed from the corresponding value type's
   * canonical representation. If the `headers` field doesn't exist then the
   * resulting HTTP request will be the headers of the CloudEvent HTTP Binding
   * Binary Content Mode representation of the final message. Note: If the
   * specified binding expression, has updated the `datacontenttype` field on
   * the message so that it is not the same as the payload format type but it is
   * still a prefix of the payload format type, then the `content-type` header
   * in the `headers` map is set to this `datacontenttype` value. - If a field
   * named `body` exists on the result of the expression then its value is
   * directly mapped to the body of the request. If the value of the `body`
   * field is of type bytes or string then it is used for the HTTP request body
   * as-is, with no conversion. If the body field is of any other type then it
   * is converted to a JSON string. If the body field does not exist then the
   * resulting payload of the HTTP request will be data value of the CloudEvent
   * HTTP Binding Binary Content Mode representation of the final message as
   * described earlier. - Any other fields in the resulting expression will be
   * ignored. The CEL expression may access the incoming CloudEvent message in
   * its definition, as follows: - The `data` field of the incoming CloudEvent
   * message can be accessed using the `message.data` value. Subfields of
   * `message.data` may also be accessed if an input_payload_format has been
   * specified on the Pipeline. - Each attribute of the incoming CloudEvent
   * message can be accessed using the `message.` value, where is replaced with
   * the name of the attribute. - Existing headers can be accessed in the CEL
   * expression using the `headers` variable. The `headers` variable defines a
   * map of key/value pairs corresponding to the HTTP headers of the CloudEvent
   * HTTP Binding Binary Content Mode representation of the final message as
   * described earlier. For example, the following CEL expression can be used to
   * construct an HTTP request by adding an additional header to the HTTP
   * headers of the CloudEvent HTTP Binding Binary Content Mode representation
   * of the final message and by overwriting the body of the request: ``` {
   * "headers": headers.merge({"new-header-key": "new-header-value"}), "body":
   * "new-body" } ``` - The default binding for the message payload can be
   * accessed using the `body` variable. It conatins a string representation of
   * the message payload in the format specified by the `output_payload_format`
   * field. If the `input_payload_format` field is not set, the `body` variable
   * contains the same message payload bytes that were published. Additionally,
   * the following CEL extension functions are provided for use in this CEL
   * expression: - toBase64Url: map.toBase64Url() -> string - Converts a
   * CelValue to a base64url encoded string - toJsonString: map.toJsonString()
   * -> string - Converts a CelValue to a JSON string - merge: map1.merge(map2)
   * -> map3 - Merges the passed CEL map with the existing CEL map the function
   * is applied to. - If the same key exists in both maps, if the key's value is
   * type map both maps are merged else the value from the passed map is used. -
   * denormalize: map.denormalize() -> map - Denormalizes a CEL map such that
   * every value of type map or key in the map is expanded to return a single
   * level map. - The resulting keys are "." separated indices of the map keys.
   * - For example: { "a": 1, "b": { "c": 2, "d": 3 } "e": [4, 5] }
   * .denormalize() -> { "a": 1, "b.c": 2, "b.d": 3, "e.0": 4, "e.1": 5 } -
   * setField: map.setField(key, value) -> message - Sets the field of the
   * message with the given key to the given value. - If the field is not
   * present it will be added. - If the field is present it will be overwritten.
   * - The key can be a dot separated path to set a field in a nested message. -
   * Key must be of type string. - Value may be any valid type. - removeFields:
   * map.removeFields([key1, key2, ...]) -> message - Removes the fields of the
   * map with the given keys. - The keys can be a dot separated path to remove a
   * field in a nested message. - If a key is not found it will be ignored. -
   * Keys must be of type string. - toMap: [map1, map2, ...].toMap() -> map -
   * Converts a CEL list of CEL maps to a single CEL map -
   * toCloudEventJsonWithPayloadFormat:
   * message.toCloudEventJsonWithPayloadFormat() -> map - Converts a message to
   * the corresponding structure of JSON format for CloudEvents. - It converts
   * `data` to destination payload format specified in `output_payload_format`.
   * If `output_payload_format` is not set, the data will remain unchanged. - It
   * also sets the corresponding datacontenttype of the CloudEvent, as indicated
   * by `output_payload_format`. If no `output_payload_format` is set it will
   * use the value of the "datacontenttype" attribute on the CloudEvent if
   * present, else remove "datacontenttype" attribute. - This function expects
   * that the content of the message will adhere to the standard CloudEvent
   * format. If it doesn't then this function will fail. - The result is a CEL
   * map that corresponds to the JSON representation of the CloudEvent. To
   * convert that data to a JSON string it can be chained with the toJsonString
   * function. The Pipeline expects that the message it receives adheres to the
   * standard CloudEvent format. If it doesn't then the outgoing message request
   * may fail with a persistent error.
   *
   * @var string
   */
  public $messageBindingTemplate;
  /**
   * Required. The URI of the HTTP endpoint. The value must be a RFC2396 URI
   * string. Examples: `https://svc.us-central1.p.local:8080/route`. Only the
   * HTTPS protocol is supported.
   *
   * @var string
   */
  public $uri;

  /**
   * Optional. The CEL expression used to modify how the destination-bound HTTP
   * request is constructed. If a binding expression is not specified here, the
   * message is treated as a CloudEvent and is mapped to the HTTP request
   * according to the CloudEvent HTTP Protocol Binding Binary Content Mode
   * (https://github.com/cloudevents/spec/blob/main/cloudevents/bindings/http-
   * protocol-binding.md#31-binary-content-mode). In this representation, all
   * fields except the `data` and `datacontenttype` field on the message are
   * mapped to HTTP request headers with a prefix of `ce-`. To construct the
   * HTTP request payload and the value of the content-type HTTP header, the
   * payload format is defined as follows: 1) Use the output_payload_format_type
   * on the Pipeline.Destination if it is set, else: 2) Use the
   * input_payload_format_type on the Pipeline if it is set, else: 3) Treat the
   * payload as opaque binary data. The `data` field of the message is converted
   * to the payload format or left as-is for case 3) and then attached as the
   * payload of the HTTP request. The `content-type` header on the HTTP request
   * is set to the payload format type or left empty for case 3). However, if a
   * mediation has updated the `datacontenttype` field on the message so that it
   * is not the same as the payload format type but it is still a prefix of the
   * payload format type, then the `content-type` header on the HTTP request is
   * set to this `datacontenttype` value. For example, if the `datacontenttype`
   * is "application/json" and the payload format type is "application/json;
   * charset=utf-8", then the `content-type` header on the HTTP request is set
   * to "application/json; charset=utf-8". If a non-empty binding expression is
   * specified then this expression is used to modify the default CloudEvent
   * HTTP Protocol Binding Binary Content representation. The result of the CEL
   * expression must be a map of key/value pairs which is used as follows: - If
   * a map named `headers` exists on the result of the expression, then its
   * key/value pairs are directly mapped to the HTTP request headers. The
   * headers values are constructed from the corresponding value type's
   * canonical representation. If the `headers` field doesn't exist then the
   * resulting HTTP request will be the headers of the CloudEvent HTTP Binding
   * Binary Content Mode representation of the final message. Note: If the
   * specified binding expression, has updated the `datacontenttype` field on
   * the message so that it is not the same as the payload format type but it is
   * still a prefix of the payload format type, then the `content-type` header
   * in the `headers` map is set to this `datacontenttype` value. - If a field
   * named `body` exists on the result of the expression then its value is
   * directly mapped to the body of the request. If the value of the `body`
   * field is of type bytes or string then it is used for the HTTP request body
   * as-is, with no conversion. If the body field is of any other type then it
   * is converted to a JSON string. If the body field does not exist then the
   * resulting payload of the HTTP request will be data value of the CloudEvent
   * HTTP Binding Binary Content Mode representation of the final message as
   * described earlier. - Any other fields in the resulting expression will be
   * ignored. The CEL expression may access the incoming CloudEvent message in
   * its definition, as follows: - The `data` field of the incoming CloudEvent
   * message can be accessed using the `message.data` value. Subfields of
   * `message.data` may also be accessed if an input_payload_format has been
   * specified on the Pipeline. - Each attribute of the incoming CloudEvent
   * message can be accessed using the `message.` value, where is replaced with
   * the name of the attribute. - Existing headers can be accessed in the CEL
   * expression using the `headers` variable. The `headers` variable defines a
   * map of key/value pairs corresponding to the HTTP headers of the CloudEvent
   * HTTP Binding Binary Content Mode representation of the final message as
   * described earlier. For example, the following CEL expression can be used to
   * construct an HTTP request by adding an additional header to the HTTP
   * headers of the CloudEvent HTTP Binding Binary Content Mode representation
   * of the final message and by overwriting the body of the request: ``` {
   * "headers": headers.merge({"new-header-key": "new-header-value"}), "body":
   * "new-body" } ``` - The default binding for the message payload can be
   * accessed using the `body` variable. It conatins a string representation of
   * the message payload in the format specified by the `output_payload_format`
   * field. If the `input_payload_format` field is not set, the `body` variable
   * contains the same message payload bytes that were published. Additionally,
   * the following CEL extension functions are provided for use in this CEL
   * expression: - toBase64Url: map.toBase64Url() -> string - Converts a
   * CelValue to a base64url encoded string - toJsonString: map.toJsonString()
   * -> string - Converts a CelValue to a JSON string - merge: map1.merge(map2)
   * -> map3 - Merges the passed CEL map with the existing CEL map the function
   * is applied to. - If the same key exists in both maps, if the key's value is
   * type map both maps are merged else the value from the passed map is used. -
   * denormalize: map.denormalize() -> map - Denormalizes a CEL map such that
   * every value of type map or key in the map is expanded to return a single
   * level map. - The resulting keys are "." separated indices of the map keys.
   * - For example: { "a": 1, "b": { "c": 2, "d": 3 } "e": [4, 5] }
   * .denormalize() -> { "a": 1, "b.c": 2, "b.d": 3, "e.0": 4, "e.1": 5 } -
   * setField: map.setField(key, value) -> message - Sets the field of the
   * message with the given key to the given value. - If the field is not
   * present it will be added. - If the field is present it will be overwritten.
   * - The key can be a dot separated path to set a field in a nested message. -
   * Key must be of type string. - Value may be any valid type. - removeFields:
   * map.removeFields([key1, key2, ...]) -> message - Removes the fields of the
   * map with the given keys. - The keys can be a dot separated path to remove a
   * field in a nested message. - If a key is not found it will be ignored. -
   * Keys must be of type string. - toMap: [map1, map2, ...].toMap() -> map -
   * Converts a CEL list of CEL maps to a single CEL map -
   * toCloudEventJsonWithPayloadFormat:
   * message.toCloudEventJsonWithPayloadFormat() -> map - Converts a message to
   * the corresponding structure of JSON format for CloudEvents. - It converts
   * `data` to destination payload format specified in `output_payload_format`.
   * If `output_payload_format` is not set, the data will remain unchanged. - It
   * also sets the corresponding datacontenttype of the CloudEvent, as indicated
   * by `output_payload_format`. If no `output_payload_format` is set it will
   * use the value of the "datacontenttype" attribute on the CloudEvent if
   * present, else remove "datacontenttype" attribute. - This function expects
   * that the content of the message will adhere to the standard CloudEvent
   * format. If it doesn't then this function will fail. - The result is a CEL
   * map that corresponds to the JSON representation of the CloudEvent. To
   * convert that data to a JSON string it can be chained with the toJsonString
   * function. The Pipeline expects that the message it receives adheres to the
   * standard CloudEvent format. If it doesn't then the outgoing message request
   * may fail with a persistent error.
   *
   * @param string $messageBindingTemplate
   */
  public function setMessageBindingTemplate($messageBindingTemplate)
  {
    $this->messageBindingTemplate = $messageBindingTemplate;
  }
  /**
   * @return string
   */
  public function getMessageBindingTemplate()
  {
    return $this->messageBindingTemplate;
  }
  /**
   * Required. The URI of the HTTP endpoint. The value must be a RFC2396 URI
   * string. Examples: `https://svc.us-central1.p.local:8080/route`. Only the
   * HTTPS protocol is supported.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEventarcV1PipelineDestinationHttpEndpoint::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineDestinationHttpEndpoint');
