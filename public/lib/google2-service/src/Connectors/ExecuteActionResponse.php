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

namespace Google\Service\Connectors;

class ExecuteActionResponse extends \Google\Collection
{
  protected $collection_key = 'results';
  /**
   * Metadata like service latency, etc.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * In the case of successful invocation of the specified action, the results
   * Struct contains values based on the response of the action invoked. 1. If
   * the action execution produces any entities as a result, they are returned
   * as an array of Structs with the 'key' being the field name and the 'value'
   * being the value of that field in each result row. { 'results': [{'key':
   * 'value'}, ...] }
   *
   * @var array[]
   */
  public $results;

  /**
   * Metadata like service latency, etc.
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
   * In the case of successful invocation of the specified action, the results
   * Struct contains values based on the response of the action invoked. 1. If
   * the action execution produces any entities as a result, they are returned
   * as an array of Structs with the 'key' being the field name and the 'value'
   * being the value of that field in each result row. { 'results': [{'key':
   * 'value'}, ...] }
   *
   * @param array[] $results
   */
  public function setResults($results)
  {
    $this->results = $results;
  }
  /**
   * @return array[]
   */
  public function getResults()
  {
    return $this->results;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecuteActionResponse::class, 'Google_Service_Connectors_ExecuteActionResponse');
