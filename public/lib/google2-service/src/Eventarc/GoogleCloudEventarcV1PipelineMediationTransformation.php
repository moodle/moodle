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

class GoogleCloudEventarcV1PipelineMediationTransformation extends \Google\Model
{
  /**
   * Optional. The CEL expression template to apply to transform messages. The
   * following CEL extension functions are provided for use in this CEL
   * expression: - merge: map1.merge(map2) -> map3 - Merges the passed CEL map
   * with the existing CEL map the function is applied to. - If the same key
   * exists in both maps, if the key's value is type map both maps are merged
   * else the value from the passed map is used. - denormalize:
   * map.denormalize() -> map - Denormalizes a CEL map such that every value of
   * type map or key in the map is expanded to return a single level map. - The
   * resulting keys are "." separated indices of the map keys. - For example: {
   * "a": 1, "b": { "c": 2, "d": 3 } "e": [4, 5] } .denormalize() -> { "a": 1,
   * "b.c": 2, "b.d": 3, "e.0": 4, "e.1": 5 } - setField: map.setField(key,
   * value) -> message - Sets the field of the message with the given key to the
   * given value. - If the field is not present it will be added. - If the field
   * is present it will be overwritten. - The key can be a dot separated path to
   * set a field in a nested message. - Key must be of type string. - Value may
   * be any valid type. - removeFields: map.removeFields([key1, key2, ...]) ->
   * message - Removes the fields of the map with the given keys. - The keys can
   * be a dot separated path to remove a field in a nested message. - If a key
   * is not found it will be ignored. - Keys must be of type string. - toMap:
   * [map1, map2, ...].toMap() -> map - Converts a CEL list of CEL maps to a
   * single CEL map - toDestinationPayloadFormat():
   * message.data.toDestinationPayloadFormat() -> string or bytes - Converts the
   * message data to the destination payload format specified in
   * Pipeline.Destination.output_payload_format - This function is meant to be
   * applied to the message.data field. - If the destination payload format is
   * not set, the function will return the message data unchanged. -
   * toCloudEventJsonWithPayloadFormat:
   * message.toCloudEventJsonWithPayloadFormat() -> map - Converts a message to
   * the corresponding structure of JSON format for CloudEvents - This function
   * applies toDestinationPayloadFormat() to the message data. It also sets the
   * corresponding datacontenttype of the CloudEvent, as indicated by
   * Pipeline.Destination.output_payload_format. If no output_payload_format is
   * set it will use the existing datacontenttype on the CloudEvent if present,
   * else leave datacontenttype absent. - This function expects that the content
   * of the message will adhere to the standard CloudEvent format. If it doesn't
   * then this function will fail. - The result is a CEL map that corresponds to
   * the JSON representation of the CloudEvent. To convert that data to a JSON
   * string it can be chained with the toJsonString function.
   *
   * @var string
   */
  public $transformationTemplate;

  /**
   * Optional. The CEL expression template to apply to transform messages. The
   * following CEL extension functions are provided for use in this CEL
   * expression: - merge: map1.merge(map2) -> map3 - Merges the passed CEL map
   * with the existing CEL map the function is applied to. - If the same key
   * exists in both maps, if the key's value is type map both maps are merged
   * else the value from the passed map is used. - denormalize:
   * map.denormalize() -> map - Denormalizes a CEL map such that every value of
   * type map or key in the map is expanded to return a single level map. - The
   * resulting keys are "." separated indices of the map keys. - For example: {
   * "a": 1, "b": { "c": 2, "d": 3 } "e": [4, 5] } .denormalize() -> { "a": 1,
   * "b.c": 2, "b.d": 3, "e.0": 4, "e.1": 5 } - setField: map.setField(key,
   * value) -> message - Sets the field of the message with the given key to the
   * given value. - If the field is not present it will be added. - If the field
   * is present it will be overwritten. - The key can be a dot separated path to
   * set a field in a nested message. - Key must be of type string. - Value may
   * be any valid type. - removeFields: map.removeFields([key1, key2, ...]) ->
   * message - Removes the fields of the map with the given keys. - The keys can
   * be a dot separated path to remove a field in a nested message. - If a key
   * is not found it will be ignored. - Keys must be of type string. - toMap:
   * [map1, map2, ...].toMap() -> map - Converts a CEL list of CEL maps to a
   * single CEL map - toDestinationPayloadFormat():
   * message.data.toDestinationPayloadFormat() -> string or bytes - Converts the
   * message data to the destination payload format specified in
   * Pipeline.Destination.output_payload_format - This function is meant to be
   * applied to the message.data field. - If the destination payload format is
   * not set, the function will return the message data unchanged. -
   * toCloudEventJsonWithPayloadFormat:
   * message.toCloudEventJsonWithPayloadFormat() -> map - Converts a message to
   * the corresponding structure of JSON format for CloudEvents - This function
   * applies toDestinationPayloadFormat() to the message data. It also sets the
   * corresponding datacontenttype of the CloudEvent, as indicated by
   * Pipeline.Destination.output_payload_format. If no output_payload_format is
   * set it will use the existing datacontenttype on the CloudEvent if present,
   * else leave datacontenttype absent. - This function expects that the content
   * of the message will adhere to the standard CloudEvent format. If it doesn't
   * then this function will fail. - The result is a CEL map that corresponds to
   * the JSON representation of the CloudEvent. To convert that data to a JSON
   * string it can be chained with the toJsonString function.
   *
   * @param string $transformationTemplate
   */
  public function setTransformationTemplate($transformationTemplate)
  {
    $this->transformationTemplate = $transformationTemplate;
  }
  /**
   * @return string
   */
  public function getTransformationTemplate()
  {
    return $this->transformationTemplate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEventarcV1PipelineMediationTransformation::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineMediationTransformation');
