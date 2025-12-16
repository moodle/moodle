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

namespace Google\Service\AnalyticsHub;

class SelectedResource extends \Google\Model
{
  /**
   * Optional. Format: For routine:
   * `projects/{projectId}/datasets/{datasetId}/routines/{routineId}`
   * Example:"projects/test_project/datasets/test_dataset/routines/test_routine"
   *
   * @var string
   */
  public $routine;
  /**
   * Optional. Format: For table:
   * `projects/{projectId}/datasets/{datasetId}/tables/{tableId}`
   * Example:"projects/test_project/datasets/test_dataset/tables/test_table"
   *
   * @var string
   */
  public $table;

  /**
   * Optional. Format: For routine:
   * `projects/{projectId}/datasets/{datasetId}/routines/{routineId}`
   * Example:"projects/test_project/datasets/test_dataset/routines/test_routine"
   *
   * @param string $routine
   */
  public function setRoutine($routine)
  {
    $this->routine = $routine;
  }
  /**
   * @return string
   */
  public function getRoutine()
  {
    return $this->routine;
  }
  /**
   * Optional. Format: For table:
   * `projects/{projectId}/datasets/{datasetId}/tables/{tableId}`
   * Example:"projects/test_project/datasets/test_dataset/tables/test_table"
   *
   * @param string $table
   */
  public function setTable($table)
  {
    $this->table = $table;
  }
  /**
   * @return string
   */
  public function getTable()
  {
    return $this->table;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SelectedResource::class, 'Google_Service_AnalyticsHub_SelectedResource');
