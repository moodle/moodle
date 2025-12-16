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

namespace Google\Service\Fitness;

class AggregateBy extends \Google\Model
{
  /**
   * A data source ID to aggregate. Only data from the specified data source ID
   * will be included in the aggregation. If specified, this data source must
   * exist; the OAuth scopes in the supplied credentials must grant read access
   * to this data type. The dataset in the response will have the same data
   * source ID. Note: Data can be aggregated by either the dataTypeName or the
   * dataSourceId, not both.
   *
   * @var string
   */
  public $dataSourceId;
  /**
   * The data type to aggregate. All data sources providing this data type will
   * contribute data to the aggregation. The response will contain a single
   * dataset for this data type name. The dataset will have a data source ID of
   * derived::com.google.android.gms:aggregated. If the user has no data for
   * this data type, an empty data set will be returned. Note: Data can be
   * aggregated by either the dataTypeName or the dataSourceId, not both.
   *
   * @var string
   */
  public $dataTypeName;

  /**
   * A data source ID to aggregate. Only data from the specified data source ID
   * will be included in the aggregation. If specified, this data source must
   * exist; the OAuth scopes in the supplied credentials must grant read access
   * to this data type. The dataset in the response will have the same data
   * source ID. Note: Data can be aggregated by either the dataTypeName or the
   * dataSourceId, not both.
   *
   * @param string $dataSourceId
   */
  public function setDataSourceId($dataSourceId)
  {
    $this->dataSourceId = $dataSourceId;
  }
  /**
   * @return string
   */
  public function getDataSourceId()
  {
    return $this->dataSourceId;
  }
  /**
   * The data type to aggregate. All data sources providing this data type will
   * contribute data to the aggregation. The response will contain a single
   * dataset for this data type name. The dataset will have a data source ID of
   * derived::com.google.android.gms:aggregated. If the user has no data for
   * this data type, an empty data set will be returned. Note: Data can be
   * aggregated by either the dataTypeName or the dataSourceId, not both.
   *
   * @param string $dataTypeName
   */
  public function setDataTypeName($dataTypeName)
  {
    $this->dataTypeName = $dataTypeName;
  }
  /**
   * @return string
   */
  public function getDataTypeName()
  {
    return $this->dataTypeName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AggregateBy::class, 'Google_Service_Fitness_AggregateBy');
