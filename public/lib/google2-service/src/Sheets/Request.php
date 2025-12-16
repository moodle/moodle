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

class Request extends \Google\Model
{
  protected $addBandingType = AddBandingRequest::class;
  protected $addBandingDataType = '';
  protected $addChartType = AddChartRequest::class;
  protected $addChartDataType = '';
  protected $addConditionalFormatRuleType = AddConditionalFormatRuleRequest::class;
  protected $addConditionalFormatRuleDataType = '';
  protected $addDataSourceType = AddDataSourceRequest::class;
  protected $addDataSourceDataType = '';
  protected $addDimensionGroupType = AddDimensionGroupRequest::class;
  protected $addDimensionGroupDataType = '';
  protected $addFilterViewType = AddFilterViewRequest::class;
  protected $addFilterViewDataType = '';
  protected $addNamedRangeType = AddNamedRangeRequest::class;
  protected $addNamedRangeDataType = '';
  protected $addProtectedRangeType = AddProtectedRangeRequest::class;
  protected $addProtectedRangeDataType = '';
  protected $addSheetType = AddSheetRequest::class;
  protected $addSheetDataType = '';
  protected $addSlicerType = AddSlicerRequest::class;
  protected $addSlicerDataType = '';
  protected $addTableType = AddTableRequest::class;
  protected $addTableDataType = '';
  protected $appendCellsType = AppendCellsRequest::class;
  protected $appendCellsDataType = '';
  protected $appendDimensionType = AppendDimensionRequest::class;
  protected $appendDimensionDataType = '';
  protected $autoFillType = AutoFillRequest::class;
  protected $autoFillDataType = '';
  protected $autoResizeDimensionsType = AutoResizeDimensionsRequest::class;
  protected $autoResizeDimensionsDataType = '';
  protected $cancelDataSourceRefreshType = CancelDataSourceRefreshRequest::class;
  protected $cancelDataSourceRefreshDataType = '';
  protected $clearBasicFilterType = ClearBasicFilterRequest::class;
  protected $clearBasicFilterDataType = '';
  protected $copyPasteType = CopyPasteRequest::class;
  protected $copyPasteDataType = '';
  protected $createDeveloperMetadataType = CreateDeveloperMetadataRequest::class;
  protected $createDeveloperMetadataDataType = '';
  protected $cutPasteType = CutPasteRequest::class;
  protected $cutPasteDataType = '';
  protected $deleteBandingType = DeleteBandingRequest::class;
  protected $deleteBandingDataType = '';
  protected $deleteConditionalFormatRuleType = DeleteConditionalFormatRuleRequest::class;
  protected $deleteConditionalFormatRuleDataType = '';
  protected $deleteDataSourceType = DeleteDataSourceRequest::class;
  protected $deleteDataSourceDataType = '';
  protected $deleteDeveloperMetadataType = DeleteDeveloperMetadataRequest::class;
  protected $deleteDeveloperMetadataDataType = '';
  protected $deleteDimensionType = DeleteDimensionRequest::class;
  protected $deleteDimensionDataType = '';
  protected $deleteDimensionGroupType = DeleteDimensionGroupRequest::class;
  protected $deleteDimensionGroupDataType = '';
  protected $deleteDuplicatesType = DeleteDuplicatesRequest::class;
  protected $deleteDuplicatesDataType = '';
  protected $deleteEmbeddedObjectType = DeleteEmbeddedObjectRequest::class;
  protected $deleteEmbeddedObjectDataType = '';
  protected $deleteFilterViewType = DeleteFilterViewRequest::class;
  protected $deleteFilterViewDataType = '';
  protected $deleteNamedRangeType = DeleteNamedRangeRequest::class;
  protected $deleteNamedRangeDataType = '';
  protected $deleteProtectedRangeType = DeleteProtectedRangeRequest::class;
  protected $deleteProtectedRangeDataType = '';
  protected $deleteRangeType = DeleteRangeRequest::class;
  protected $deleteRangeDataType = '';
  protected $deleteSheetType = DeleteSheetRequest::class;
  protected $deleteSheetDataType = '';
  protected $deleteTableType = DeleteTableRequest::class;
  protected $deleteTableDataType = '';
  protected $duplicateFilterViewType = DuplicateFilterViewRequest::class;
  protected $duplicateFilterViewDataType = '';
  protected $duplicateSheetType = DuplicateSheetRequest::class;
  protected $duplicateSheetDataType = '';
  protected $findReplaceType = FindReplaceRequest::class;
  protected $findReplaceDataType = '';
  protected $insertDimensionType = InsertDimensionRequest::class;
  protected $insertDimensionDataType = '';
  protected $insertRangeType = InsertRangeRequest::class;
  protected $insertRangeDataType = '';
  protected $mergeCellsType = MergeCellsRequest::class;
  protected $mergeCellsDataType = '';
  protected $moveDimensionType = MoveDimensionRequest::class;
  protected $moveDimensionDataType = '';
  protected $pasteDataType = PasteDataRequest::class;
  protected $pasteDataDataType = '';
  protected $randomizeRangeType = RandomizeRangeRequest::class;
  protected $randomizeRangeDataType = '';
  protected $refreshDataSourceType = RefreshDataSourceRequest::class;
  protected $refreshDataSourceDataType = '';
  protected $repeatCellType = RepeatCellRequest::class;
  protected $repeatCellDataType = '';
  protected $setBasicFilterType = SetBasicFilterRequest::class;
  protected $setBasicFilterDataType = '';
  protected $setDataValidationType = SetDataValidationRequest::class;
  protected $setDataValidationDataType = '';
  protected $sortRangeType = SortRangeRequest::class;
  protected $sortRangeDataType = '';
  protected $textToColumnsType = TextToColumnsRequest::class;
  protected $textToColumnsDataType = '';
  protected $trimWhitespaceType = TrimWhitespaceRequest::class;
  protected $trimWhitespaceDataType = '';
  protected $unmergeCellsType = UnmergeCellsRequest::class;
  protected $unmergeCellsDataType = '';
  protected $updateBandingType = UpdateBandingRequest::class;
  protected $updateBandingDataType = '';
  protected $updateBordersType = UpdateBordersRequest::class;
  protected $updateBordersDataType = '';
  protected $updateCellsType = UpdateCellsRequest::class;
  protected $updateCellsDataType = '';
  protected $updateChartSpecType = UpdateChartSpecRequest::class;
  protected $updateChartSpecDataType = '';
  protected $updateConditionalFormatRuleType = UpdateConditionalFormatRuleRequest::class;
  protected $updateConditionalFormatRuleDataType = '';
  protected $updateDataSourceType = UpdateDataSourceRequest::class;
  protected $updateDataSourceDataType = '';
  protected $updateDeveloperMetadataType = UpdateDeveloperMetadataRequest::class;
  protected $updateDeveloperMetadataDataType = '';
  protected $updateDimensionGroupType = UpdateDimensionGroupRequest::class;
  protected $updateDimensionGroupDataType = '';
  protected $updateDimensionPropertiesType = UpdateDimensionPropertiesRequest::class;
  protected $updateDimensionPropertiesDataType = '';
  protected $updateEmbeddedObjectBorderType = UpdateEmbeddedObjectBorderRequest::class;
  protected $updateEmbeddedObjectBorderDataType = '';
  protected $updateEmbeddedObjectPositionType = UpdateEmbeddedObjectPositionRequest::class;
  protected $updateEmbeddedObjectPositionDataType = '';
  protected $updateFilterViewType = UpdateFilterViewRequest::class;
  protected $updateFilterViewDataType = '';
  protected $updateNamedRangeType = UpdateNamedRangeRequest::class;
  protected $updateNamedRangeDataType = '';
  protected $updateProtectedRangeType = UpdateProtectedRangeRequest::class;
  protected $updateProtectedRangeDataType = '';
  protected $updateSheetPropertiesType = UpdateSheetPropertiesRequest::class;
  protected $updateSheetPropertiesDataType = '';
  protected $updateSlicerSpecType = UpdateSlicerSpecRequest::class;
  protected $updateSlicerSpecDataType = '';
  protected $updateSpreadsheetPropertiesType = UpdateSpreadsheetPropertiesRequest::class;
  protected $updateSpreadsheetPropertiesDataType = '';
  protected $updateTableType = UpdateTableRequest::class;
  protected $updateTableDataType = '';

