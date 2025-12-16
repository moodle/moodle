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

namespace Google\Service\Docs;

class Request extends \Google\Model
{
  protected $createFooterType = CreateFooterRequest::class;
  protected $createFooterDataType = '';
  protected $createFootnoteType = CreateFootnoteRequest::class;
  protected $createFootnoteDataType = '';
  protected $createHeaderType = CreateHeaderRequest::class;
  protected $createHeaderDataType = '';
  protected $createNamedRangeType = CreateNamedRangeRequest::class;
  protected $createNamedRangeDataType = '';
  protected $createParagraphBulletsType = CreateParagraphBulletsRequest::class;
  protected $createParagraphBulletsDataType = '';
  protected $deleteContentRangeType = DeleteContentRangeRequest::class;
  protected $deleteContentRangeDataType = '';
  protected $deleteFooterType = DeleteFooterRequest::class;
  protected $deleteFooterDataType = '';
  protected $deleteHeaderType = DeleteHeaderRequest::class;
  protected $deleteHeaderDataType = '';
  protected $deleteNamedRangeType = DeleteNamedRangeRequest::class;
  protected $deleteNamedRangeDataType = '';
  protected $deleteParagraphBulletsType = DeleteParagraphBulletsRequest::class;
  protected $deleteParagraphBulletsDataType = '';
  protected $deletePositionedObjectType = DeletePositionedObjectRequest::class;
  protected $deletePositionedObjectDataType = '';
  protected $deleteTableColumnType = DeleteTableColumnRequest::class;
  protected $deleteTableColumnDataType = '';
  protected $deleteTableRowType = DeleteTableRowRequest::class;
  protected $deleteTableRowDataType = '';
  protected $insertDateType = InsertDateRequest::class;
  protected $insertDateDataType = '';
  protected $insertInlineImageType = InsertInlineImageRequest::class;
  protected $insertInlineImageDataType = '';
  protected $insertPageBreakType = InsertPageBreakRequest::class;
  protected $insertPageBreakDataType = '';
  protected $insertPersonType = InsertPersonRequest::class;
  protected $insertPersonDataType = '';
  protected $insertSectionBreakType = InsertSectionBreakRequest::class;
  protected $insertSectionBreakDataType = '';
  protected $insertTableType = InsertTableRequest::class;
  protected $insertTableDataType = '';
  protected $insertTableColumnType = InsertTableColumnRequest::class;
  protected $insertTableColumnDataType = '';
  protected $insertTableRowType = InsertTableRowRequest::class;
  protected $insertTableRowDataType = '';
  protected $insertTextType = InsertTextRequest::class;
  protected $insertTextDataType = '';
  protected $mergeTableCellsType = MergeTableCellsRequest::class;
  protected $mergeTableCellsDataType = '';
  protected $pinTableHeaderRowsType = PinTableHeaderRowsRequest::class;
  protected $pinTableHeaderRowsDataType = '';
  protected $replaceAllTextType = ReplaceAllTextRequest::class;
  protected $replaceAllTextDataType = '';
  protected $replaceImageType = ReplaceImageRequest::class;
  protected $replaceImageDataType = '';
  protected $replaceNamedRangeContentType = ReplaceNamedRangeContentRequest::class;
  protected $replaceNamedRangeContentDataType = '';
  protected $unmergeTableCellsType = UnmergeTableCellsRequest::class;
  protected $unmergeTableCellsDataType = '';
  protected $updateDocumentStyleType = UpdateDocumentStyleRequest::class;
  protected $updateDocumentStyleDataType = '';
  protected $updateParagraphStyleType = UpdateParagraphStyleRequest::class;
  protected $updateParagraphStyleDataType = '';
  protected $updateSectionStyleType = UpdateSectionStyleRequest::class;
  protected $updateSectionStyleDataType = '';
  protected $updateTableCellStyleType = UpdateTableCellStyleRequest::class;
  protected $updateTableCellStyleDataType = '';
  protected $updateTableColumnPropertiesType = UpdateTableColumnPropertiesRequest::class;
  protected $updateTableColumnPropertiesDataType = '';
  protected $updateTableRowStyleType = UpdateTableRowStyleRequest::class;
  protected $updateTableRowStyleDataType = '';
  protected $updateTextStyleType = UpdateTextStyleRequest::class;
  protected $updateTextStyleDataType = '';

