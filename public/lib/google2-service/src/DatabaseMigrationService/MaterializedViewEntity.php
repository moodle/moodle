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

namespace Google\Service\DatabaseMigrationService;

class MaterializedViewEntity extends \Google\Collection
{
  protected $collection_key = 'indices';
  /**
   * Custom engine specific features.
   *
   * @var array[]
   */
  public $customFeatures;
  protected $indicesType = IndexEntity::class;
  protected $indicesDataType = 'array';
  /**
   * The SQL code which creates the view.
   *
   * @var string
   */
  public $sqlCode;

  /**
   * Custom engine specific features.
   *
   * @param array[] $customFeatures
   */
  public function setCustomFeatures($customFeatures)
  {
    $this->customFeatures = $customFeatures;
  }
  /**
   * @return array[]
   */
  public function getCustomFeatures()
  {
    return $this->customFeatures;
  }
  /**
   * View indices.
   *
   * @param IndexEntity[] $indices
   */
  public function setIndices($indices)
  {
    $this->indices = $indices;
  }
  /**
   * @return IndexEntity[]
   */
  public function getIndices()
  {
    return $this->indices;
  }
  /**
   * The SQL code which creates the view.
   *
   * @param string $sqlCode
   */
  public function setSqlCode($sqlCode)
  {
    $this->sqlCode = $sqlCode;
  }
  /**
   * @return string
   */
  public function getSqlCode()
  {
    return $this->sqlCode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MaterializedViewEntity::class, 'Google_Service_DatabaseMigrationService_MaterializedViewEntity');
