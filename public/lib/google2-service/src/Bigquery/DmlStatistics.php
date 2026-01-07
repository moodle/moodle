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

class DmlStatistics extends \Google\Model
{
  /**
   * Output only. Number of deleted Rows. populated by DML DELETE, MERGE and
   * TRUNCATE statements.
   *
   * @var string
   */
  public $deletedRowCount;
  /**
   * Output only. Number of inserted Rows. Populated by DML INSERT and MERGE
   * statements
   *
   * @var string
   */
  public $insertedRowCount;
  /**
   * Output only. Number of updated Rows. Populated by DML UPDATE and MERGE
   * statements.
   *
   * @var string
   */
  public $updatedRowCount;

  /**
   * Output only. Number of deleted Rows. populated by DML DELETE, MERGE and
   * TRUNCATE statements.
   *
   * @param string $deletedRowCount
   */
  public function setDeletedRowCount($deletedRowCount)
  {
    $this->deletedRowCount = $deletedRowCount;
  }
  /**
   * @return string
   */
  public function getDeletedRowCount()
  {
    return $this->deletedRowCount;
  }
  /**
   * Output only. Number of inserted Rows. Populated by DML INSERT and MERGE
   * statements
   *
   * @param string $insertedRowCount
   */
  public function setInsertedRowCount($insertedRowCount)
  {
    $this->insertedRowCount = $insertedRowCount;
  }
  /**
   * @return string
   */
  public function getInsertedRowCount()
  {
    return $this->insertedRowCount;
  }
  /**
   * Output only. Number of updated Rows. Populated by DML UPDATE and MERGE
   * statements.
   *
   * @param string $updatedRowCount
   */
  public function setUpdatedRowCount($updatedRowCount)
  {
    $this->updatedRowCount = $updatedRowCount;
  }
  /**
   * @return string
   */
  public function getUpdatedRowCount()
  {
    return $this->updatedRowCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DmlStatistics::class, 'Google_Service_Bigquery_DmlStatistics');
