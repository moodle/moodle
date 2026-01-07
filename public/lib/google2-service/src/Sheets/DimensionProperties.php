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

namespace Google\Service\Sheets;

class DimensionProperties extends \Google\Collection
{
  protected $collection_key = 'developerMetadata';
  protected $dataSourceColumnReferenceType = DataSourceColumnReference::class;
  protected $dataSourceColumnReferenceDataType = '';
  protected $developerMetadataType = DeveloperMetadata::class;
  protected $developerMetadataDataType = 'array';
  /**
   * True if this dimension is being filtered. This field is read-only.
   *
   * @var bool
   */
  public $hiddenByFilter;
  /**
   * True if this dimension is explicitly hidden.
   *
   * @var bool
   */
  public $hiddenByUser;
  /**
   * The height (if a row) or width (if a column) of the dimension in pixels.
   *
   * @var int
   */
  public $pixelSize;

  /**
   * Output only. If set, this is a column in a data source sheet.
   *
   * @param DataSourceColumnReference $dataSourceColumnReference
   */
  public function setDataSourceColumnReference(DataSourceColumnReference $dataSourceColumnReference)
  {
    $this->dataSourceColumnReference = $dataSourceColumnReference;
  }
  /**
   * @return DataSourceColumnReference
   */
  public function getDataSourceColumnReference()
  {
    return $this->dataSourceColumnReference;
  }
  /**
   * The developer metadata associated with a single row or column.
   *
   * @param DeveloperMetadata[] $developerMetadata
   */
  public function setDeveloperMetadata($developerMetadata)
  {
    $this->developerMetadata = $developerMetadata;
  }
  /**
   * @return DeveloperMetadata[]
   */
  public function getDeveloperMetadata()
  {
    return $this->developerMetadata;
  }
  /**
   * True if this dimension is being filtered. This field is read-only.
   *
   * @param bool $hiddenByFilter
   */
  public function setHiddenByFilter($hiddenByFilter)
  {
    $this->hiddenByFilter = $hiddenByFilter;
  }
  /**
   * @return bool
   */
  public function getHiddenByFilter()
  {
    return $this->hiddenByFilter;
  }
  /**
   * True if this dimension is explicitly hidden.
   *
   * @param bool $hiddenByUser
   */
  public function setHiddenByUser($hiddenByUser)
  {
    $this->hiddenByUser = $hiddenByUser;
  }
  /**
   * @return bool
   */
  public function getHiddenByUser()
  {
    return $this->hiddenByUser;
  }
  /**
   * The height (if a row) or width (if a column) of the dimension in pixels.
   *
   * @param int $pixelSize
   */
  public function setPixelSize($pixelSize)
  {
    $this->pixelSize = $pixelSize;
  }
  /**
   * @return int
   */
  public function getPixelSize()
  {
    return $this->pixelSize;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DimensionProperties::class, 'Google_Service_Sheets_DimensionProperties');
