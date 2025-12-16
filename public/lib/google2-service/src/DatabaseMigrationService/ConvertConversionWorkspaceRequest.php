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

class ConvertConversionWorkspaceRequest extends \Google\Model
{
  /**
   * Optional. Specifies whether the conversion workspace is to be committed
   * automatically after the conversion.
   *
   * @var bool
   */
  public $autoCommit;
  /**
   * Optional. Automatically convert the full entity path for each entity
   * specified by the filter. For example, if the filter specifies a table, that
   * table schema (and database if there is one) will also be converted.
   *
   * @var bool
   */
  public $convertFullPath;
  /**
   * Optional. Filter the entities to convert. Leaving this field empty will
   * convert all of the entities. Supports Google AIP-160 style filtering.
   *
   * @var string
   */
  public $filter;

  /**
   * Optional. Specifies whether the conversion workspace is to be committed
   * automatically after the conversion.
   *
   * @param bool $autoCommit
   */
  public function setAutoCommit($autoCommit)
  {
    $this->autoCommit = $autoCommit;
  }
  /**
   * @return bool
   */
  public function getAutoCommit()
  {
    return $this->autoCommit;
  }
  /**
   * Optional. Automatically convert the full entity path for each entity
   * specified by the filter. For example, if the filter specifies a table, that
   * table schema (and database if there is one) will also be converted.
   *
   * @param bool $convertFullPath
   */
  public function setConvertFullPath($convertFullPath)
  {
    $this->convertFullPath = $convertFullPath;
  }
  /**
   * @return bool
   */
  public function getConvertFullPath()
  {
    return $this->convertFullPath;
  }
  /**
   * Optional. Filter the entities to convert. Leaving this field empty will
   * convert all of the entities. Supports Google AIP-160 style filtering.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConvertConversionWorkspaceRequest::class, 'Google_Service_DatabaseMigrationService_ConvertConversionWorkspaceRequest');
