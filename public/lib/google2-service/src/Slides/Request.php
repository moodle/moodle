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

namespace Google\Service\Slides;

class Request extends \Google\Model
{
  protected $createImageType = CreateImageRequest::class;
  protected $createImageDataType = '';
  protected $createLineType = CreateLineRequest::class;
  protected $createLineDataType = '';
  protected $createParagraphBulletsType = CreateParagraphBulletsRequest::class;
  protected $createParagraphBulletsDataType = '';
  protected $createShapeType = CreateShapeRequest::class;
  protected $createShapeDataType = '';
  protected $createSheetsChartType = CreateSheetsChartRequest::class;
  protected $createSheetsChartDataType = '';
  protected $createSlideType = CreateSlideRequest::class;
  protected $createSlideDataType = '';
  protected $createTableType = CreateTableRequest::class;
  protected $createTableDataType = '';
  protected $createVideoType = CreateVideoRequest::class;
  protected $createVideoDataType = '';
  protected $deleteObjectType = DeleteObjectRequest::class;
  protected $deleteObjectDataType = '';
  protected $deleteParagraphBulletsType = DeleteParagraphBulletsRequest::class;
  protected $deleteParagraphBulletsDataType = '';
  protected $deleteTableColumnType = DeleteTableColumnRequest::class;
  protected $deleteTableColumnDataType = '';
  protected $deleteTableRowType = DeleteTableRowRequest::class;
  protected $deleteTableRowDataType = '';
  protected $deleteTextType = DeleteTextRequest::class;
  protected $deleteTextDataType = '';
  protected $duplicateObjectType = DuplicateObjectRequest::class;
  protected $duplicateObjectDataType = '';
  protected $groupObjectsType = GroupObjectsRequest::class;
  protected $groupObjectsDataType = '';
  protected $insertTableColumnsType = InsertTableColumnsRequest::class;
  protected $insertTableColumnsDataType = '';
  protected $insertTableRowsType = InsertTableRowsRequest::class;
  protected $insertTableRowsDataType = '';
  protected $insertTextType = InsertTextRequest::class;
  protected $insertTextDataType = '';
  protected $mergeTableCellsType = MergeTableCellsRequest::class;
  protected $mergeTableCellsDataType = '';
  protected $refreshSheetsChartType = RefreshSheetsChartRequest::class;
  protected $refreshSheetsChartDataType = '';
  protected $replaceAllShapesWithImageType = ReplaceAllShapesWithImageRequest::class;
  protected $replaceAllShapesWithImageDataType = '';
  protected $replaceAllShapesWithSheetsChartType = ReplaceAllShapesWithSheetsChartRequest::class;
  protected $replaceAllShapesWithSheetsChartDataType = '';
  protected $replaceAllTextType = ReplaceAllTextRequest::class;
  protected $replaceAllTextDataType = '';
  protected $replaceImageType = ReplaceImageRequest::class;
  protected $replaceImageDataType = '';
  protected $rerouteLineType = RerouteLineRequest::class;
  protected $rerouteLineDataType = '';
  protected $ungroupObjectsType = UngroupObjectsRequest::class;
  protected $ungroupObjectsDataType = '';
  protected $unmergeTableCellsType = UnmergeTableCellsRequest::class;
  protected $unmergeTableCellsDataType = '';
  protected $updateImagePropertiesType = UpdateImagePropertiesRequest::class;
  protected $updateImagePropertiesDataType = '';
  protected $updateLineCategoryType = UpdateLineCategoryRequest::class;
  protected $updateLineCategoryDataType = '';
  protected $updateLinePropertiesType = UpdateLinePropertiesRequest::class;
  protected $updateLinePropertiesDataType = '';
  protected $updatePageElementAltTextType = UpdatePageElementAltTextRequest::class;
  protected $updatePageElementAltTextDataType = '';
  protected $updatePageElementTransformType = UpdatePageElementTransformRequest::class;
  protected $updatePageElementTransformDataType = '';
  protected $updatePageElementsZOrderType = UpdatePageElementsZOrderRequest::class;
  protected $updatePageElementsZOrderDataType = '';
  protected $updatePagePropertiesType = UpdatePagePropertiesRequest::class;
  protected $updatePagePropertiesDataType = '';
  protected $updateParagraphStyleType = UpdateParagraphStyleRequest::class;
  protected $updateParagraphStyleDataType = '';
  protected $updateShapePropertiesType = UpdateShapePropertiesRequest::class;
  protected $updateShapePropertiesDataType = '';
  protected $updateSlidePropertiesType = UpdateSlidePropertiesRequest::class;
  protected $updateSlidePropertiesDataType = '';
  protected $updateSlidesPositionType = UpdateSlidesPositionRequest::class;
  protected $updateSlidesPositionDataType = '';
  protected $updateTableBorderPropertiesType = UpdateTableBorderPropertiesRequest::class;
  protected $updateTableBorderPropertiesDataType = '';
  protected $updateTableCellPropertiesType = UpdateTableCellPropertiesRequest::class;
  protected $updateTableCellPropertiesDataType = '';
  protected $updateTableColumnPropertiesType = UpdateTableColumnPropertiesRequest::class;
  protected $updateTableColumnPropertiesDataType = '';
  protected $updateTableRowPropertiesType = UpdateTableRowPropertiesRequest::class;
  protected $updateTableRowPropertiesDataType = '';
  protected $updateTextStyleType = UpdateTextStyleRequest::class;
  protected $updateTextStyleDataType = '';
  protected $updateVideoPropertiesType = UpdateVideoPropertiesRequest::class;
  protected $updateVideoPropertiesDataType = '';

