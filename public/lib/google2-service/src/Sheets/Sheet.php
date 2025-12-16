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

class Sheet extends \Google\Collection
{
  protected $collection_key = 'tables';
  protected $bandedRangesType = BandedRange::class;
  protected $bandedRangesDataType = 'array';
  protected $basicFilterType = BasicFilter::class;
  protected $basicFilterDataType = '';
  protected $chartsType = EmbeddedChart::class;
  protected $chartsDataType = 'array';
  protected $columnGroupsType = DimensionGroup::class;
  protected $columnGroupsDataType = 'array';
  protected $conditionalFormatsType = ConditionalFormatRule::class;
  protected $conditionalFormatsDataType = 'array';
  protected $dataType = GridData::class;
  protected $dataDataType = 'array';
  protected $developerMetadataType = DeveloperMetadata::class;
  protected $developerMetadataDataType = 'array';
  protected $filterViewsType = FilterView::class;
  protected $filterViewsDataType = 'array';
  protected $mergesType = GridRange::class;
  protected $mergesDataType = 'array';
  protected $propertiesType = SheetProperties::class;
  protected $propertiesDataType = '';
  protected $protectedRangesType = ProtectedRange::class;
  protected $protectedRangesDataType = 'array';
  protected $rowGroupsType = DimensionGroup::class;
  protected $rowGroupsDataType = 'array';
  protected $slicersType = Slicer::class;
  protected $slicersDataType = 'array';
  protected $tablesType = Table::class;
  protected $tablesDataType = 'array';

  /**
   * The banded (alternating colors) ranges on this sheet.
   *
   * @param BandedRange[] $bandedRanges
   */
  public function setBandedRanges($bandedRanges)
  {
    $this->bandedRanges = $bandedRanges;
  }
  /**
   * @return BandedRange[]
   */
  public function getBandedRanges()
  {
    return $this->bandedRanges;
  }
  /**
   * The filter on this sheet, if any.
   *
   * @param BasicFilter $basicFilter
   */
  public function setBasicFilter(BasicFilter $basicFilter)
  {
    $this->basicFilter = $basicFilter;
  }
  /**
   * @return BasicFilter
   */
  public function getBasicFilter()
  {
    return $this->basicFilter;
  }
  /**
   * The specifications of every chart on this sheet.
   *
   * @param EmbeddedChart[] $charts
   */
  public function setCharts($charts)
  {
    $this->charts = $charts;
  }
  /**
   * @return EmbeddedChart[]
   */
  public function getCharts()
  {
    return $this->charts;
  }
  /**
   * All column groups on this sheet, ordered by increasing range start index,
   * then by group depth.
   *
   * @param DimensionGroup[] $columnGroups
   */
  public function setColumnGroups($columnGroups)
  {
    $this->columnGroups = $columnGroups;
  }
  /**
   * @return DimensionGroup[]
   */
  public function getColumnGroups()
  {
    return $this->columnGroups;
  }
  /**
   * The conditional format rules in this sheet.
   *
   * @param ConditionalFormatRule[] $conditionalFormats
   */
  public function setConditionalFormats($conditionalFormats)
  {
    $this->conditionalFormats = $conditionalFormats;
  }
  /**
   * @return ConditionalFormatRule[]
   */
  public function getConditionalFormats()
  {
    return $this->conditionalFormats;
  }
  /**
   * Data in the grid, if this is a grid sheet. The number of GridData objects
   * returned is dependent on the number of ranges requested on this sheet. For
   * example, if this is representing `Sheet1`, and the spreadsheet was
   * requested with ranges `Sheet1!A1:C10` and `Sheet1!D15:E20`, then the first
   * GridData will have a startRow/startColumn of `0`, while the second one will
   * have `startRow 14` (zero-based row 15), and `startColumn 3` (zero-based
   * column D). For a DATA_SOURCE sheet, you can not request a specific range,
   * the GridData contains all the values.
   *
   * @param GridData[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return GridData[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * The developer metadata associated with a sheet.
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
   * The filter views in this sheet.
   *
   * @param FilterView[] $filterViews
   */
  public function setFilterViews($filterViews)
  {
    $this->filterViews = $filterViews;
  }
  /**
   * @return FilterView[]
   */
  public function getFilterViews()
  {
    return $this->filterViews;
  }
  /**
   * The ranges that are merged together.
   *
   * @param GridRange[] $merges
   */
  public function setMerges($merges)
  {
    $this->merges = $merges;
  }
  /**
   * @return GridRange[]
   */
  public function getMerges()
  {
    return $this->merges;
  }
  /**
   * The properties of the sheet.
   *
   * @param SheetProperties $properties
   */
  public function setProperties(SheetProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return SheetProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * The protected ranges in this sheet.
   *
   * @param ProtectedRange[] $protectedRanges
   */
  public function setProtectedRanges($protectedRanges)
  {
    $this->protectedRanges = $protectedRanges;
  }
  /**
   * @return ProtectedRange[]
   */
  public function getProtectedRanges()
  {
    return $this->protectedRanges;
  }
  /**
   * All row groups on this sheet, ordered by increasing range start index, then
   * by group depth.
   *
   * @param DimensionGroup[] $rowGroups
   */
  public function setRowGroups($rowGroups)
  {
    $this->rowGroups = $rowGroups;
  }
  /**
   * @return DimensionGroup[]
   */
  public function getRowGroups()
  {
    return $this->rowGroups;
  }
  /**
   * The slicers on this sheet.
   *
   * @param Slicer[] $slicers
   */
  public function setSlicers($slicers)
  {
    $this->slicers = $slicers;
  }
  /**
   * @return Slicer[]
   */
  public function getSlicers()
  {
    return $this->slicers;
  }
  /**
   * The tables on this sheet.
   *
   * @param Table[] $tables
   */
  public function setTables($tables)
  {
    $this->tables = $tables;
  }
  /**
   * @return Table[]
   */
  public function getTables()
  {
    return $this->tables;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Sheet::class, 'Google_Service_Sheets_Sheet');
