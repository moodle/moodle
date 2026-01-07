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

namespace Google\Service\Datastream;

class CustomizationRule extends \Google\Model
{
  protected $bigqueryClusteringType = BigQueryClustering::class;
  protected $bigqueryClusteringDataType = '';
  protected $bigqueryPartitioningType = BigQueryPartitioning::class;
  protected $bigqueryPartitioningDataType = '';

  /**
   * BigQuery clustering rule.
   *
   * @param BigQueryClustering $bigqueryClustering
   */
  public function setBigqueryClustering(BigQueryClustering $bigqueryClustering)
  {
    $this->bigqueryClustering = $bigqueryClustering;
  }
  /**
   * @return BigQueryClustering
   */
  public function getBigqueryClustering()
  {
    return $this->bigqueryClustering;
  }
  /**
   * BigQuery partitioning rule.
   *
   * @param BigQueryPartitioning $bigqueryPartitioning
   */
  public function setBigqueryPartitioning(BigQueryPartitioning $bigqueryPartitioning)
  {
    $this->bigqueryPartitioning = $bigqueryPartitioning;
  }
  /**
   * @return BigQueryPartitioning
   */
  public function getBigqueryPartitioning()
  {
    return $this->bigqueryPartitioning;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomizationRule::class, 'Google_Service_Datastream_CustomizationRule');
