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

class DocumentTab extends \Google\Model
{
  protected $bodyType = Body::class;
  protected $bodyDataType = '';
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
  protected $suggestedDocumentStyleChangesType = SuggestedDocumentStyle::class;
  protected $suggestedDocumentStyleChangesDataType = 'map';
  protected $suggestedNamedStylesChangesType = SuggestedNamedStyles::class;
  protected $suggestedNamedStylesChangesDataType = 'map';

  /**
   * The main body of the document tab.
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
   * The style of the document tab.
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
   * The footers in the document tab, keyed by footer ID.
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
   * The footnotes in the document tab, keyed by footnote ID.
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
   * The headers in the document tab, keyed by header ID.
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
   * The inline objects in the document tab, keyed by object ID.
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
   * The lists in the document tab, keyed by list ID.
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
   * The named ranges in the document tab, keyed by name.
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
   * The named styles of the document tab.
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
   * The positioned objects in the document tab, keyed by object ID.
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
   * The suggested changes to the style of the document tab, keyed by suggestion
   * ID.
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
   * The suggested changes to the named styles of the document tab, keyed by
   * suggestion ID.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentTab::class, 'Google_Service_Docs_DocumentTab');
