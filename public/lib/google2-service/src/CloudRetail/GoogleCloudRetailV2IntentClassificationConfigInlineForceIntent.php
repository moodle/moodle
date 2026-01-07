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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2IntentClassificationConfigInlineForceIntent extends \Google\Model
{
  /**
   * Unspecified match operation.
   */
  public const OPERATION_OPERATION_UNSPECIFIED = 'OPERATION_UNSPECIFIED';
  /**
   * Exact match.
   */
  public const OPERATION_EXACT_MATCH = 'EXACT_MATCH';
  /**
   * Contains match.
   */
  public const OPERATION_CONTAINS = 'CONTAINS';
  /**
   * Optional. The intent_type must match one of the predefined intent types
   * defined at https://cloud.google.com/retail/docs/reference/rpc/google.cloud.
   * retail.v2alpha#querytype
   *
   * @var string
   */
  public $intentType;
  /**
   * Optional. The operation to perform for the query.
   *
   * @var string
   */
  public $operation;
  /**
   * Optional. A example query.
   *
   * @var string
   */
  public $query;

  /**
   * Optional. The intent_type must match one of the predefined intent types
   * defined at https://cloud.google.com/retail/docs/reference/rpc/google.cloud.
   * retail.v2alpha#querytype
   *
   * @param string $intentType
   */
  public function setIntentType($intentType)
  {
    $this->intentType = $intentType;
  }
  /**
   * @return string
   */
  public function getIntentType()
  {
    return $this->intentType;
  }
  /**
   * Optional. The operation to perform for the query.
   *
   * Accepted values: OPERATION_UNSPECIFIED, EXACT_MATCH, CONTAINS
   *
   * @param self::OPERATION_* $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return self::OPERATION_*
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Optional. A example query.
   *
   * @param string $query
   */
  public function setQuery($query)
  {
    $this->query = $query;
  }
  /**
   * @return string
   */
  public function getQuery()
  {
    return $this->query;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2IntentClassificationConfigInlineForceIntent::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2IntentClassificationConfigInlineForceIntent');
