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

namespace Google\Service\Bigquery;

class TableReference extends \Google\Model
{
  /**
   * Required. The ID of the dataset containing this table.
   *
   * @var string
   */
  public $datasetId;
  /**
   * Required. The ID of the project containing this table.
   *
   * @var string
   */
  public $projectId;
  /**
   * Required. The ID of the table. The ID can contain Unicode characters in
   * category L (letter), M (mark), N (number), Pc (connector, including
   * underscore), Pd (dash), and Zs (space). For more information, see [General
   * Category](https://wikipedia.org/wiki/Unicode_character_property#General_Cat
   * egory). The maximum length is 1,024 characters. Certain operations allow
   * suffixing of the table ID with a partition decorator, such as
   * `sample_table$20190123`.
   *
   * @var string
   */
  public $tableId;

  /**
   * Required. The ID of the dataset containing this table.
   *
   * @param string $datasetId
   */
  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }
  /**
   * @return string
   */
  public function getDatasetId()
  {
    return $this->datasetId;
  }
  /**
   * Required. The ID of the project containing this table.
   *
   * @param string $projectId
   */
  public function setProjectId($projectId)
  {
    $this->projectId = $projectId;
  }
  /**
   * @return string
   */
  public function getProjectId()
  {
    return $this->projectId;
  }
  /**
   * Required. The ID of the table. The ID can contain Unicode characters in
   * category L (letter), M (mark), N (number), Pc (connector, including
   * underscore), Pd (dash), and Zs (space). For more information, see [General
   * Category](https://wikipedia.org/wiki/Unicode_character_property#General_Cat
   * egory). The maximum length is 1,024 characters. Certain operations allow
   * suffixing of the table ID with a partition decorator, such as
   * `sample_table$20190123`.
   *
   * @param string $tableId
   */
  public function setTableId($tableId)
  {
    $this->tableId = $tableId;
  }
  /**
   * @return string
   */
  public function getTableId()
  {
    return $this->tableId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TableReference::class, 'Google_Service_Bigquery_TableReference');