  /**
   * Adds a new banded range
   *
   * @param AddBandingRequest $addBanding
   */
  public function setAddBanding(AddBandingRequest $addBanding)
  {
    $this->addBanding = $addBanding;
  }
  /**
   * @return AddBandingRequest
   */
  public function getAddBanding()
  {
    return $this->addBanding;
  }
  /**
   * Adds a chart.
   *
   * @param AddChartRequest $addChart
   */
  public function setAddChart(AddChartRequest $addChart)
  {
    $this->addChart = $addChart;
  }
  /**
   * @return AddChartRequest
   */
  public function getAddChart()
  {
    return $this->addChart;
  }
  /**
   * Adds a new conditional format rule.
   *
   * @param AddConditionalFormatRuleRequest $addConditionalFormatRule
   */
  public function setAddConditionalFormatRule(AddConditionalFormatRuleRequest $addConditionalFormatRule)
  {
    $this->addConditionalFormatRule = $addConditionalFormatRule;
  }
  /**
   * @return AddConditionalFormatRuleRequest
   */
  public function getAddConditionalFormatRule()
  {
    return $this->addConditionalFormatRule;
  }
  /**
   * Adds a data source.
   *
   * @param AddDataSourceRequest $addDataSource
   */
  public function setAddDataSource(AddDataSourceRequest $addDataSource)
  {
    $this->addDataSource = $addDataSource;
  }
  /**
   * @return AddDataSourceRequest
   */
  public function getAddDataSource()
  {
    return $this->addDataSource;
  }
  /**
   * Creates a group over the specified range.
   *
   * @param AddDimensionGroupRequest $addDimensionGroup
   */
  public function setAddDimensionGroup(AddDimensionGroupRequest $addDimensionGroup)
  {
    $this->addDimensionGroup = $addDimensionGroup;
  }
  /**
   * @return AddDimensionGroupRequest
   */
  public function getAddDimensionGroup()
  {
    return $this->addDimensionGroup;
  }
  /**
   * Adds a filter view.
   *
   * @param AddFilterViewRequest $addFilterView
   */
  public function setAddFilterView(AddFilterViewRequest $addFilterView)
  {
    $this->addFilterView = $addFilterView;
  }
  /**
   * @return AddFilterViewRequest
   */
  public function getAddFilterView()
  {
    return $this->addFilterView;
  }
  /**
   * Adds a named range.
   *
   * @param AddNamedRangeRequest $addNamedRange
   */
  public function setAddNamedRange(AddNamedRangeRequest $addNamedRange)
  {
    $this->addNamedRange = $addNamedRange;
  }
  /**
   * @return AddNamedRangeRequest
   */
  public function getAddNamedRange()
  {
    return $this->addNamedRange;
  }
  /**
   * Adds a protected range.
   *
   * @param AddProtectedRangeRequest $addProtectedRange
   */
  public function setAddProtectedRange(AddProtectedRangeRequest $addProtectedRange)
  {
    $this->addProtectedRange = $addProtectedRange;
  }
  /**
   * @return AddProtectedRangeRequest
   */
  public function getAddProtectedRange()
  {
    return $this->addProtectedRange;
  }
  /**
   * Adds a sheet.
   *
   * @param AddSheetRequest $addSheet
   */
  public function setAddSheet(AddSheetRequest $addSheet)
  {
    $this->addSheet = $addSheet;
  }
  /**
   * @return AddSheetRequest
   */
  public function getAddSheet()
  {
    return $this->addSheet;
  }
  /**
   * Adds a slicer.
   *
   * @param AddSlicerRequest $addSlicer
   */
  public function setAddSlicer(AddSlicerRequest $addSlicer)
  {
    $this->addSlicer = $addSlicer;
  }
  /**
   * @return AddSlicerRequest
   */
  public function getAddSlicer()
  {
    return $this->addSlicer;
  }
  /**
   * Adds a table.
   *
   * @param AddTableRequest $addTable
   */
  public function setAddTable(AddTableRequest $addTable)
  {
    $this->addTable = $addTable;
  }
  /**
   * @return AddTableRequest
   */
  public function getAddTable()
  {
    return $this->addTable;
  }
  /**
   * Appends cells after the last row with data in a sheet.
   *
   * @param AppendCellsRequest $appendCells
   */
  public function setAppendCells(AppendCellsRequest $appendCells)
  {
    $this->appendCells = $appendCells;
  }
  /**
   * @return AppendCellsRequest
   */
  public function getAppendCells()
  {
    return $this->appendCells;
  }
  /**
   * Appends dimensions to the end of a sheet.
   *
   * @param AppendDimensionRequest $appendDimension
   */
  public function setAppendDimension(AppendDimensionRequest $appendDimension)
  {
    $this->appendDimension = $appendDimension;
  }
  /**
   * @return AppendDimensionRequest
   */
  public function getAppendDimension()
  {
    return $this->appendDimension;
  }
  /**
   * Automatically fills in more data based on existing data.
   *
   * @param AutoFillRequest $autoFill
   */
  public function setAutoFill(AutoFillRequest $autoFill)
  {
    $this->autoFill = $autoFill;
  }
  /**
   * @return AutoFillRequest
   */
  public function getAutoFill()
  {
    return $this->autoFill;
  }
  /**
   * Automatically resizes one or more dimensions based on the contents of the
   * cells in that dimension.
   *
   * @param AutoResizeDimensionsRequest $autoResizeDimensions
   */
  public function setAutoResizeDimensions(AutoResizeDimensionsRequest $autoResizeDimensions)
  {
    $this->autoResizeDimensions = $autoResizeDimensions;
  }
  /**
   * @return AutoResizeDimensionsRequest
   */
  public function getAutoResizeDimensions()
  {
    return $this->autoResizeDimensions;
  }
  /**
   * Cancels refreshes of one or multiple data sources and associated dbobjects.
   *
   * @param CancelDataSourceRefreshRequest $cancelDataSourceRefresh
   */
  public function setCancelDataSourceRefresh(CancelDataSourceRefreshRequest $cancelDataSourceRefresh)
  {
    $this->cancelDataSourceRefresh = $cancelDataSourceRefresh;
  }
  /**
   * @return CancelDataSourceRefreshRequest
   */
  public function getCancelDataSourceRefresh()
  {
    return $this->cancelDataSourceRefresh;
  }
  /**
   * Clears the basic filter on a sheet.
   *
   * @param ClearBasicFilterRequest $clearBasicFilter
   */
  public function setClearBasicFilter(ClearBasicFilterRequest $clearBasicFilter)
  {
    $this->clearBasicFilter = $clearBasicFilter;
  }
  /**
   * @return ClearBasicFilterRequest
   */
  public function getClearBasicFilter()
  {
    return $this->clearBasicFilter;
  }
  /**
   * Copies data from one area and pastes it to another.
   *
   * @param CopyPasteRequest $copyPaste
   */
  public function setCopyPaste(CopyPasteRequest $copyPaste)
  {
    $this->copyPaste = $copyPaste;
  }
  /**
   * @return CopyPasteRequest
   */
  public function getCopyPaste()
  {
    return $this->copyPaste;
  }
  /**
   * Creates new developer metadata
   *
   * @param CreateDeveloperMetadataRequest $createDeveloperMetadata
   */
  public function setCreateDeveloperMetadata(CreateDeveloperMetadataRequest $createDeveloperMetadata)
  {
    $this->createDeveloperMetadata = $createDeveloperMetadata;
  }
  /**
   * @return CreateDeveloperMetadataRequest
   */
  public function getCreateDeveloperMetadata()
  {
    return $this->createDeveloperMetadata;
  }
  /**
   * Cuts data from one area and pastes it to another.
   *
   * @param CutPasteRequest $cutPaste
   */
  public function setCutPaste(CutPasteRequest $cutPaste)
  {
    $this->cutPaste = $cutPaste;
  }
  /**
   * @return CutPasteRequest
   */
  public function getCutPaste()
  {
    return $this->cutPaste;
  }
  /**
   * Removes a banded range
   *
   * @param DeleteBandingRequest $deleteBanding
   */
  public function setDeleteBanding(DeleteBandingRequest $deleteBanding)
  {
    $this->deleteBanding = $deleteBanding;
  }
  /**
   * @return DeleteBandingRequest
   */
  public function getDeleteBanding()
  {
    return $this->deleteBanding;
  }
  /**
   * Deletes an existing conditional format rule.
   *
   * @param DeleteConditionalFormatRuleRequest $deleteConditionalFormatRule
   */
  public function setDeleteConditionalFormatRule(DeleteConditionalFormatRuleRequest $deleteConditionalFormatRule)
  {
    $this->deleteConditionalFormatRule = $deleteConditionalFormatRule;
  }
  /**
   * @return DeleteConditionalFormatRuleRequest
   */
  public function getDeleteConditionalFormatRule()
  {
    return $this->deleteConditionalFormatRule;
  }
  /**
   * Deletes a data source.
   *
   * @param DeleteDataSourceRequest $deleteDataSource
   */
  public function setDeleteDataSource(DeleteDataSourceRequest $deleteDataSource)
  {
    $this->deleteDataSource = $deleteDataSource;
  }
  /**
   * @return DeleteDataSourceRequest
   */
  public function getDeleteDataSource()
  {
    return $this->deleteDataSource;
  }
  /**
   * Deletes developer metadata
   *
   * @param DeleteDeveloperMetadataRequest $deleteDeveloperMetadata
   */
  public function setDeleteDeveloperMetadata(DeleteDeveloperMetadataRequest $deleteDeveloperMetadata)
  {
    $this->deleteDeveloperMetadata = $deleteDeveloperMetadata;
  }
  /**
   * @return DeleteDeveloperMetadataRequest
   */
  public function getDeleteDeveloperMetadata()
  {
    return $this->deleteDeveloperMetadata;
  }
  /**
   * Deletes rows or columns in a sheet.
   *
   * @param DeleteDimensionRequest $deleteDimension
   */
  public function setDeleteDimension(DeleteDimensionRequest $deleteDimension)
  {
    $this->deleteDimension = $deleteDimension;
  }
  /**
   * @return DeleteDimensionRequest
   */
  public function getDeleteDimension()
  {
    return $this->deleteDimension;
  }
  /**
   * Deletes a group over the specified range.
   *
   * @param DeleteDimensionGroupRequest $deleteDimensionGroup
   */
  public function setDeleteDimensionGroup(DeleteDimensionGroupRequest $deleteDimensionGroup)
  {
    $this->deleteDimensionGroup = $deleteDimensionGroup;
  }
  /**
   * @return DeleteDimensionGroupRequest
   */
  public function getDeleteDimensionGroup()
  {
    return $this->deleteDimensionGroup;
  }
  /**
   * Removes rows containing duplicate values in specified columns of a cell
   * range.
   *
   * @param DeleteDuplicatesRequest $deleteDuplicates
   */
  public function setDeleteDuplicates(DeleteDuplicatesRequest $deleteDuplicates)
  {
    $this->deleteDuplicates = $deleteDuplicates;
  }
  /**
   * @return DeleteDuplicatesRequest
   */
  public function getDeleteDuplicates()
  {
    return $this->deleteDuplicates;
  }
  /**
   * Deletes an embedded object (e.g, chart, image) in a sheet.
   *
   * @param DeleteEmbeddedObjectRequest $deleteEmbeddedObject
   */
  public function setDeleteEmbeddedObject(DeleteEmbeddedObjectRequest $deleteEmbeddedObject)
  {
    $this->deleteEmbeddedObject = $deleteEmbeddedObject;
  }
  /**
   * @return DeleteEmbeddedObjectRequest
   */
  public function getDeleteEmbeddedObject()
  {
    return $this->deleteEmbeddedObject;
  }
  /**
   * Deletes a filter view from a sheet.
   *
   * @param DeleteFilterViewRequest $deleteFilterView
   */
  public function setDeleteFilterView(DeleteFilterViewRequest $deleteFilterView)
  {
    $this->deleteFilterView = $deleteFilterView;
  }
  /**
   * @return DeleteFilterViewRequest
   */
  public function getDeleteFilterView()
  {
    return $this->deleteFilterView;
  }
  /**
   * Deletes a named range.
   *
   * @param DeleteNamedRangeRequest $deleteNamedRange
   */
  public function setDeleteNamedRange(DeleteNamedRangeRequest $deleteNamedRange)
  {
    $this->deleteNamedRange = $deleteNamedRange;
  }
  /**
   * @return DeleteNamedRangeRequest
   */
  public function getDeleteNamedRange()
  {
    return $this->deleteNamedRange;
  }
  /**
   * Deletes a protected range.
   *
   * @param DeleteProtectedRangeRequest $deleteProtectedRange
   */
  public function setDeleteProtectedRange(DeleteProtectedRangeRequest $deleteProtectedRange)
  {
    $this->deleteProtectedRange = $deleteProtectedRange;
  }
  /**
   * @return DeleteProtectedRangeRequest
   */
  public function getDeleteProtectedRange()
  {
    return $this->deleteProtectedRange;
  }
  /**
   * Deletes a range of cells from a sheet, shifting the remaining cells.
   *
   * @param DeleteRangeRequest $deleteRange
   */
  public function setDeleteRange(DeleteRangeRequest $deleteRange)
  {
    $this->deleteRange = $deleteRange;
  }
  /**
   * @return DeleteRangeRequest
   */
  public function getDeleteRange()
  {
    return $this->deleteRange;
  }
  /**
   * Deletes a sheet.
   *
   * @param DeleteSheetRequest $deleteSheet
   */
  public function setDeleteSheet(DeleteSheetRequest $deleteSheet)
  {
    $this->deleteSheet = $deleteSheet;
  }
  /**
   * @return DeleteSheetRequest
   */
  public function getDeleteSheet()
  {
    return $this->deleteSheet;
  }
  /**
   * A request for deleting a table.
   *
   * @param DeleteTableRequest $deleteTable
   */
  public function setDeleteTable(DeleteTableRequest $deleteTable)
  {
    $this->deleteTable = $deleteTable;
  }
  /**
   * @return DeleteTableRequest
   */
  public function getDeleteTable()
  {
    return $this->deleteTable;
  }
  /**
   * Duplicates a filter view.
   *
   * @param DuplicateFilterViewRequest $duplicateFilterView
   */
  public function setDuplicateFilterView(DuplicateFilterViewRequest $duplicateFilterView)
  {
    $this->duplicateFilterView = $duplicateFilterView;
  }
  /**
   * @return DuplicateFilterViewRequest
   */
  public function getDuplicateFilterView()
  {
    return $this->duplicateFilterView;
  }
  /**
   * Duplicates a sheet.
   *
   * @param DuplicateSheetRequest $duplicateSheet
   */
  public function setDuplicateSheet(DuplicateSheetRequest $duplicateSheet)
  {
    $this->duplicateSheet = $duplicateSheet;
  }
  /**
   * @return DuplicateSheetRequest
   */
  public function getDuplicateSheet()
  {
    return $this->duplicateSheet;
  }
  /**
   * Finds and replaces occurrences of some text with other text.
   *
   * @param FindReplaceRequest $findReplace
   */
  public function setFindReplace(FindReplaceRequest $findReplace)
  {
    $this->findReplace = $findReplace;
  }
  /**
   * @return FindReplaceRequest
   */
  public function getFindReplace()
  {
    return $this->findReplace;
  }
  /**
   * Inserts new rows or columns in a sheet.
   *
   * @param InsertDimensionRequest $insertDimension
   */
  public function setInsertDimension(InsertDimensionRequest $insertDimension)
  {
    $this->insertDimension = $insertDimension;
  }
  /**
   * @return InsertDimensionRequest
   */
  public function getInsertDimension()
  {
    return $this->insertDimension;
  }
  /**
   * Inserts new cells in a sheet, shifting the existing cells.
   *
   * @param InsertRangeRequest $insertRange
   */
  public function setInsertRange(InsertRangeRequest $insertRange)
  {
    $this->insertRange = $insertRange;
  }
  /**
   * @return InsertRangeRequest
   */
  public function getInsertRange()
  {
    return $this->insertRange;
  }
  /**
   * Merges cells together.
   *
   * @param MergeCellsRequest $mergeCells
   */
  public function setMergeCells(MergeCellsRequest $mergeCells)
  {
    $this->mergeCells = $mergeCells;
  }
  /**
   * @return MergeCellsRequest
   */
  public function getMergeCells()
  {
    return $this->mergeCells;
  }
  /**
   * Moves rows or columns to another location in a sheet.
   *
   * @param MoveDimensionRequest $moveDimension
   */
  public function setMoveDimension(MoveDimensionRequest $moveDimension)
  {
    $this->moveDimension = $moveDimension;
  }
  /**
   * @return MoveDimensionRequest
   */
  public function getMoveDimension()
  {
    return $this->moveDimension;
  }
  /**
   * Pastes data (HTML or delimited) into a sheet.
   *
   * @param PasteDataRequest $pasteData
   */
  public function setPasteData(PasteDataRequest $pasteData)
  {
    $this->pasteData = $pasteData;
  }
  /**
   * @return PasteDataRequest
   */
  public function getPasteData()
  {
    return $this->pasteData;
  }
  /**
   * Randomizes the order of the rows in a range.
   *
   * @param RandomizeRangeRequest $randomizeRange
   */
  public function setRandomizeRange(RandomizeRangeRequest $randomizeRange)
  {
    $this->randomizeRange = $randomizeRange;
  }
  /**
   * @return RandomizeRangeRequest
   */
  public function getRandomizeRange()
  {
    return $this->randomizeRange;
  }
  /**
   * Refreshes one or multiple data sources and associated dbobjects.
   *
   * @param RefreshDataSourceRequest $refreshDataSource
   */
  public function setRefreshDataSource(RefreshDataSourceRequest $refreshDataSource)
  {
    $this->refreshDataSource = $refreshDataSource;
  }
  /**
   * @return RefreshDataSourceRequest
   */
  public function getRefreshDataSource()
  {
    return $this->refreshDataSource;
  }
  /**
   * Repeats a single cell across a range.
   *
   * @param RepeatCellRequest $repeatCell
   */
  public function setRepeatCell(RepeatCellRequest $repeatCell)
  {
    $this->repeatCell = $repeatCell;
  }
  /**
   * @return RepeatCellRequest
   */
  public function getRepeatCell()
  {
    return $this->repeatCell;
  }
  /**
   * Sets the basic filter on a sheet.
   *
   * @param SetBasicFilterRequest $setBasicFilter
   */
  public function setSetBasicFilter(SetBasicFilterRequest $setBasicFilter)
  {
    $this->setBasicFilter = $setBasicFilter;
  }
  /**
   * @return SetBasicFilterRequest
   */
  public function getSetBasicFilter()
  {
    return $this->setBasicFilter;
  }
  /**
   * Sets data validation for one or more cells.
   *
   * @param SetDataValidationRequest $setDataValidation
   */
  public function setSetDataValidation(SetDataValidationRequest $setDataValidation)
  {
    $this->setDataValidation = $setDataValidation;
  }
  /**
   * @return SetDataValidationRequest
   */
  public function getSetDataValidation()
  {
    return $this->setDataValidation;
  }
  /**
   * Sorts data in a range.
   *
   * @param SortRangeRequest $sortRange
   */
  public function setSortRange(SortRangeRequest $sortRange)
  {
    $this->sortRange = $sortRange;
  }
  /**
   * @return SortRangeRequest
   */
  public function getSortRange()
  {
    return $this->sortRange;
  }
  /**
   * Converts a column of text into many columns of text.
   *
   * @param TextToColumnsRequest $textToColumns
   */
  public function setTextToColumns(TextToColumnsRequest $textToColumns)
  {
    $this->textToColumns = $textToColumns;
  }
  /**
   * @return TextToColumnsRequest
   */
  public function getTextToColumns()
  {
    return $this->textToColumns;
  }
  /**
   * Trims cells of whitespace (such as spaces, tabs, or new lines).
   *
   * @param TrimWhitespaceRequest $trimWhitespace
   */
  public function setTrimWhitespace(TrimWhitespaceRequest $trimWhitespace)
  {
    $this->trimWhitespace = $trimWhitespace;
  }
  /**
   * @return TrimWhitespaceRequest
   */
  public function getTrimWhitespace()
  {
    return $this->trimWhitespace;
  }
  /**
   * Unmerges merged cells.
   *
   * @param UnmergeCellsRequest $unmergeCells
   */
  public function setUnmergeCells(UnmergeCellsRequest $unmergeCells)
  {
    $this->unmergeCells = $unmergeCells;
  }
  /**
   * @return UnmergeCellsRequest
   */
  public function getUnmergeCells()
  {
    return $this->unmergeCells;
  }
  /**
   * Updates a banded range
   *
   * @param UpdateBandingRequest $updateBanding
   */
  public function setUpdateBanding(UpdateBandingRequest $updateBanding)
  {
    $this->updateBanding = $updateBanding;
  }
  /**
   * @return UpdateBandingRequest
   */
  public function getUpdateBanding()
  {
    return $this->updateBanding;
  }
  /**
   * Updates the borders in a range of cells.
   *
   * @param UpdateBordersRequest $updateBorders
   */
  public function setUpdateBorders(UpdateBordersRequest $updateBorders)
  {
    $this->updateBorders = $updateBorders;
  }
  /**
   * @return UpdateBordersRequest
   */
  public function getUpdateBorders()
  {
    return $this->updateBorders;
  }
  /**
   * Updates many cells at once.
   *
   * @param UpdateCellsRequest $updateCells
   */
  public function setUpdateCells(UpdateCellsRequest $updateCells)
  {
    $this->updateCells = $updateCells;
  }
  /**
   * @return UpdateCellsRequest
   */
  public function getUpdateCells()
  {
    return $this->updateCells;
  }
  /**
   * Updates a chart's specifications.
   *
   * @param UpdateChartSpecRequest $updateChartSpec
   */
  public function setUpdateChartSpec(UpdateChartSpecRequest $updateChartSpec)
  {
    $this->updateChartSpec = $updateChartSpec;
  }
  /**
   * @return UpdateChartSpecRequest
   */
  public function getUpdateChartSpec()
  {
    return $this->updateChartSpec;
  }
  /**
   * Updates an existing conditional format rule.
   *
   * @param UpdateConditionalFormatRuleRequest $updateConditionalFormatRule
   */
  public function setUpdateConditionalFormatRule(UpdateConditionalFormatRuleRequest $updateConditionalFormatRule)
  {
    $this->updateConditionalFormatRule = $updateConditionalFormatRule;
  }
  /**
   * @return UpdateConditionalFormatRuleRequest
   */
  public function getUpdateConditionalFormatRule()
  {
    return $this->updateConditionalFormatRule;
  }
  /**
   * Updates a data source.
   *
   * @param UpdateDataSourceRequest $updateDataSource
   */
  public function setUpdateDataSource(UpdateDataSourceRequest $updateDataSource)
  {
    $this->updateDataSource = $updateDataSource;
  }
  /**
   * @return UpdateDataSourceRequest
   */
  public function getUpdateDataSource()
  {
    return $this->updateDataSource;
  }
  /**
   * Updates an existing developer metadata entry
   *
   * @param UpdateDeveloperMetadataRequest $updateDeveloperMetadata
   */
  public function setUpdateDeveloperMetadata(UpdateDeveloperMetadataRequest $updateDeveloperMetadata)
  {
    $this->updateDeveloperMetadata = $updateDeveloperMetadata;
  }
  /**
   * @return UpdateDeveloperMetadataRequest
   */
  public function getUpdateDeveloperMetadata()
  {
    return $this->updateDeveloperMetadata;
  }
  /**
   * Updates the state of the specified group.
   *
   * @param UpdateDimensionGroupRequest $updateDimensionGroup
   */
  public function setUpdateDimensionGroup(UpdateDimensionGroupRequest $updateDimensionGroup)
  {
    $this->updateDimensionGroup = $updateDimensionGroup;
  }
  /**
   * @return UpdateDimensionGroupRequest
   */
  public function getUpdateDimensionGroup()
  {
    return $this->updateDimensionGroup;
  }
  /**
   * Updates dimensions' properties.
   *
   * @param UpdateDimensionPropertiesRequest $updateDimensionProperties
   */
  public function setUpdateDimensionProperties(UpdateDimensionPropertiesRequest $updateDimensionProperties)
  {
    $this->updateDimensionProperties = $updateDimensionProperties;
  }
  /**
   * @return UpdateDimensionPropertiesRequest
   */
  public function getUpdateDimensionProperties()
  {
    return $this->updateDimensionProperties;
  }
  /**
   * Updates an embedded object's border.
   *
   * @param UpdateEmbeddedObjectBorderRequest $updateEmbeddedObjectBorder
   */
  public function setUpdateEmbeddedObjectBorder(UpdateEmbeddedObjectBorderRequest $updateEmbeddedObjectBorder)
  {
    $this->updateEmbeddedObjectBorder = $updateEmbeddedObjectBorder;
  }
  /**
   * @return UpdateEmbeddedObjectBorderRequest
   */
  public function getUpdateEmbeddedObjectBorder()
  {
    return $this->updateEmbeddedObjectBorder;
  }
  /**
   * Updates an embedded object's (e.g. chart, image) position.
   *
   * @param UpdateEmbeddedObjectPositionRequest $updateEmbeddedObjectPosition
   */
  public function setUpdateEmbeddedObjectPosition(UpdateEmbeddedObjectPositionRequest $updateEmbeddedObjectPosition)
  {
    $this->updateEmbeddedObjectPosition = $updateEmbeddedObjectPosition;
  }
  /**
   * @return UpdateEmbeddedObjectPositionRequest
   */
  public function getUpdateEmbeddedObjectPosition()
  {
    return $this->updateEmbeddedObjectPosition;
  }
  /**
   * Updates the properties of a filter view.
   *
   * @param UpdateFilterViewRequest $updateFilterView
   */
  public function setUpdateFilterView(UpdateFilterViewRequest $updateFilterView)
  {
    $this->updateFilterView = $updateFilterView;
  }
  /**
   * @return UpdateFilterViewRequest
   */
  public function getUpdateFilterView()
  {
    return $this->updateFilterView;
  }
  /**
   * Updates a named range.
   *
   * @param UpdateNamedRangeRequest $updateNamedRange
   */
  public function setUpdateNamedRange(UpdateNamedRangeRequest $updateNamedRange)
  {
    $this->updateNamedRange = $updateNamedRange;
  }
  /**
   * @return UpdateNamedRangeRequest
   */
  public function getUpdateNamedRange()
  {
    return $this->updateNamedRange;
  }
  /**
   * Updates a protected range.
   *
   * @param UpdateProtectedRangeRequest $updateProtectedRange
   */
  public function setUpdateProtectedRange(UpdateProtectedRangeRequest $updateProtectedRange)
  {
    $this->updateProtectedRange = $updateProtectedRange;
  }
  /**
   * @return UpdateProtectedRangeRequest
   */
  public function getUpdateProtectedRange()
  {
    return $this->updateProtectedRange;
  }
  /**
   * Updates a sheet's properties.
   *
   * @param UpdateSheetPropertiesRequest $updateSheetProperties
   */
  public function setUpdateSheetProperties(UpdateSheetPropertiesRequest $updateSheetProperties)
  {
    $this->updateSheetProperties = $updateSheetProperties;
  }
  /**
   * @return UpdateSheetPropertiesRequest
   */
  public function getUpdateSheetProperties()
  {
    return $this->updateSheetProperties;
  }
  /**
   * Updates a slicer's specifications.
   *
   * @param UpdateSlicerSpecRequest $updateSlicerSpec
   */
  public function setUpdateSlicerSpec(UpdateSlicerSpecRequest $updateSlicerSpec)
  {
    $this->updateSlicerSpec = $updateSlicerSpec;
  }
  /**
   * @return UpdateSlicerSpecRequest
   */
  public function getUpdateSlicerSpec()
  {
    return $this->updateSlicerSpec;
  }
  /**
   * Updates the spreadsheet's properties.
   *
   * @param UpdateSpreadsheetPropertiesRequest $updateSpreadsheetProperties
   */
  public function setUpdateSpreadsheetProperties(UpdateSpreadsheetPropertiesRequest $updateSpreadsheetProperties)
  {
    $this->updateSpreadsheetProperties = $updateSpreadsheetProperties;
  }
  /**
   * @return UpdateSpreadsheetPropertiesRequest
   */
  public function getUpdateSpreadsheetProperties()
  {
    return $this->updateSpreadsheetProperties;
  }
  /**
   * Updates a table.
   *
   * @param UpdateTableRequest $updateTable
   */
  public function setUpdateTable(UpdateTableRequest $updateTable)
  {
    $this->updateTable = $updateTable;
  }
  /**
   * @return UpdateTableRequest
   */
  public function getUpdateTable()
  {
    return $this->updateTable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Request::class, 'Google_Service_Sheets_Request');
