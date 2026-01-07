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

namespace Google\Service\AnalyticsHub;

class JavaScriptUDF extends \Google\Model
{
  /**
   * Required. JavaScript code that contains a function `function_name` with the
   * below signature: ``` * * Transforms a Pub/Sub message. * @return
   * {(Object)>|null)} - To * filter a message, return `null`. To transform a
   * message return a map * with the following keys: * - (required) 'data' :
   * {string} * - (optional) 'attributes' : {Object} * Returning empty
   * `attributes` will remove all attributes from the * message. * * @param
   * {(Object)>} Pub/Sub * message. Keys: * - (required) 'data' : {string} * -
   * (required) 'attributes' : {Object} * * @param {Object} metadata - Pub/Sub
   * message metadata. * Keys: * - (required) 'message_id' : {string} * -
   * (optional) 'publish_time': {string} YYYY-MM-DDTHH:MM:SSZ format * -
   * (optional) 'ordering_key': {string}  function (message, metadata) { } ```
   *
   * @var string
   */
  public $code;
  /**
   * Required. Name of the JavasScript function that should applied to Pub/Sub
   * messages.
   *
   * @var string
   */
  public $functionName;

  /**
   * Required. JavaScript code that contains a function `function_name` with the
   * below signature: ``` * * Transforms a Pub/Sub message. * @return
   * {(Object)>|null)} - To * filter a message, return `null`. To transform a
   * message return a map * with the following keys: * - (required) 'data' :
   * {string} * - (optional) 'attributes' : {Object} * Returning empty
   * `attributes` will remove all attributes from the * message. * * @param
   * {(Object)>} Pub/Sub * message. Keys: * - (required) 'data' : {string} * -
   * (required) 'attributes' : {Object} * * @param {Object} metadata - Pub/Sub
   * message metadata. * Keys: * - (required) 'message_id' : {string} * -
   * (optional) 'publish_time': {string} YYYY-MM-DDTHH:MM:SSZ format * -
   * (optional) 'ordering_key': {string}  function (message, metadata) { } ```
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Required. Name of the JavasScript function that should applied to Pub/Sub
   * messages.
   *
   * @param string $functionName
   */
  public function setFunctionName($functionName)
  {
    $this->functionName = $functionName;
  }
  /**
   * @return string
   */
  public function getFunctionName()
  {
    return $this->functionName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(JavaScriptUDF::class, 'Google_Service_AnalyticsHub_JavaScriptUDF');
