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

class Response extends \Google\Model
{
  protected $addBandingType = AddBandingResponse::class;
  protected $addBandingDataType = '';
  protected $addChartType = AddChartResponse::class;
  protected $addChartDataType = '';
  protected $addDataSourceType = AddDataSourceResponse::class;
  protected $addDataSourceDataType = '';
  protected $addDimensionGroupType = AddDimensionGroupResponse::class;
  protected $addDimensionGroupDataType = '';
  protected $addFilterViewType = AddFilterViewResponse::class;
  protected $addFilterViewDataType = '';
  protected $addNamedRangeType = AddNamedRangeResponse::class;
  protected $addNamedRangeDataType = '';
  protected $addProtectedRangeType = AddProtectedRangeResponse::class;
  protected $addProtectedRangeDataType = '';
  protected $addSheetType = AddSheetResponse::class;
  protected $addSheetDataType = '';
  protected $addSlicerType = AddSlicerResponse::class;
  protected $addSlicerDataType = '';
  protected $addTableType = AddTableResponse::class;
  protected $addTableDataType = '';
  protected $cancelDataSourceRefreshType = CancelDataSourceRefreshResponse::class;
  protected $cancelDataSourceRefreshDataType = '';
  protected $createDeveloperMetadataType = CreateDeveloperMetadataResponse::class;
  protected $createDeveloperMetadataDataType = '';
  protected $deleteConditionalFormatRuleType = DeleteConditionalFormatRuleResponse::class;
  protected $deleteConditionalFormatRuleDataType = '';
  protected $deleteDeveloperMetadataType = DeleteDeveloperMetadataResponse::class;
  protected $deleteDeveloperMetadataDataType = '';
  protected $deleteDimensionGroupType = DeleteDimensionGroupResponse::class;
  protected $deleteDimensionGroupDataType = '';
  protected $deleteDuplicatesType = DeleteDuplicatesResponse::class;
  protected $deleteDuplicatesDataType = '';
  protected $duplicateFilterViewType = DuplicateFilterViewResponse::class;
  protected $duplicateFilterViewDataType = '';
  protected $duplicateSheetType = DuplicateSheetResponse::class;
  protected $duplicateSheetDataType = '';
  protected $findReplaceType = FindReplaceResponse::class;
  protected $findReplaceDataType = '';
  protected $refreshDataSourceType = RefreshDataSourceResponse::class;
  protected $refreshDataSourceDataType = '';
  protected $trimWhitespaceType = TrimWhitespaceResponse::class;
  protected $trimWhitespaceDataType = '';
  protected $updateConditionalFormatRuleType = UpdateConditionalFormatRuleResponse::class;
  protected $updateConditionalFormatRuleDataType = '';
  protected $updateDataSourceType = UpdateDataSourceResponse::class;
  protected $updateDataSourceDataType = '';
  protected $updateDeveloperMetadataType = UpdateDeveloperMetadataResponse::class;
  protected $updateDeveloperMetadataDataType = '';
  protected $updateEmbeddedObjectPositionType = UpdateEmbeddedObjectPositionResponse::class;
  protected $updateEmbeddedObjectPositionDataType = '';

