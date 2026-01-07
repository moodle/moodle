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

namespace Google\Service\Datastore;

class AggregationResult extends \Google\Model
{
  protected $aggregatePropertiesType = Value::class;
  protected $aggregatePropertiesDataType = 'map';

  /**
   * The result of the aggregation functions, ex: `COUNT(*) AS total_entities`.
   * The key is the alias assigned to the aggregation function on input and the
   * size of this map equals the number of aggregation functions in the query.
   *
   * @param Value[] $aggregateProperties
   */
  public function setAggregateProperties($aggregateProperties)
  {
    $this->aggregateProperties = $aggregateProperties;
  }
  /**
   * @return Value[]
   */
  public function getAggregateProperties()
  {
    return $this->aggregateProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregationResult::class, 'Google_Service_Datastore_AggregationResult');
