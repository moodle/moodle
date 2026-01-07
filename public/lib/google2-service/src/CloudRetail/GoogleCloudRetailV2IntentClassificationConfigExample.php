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

class GoogleCloudRetailV2IntentClassificationConfigExample extends \Google\Model
{
  /**
   * Required. Whether the example is classified positively.
   *
   * @var bool
   */
  public $classifiedPositive;
  /**
   * Optional. The intent_type must match one of the predefined intent types
   * defined at https://cloud.google.com/retail/docs/reference/rpc/google.cloud.
   * retail.v2alpha#querytype
   *
   * @var string
   */
  public $intentType;
  /**
   * Required. Example query.
   *
   * @var string
   */
  public $query;
  /**
   * Optional. The reason for the intent classification. This is used to explain
   * the intent classification decision.
   *
   * @var string
   */
  public $reason;

  /**
   * Required. Whether the example is classified positively.
   *
   * @param bool $classifiedPositive
   */
  public function setClassifiedPositive($classifiedPositive)
  {
    $this->classifiedPositive = $classifiedPositive;
  }
  /**
   * @return bool
   */
  public function getClassifiedPositive()
  {
    return $this->classifiedPositive;
  }
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
   * Required. Example query.
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
  /**
   * Optional. The reason for the intent classification. This is used to explain
   * the intent classification decision.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2IntentClassificationConfigExample::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2IntentClassificationConfigExample');
