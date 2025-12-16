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

namespace Google\Service\FirebaseDataConnect;

class ExecuteQueryRequest extends \Google\Model
{
  /**
   * Required. The name of the GraphQL operation name. Required because all
   * Connector operations must be named. See
   * https://graphql.org/learn/queries/#operation-name.
   *
   * @var string
   */
  public $operationName;
  /**
   * Optional. Values for GraphQL variables provided in this request.
   *
   * @var array[]
   */
  public $variables;

  /**
   * Required. The name of the GraphQL operation name. Required because all
   * Connector operations must be named. See
   * https://graphql.org/learn/queries/#operation-name.
   *
   * @param string $operationName
   */
  public function setOperationName($operationName)
  {
    $this->operationName = $operationName;
  }
  /**
   * @return string
   */
  public function getOperationName()
  {
    return $this->operationName;
  }
  /**
   * Optional. Values for GraphQL variables provided in this request.
   *
   * @param array[] $variables
   */
  public function setVariables($variables)
  {
    $this->variables = $variables;
  }
  /**
   * @return array[]
   */
  public function getVariables()
  {
    return $this->variables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExecuteQueryRequest::class, 'Google_Service_FirebaseDataConnect_ExecuteQueryRequest');