  /**
   * Creates a footer.
   *
   * @param CreateFooterRequest $createFooter
   */
  public function setCreateFooter(CreateFooterRequest $createFooter)
  {
    $this->createFooter = $createFooter;
  }
  /**
   * @return CreateFooterRequest
   */
  public function getCreateFooter()
  {
    return $this->createFooter;
  }
  /**
   * Creates a footnote.
   *
   * @param CreateFootnoteRequest $createFootnote
   */
  public function setCreateFootnote(CreateFootnoteRequest $createFootnote)
  {
    $this->createFootnote = $createFootnote;
  }
  /**
   * @return CreateFootnoteRequest
   */
  public function getCreateFootnote()
  {
    return $this->createFootnote;
  }
  /**
   * Creates a header.
   *
   * @param CreateHeaderRequest $createHeader
   */
  public function setCreateHeader(CreateHeaderRequest $createHeader)
  {
    $this->createHeader = $createHeader;
  }
  /**
   * @return CreateHeaderRequest
   */
  public function getCreateHeader()
  {
    return $this->createHeader;
  }
  /**
   * Creates a named range.
   *
   * @param CreateNamedRangeRequest $createNamedRange
   */
  public function setCreateNamedRange(CreateNamedRangeRequest $createNamedRange)
  {
    $this->createNamedRange = $createNamedRange;
  }
  /**
   * @return CreateNamedRangeRequest
   */
  public function getCreateNamedRange()
  {
    return $this->createNamedRange;
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
   * Deletes content from the document.
   *
   * @param DeleteContentRangeRequest $deleteContentRange
   */
  public function setDeleteContentRange(DeleteContentRangeRequest $deleteContentRange)
  {
    $this->deleteContentRange = $deleteContentRange;
  }
  /**
   * @return DeleteContentRangeRequest
   */
  public function getDeleteContentRange()
  {
    return $this->deleteContentRange;
  }
  /**
   * Deletes a footer from the document.
   *
   * @param DeleteFooterRequest $deleteFooter
   */
  public function setDeleteFooter(DeleteFooterRequest $deleteFooter)
  {
    $this->deleteFooter = $deleteFooter;
  }
  /**
   * @return DeleteFooterRequest
   */
  public function getDeleteFooter()
  {
    return $this->deleteFooter;
  }
  /**
   * Deletes a header from the document.
   *
   * @param DeleteHeaderRequest $deleteHeader
   */
  public function setDeleteHeader(DeleteHeaderRequest $deleteHeader)
  {
    $this->deleteHeader = $deleteHeader;
  }
  /**
   * @return DeleteHeaderRequest
   */
  public function getDeleteHeader()
  {
    return $this->deleteHeader;
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
   * Deletes a positioned object from the document.
   *
   * @param DeletePositionedObjectRequest $deletePositionedObject
   */
  public function setDeletePositionedObject(DeletePositionedObjectRequest $deletePositionedObject)
  {
    $this->deletePositionedObject = $deletePositionedObject;
  }
  /**
   * @return DeletePositionedObjectRequest
   */
  public function getDeletePositionedObject()
  {
    return $this->deletePositionedObject;
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
   * Inserts a date.
   *
   * @param InsertDateRequest $insertDate
   */
  public function setInsertDate(InsertDateRequest $insertDate)
  {
    $this->insertDate = $insertDate;
  }
  /**
   * @return InsertDateRequest
   */
  public function getInsertDate()
  {
    return $this->insertDate;
  }
  /**
   * Inserts an inline image at the specified location.
   *
   * @param InsertInlineImageRequest $insertInlineImage
   */
  public function setInsertInlineImage(InsertInlineImageRequest $insertInlineImage)
  {
    $this->insertInlineImage = $insertInlineImage;
  }
  /**
   * @return InsertInlineImageRequest
   */
  public function getInsertInlineImage()
  {
    return $this->insertInlineImage;
  }
  /**
   * Inserts a page break at the specified location.
   *
   * @param InsertPageBreakRequest $insertPageBreak
   */
  public function setInsertPageBreak(InsertPageBreakRequest $insertPageBreak)
  {
    $this->insertPageBreak = $insertPageBreak;
  }
  /**
   * @return InsertPageBreakRequest
   */
  public function getInsertPageBreak()
  {
    return $this->insertPageBreak;
  }
  /**
   * Inserts a person mention.
   *
   * @param InsertPersonRequest $insertPerson
   */
  public function setInsertPerson(InsertPersonRequest $insertPerson)
  {
    $this->insertPerson = $insertPerson;
  }
  /**
   * @return InsertPersonRequest
   */
  public function getInsertPerson()
  {
    return $this->insertPerson;
  }
  /**
   * Inserts a section break at the specified location.
   *
   * @param InsertSectionBreakRequest $insertSectionBreak
   */
  public function setInsertSectionBreak(InsertSectionBreakRequest $insertSectionBreak)
  {
    $this->insertSectionBreak = $insertSectionBreak;
  }
  /**
   * @return InsertSectionBreakRequest
   */
  public function getInsertSectionBreak()
  {
    return $this->insertSectionBreak;
  }
  /**
   * Inserts a table at the specified location.
   *
   * @param InsertTableRequest $insertTable
   */
  public function setInsertTable(InsertTableRequest $insertTable)
  {
    $this->insertTable = $insertTable;
  }
  /**
   * @return InsertTableRequest
   */
  public function getInsertTable()
  {
    return $this->insertTable;
  }
  /**
   * Inserts an empty column into a table.
   *
   * @param InsertTableColumnRequest $insertTableColumn
   */
  public function setInsertTableColumn(InsertTableColumnRequest $insertTableColumn)
  {
    $this->insertTableColumn = $insertTableColumn;
  }
  /**
   * @return InsertTableColumnRequest
   */
  public function getInsertTableColumn()
  {
    return $this->insertTableColumn;
  }
  /**
   * Inserts an empty row into a table.
   *
   * @param InsertTableRowRequest $insertTableRow
   */
  public function setInsertTableRow(InsertTableRowRequest $insertTableRow)
  {
    $this->insertTableRow = $insertTableRow;
  }
  /**
   * @return InsertTableRowRequest
   */
  public function getInsertTableRow()
  {
    return $this->insertTableRow;
  }
  /**
   * Inserts text at the specified location.
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
   * Merges cells in a table.
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
   * Updates the number of pinned header rows in a table.
   *
   * @param PinTableHeaderRowsRequest $pinTableHeaderRows
   */
  public function setPinTableHeaderRows(PinTableHeaderRowsRequest $pinTableHeaderRows)
  {
    $this->pinTableHeaderRows = $pinTableHeaderRows;
  }
  /**
   * @return PinTableHeaderRowsRequest
   */
  public function getPinTableHeaderRows()
  {
    return $this->pinTableHeaderRows;
  }
  /**
   * Replaces all instances of the specified text.
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
   * Replaces an image in the document.
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
   * Replaces the content in a named range.
   *
   * @param ReplaceNamedRangeContentRequest $replaceNamedRangeContent
   */
  public function setReplaceNamedRangeContent(ReplaceNamedRangeContentRequest $replaceNamedRangeContent)
  {
    $this->replaceNamedRangeContent = $replaceNamedRangeContent;
  }
  /**
   * @return ReplaceNamedRangeContentRequest
   */
  public function getReplaceNamedRangeContent()
  {
    return $this->replaceNamedRangeContent;
  }
  /**
   * Unmerges cells in a table.
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
   * Updates the style of the document.
   *
   * @param UpdateDocumentStyleRequest $updateDocumentStyle
   */
  public function setUpdateDocumentStyle(UpdateDocumentStyleRequest $updateDocumentStyle)
  {
    $this->updateDocumentStyle = $updateDocumentStyle;
  }
  /**
   * @return UpdateDocumentStyleRequest
   */
  public function getUpdateDocumentStyle()
  {
    return $this->updateDocumentStyle;
  }
  /**
   * Updates the paragraph style at the specified range.
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
   * Updates the section style of the specified range.
   *
   * @param UpdateSectionStyleRequest $updateSectionStyle
   */
  public function setUpdateSectionStyle(UpdateSectionStyleRequest $updateSectionStyle)
  {
    $this->updateSectionStyle = $updateSectionStyle;
  }
  /**
   * @return UpdateSectionStyleRequest
   */
  public function getUpdateSectionStyle()
  {
    return $this->updateSectionStyle;
  }
  /**
   * Updates the style of table cells.
   *
   * @param UpdateTableCellStyleRequest $updateTableCellStyle
   */
  public function setUpdateTableCellStyle(UpdateTableCellStyleRequest $updateTableCellStyle)
  {
    $this->updateTableCellStyle = $updateTableCellStyle;
  }
  /**
   * @return UpdateTableCellStyleRequest
   */
  public function getUpdateTableCellStyle()
  {
    return $this->updateTableCellStyle;
  }
  /**
   * Updates the properties of columns in a table.
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
   * Updates the row style in a table.
   *
   * @param UpdateTableRowStyleRequest $updateTableRowStyle
   */
  public function setUpdateTableRowStyle(UpdateTableRowStyleRequest $updateTableRowStyle)
  {
    $this->updateTableRowStyle = $updateTableRowStyle;
  }
  /**
   * @return UpdateTableRowStyleRequest
   */
  public function getUpdateTableRowStyle()
  {
    return $this->updateTableRowStyle;
  }
  /**
   * Updates the text style at the specified range.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Request::class, 'Google_Service_Docs_Request');
