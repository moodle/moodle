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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentPageAnchorPageRef extends \Google\Model
{
  /**
   * Layout Unspecified.
   */
  public const LAYOUT_TYPE_LAYOUT_TYPE_UNSPECIFIED = 'LAYOUT_TYPE_UNSPECIFIED';
  /**
   * References a Page.blocks element.
   */
  public const LAYOUT_TYPE_BLOCK = 'BLOCK';
  /**
   * References a Page.paragraphs element.
   */
  public const LAYOUT_TYPE_PARAGRAPH = 'PARAGRAPH';
  /**
   * References a Page.lines element.
   */
  public const LAYOUT_TYPE_LINE = 'LINE';
  /**
   * References a Page.tokens element.
   */
  public const LAYOUT_TYPE_TOKEN = 'TOKEN';
  /**
   * References a Page.visual_elements element.
   */
  public const LAYOUT_TYPE_VISUAL_ELEMENT = 'VISUAL_ELEMENT';
  /**
   * Refrrences a Page.tables element.
   */
  public const LAYOUT_TYPE_TABLE = 'TABLE';
  /**
   * References a Page.form_fields element.
   */
  public const LAYOUT_TYPE_FORM_FIELD = 'FORM_FIELD';
  protected $boundingPolyType = GoogleCloudDocumentaiV1BoundingPoly::class;
  protected $boundingPolyDataType = '';
  /**
   * Optional. Confidence of detected page element, if applicable. Range `[0,
   * 1]`.
   *
   * @var float
   */
  public $confidence;
  /**
   * Optional. Deprecated. Use PageRef.bounding_poly instead.
   *
   * @deprecated
   * @var string
   */
  public $layoutId;
  /**
   * Optional. The type of the layout element that is being referenced if any.
   *
   * @var string
   */
  public $layoutType;
  /**
   * Required. Index into the Document.pages element, for example using
   * `Document.pages` to locate the related page element. This field is skipped
   * when its value is the default `0`. See
   * https://developers.google.com/protocol-buffers/docs/proto3#json.
   *
   * @var string
   */
  public $page;

  /**
   * Optional. Identifies the bounding polygon of a layout element on the page.
   * If `layout_type` is set, the bounding polygon must be exactly the same to
   * the layout element it's referring to.
   *
   * @param GoogleCloudDocumentaiV1BoundingPoly $boundingPoly
   */
  public function setBoundingPoly(GoogleCloudDocumentaiV1BoundingPoly $boundingPoly)
  {
    $this->boundingPoly = $boundingPoly;
  }
  /**
   * @return GoogleCloudDocumentaiV1BoundingPoly
   */
  public function getBoundingPoly()
  {
    return $this->boundingPoly;
  }
  /**
   * Optional. Confidence of detected page element, if applicable. Range `[0,
   * 1]`.
   *
   * @param float $confidence
   */
  public function setConfidence($confidence)
  {
    $this->confidence = $confidence;
  }
  /**
   * @return float
   */
  public function getConfidence()
  {
    return $this->confidence;
  }
  /**
   * Optional. Deprecated. Use PageRef.bounding_poly instead.
   *
   * @deprecated
   * @param string $layoutId
   */
  public function setLayoutId($layoutId)
  {
    $this->layoutId = $layoutId;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getLayoutId()
  {
    return $this->layoutId;
  }
  /**
   * Optional. The type of the layout element that is being referenced if any.
   *
   * Accepted values: LAYOUT_TYPE_UNSPECIFIED, BLOCK, PARAGRAPH, LINE, TOKEN,
   * VISUAL_ELEMENT, TABLE, FORM_FIELD
   *
   * @param self::LAYOUT_TYPE_* $layoutType
   */
  public function setLayoutType($layoutType)
  {
    $this->layoutType = $layoutType;
  }
  /**
   * @return self::LAYOUT_TYPE_*
   */
  public function getLayoutType()
  {
    return $this->layoutType;
  }
  /**
   * Required. Index into the Document.pages element, for example using
   * `Document.pages` to locate the related page element. This field is skipped
   * when its value is the default `0`. See
   * https://developers.google.com/protocol-buffers/docs/proto3#json.
   *
   * @param string $page
   */
  public function setPage($page)
  {
    $this->page = $page;
  }
  /**
   * @return string
   */
  public function getPage()
  {
    return $this->page;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentPageAnchorPageRef::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentPageAnchorPageRef');