  /**
   * A reply from adding a banded range.
   *
   * @param AddBandingResponse $addBanding
   */
  public function setAddBanding(AddBandingResponse $addBanding)
  {
    $this->addBanding = $addBanding;
  }
  /**
   * @return AddBandingResponse
   */
  public function getAddBanding()
  {
    return $this->addBanding;
  }
  /**
   * A reply from adding a chart.
   *
   * @param AddChartResponse $addChart
   */
  public function setAddChart(AddChartResponse $addChart)
  {
    $this->addChart = $addChart;
  }
  /**
   * @return AddChartResponse
   */
  public function getAddChart()
  {
    return $this->addChart;
  }
  /**
   * A reply from adding a data source.
   *
   * @param AddDataSourceResponse $addDataSource
   */
  public function setAddDataSource(AddDataSourceResponse $addDataSource)
  {
    $this->addDataSource = $addDataSource;
  }
  /**
   * @return AddDataSourceResponse
   */
  public function getAddDataSource()
  {
    return $this->addDataSource;
  }
  /**
   * A reply from adding a dimension group.
   *
   * @param AddDimensionGroupResponse $addDimensionGroup
   */
  public function setAddDimensionGroup(AddDimensionGroupResponse $addDimensionGroup)
  {
    $this->addDimensionGroup = $addDimensionGroup;
  }
  /**
   * @return AddDimensionGroupResponse
   */
  public function getAddDimensionGroup()
  {
    return $this->addDimensionGroup;
  }
  /**
   * A reply from adding a filter view.
   *
   * @param AddFilterViewResponse $addFilterView
   */
  public function setAddFilterView(AddFilterViewResponse $addFilterView)
  {
    $this->addFilterView = $addFilterView;
  }
  /**
   * @return AddFilterViewResponse
   */
  public function getAddFilterView()
  {
    return $this->addFilterView;
  }
  /**
   * A reply from adding a named range.
   *
   * @param AddNamedRangeResponse $addNamedRange
   */
  public function setAddNamedRange(AddNamedRangeResponse $addNamedRange)
  {
    $this->addNamedRange = $addNamedRange;
  }
  /**
   * @return AddNamedRangeResponse
   */
  public function getAddNamedRange()
  {
    return $this->addNamedRange;
  }
  /**
   * A reply from adding a protected range.
   *
   * @param AddProtectedRangeResponse $addProtectedRange
   */
  public function setAddProtectedRange(AddProtectedRangeResponse $addProtectedRange)
  {
    $this->addProtectedRange = $addProtectedRange;
  }
  /**
   * @return AddProtectedRangeResponse
   */
  public function getAddProtectedRange()
  {
    return $this->addProtectedRange;
  }
  /**
   * A reply from adding a sheet.
   *
   * @param AddSheetResponse $addSheet
   */
  public function setAddSheet(AddSheetResponse $addSheet)
  {
    $this->addSheet = $addSheet;
  }
  /**
   * @return AddSheetResponse
   */
  public function getAddSheet()
  {
    return $this->addSheet;
  }
  /**
   * A reply from adding a slicer.
   *
   * @param AddSlicerResponse $addSlicer
   */
  public function setAddSlicer(AddSlicerResponse $addSlicer)
  {
    $this->addSlicer = $addSlicer;
  }
  /**
   * @return AddSlicerResponse
   */
  public function getAddSlicer()
  {
    return $this->addSlicer;
  }
  /**
   * A reply from adding a table.
   *
   * @param AddTableResponse $addTable
   */
  public function setAddTable(AddTableResponse $addTable)
  {
    $this->addTable = $addTable;
  }
  /**
   * @return AddTableResponse
   */
  public function getAddTable()
  {
    return $this->addTable;
  }
  /**
   * A reply from cancelling data source object refreshes.
   *
   * @param CancelDataSourceRefreshResponse $cancelDataSourceRefresh
   */
  public function setCancelDataSourceRefresh(CancelDataSourceRefreshResponse $cancelDataSourceRefresh)
  {
    $this->cancelDataSourceRefresh = $cancelDataSourceRefresh;
  }
  /**
   * @return CancelDataSourceRefreshResponse
   */
  public function getCancelDataSourceRefresh()
  {
    return $this->cancelDataSourceRefresh;
  }
  /**
   * A reply from creating a developer metadata entry.
   *
   * @param CreateDeveloperMetadataResponse $createDeveloperMetadata
   */
  public function setCreateDeveloperMetadata(CreateDeveloperMetadataResponse $createDeveloperMetadata)
  {
    $this->createDeveloperMetadata = $createDeveloperMetadata;
  }
  /**
   * @return CreateDeveloperMetadataResponse
   */
  public function getCreateDeveloperMetadata()
  {
    return $this->createDeveloperMetadata;
  }
  /**
   * A reply from deleting a conditional format rule.
   *
   * @param DeleteConditionalFormatRuleResponse $deleteConditionalFormatRule
   */
  public function setDeleteConditionalFormatRule(DeleteConditionalFormatRuleResponse $deleteConditionalFormatRule)
  {
    $this->deleteConditionalFormatRule = $deleteConditionalFormatRule;
  }
  /**
   * @return DeleteConditionalFormatRuleResponse
   */
  public function getDeleteConditionalFormatRule()
  {
    return $this->deleteConditionalFormatRule;
  }
  /**
   * A reply from deleting a developer metadata entry.
   *
   * @param DeleteDeveloperMetadataResponse $deleteDeveloperMetadata
   */
  public function setDeleteDeveloperMetadata(DeleteDeveloperMetadataResponse $deleteDeveloperMetadata)
  {
    $this->deleteDeveloperMetadata = $deleteDeveloperMetadata;
  }
  /**
   * @return DeleteDeveloperMetadataResponse
   */
  public function getDeleteDeveloperMetadata()
  {
    return $this->deleteDeveloperMetadata;
  }
  /**
   * A reply from deleting a dimension group.
   *
   * @param DeleteDimensionGroupResponse $deleteDimensionGroup
   */
  public function setDeleteDimensionGroup(DeleteDimensionGroupResponse $deleteDimensionGroup)
  {
    $this->deleteDimensionGroup = $deleteDimensionGroup;
  }
  /**
   * @return DeleteDimensionGroupResponse
   */
  public function getDeleteDimensionGroup()
  {
    return $this->deleteDimensionGroup;
  }
  /**
   * A reply from removing rows containing duplicate values.
   *
   * @param DeleteDuplicatesResponse $deleteDuplicates
   */
  public function setDeleteDuplicates(DeleteDuplicatesResponse $deleteDuplicates)
  {
    $this->deleteDuplicates = $deleteDuplicates;
  }
  /**
   * @return DeleteDuplicatesResponse
   */
  public function getDeleteDuplicates()
  {
    return $this->deleteDuplicates;
  }
  /**
   * A reply from duplicating a filter view.
   *
   * @param DuplicateFilterViewResponse $duplicateFilterView
   */
  public function setDuplicateFilterView(DuplicateFilterViewResponse $duplicateFilterView)
  {
    $this->duplicateFilterView = $duplicateFilterView;
  }
  /**
   * @return DuplicateFilterViewResponse
   */
  public function getDuplicateFilterView()
  {
    return $this->duplicateFilterView;
  }
  /**
   * A reply from duplicating a sheet.
   *
   * @param DuplicateSheetResponse $duplicateSheet
   */
  public function setDuplicateSheet(DuplicateSheetResponse $duplicateSheet)
  {
    $this->duplicateSheet = $duplicateSheet;
  }
  /**
   * @return DuplicateSheetResponse
   */
  public function getDuplicateSheet()
  {
    return $this->duplicateSheet;
  }
  /**
   * A reply from doing a find/replace.
   *
   * @param FindReplaceResponse $findReplace
   */
  public function setFindReplace(FindReplaceResponse $findReplace)
  {
    $this->findReplace = $findReplace;
  }
  /**
   * @return FindReplaceResponse
   */
  public function getFindReplace()
  {
    return $this->findReplace;
  }
  /**
   * A reply from refreshing data source objects.
   *
   * @param RefreshDataSourceResponse $refreshDataSource
   */
  public function setRefreshDataSource(RefreshDataSourceResponse $refreshDataSource)
  {
    $this->refreshDataSource = $refreshDataSource;
  }
  /**
   * @return RefreshDataSourceResponse
   */
  public function getRefreshDataSource()
  {
    return $this->refreshDataSource;
  }
  /**
   * A reply from trimming whitespace.
   *
   * @param TrimWhitespaceResponse $trimWhitespace
   */
  public function setTrimWhitespace(TrimWhitespaceResponse $trimWhitespace)
  {
    $this->trimWhitespace = $trimWhitespace;
  }
  /**
   * @return TrimWhitespaceResponse
   */
  public function getTrimWhitespace()
  {
    return $this->trimWhitespace;
  }
  /**
   * A reply from updating a conditional format rule.
   *
   * @param UpdateConditionalFormatRuleResponse $updateConditionalFormatRule
   */
  public function setUpdateConditionalFormatRule(UpdateConditionalFormatRuleResponse $updateConditionalFormatRule)
  {
    $this->updateConditionalFormatRule = $updateConditionalFormatRule;
  }
  /**
   * @return UpdateConditionalFormatRuleResponse
   */
  public function getUpdateConditionalFormatRule()
  {
    return $this->updateConditionalFormatRule;
  }
  /**
   * A reply from updating a data source.
   *
   * @param UpdateDataSourceResponse $updateDataSource
   */
  public function setUpdateDataSource(UpdateDataSourceResponse $updateDataSource)
  {
    $this->updateDataSource = $updateDataSource;
  }
  /**
   * @return UpdateDataSourceResponse
   */
  public function getUpdateDataSource()
  {
    return $this->updateDataSource;
  }
  /**
   * A reply from updating a developer metadata entry.
   *
   * @param UpdateDeveloperMetadataResponse $updateDeveloperMetadata
   */
  public function setUpdateDeveloperMetadata(UpdateDeveloperMetadataResponse $updateDeveloperMetadata)
  {
    $this->updateDeveloperMetadata = $updateDeveloperMetadata;
  }
  /**
   * @return UpdateDeveloperMetadataResponse
   */
  public function getUpdateDeveloperMetadata()
  {
    return $this->updateDeveloperMetadata;
  }
  /**
   * A reply from updating an embedded object's position.
   *
   * @param UpdateEmbeddedObjectPositionResponse $updateEmbeddedObjectPosition
   */
  public function setUpdateEmbeddedObjectPosition(UpdateEmbeddedObjectPositionResponse $updateEmbeddedObjectPosition)
  {
    $this->updateEmbeddedObjectPosition = $updateEmbeddedObjectPosition;
  }
  /**
   * @return UpdateEmbeddedObjectPositionResponse
   */
  public function getUpdateEmbeddedObjectPosition()
  {
    return $this->updateEmbeddedObjectPosition;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Response::class, 'Google_Service_Sheets_Response');
