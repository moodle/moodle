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

class GoogleCloudApigeeV1DebugMask extends \Google\Collection
{
  protected $collection_key = 'variables';
  /**
   * List of JSON paths that specify the JSON elements to be filtered from JSON
   * payloads in error flows.
   *
   * @var string[]
   */
  public $faultJSONPaths;
  /**
   * List of XPaths that specify the XML elements to be filtered from XML
   * payloads in error flows.
   *
   * @var string[]
   */
  public $faultXPaths;
  /**
   * Name of the debug mask.
   *
   * @var string
   */
  public $name;
  /**
   * Map of namespaces to URIs.
   *
   * @var string[]
   */
  public $namespaces;
  /**
   * List of JSON paths that specify the JSON elements to be filtered from JSON
   * request message payloads.
   *
   * @var string[]
   */
  public $requestJSONPaths;
  /**
   * List of XPaths that specify the XML elements to be filtered from XML
   * request message payloads.
   *
   * @var string[]
   */
  public $requestXPaths;
  /**
   * List of JSON paths that specify the JSON elements to be filtered from JSON
   * response message payloads.
   *
   * @var string[]
   */
  public $responseJSONPaths;
  /**
   * List of XPaths that specify the XML elements to be filtered from XML
   * response message payloads.
   *
   * @var string[]
   */
  public $responseXPaths;
  /**
   * List of variables that should be masked from the debug output.
   *
   * @var string[]
   */
  public $variables;

  /**
   * List of JSON paths that specify the JSON elements to be filtered from JSON
   * payloads in error flows.
   *
   * @param string[] $faultJSONPaths
   */
  public function setFaultJSONPaths($faultJSONPaths)
  {
    $this->faultJSONPaths = $faultJSONPaths;
  }
  /**
   * @return string[]
   */
  public function getFaultJSONPaths()
  {
    return $this->faultJSONPaths;
  }
  /**
   * List of XPaths that specify the XML elements to be filtered from XML
   * payloads in error flows.
   *
   * @param string[] $faultXPaths
   */
  public function setFaultXPaths($faultXPaths)
  {
    $this->faultXPaths = $faultXPaths;
  }
  /**
   * @return string[]
   */
  public function getFaultXPaths()
  {
    return $this->faultXPaths;
  }
  /**
   * Name of the debug mask.
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
   * Map of namespaces to URIs.
   *
   * @param string[] $namespaces
   */
  public function setNamespaces($namespaces)
  {
    $this->namespaces = $namespaces;
  }
  /**
   * @return string[]
   */
  public function getNamespaces()
  {
    return $this->namespaces;
  }
  /**
   * List of JSON paths that specify the JSON elements to be filtered from JSON
   * request message payloads.
   *
   * @param string[] $requestJSONPaths
   */
  public function setRequestJSONPaths($requestJSONPaths)
  {
    $this->requestJSONPaths = $requestJSONPaths;
  }
  /**
   * @return string[]
   */
  public function getRequestJSONPaths()
  {
    return $this->requestJSONPaths;
  }
  /**
   * List of XPaths that specify the XML elements to be filtered from XML
   * request message payloads.
   *
   * @param string[] $requestXPaths
   */
  public function setRequestXPaths($requestXPaths)
  {
    $this->requestXPaths = $requestXPaths;
  }
  /**
   * @return string[]
   */
  public function getRequestXPaths()
  {
    return $this->requestXPaths;
  }
  /**
   * List of JSON paths that specify the JSON elements to be filtered from JSON
   * response message payloads.
   *
   * @param string[] $responseJSONPaths
   */
  public function setResponseJSONPaths($responseJSONPaths)
  {
    $this->responseJSONPaths = $responseJSONPaths;
  }
  /**
   * @return string[]
   */
  public function getResponseJSONPaths()
  {
    return $this->responseJSONPaths;
  }
  /**
   * List of XPaths that specify the XML elements to be filtered from XML
   * response message payloads.
   *
   * @param string[] $responseXPaths
   */
  public function setResponseXPaths($responseXPaths)
  {
    $this->responseXPaths = $responseXPaths;
  }
  /**
   * @return string[]
   */
  public function getResponseXPaths()
  {
    return $this->responseXPaths;
  }
  /**
   * List of variables that should be masked from the debug output.
   *
   * @param string[] $variables
   */
  public function setVariables($variables)
  {
    $this->variables = $variables;
  }
  /**
   * @return string[]
   */
  public function getVariables()
  {
    return $this->variables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1DebugMask::class, 'Google_Service_Apigee_GoogleCloudApigeeV1DebugMask');
