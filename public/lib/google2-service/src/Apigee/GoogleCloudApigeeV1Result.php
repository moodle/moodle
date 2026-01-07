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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1Result extends \Google\Collection
{
  protected $collection_key = 'headers';
  protected $internal_gapi_mappings = [
        "actionResult" => "ActionResult",
  ];
  /**
   * Type of the action result. Can be one of the five: DebugInfo,
   * RequestMessage, ResponseMessage, ErrorMessage, VariableAccess
   *
   * @var string
   */
  public $actionResult;
  protected $accessListType = GoogleCloudApigeeV1Access::class;
  protected $accessListDataType = 'array';
  /**
   * Error message content. for example, "content" :
   * "{\"fault\":{\"faultstring\":\"API timed
   * out\",\"detail\":{\"errorcode\":\"flow.APITimedOut\"}}}"
   *
   * @var string
   */
  public $content;
  protected $headersType = GoogleCloudApigeeV1Property::class;
  protected $headersDataType = 'array';
  protected $propertiesType = GoogleCloudApigeeV1Properties::class;
  protected $propertiesDataType = '';
  /**
   * HTTP response phrase
   *
   * @var string
   */
  public $reasonPhrase;
  /**
   * HTTP response code
   *
   * @var string
   */
  public $statusCode;
  /**
   * Timestamp of when the result is recorded. Its format is dd-mm-yy
   * hh:mm:ss:xxx. For example, `"timestamp" : "12-08-19 00:31:59:960"`
   *
   * @var string
   */
  public $timestamp;
  /**
   * The relative path of the api proxy. for example, `"uRI" : "/iloveapis"`
   *
   * @var string
   */
  public $uRI;
  /**
   * HTTP method verb
   *
   * @var string
   */
  public $verb;

  /**
   * Type of the action result. Can be one of the five: DebugInfo,
   * RequestMessage, ResponseMessage, ErrorMessage, VariableAccess
   *
   * @param string $actionResult
   */
  public function setActionResult($actionResult)
  {
    $this->actionResult = $actionResult;
  }
  /**
   * @return string
   */
  public function getActionResult()
  {
    return $this->actionResult;
  }
  /**
   * A list of variable access actions agaist the api proxy. Supported values:
   * Get, Set, Remove.
   *
   * @param GoogleCloudApigeeV1Access[] $accessList
   */
  public function setAccessList($accessList)
  {
    $this->accessList = $accessList;
  }
  /**
   * @return GoogleCloudApigeeV1Access[]
   */
  public function getAccessList()
  {
    return $this->accessList;
  }
  /**
   * Error message content. for example, "content" :
   * "{\"fault\":{\"faultstring\":\"API timed
   * out\",\"detail\":{\"errorcode\":\"flow.APITimedOut\"}}}"
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * A list of HTTP headers. for example, '"headers" : [ { "name" : "Content-
   * Length", "value" : "83" }, { "name" : "Content-Type", "value" :
   * "application/json" } ]'
   *
   * @param GoogleCloudApigeeV1Property[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return GoogleCloudApigeeV1Property[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Name value pairs used for DebugInfo ActionResult.
   *
   * @param GoogleCloudApigeeV1Properties $properties
   */
  public function setProperties(GoogleCloudApigeeV1Properties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudApigeeV1Properties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * HTTP response phrase
   *
   * @param string $reasonPhrase
   */
  public function setReasonPhrase($reasonPhrase)
  {
    $this->reasonPhrase = $reasonPhrase;
  }
  /**
   * @return string
   */
  public function getReasonPhrase()
  {
    return $this->reasonPhrase;
  }
  /**
   * HTTP response code
   *
   * @param string $statusCode
   */
  public function setStatusCode($statusCode)
  {
    $this->statusCode = $statusCode;
  }
  /**
   * @return string
   */
  public function getStatusCode()
  {
    return $this->statusCode;
  }
  /**
   * Timestamp of when the result is recorded. Its format is dd-mm-yy
   * hh:mm:ss:xxx. For example, `"timestamp" : "12-08-19 00:31:59:960"`
   *
   * @param string $timestamp
   */
  public function setTimestamp($timestamp)
  {
    $this->timestamp = $timestamp;
  }
  /**
   * @return string
   */
  public function getTimestamp()
  {
    return $this->timestamp;
  }
  /**
   * The relative path of the api proxy. for example, `"uRI" : "/iloveapis"`
   *
   * @param string $uRI
   */
  public function setURI($uRI)
  {
    $this->uRI = $uRI;
  }
  /**
   * @return string
   */
  public function getURI()
  {
    return $this->uRI;
  }
  /**
   * HTTP method verb
   *
   * @param string $verb
   */
  public function setVerb($verb)
  {
    $this->verb = $verb;
  }
  /**
   * @return string
   */
  public function getVerb()
  {
    return $this->verb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1Result::class, 'Google_Service_Apigee_GoogleCloudApigeeV1Result');
