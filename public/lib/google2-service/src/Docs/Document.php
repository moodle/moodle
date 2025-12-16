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

class Document extends \Google\Collection
{
  /**
   * The SuggestionsViewMode applied to the returned document depends on the
   * user's current access level. If the user only has view access,
   * PREVIEW_WITHOUT_SUGGESTIONS is applied. Otherwise, SUGGESTIONS_INLINE is
   * applied. This is the default suggestions view mode.
   */
  public const SUGGESTIONS_VIEW_MODE_DEFAULT_FOR_CURRENT_ACCESS = 'DEFAULT_FOR_CURRENT_ACCESS';
  /**
   * The returned document has suggestions inline. Suggested changes will be
   * differentiated from base content within the document. Requests to retrieve
   * a document using this mode will return a 403 error if the user does not
   * have permission to view suggested changes.
   */
  public const SUGGESTIONS_VIEW_MODE_SUGGESTIONS_INLINE = 'SUGGESTIONS_INLINE';
  /**
   * The returned document is a preview with all suggested changes accepted.
   * Requests to retrieve a document using this mode will return a 403 error if
   * the user does not have permission to view suggested changes.
   */
  public const SUGGESTIONS_VIEW_MODE_PREVIEW_SUGGESTIONS_ACCEPTED = 'PREVIEW_SUGGESTIONS_ACCEPTED';
  /**
   * The returned document is a preview with all suggested changes rejected if
   * there are any suggestions in the document.
   */
  public const SUGGESTIONS_VIEW_MODE_PREVIEW_WITHOUT_SUGGESTIONS = 'PREVIEW_WITHOUT_SUGGESTIONS';
  protected $collection_key = 'tabs';
  protected $bodyType = Body::class;
  protected $bodyDataType = '';
  /**
   * Output only. The ID of the document.
   *
   * @var string
   */
  public $documentId;
  protected $documentStyleType = DocumentStyle::class;
  protected $documentStyleDataType = '';
  protected $footersType = Footer::class;
  protected $footersDataType = 'map';
  protected $footnotesType = Footnote::class;
  protected $footnotesDataType = 'map';
  protected $headersType = Header::class;
  protected $headersDataType = 'map';
  protected $inlineObjectsType = InlineObject::class;
  protected $inlineObjectsDataType = 'map';
  protected $listsType = DocsList::class;
  protected $listsDataType = 'map';
  protected $namedRangesType = NamedRanges::class;
  protected $namedRangesDataType = 'map';
  protected $namedStylesType = NamedStyles::class;
  protected $namedStylesDataType = '';
  protected $positionedObjectsType = PositionedObject::class;
  protected $positionedObjectsDataType = 'map';
  /**
   * Output only. The revision ID of the document. Can be used in update
   * requests to specify which revision of a document to apply updates to and
   * how the request should behave if the document has been edited since that
   * revision. Only populated if the user has edit access to the document. The
   * revision ID is not a sequential number but an opaque string. The format of
   * the revision ID might change over time. A returned revision ID is only
   * guaranteed to be valid for 24 hours after it has been returned and cannot
   * be shared across users. If the revision ID is unchanged between calls, then
   * the document has not changed. Conversely, a changed ID (for the same
   * document and user) usually means the document has been updated. However, a
   * changed ID can also be due to internal factors such as ID format changes.
   *
   * @var string
   */
  public $revisionId;
  protected $suggestedDocumentStyleChangesType = SuggestedDocumentStyle::class;
  protected $suggestedDocumentStyleChangesDataType = 'map';
  protected $suggestedNamedStylesChangesType = SuggestedNamedStyles::class;
  protected $suggestedNamedStylesChangesDataType = 'map';
  /**
   * Output only. The suggestions view mode applied to the document. Note: When
   * editing a document, changes must be based on a document with
   * SUGGESTIONS_INLINE.
   *
   * @var string
   */
  public $suggestionsViewMode;
  protected $tabsType = Tab::class;
  protected $tabsDataType = 'array';
  /**
   * The title of the document.
   *
   * @var string
   */
  public $title;

