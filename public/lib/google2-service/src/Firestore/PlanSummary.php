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

namespace Google\Service\Firestore;

class PlanSummary extends \Google\Collection
{
  protected $collection_key = 'indexesUsed';
  /**
   * The indexes selected for the query. For example: [ {"query_scope":
   * "Collection", "properties": "(foo ASC, __name__ ASC)"}, {"query_scope":
   * "Collection", "properties": "(bar ASC, __name__ ASC)"} ]
   *
   * @var array[]
   */
  public $indexesUsed;

  /**
   * The indexes selected for the query. For example: [ {"query_scope":
   * "Collection", "properties": "(foo ASC, __name__ ASC)"}, {"query_scope":
   * "Collection", "properties": "(bar ASC, __name__ ASC)"} ]
   *
   * @param array[] $indexesUsed
   */
  public function setIndexesUsed($indexesUsed)
  {
    $this->indexesUsed = $indexesUsed;
  }
  /**
   * @return array[]
   */
  public function getIndexesUsed()
  {
    return $this->indexesUsed;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PlanSummary::class, 'Google_Service_Firestore_PlanSummary');
