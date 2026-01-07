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

namespace Google\Service\CloudNaturalLanguage;

class XPSTablesDatasetMetadata extends \Google\Model
{
  /**
   * Id the column to split the table.
   *
   * @var int
   */
  public $mlUseColumnId;
  protected $primaryTableSpecType = XPSTableSpec::class;
  protected $primaryTableSpecDataType = '';
  protected $targetColumnCorrelationsType = XPSCorrelationStats::class;
  protected $targetColumnCorrelationsDataType = 'map';
  /**
   * Id of the primary table column that should be used as the training label.
   *
   * @var int
   */
  public $targetColumnId;
  /**
   * Id of the primary table column that should be used as the weight column.
   *
   * @var int
   */
  public $weightColumnId;

  /**
   * Id the column to split the table.
   *
   * @param int $mlUseColumnId
   */
  public function setMlUseColumnId($mlUseColumnId)
  {
    $this->mlUseColumnId = $mlUseColumnId;
  }
  /**
   * @return int
   */
  public function getMlUseColumnId()
  {
    return $this->mlUseColumnId;
  }
  /**
   * Primary table.
   *
   * @param XPSTableSpec $primaryTableSpec
   */
  public function setPrimaryTableSpec(XPSTableSpec $primaryTableSpec)
  {
    $this->primaryTableSpec = $primaryTableSpec;
  }
  /**
   * @return XPSTableSpec
   */
  public function getPrimaryTableSpec()
  {
    return $this->primaryTableSpec;
  }
  /**
   * (the column id : its CorrelationStats with target column).
   *
   * @param XPSCorrelationStats[] $targetColumnCorrelations
   */
  public function setTargetColumnCorrelations($targetColumnCorrelations)
  {
    $this->targetColumnCorrelations = $targetColumnCorrelations;
  }
  /**
   * @return XPSCorrelationStats[]
   */
  public function getTargetColumnCorrelations()
  {
    return $this->targetColumnCorrelations;
  }
  /**
   * Id of the primary table column that should be used as the training label.
   *
   * @param int $targetColumnId
   */
  public function setTargetColumnId($targetColumnId)
  {
    $this->targetColumnId = $targetColumnId;
  }
  /**
   * @return int
   */
  public function getTargetColumnId()
  {
    return $this->targetColumnId;
  }
  /**
   * Id of the primary table column that should be used as the weight column.
   *
   * @param int $weightColumnId
   */
  public function setWeightColumnId($weightColumnId)
  {
    $this->weightColumnId = $weightColumnId;
  }
  /**
   * @return int
   */
  public function getWeightColumnId()
  {
    return $this->weightColumnId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSTablesDatasetMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSTablesDatasetMetadata');