  /**
   * Output only. The main body of the document. Legacy field: Instead, use
   * Document.tabs.documentTab.body, which exposes the actual document content
   * from all tabs when the includeTabsContent parameter is set to `true`. If
   * `false` or unset, this field contains information about the first tab in
   * the document.
   *
   * @param Body $body
   */
  public function setBody(Body $body)
  {
    $this->body = $body;
  }
  /**
   * @return Body
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * Output only. The ID of the document.
   *
   * @param string $documentId
   */
  public function setDocumentId($documentId)
  {
    $this->documentId = $documentId;
  }
  /**
   * @return string
   */
  public function getDocumentId()
  {
    return $this->documentId;
  }
  /**
   * Output only. The style of the document. Legacy field: Instead, use
   * Document.tabs.documentTab.documentStyle, which exposes the actual document
   * content from all tabs when the includeTabsContent parameter is set to
   * `true`. If `false` or unset, this field contains information about the
   * first tab in the document.
   *
   * @param DocumentStyle $documentStyle
   */
  public function setDocumentStyle(DocumentStyle $documentStyle)
  {
    $this->documentStyle = $documentStyle;
  }
  /**
   * @return DocumentStyle
   */
  public function getDocumentStyle()
  {
    return $this->documentStyle;
  }
  /**
   * Output only. The footers in the document, keyed by footer ID. Legacy field:
   * Instead, use Document.tabs.documentTab.footers, which exposes the actual
   * document content from all tabs when the includeTabsContent parameter is set
   * to `true`. If `false` or unset, this field contains information about the
   * first tab in the document.
   *
   * @param Footer[] $footers
   */
  public function setFooters($footers)
  {
    $this->footers = $footers;
  }
  /**
   * @return Footer[]
   */
  public function getFooters()
  {
    return $this->footers;
  }
  /**
   * Output only. The footnotes in the document, keyed by footnote ID. Legacy
   * field: Instead, use Document.tabs.documentTab.footnotes, which exposes the
   * actual document content from all tabs when the includeTabsContent parameter
   * is set to `true`. If `false` or unset, this field contains information
   * about the first tab in the document.
   *
   * @param Footnote[] $footnotes
   */
  public function setFootnotes($footnotes)
  {
    $this->footnotes = $footnotes;
  }
  /**
   * @return Footnote[]
   */
  public function getFootnotes()
  {
    return $this->footnotes;
  }
  /**
   * Output only. The headers in the document, keyed by header ID. Legacy field:
   * Instead, use Document.tabs.documentTab.headers, which exposes the actual
   * document content from all tabs when the includeTabsContent parameter is set
   * to `true`. If `false` or unset, this field contains information about the
   * first tab in the document.
   *
   * @param Header[] $headers
   */
  public function setHeaders($headers)
  {
    $this->headers = $headers;
  }
  /**
   * @return Header[]
   */
  public function getHeaders()
  {
    return $this->headers;
  }
  /**
   * Output only. The inline objects in the document, keyed by object ID. Legacy
   * field: Instead, use Document.tabs.documentTab.inlineObjects, which exposes
   * the actual document content from all tabs when the includeTabsContent
   * parameter is set to `true`. If `false` or unset, this field contains
   * information about the first tab in the document.
   *
   * @param InlineObject[] $inlineObjects
   */
  public function setInlineObjects($inlineObjects)
  {
    $this->inlineObjects = $inlineObjects;
  }
  /**
   * @return InlineObject[]
   */
  public function getInlineObjects()
  {
    return $this->inlineObjects;
  }
  /**
   * Output only. The lists in the document, keyed by list ID. Legacy field:
   * Instead, use Document.tabs.documentTab.lists, which exposes the actual
   * document content from all tabs when the includeTabsContent parameter is set
   * to `true`. If `false` or unset, this field contains information about the
   * first tab in the document.
   *
   * @param DocsList[] $lists
   */
  public function setLists($lists)
  {
    $this->lists = $lists;
  }
  /**
   * @return DocsList[]
   */
  public function getLists()
  {
    return $this->lists;
  }
  /**
   * Output only. The named ranges in the document, keyed by name. Legacy field:
   * Instead, use Document.tabs.documentTab.namedRanges, which exposes the
   * actual document content from all tabs when the includeTabsContent parameter
   * is set to `true`. If `false` or unset, this field contains information
   * about the first tab in the document.
   *
   * @param NamedRanges[] $namedRanges
   */
  public function setNamedRanges($namedRanges)
  {
    $this->namedRanges = $namedRanges;
  }
  /**
   * @return NamedRanges[]
   */
  public function getNamedRanges()
  {
    return $this->namedRanges;
  }
  /**
   * Output only. The named styles of the document. Legacy field: Instead, use
   * Document.tabs.documentTab.namedStyles, which exposes the actual document
   * content from all tabs when the includeTabsContent parameter is set to
   * `true`. If `false` or unset, this field contains information about the
   * first tab in the document.
   *
   * @param NamedStyles $namedStyles
   */
  public function setNamedStyles(NamedStyles $namedStyles)
  {
    $this->namedStyles = $namedStyles;
  }
  /**
   * @return NamedStyles
   */
  public function getNamedStyles()
  {
    return $this->namedStyles;
  }
  /**
   * Output only. The positioned objects in the document, keyed by object ID.
   * Legacy field: Instead, use Document.tabs.documentTab.positionedObjects,
   * which exposes the actual document content from all tabs when the
   * includeTabsContent parameter is set to `true`. If `false` or unset, this
   * field contains information about the first tab in the document.
   *
   * @param PositionedObject[] $positionedObjects
   */
  public function setPositionedObjects($positionedObjects)
  {
    $this->positionedObjects = $positionedObjects;
  }
  /**
   * @return PositionedObject[]
   */
  public function getPositionedObjects()
  {
    return $this->positionedObjects;
  }
  /**
   * Output only. The revision ID of the document. Can be used in update
   * requests to specify which revision of a document to apply updates to and
   * how the request should behave if the document has been edited since that
   * revision. Only populated if the user has edit access to the document. The
   * revision ID is not a sequential number but an opaque string. The format of
   * the revision ID might change over time. A returned revision ID is only
   * guaranteed to be valid for 24 hours after it has been returned and cannot
   * be shared across users. If the revision ID is unchanged between calls, then
   * the document has not changed. Conversely, a changed ID (for the same
   * document and user) usually means the document has been updated. However, a
   * changed ID can also be due to internal factors such as ID format changes.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. The suggested changes to the style of the document, keyed by
   * suggestion ID. Legacy field: Instead, use
   * Document.tabs.documentTab.suggestedDocumentStyleChanges, which exposes the
   * actual document content from all tabs when the includeTabsContent parameter
   * is set to `true`. If `false` or unset, this field contains information
   * about the first tab in the document.
   *
   * @param SuggestedDocumentStyle[] $suggestedDocumentStyleChanges
   */
  public function setSuggestedDocumentStyleChanges($suggestedDocumentStyleChanges)
  {
    $this->suggestedDocumentStyleChanges = $suggestedDocumentStyleChanges;
  }
  /**
   * @return SuggestedDocumentStyle[]
   */
  public function getSuggestedDocumentStyleChanges()
  {
    return $this->suggestedDocumentStyleChanges;
  }
  /**
   * Output only. The suggested changes to the named styles of the document,
   * keyed by suggestion ID. Legacy field: Instead, use
   * Document.tabs.documentTab.suggestedNamedStylesChanges, which exposes the
   * actual document content from all tabs when the includeTabsContent parameter
   * is set to `true`. If `false` or unset, this field contains information
   * about the first tab in the document.
   *
   * @param SuggestedNamedStyles[] $suggestedNamedStylesChanges
   */
  public function setSuggestedNamedStylesChanges($suggestedNamedStylesChanges)
  {
    $this->suggestedNamedStylesChanges = $suggestedNamedStylesChanges;
  }
  /**
   * @return SuggestedNamedStyles[]
   */
  public function getSuggestedNamedStylesChanges()
  {
    return $this->suggestedNamedStylesChanges;
  }
  /**
   * Output only. The suggestions view mode applied to the document. Note: When
   * editing a document, changes must be based on a document with
   * SUGGESTIONS_INLINE.
   *
   * Accepted values: DEFAULT_FOR_CURRENT_ACCESS, SUGGESTIONS_INLINE,
   * PREVIEW_SUGGESTIONS_ACCEPTED, PREVIEW_WITHOUT_SUGGESTIONS
   *
   * @param self::SUGGESTIONS_VIEW_MODE_* $suggestionsViewMode
   */
  public function setSuggestionsViewMode($suggestionsViewMode)
  {
    $this->suggestionsViewMode = $suggestionsViewMode;
  }
  /**
   * @return self::SUGGESTIONS_VIEW_MODE_*
   */
  public function getSuggestionsViewMode()
  {
    return $this->suggestionsViewMode;
  }
  /**
   * Tabs that are part of a document. Tabs can contain child tabs, a tab nested
   * within another tab. Child tabs are represented by the Tab.childTabs field.
   *
   * @param Tab[] $tabs
   */
  public function setTabs($tabs)
  {
    $this->tabs = $tabs;
  }
  /**
   * @return Tab[]
   */
  public function getTabs()
  {
    return $this->tabs;
  }
  /**
   * The title of the document.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Document::class, 'Google_Service_Docs_Document');
