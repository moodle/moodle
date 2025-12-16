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

namespace Google\Service\Spanner;

class CommitStats extends \Google\Model
{
  /**
   * The total number of mutations for the transaction. Knowing the
   * `mutation_count` value can help you maximize the number of mutations in a
   * transaction and minimize the number of API round trips. You can also
   * monitor this value to prevent transactions from exceeding the system [limit
   * ](https://cloud.google.com/spanner/quotas#limits_for_creating_reading_updat
   * ing_and_deleting_data). If the number of mutations exceeds the limit, the
   * server returns [INVALID_ARGUMENT](https://cloud.google.com/spanner/docs/ref
   * erence/rest/v1/Code#ENUM_VALUES.INVALID_ARGUMENT).
   *
   * @var string
   */
  public $mutationCount;

  /**
   * The total number of mutations for the transaction. Knowing the
   * `mutation_count` value can help you maximize the number of mutations in a
   * transaction and minimize the number of API round trips. You can also
   * monitor this value to prevent transactions from exceeding the system [limit
   * ](https://cloud.google.com/spanner/quotas#limits_for_creating_reading_updat
   * ing_and_deleting_data). If the number of mutations exceeds the limit, the
   * server returns [INVALID_ARGUMENT](https://cloud.google.com/spanner/docs/ref
   * erence/rest/v1/Code#ENUM_VALUES.INVALID_ARGUMENT).
   *
   * @param string $mutationCount
   */
  public function setMutationCount($mutationCount)
  {
    $this->mutationCount = $mutationCount;
  }
  /**
   * @return string
   */
  public function getMutationCount()
  {
    return $this->mutationCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CommitStats::class, 'Google_Service_Spanner_CommitStats');
