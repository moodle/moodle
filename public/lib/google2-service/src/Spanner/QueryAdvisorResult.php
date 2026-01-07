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

class QueryAdvisorResult extends \Google\Collection
{
  protected $collection_key = 'indexAdvice';
  protected $indexAdviceType = IndexAdvice::class;
  protected $indexAdviceDataType = 'array';

  /**
   * Optional. Index Recommendation for a query. This is an optional field and
   * the recommendation will only be available when the recommendation
   * guarantees significant improvement in query performance.
   *
   * @param IndexAdvice[] $indexAdvice
   */
  public function setIndexAdvice($indexAdvice)
  {
    $this->indexAdvice = $indexAdvice;
  }
  /**
   * @return IndexAdvice[]
   */
  public function getIndexAdvice()
  {
    return $this->indexAdvice;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueryAdvisorResult::class, 'Google_Service_Spanner_QueryAdvisorResult');