  /**
   * Creates an image.
   *
   * @param CreateImageRequest $createImage
   */
  public function setCreateImage(CreateImageRequest $createImage)
  {
    $this->createImage = $createImage;
  }
  /**
   * @return CreateImageRequest
   */
  public function getCreateImage()
  {
    return $this->createImage;
  }
  /**
   * Creates a line.
   *
   * @param CreateLineRequest $createLine
   */
  public function setCreateLine(CreateLineRequest $createLine)
  {
    $this->createLine = $createLine;
  }
  /**
   * @return CreateLineRequest
   */
  public function getCreateLine()
  {
    return $this->createLine;
  }
  /**
   * Creates bullets for paragraphs.
   *
   * @param CreateParagraphBulletsRequest $createParagraphBullets
   */
  public function setCreateParagraphBullets(CreateParagraphBulletsRequest $createParagraphBullets)
  {
    $this->createParagraphBullets = $createParagraphBullets;
  }
  /**
   * @return CreateParagraphBulletsRequest
   */
  public function getCreateParagraphBullets()
  {
    return $this->createParagraphBullets;
  }
  /**
   * Creates a new shape.
   *
   * @param CreateShapeRequest $createShape
   */
  public function setCreateShape(CreateShapeRequest $createShape)
  {
    $this->createShape = $createShape;
  }
  /**
   * @return CreateShapeRequest
   */
  public function getCreateShape()
  {
    return $this->createShape;
  }
  /**
   * Creates an embedded Google Sheets chart.
   *
   * @param CreateSheetsChartRequest $createSheetsChart
   */
  public function setCreateSheetsChart(CreateSheetsChartRequest $createSheetsChart)
  {
    $this->createSheetsChart = $createSheetsChart;
  }
  /**
   * @return CreateSheetsChartRequest
   */
  public function getCreateSheetsChart()
  {
    return $this->createSheetsChart;
  }
  /**
   * Creates a new slide.
   *
   * @param CreateSlideRequest $createSlide
   */
  public function setCreateSlide(CreateSlideRequest $createSlide)
  {
    $this->createSlide = $createSlide;
  }
  /**
   * @return CreateSlideRequest
   */
  public function getCreateSlide()
  {
    return $this->createSlide;
  }
  /**
   * Creates a new table.
   *
   * @param CreateTableRequest $createTable
   */
  public function setCreateTable(CreateTableRequest $createTable)
  {
    $this->createTable = $createTable;
  }
  /**
   * @return CreateTableRequest
   */
  public function getCreateTable()
  {
    return $this->createTable;
  }
  /**
   * Creates a video.
   *
   * @param CreateVideoRequest $createVideo
   */
  public function setCreateVideo(CreateVideoRequest $createVideo)
  {
    $this->createVideo = $createVideo;
  }
  /**
   * @return CreateVideoRequest
   */
  public function getCreateVideo()
  {
    return $this->createVideo;
  }
  /**
   * Deletes a page or page element from the presentation.
   *
   * @param DeleteObjectRequest $deleteObject
   */
  public function setDeleteObject(DeleteObjectRequest $deleteObject)
  {
    $this->deleteObject = $deleteObject;
  }
  /**
   * @return DeleteObjectRequest
   */
  public function getDeleteObject()
  {
    return $this->deleteObject;
  }
  /**
   * Deletes bullets from paragraphs.
   *
   * @param DeleteParagraphBulletsRequest $deleteParagraphBullets
   */
  public function setDeleteParagraphBullets(DeleteParagraphBulletsRequest $deleteParagraphBullets)
  {
    $this->deleteParagraphBullets = $deleteParagraphBullets;
  }
  /**
   * @return DeleteParagraphBulletsRequest
   */
  public function getDeleteParagraphBullets()
  {
    return $this->deleteParagraphBullets;
  }
  /**
   * Deletes a column from a table.
   *
   * @param DeleteTableColumnRequest $deleteTableColumn
   */
  public function setDeleteTableColumn(DeleteTableColumnRequest $deleteTableColumn)
  {
    $this->deleteTableColumn = $deleteTableColumn;
  }
  /**
   * @return DeleteTableColumnRequest
   */
  public function getDeleteTableColumn()
  {
    return $this->deleteTableColumn;
  }
  /**
   * Deletes a row from a table.
   *
   * @param DeleteTableRowRequest $deleteTableRow
   */
  public function setDeleteTableRow(DeleteTableRowRequest $deleteTableRow)
  {
    $this->deleteTableRow = $deleteTableRow;
  }
  /**
   * @return DeleteTableRowRequest
   */
  public function getDeleteTableRow()
  {
    return $this->deleteTableRow;
  }
  /**
   * Deletes text from a shape or a table cell.
   *
   * @param DeleteTextRequest $deleteText
   */
  public function setDeleteText(DeleteTextRequest $deleteText)
  {
    $this->deleteText = $deleteText;
  }
  /**
   * @return DeleteTextRequest
   */
  public function getDeleteText()
  {
    return $this->deleteText;
  }
  /**
   * Duplicates a slide or page element.
   *
   * @param DuplicateObjectRequest $duplicateObject
   */
  public function setDuplicateObject(DuplicateObjectRequest $duplicateObject)
  {
    $this->duplicateObject = $duplicateObject;
  }
  /**
   * @return DuplicateObjectRequest
   */
  public function getDuplicateObject()
  {
    return $this->duplicateObject;
  }
  /**
   * Groups objects, such as page elements.
   *
   * @param GroupObjectsRequest $groupObjects
   */
  public function setGroupObjects(GroupObjectsRequest $groupObjects)
  {
    $this->groupObjects = $groupObjects;
  }
  /**
   * @return GroupObjectsRequest
   */
  public function getGroupObjects()
  {
    return $this->groupObjects;
  }
  /**
   * Inserts columns into a table.
   *
   * @param InsertTableColumnsRequest $insertTableColumns
   */
  public function setInsertTableColumns(InsertTableColumnsRequest $insertTableColumns)
  {
    $this->insertTableColumns = $insertTableColumns;
  }
  /**
   * @return InsertTableColumnsRequest
   */
  public function getInsertTableColumns()
  {
    return $this->insertTableColumns;
  }
  /**
   * Inserts rows into a table.
   *
   * @param InsertTableRowsRequest $insertTableRows
   */
  public function setInsertTableRows(InsertTableRowsRequest $insertTableRows)
  {
    $this->insertTableRows = $insertTableRows;
  }
  /**
   * @return InsertTableRowsRequest
   */
  public function getInsertTableRows()
  {
    return $this->insertTableRows;
  }
  /**
   * Inserts text into a shape or table cell.
   *
   * @param InsertTextRequest $insertText
   */
  public function setInsertText(InsertTextRequest $insertText)
  {
    $this->insertText = $insertText;
  }
  /**
   * @return InsertTextRequest
   */
  public function getInsertText()
  {
    return $this->insertText;
  }
  /**
   * Merges cells in a Table.
   *
   * @param MergeTableCellsRequest $mergeTableCells
   */
  public function setMergeTableCells(MergeTableCellsRequest $mergeTableCells)
  {
    $this->mergeTableCells = $mergeTableCells;
  }
  /**
   * @return MergeTableCellsRequest
   */
  public function getMergeTableCells()
  {
    return $this->mergeTableCells;
  }
  /**
   * Refreshes a Google Sheets chart.
   *
   * @param RefreshSheetsChartRequest $refreshSheetsChart
   */
  public function setRefreshSheetsChart(RefreshSheetsChartRequest $refreshSheetsChart)
  {
    $this->refreshSheetsChart = $refreshSheetsChart;
  }
  /**
   * @return RefreshSheetsChartRequest
   */
  public function getRefreshSheetsChart()
  {
    return $this->refreshSheetsChart;
  }
  /**
   * Replaces all shapes matching some criteria with an image.
   *
   * @param ReplaceAllShapesWithImageRequest $replaceAllShapesWithImage
   */
  public function setReplaceAllShapesWithImage(ReplaceAllShapesWithImageRequest $replaceAllShapesWithImage)
  {
    $this->replaceAllShapesWithImage = $replaceAllShapesWithImage;
  }
  /**
   * @return ReplaceAllShapesWithImageRequest
   */
  public function getReplaceAllShapesWithImage()
  {
    return $this->replaceAllShapesWithImage;
  }
  /**
   * Replaces all shapes matching some criteria with a Google Sheets chart.
   *
   * @param ReplaceAllShapesWithSheetsChartRequest $replaceAllShapesWithSheetsChart
   */
  public function setReplaceAllShapesWithSheetsChart(ReplaceAllShapesWithSheetsChartRequest $replaceAllShapesWithSheetsChart)
  {
    $this->replaceAllShapesWithSheetsChart = $replaceAllShapesWithSheetsChart;
  }
  /**
   * @return ReplaceAllShapesWithSheetsChartRequest
   */
  public function getReplaceAllShapesWithSheetsChart()
  {
    return $this->replaceAllShapesWithSheetsChart;
  }
  /**
   * Replaces all instances of specified text.
   *
   * @param ReplaceAllTextRequest $replaceAllText
   */
  public function setReplaceAllText(ReplaceAllTextRequest $replaceAllText)
  {
    $this->replaceAllText = $replaceAllText;
  }
  /**
   * @return ReplaceAllTextRequest
   */
  public function getReplaceAllText()
  {
    return $this->replaceAllText;
  }
  /**
   * Replaces an existing image with a new image.
   *
   * @param ReplaceImageRequest $replaceImage
   */
  public function setReplaceImage(ReplaceImageRequest $replaceImage)
  {
    $this->replaceImage = $replaceImage;
  }
  /**
   * @return ReplaceImageRequest
   */
  public function getReplaceImage()
  {
    return $this->replaceImage;
  }
  /**
   * Reroutes a line such that it's connected at the two closest connection
   * sites on the connected page elements.
   *
   * @param RerouteLineRequest $rerouteLine
   */
  public function setRerouteLine(RerouteLineRequest $rerouteLine)
  {
    $this->rerouteLine = $rerouteLine;
  }
  /**
   * @return RerouteLineRequest
   */
  public function getRerouteLine()
  {
    return $this->rerouteLine;
  }
  /**
   * Ungroups objects, such as groups.
   *
   * @param UngroupObjectsRequest $ungroupObjects
   */
  public function setUngroupObjects(UngroupObjectsRequest $ungroupObjects)
  {
    $this->ungroupObjects = $ungroupObjects;
  }
  /**
   * @return UngroupObjectsRequest
   */
  public function getUngroupObjects()
  {
    return $this->ungroupObjects;
  }
  /**
   * Unmerges cells in a Table.
   *
   * @param UnmergeTableCellsRequest $unmergeTableCells
   */
  public function setUnmergeTableCells(UnmergeTableCellsRequest $unmergeTableCells)
  {
    $this->unmergeTableCells = $unmergeTableCells;
  }
  /**
   * @return UnmergeTableCellsRequest
   */
  public function getUnmergeTableCells()
  {
    return $this->unmergeTableCells;
  }
  /**
   * Updates the properties of an Image.
   *
   * @param UpdateImagePropertiesRequest $updateImageProperties
   */
  public function setUpdateImageProperties(UpdateImagePropertiesRequest $updateImageProperties)
  {
    $this->updateImageProperties = $updateImageProperties;
  }
  /**
   * @return UpdateImagePropertiesRequest
   */
  public function getUpdateImageProperties()
  {
    return $this->updateImageProperties;
  }
  /**
   * Updates the category of a line.
   *
   * @param UpdateLineCategoryRequest $updateLineCategory
   */
  public function setUpdateLineCategory(UpdateLineCategoryRequest $updateLineCategory)
  {
    $this->updateLineCategory = $updateLineCategory;
  }
  /**
   * @return UpdateLineCategoryRequest
   */
  public function getUpdateLineCategory()
  {
    return $this->updateLineCategory;
  }
  /**
   * Updates the properties of a Line.
   *
   * @param UpdateLinePropertiesRequest $updateLineProperties
   */
  public function setUpdateLineProperties(UpdateLinePropertiesRequest $updateLineProperties)
  {
    $this->updateLineProperties = $updateLineProperties;
  }
  /**
   * @return UpdateLinePropertiesRequest
   */
  public function getUpdateLineProperties()
  {
    return $this->updateLineProperties;
  }
  /**
   * Updates the alt text title and/or description of a page element.
   *
   * @param UpdatePageElementAltTextRequest $updatePageElementAltText
   */
  public function setUpdatePageElementAltText(UpdatePageElementAltTextRequest $updatePageElementAltText)
  {
    $this->updatePageElementAltText = $updatePageElementAltText;
  }
  /**
   * @return UpdatePageElementAltTextRequest
   */
  public function getUpdatePageElementAltText()
  {
    return $this->updatePageElementAltText;
  }
  /**
   * Updates the transform of a page element.
   *
   * @param UpdatePageElementTransformRequest $updatePageElementTransform
   */
  public function setUpdatePageElementTransform(UpdatePageElementTransformRequest $updatePageElementTransform)
  {
    $this->updatePageElementTransform = $updatePageElementTransform;
  }
  /**
   * @return UpdatePageElementTransformRequest
   */
  public function getUpdatePageElementTransform()
  {
    return $this->updatePageElementTransform;
  }
  /**
   * Updates the Z-order of page elements.
   *
   * @param UpdatePageElementsZOrderRequest $updatePageElementsZOrder
   */
  public function setUpdatePageElementsZOrder(UpdatePageElementsZOrderRequest $updatePageElementsZOrder)
  {
    $this->updatePageElementsZOrder = $updatePageElementsZOrder;
  }
  /**
   * @return UpdatePageElementsZOrderRequest
   */
  public function getUpdatePageElementsZOrder()
  {
    return $this->updatePageElementsZOrder;
  }
  /**
   * Updates the properties of a Page.
   *
   * @param UpdatePagePropertiesRequest $updatePageProperties
   */
  public function setUpdatePageProperties(UpdatePagePropertiesRequest $updatePageProperties)
  {
    $this->updatePageProperties = $updatePageProperties;
  }
  /**
   * @return UpdatePagePropertiesRequest
   */
  public function getUpdatePageProperties()
  {
    return $this->updatePageProperties;
  }
  /**
   * Updates the styling of paragraphs within a Shape or Table.
   *
   * @param UpdateParagraphStyleRequest $updateParagraphStyle
   */
  public function setUpdateParagraphStyle(UpdateParagraphStyleRequest $updateParagraphStyle)
  {
    $this->updateParagraphStyle = $updateParagraphStyle;
  }
  /**
   * @return UpdateParagraphStyleRequest
   */
  public function getUpdateParagraphStyle()
  {
    return $this->updateParagraphStyle;
  }
  /**
   * Updates the properties of a Shape.
   *
   * @param UpdateShapePropertiesRequest $updateShapeProperties
   */
  public function setUpdateShapeProperties(UpdateShapePropertiesRequest $updateShapeProperties)
  {
    $this->updateShapeProperties = $updateShapeProperties;
  }
  /**
   * @return UpdateShapePropertiesRequest
   */
  public function getUpdateShapeProperties()
  {
    return $this->updateShapeProperties;
  }
  /**
   * Updates the properties of a Slide
   *
   * @param UpdateSlidePropertiesRequest $updateSlideProperties
   */
  public function setUpdateSlideProperties(UpdateSlidePropertiesRequest $updateSlideProperties)
  {
    $this->updateSlideProperties = $updateSlideProperties;
  }
  /**
   * @return UpdateSlidePropertiesRequest
   */
  public function getUpdateSlideProperties()
  {
    return $this->updateSlideProperties;
  }
  /**
   * Updates the position of a set of slides in the presentation.
   *
   * @param UpdateSlidesPositionRequest $updateSlidesPosition
   */
  public function setUpdateSlidesPosition(UpdateSlidesPositionRequest $updateSlidesPosition)
  {
    $this->updateSlidesPosition = $updateSlidesPosition;
  }
  /**
   * @return UpdateSlidesPositionRequest
   */
  public function getUpdateSlidesPosition()
  {
    return $this->updateSlidesPosition;
  }
  /**
   * Updates the properties of the table borders in a Table.
   *
   * @param UpdateTableBorderPropertiesRequest $updateTableBorderProperties
   */
  public function setUpdateTableBorderProperties(UpdateTableBorderPropertiesRequest $updateTableBorderProperties)
  {
    $this->updateTableBorderProperties = $updateTableBorderProperties;
  }
  /**
   * @return UpdateTableBorderPropertiesRequest
   */
  public function getUpdateTableBorderProperties()
  {
    return $this->updateTableBorderProperties;
  }
  /**
   * Updates the properties of a TableCell.
   *
   * @param UpdateTableCellPropertiesRequest $updateTableCellProperties
   */
  public function setUpdateTableCellProperties(UpdateTableCellPropertiesRequest $updateTableCellProperties)
  {
    $this->updateTableCellProperties = $updateTableCellProperties;
  }
  /**
   * @return UpdateTableCellPropertiesRequest
   */
  public function getUpdateTableCellProperties()
  {
    return $this->updateTableCellProperties;
  }
  /**
   * Updates the properties of a Table column.
   *
   * @param UpdateTableColumnPropertiesRequest $updateTableColumnProperties
   */
  public function setUpdateTableColumnProperties(UpdateTableColumnPropertiesRequest $updateTableColumnProperties)
  {
    $this->updateTableColumnProperties = $updateTableColumnProperties;
  }
  /**
   * @return UpdateTableColumnPropertiesRequest
   */
  public function getUpdateTableColumnProperties()
  {
    return $this->updateTableColumnProperties;
  }
  /**
   * Updates the properties of a Table row.
   *
   * @param UpdateTableRowPropertiesRequest $updateTableRowProperties
   */
  public function setUpdateTableRowProperties(UpdateTableRowPropertiesRequest $updateTableRowProperties)
  {
    $this->updateTableRowProperties = $updateTableRowProperties;
  }
  /**
   * @return UpdateTableRowPropertiesRequest
   */
  public function getUpdateTableRowProperties()
  {
    return $this->updateTableRowProperties;
  }
  /**
   * Updates the styling of text within a Shape or Table.
   *
   * @param UpdateTextStyleRequest $updateTextStyle
   */
  public function setUpdateTextStyle(UpdateTextStyleRequest $updateTextStyle)
  {
    $this->updateTextStyle = $updateTextStyle;
  }
  /**
   * @return UpdateTextStyleRequest
   */
  public function getUpdateTextStyle()
  {
    return $this->updateTextStyle;
  }
  /**
   * Updates the properties of a Video.
   *
   * @param UpdateVideoPropertiesRequest $updateVideoProperties
   */
  public function setUpdateVideoProperties(UpdateVideoPropertiesRequest $updateVideoProperties)
  {
    $this->updateVideoProperties = $updateVideoProperties;
  }
  /**
   * @return UpdateVideoPropertiesRequest
   */
  public function getUpdateVideoProperties()
  {
    return $this->updateVideoProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Request::class, 'Google_Service_Slides_Request');
