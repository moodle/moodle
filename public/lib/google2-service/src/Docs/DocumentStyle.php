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

class DocumentStyle extends \Google\Model
{
  protected $backgroundType = Background::class;
  protected $backgroundDataType = '';
  /**
   * The ID of the default footer. If not set, there's no default footer. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @var string
   */
  public $defaultFooterId;
  /**
   * The ID of the default header. If not set, there's no default header. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @var string
   */
  public $defaultHeaderId;
  protected $documentFormatType = DocumentFormat::class;
  protected $documentFormatDataType = '';
  /**
   * The ID of the footer used only for even pages. The value of
   * use_even_page_header_footer determines whether to use the default_footer_id
   * or this value for the footer on even pages. If not set, there's no even
   * page footer. If DocumentMode is PAGELESS, this property will not be
   * rendered. This property is read-only.
   *
   * @var string
   */
  public $evenPageFooterId;
  /**
   * The ID of the header used only for even pages. The value of
   * use_even_page_header_footer determines whether to use the default_header_id
   * or this value for the header on even pages. If not set, there's no even
   * page header. If DocumentMode is PAGELESS, this property will not be
   * rendered. This property is read-only.
   *
   * @var string
   */
  public $evenPageHeaderId;
  /**
   * The ID of the footer used only for the first page. If not set then a unique
   * footer for the first page does not exist. The value of
   * use_first_page_header_footer determines whether to use the
   * default_footer_id or this value for the footer on the first page. If not
   * set, there's no first page footer. If DocumentMode is PAGELESS, this
   * property will not be rendered. This property is read-only.
   *
   * @var string
   */
  public $firstPageFooterId;
  /**
   * The ID of the header used only for the first page. If not set then a unique
   * header for the first page does not exist. The value of
   * use_first_page_header_footer determines whether to use the
   * default_header_id or this value for the header on the first page. If not
   * set, there's no first page header. If DocumentMode is PAGELESS, this
   * property will not be rendered. This property is read-only.
   *
   * @var string
   */
  public $firstPageHeaderId;
  /**
   * Optional. Indicates whether to flip the dimensions of the page_size, which
   * allows changing the page orientation between portrait and landscape. If
   * DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @var bool
   */
  public $flipPageOrientation;
  protected $marginBottomType = Dimension::class;
  protected $marginBottomDataType = '';
  protected $marginFooterType = Dimension::class;
  protected $marginFooterDataType = '';
  protected $marginHeaderType = Dimension::class;
  protected $marginHeaderDataType = '';
  protected $marginLeftType = Dimension::class;
  protected $marginLeftDataType = '';
  protected $marginRightType = Dimension::class;
  protected $marginRightDataType = '';
  protected $marginTopType = Dimension::class;
  protected $marginTopDataType = '';
  /**
   * The page number from which to start counting the number of pages. If
   * DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @var int
   */
  public $pageNumberStart;
  protected $pageSizeType = Size::class;
  protected $pageSizeDataType = '';
  /**
   * Indicates whether DocumentStyle margin_header, SectionStyle margin_header
   * and DocumentStyle margin_footer, SectionStyle margin_footer are respected.
   * When false, the default values in the Docs editor for header and footer
   * margin is used. If DocumentMode is PAGELESS, this property will not be
   * rendered. This property is read-only.
   *
   * @var bool
   */
  public $useCustomHeaderFooterMargins;
  /**
   * Indicates whether to use the even page header / footer IDs for the even
   * pages. If DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @var bool
   */
  public $useEvenPageHeaderFooter;
  /**
   * Indicates whether to use the first page header / footer IDs for the first
   * page. If DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @var bool
   */
  public $useFirstPageHeaderFooter;

  /**
   * The background of the document. Documents cannot have a transparent
   * background color.
   *
   * @param Background $background
   */
  public function setBackground(Background $background)
  {
    $this->background = $background;
  }
  /**
   * @return Background
   */
  public function getBackground()
  {
    return $this->background;
  }
  /**
   * The ID of the default footer. If not set, there's no default footer. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @param string $defaultFooterId
   */
  public function setDefaultFooterId($defaultFooterId)
  {
    $this->defaultFooterId = $defaultFooterId;
  }
  /**
   * @return string
   */
  public function getDefaultFooterId()
  {
    return $this->defaultFooterId;
  }
  /**
   * The ID of the default header. If not set, there's no default header. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @param string $defaultHeaderId
   */
  public function setDefaultHeaderId($defaultHeaderId)
  {
    $this->defaultHeaderId = $defaultHeaderId;
  }
  /**
   * @return string
   */
  public function getDefaultHeaderId()
  {
    return $this->defaultHeaderId;
  }
  /**
   * Specifies document-level format settings, such as the document mode (pages
   * vs pageless).
   *
   * @param DocumentFormat $documentFormat
   */
  public function setDocumentFormat(DocumentFormat $documentFormat)
  {
    $this->documentFormat = $documentFormat;
  }
  /**
   * @return DocumentFormat
   */
  public function getDocumentFormat()
  {
    return $this->documentFormat;
  }
  /**
   * The ID of the footer used only for even pages. The value of
   * use_even_page_header_footer determines whether to use the default_footer_id
   * or this value for the footer on even pages. If not set, there's no even
   * page footer. If DocumentMode is PAGELESS, this property will not be
   * rendered. This property is read-only.
   *
   * @param string $evenPageFooterId
   */
  public function setEvenPageFooterId($evenPageFooterId)
  {
    $this->evenPageFooterId = $evenPageFooterId;
  }
  /**
   * @return string
   */
  public function getEvenPageFooterId()
  {
    return $this->evenPageFooterId;
  }
  /**
   * The ID of the header used only for even pages. The value of
   * use_even_page_header_footer determines whether to use the default_header_id
   * or this value for the header on even pages. If not set, there's no even
   * page header. If DocumentMode is PAGELESS, this property will not be
   * rendered. This property is read-only.
   *
   * @param string $evenPageHeaderId
   */
  public function setEvenPageHeaderId($evenPageHeaderId)
  {
    $this->evenPageHeaderId = $evenPageHeaderId;
  }
  /**
   * @return string
   */
  public function getEvenPageHeaderId()
  {
    return $this->evenPageHeaderId;
  }
  /**
   * The ID of the footer used only for the first page. If not set then a unique
   * footer for the first page does not exist. The value of
   * use_first_page_header_footer determines whether to use the
   * default_footer_id or this value for the footer on the first page. If not
   * set, there's no first page footer. If DocumentMode is PAGELESS, this
   * property will not be rendered. This property is read-only.
   *
   * @param string $firstPageFooterId
   */
  public function setFirstPageFooterId($firstPageFooterId)
  {
    $this->firstPageFooterId = $firstPageFooterId;
  }
  /**
   * @return string
   */
  public function getFirstPageFooterId()
  {
    return $this->firstPageFooterId;
  }
  /**
   * The ID of the header used only for the first page. If not set then a unique
   * header for the first page does not exist. The value of
   * use_first_page_header_footer determines whether to use the
   * default_header_id or this value for the header on the first page. If not
   * set, there's no first page header. If DocumentMode is PAGELESS, this
   * property will not be rendered. This property is read-only.
   *
   * @param string $firstPageHeaderId
   */
  public function setFirstPageHeaderId($firstPageHeaderId)
  {
    $this->firstPageHeaderId = $firstPageHeaderId;
  }
  /**
   * @return string
   */
  public function getFirstPageHeaderId()
  {
    return $this->firstPageHeaderId;
  }
  /**
   * Optional. Indicates whether to flip the dimensions of the page_size, which
   * allows changing the page orientation between portrait and landscape. If
   * DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @param bool $flipPageOrientation
   */
  public function setFlipPageOrientation($flipPageOrientation)
  {
    $this->flipPageOrientation = $flipPageOrientation;
  }
  /**
   * @return bool
   */
  public function getFlipPageOrientation()
  {
    return $this->flipPageOrientation;
  }
  /**
   * The bottom page margin. Updating the bottom page margin on the document
   * style clears the bottom page margin on all section styles. If DocumentMode
   * is PAGELESS, this property will not be rendered.
   *
   * @param Dimension $marginBottom
   */
  public function setMarginBottom(Dimension $marginBottom)
  {
    $this->marginBottom = $marginBottom;
  }
  /**
   * @return Dimension
   */
  public function getMarginBottom()
  {
    return $this->marginBottom;
  }
  /**
   * The amount of space between the bottom of the page and the contents of the
   * footer. If DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @param Dimension $marginFooter
   */
  public function setMarginFooter(Dimension $marginFooter)
  {
    $this->marginFooter = $marginFooter;
  }
  /**
   * @return Dimension
   */
  public function getMarginFooter()
  {
    return $this->marginFooter;
  }
  /**
   * The amount of space between the top of the page and the contents of the
   * header. If DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @param Dimension $marginHeader
   */
  public function setMarginHeader(Dimension $marginHeader)
  {
    $this->marginHeader = $marginHeader;
  }
  /**
   * @return Dimension
   */
  public function getMarginHeader()
  {
    return $this->marginHeader;
  }
  /**
   * The left page margin. Updating the left page margin on the document style
   * clears the left page margin on all section styles. It may also cause
   * columns to resize in all sections. If DocumentMode is PAGELESS, this
   * property will not be rendered.
   *
   * @param Dimension $marginLeft
   */
  public function setMarginLeft(Dimension $marginLeft)
  {
    $this->marginLeft = $marginLeft;
  }
  /**
   * @return Dimension
   */
  public function getMarginLeft()
  {
    return $this->marginLeft;
  }
  /**
   * The right page margin. Updating the right page margin on the document style
   * clears the right page margin on all section styles. It may also cause
   * columns to resize in all sections. If DocumentMode is PAGELESS, this
   * property will not be rendered.
   *
   * @param Dimension $marginRight
   */
  public function setMarginRight(Dimension $marginRight)
  {
    $this->marginRight = $marginRight;
  }
  /**
   * @return Dimension
   */
  public function getMarginRight()
  {
    return $this->marginRight;
  }
  /**
   * The top page margin. Updating the top page margin on the document style
   * clears the top page margin on all section styles. If DocumentMode is
   * PAGELESS, this property will not be rendered.
   *
   * @param Dimension $marginTop
   */
  public function setMarginTop(Dimension $marginTop)
  {
    $this->marginTop = $marginTop;
  }
  /**
   * @return Dimension
   */
  public function getMarginTop()
  {
    return $this->marginTop;
  }
  /**
   * The page number from which to start counting the number of pages. If
   * DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @param int $pageNumberStart
   */
  public function setPageNumberStart($pageNumberStart)
  {
    $this->pageNumberStart = $pageNumberStart;
  }
  /**
   * @return int
   */
  public function getPageNumberStart()
  {
    return $this->pageNumberStart;
  }
  /**
   * The size of a page in the document. If DocumentMode is PAGELESS, this
   * property will not be rendered.
   *
   * @param Size $pageSize
   */
  public function setPageSize(Size $pageSize)
  {
    $this->pageSize = $pageSize;
  }
  /**
   * @return Size
   */
  public function getPageSize()
  {
    return $this->pageSize;
  }
  /**
   * Indicates whether DocumentStyle margin_header, SectionStyle margin_header
   * and DocumentStyle margin_footer, SectionStyle margin_footer are respected.
   * When false, the default values in the Docs editor for header and footer
   * margin is used. If DocumentMode is PAGELESS, this property will not be
   * rendered. This property is read-only.
   *
   * @param bool $useCustomHeaderFooterMargins
   */
  public function setUseCustomHeaderFooterMargins($useCustomHeaderFooterMargins)
  {
    $this->useCustomHeaderFooterMargins = $useCustomHeaderFooterMargins;
  }
  /**
   * @return bool
   */
  public function getUseCustomHeaderFooterMargins()
  {
    return $this->useCustomHeaderFooterMargins;
  }
  /**
   * Indicates whether to use the even page header / footer IDs for the even
   * pages. If DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @param bool $useEvenPageHeaderFooter
   */
  public function setUseEvenPageHeaderFooter($useEvenPageHeaderFooter)
  {
    $this->useEvenPageHeaderFooter = $useEvenPageHeaderFooter;
  }
  /**
   * @return bool
   */
  public function getUseEvenPageHeaderFooter()
  {
    return $this->useEvenPageHeaderFooter;
  }
  /**
   * Indicates whether to use the first page header / footer IDs for the first
   * page. If DocumentMode is PAGELESS, this property will not be rendered.
   *
   * @param bool $useFirstPageHeaderFooter
   */
  public function setUseFirstPageHeaderFooter($useFirstPageHeaderFooter)
  {
    $this->useFirstPageHeaderFooter = $useFirstPageHeaderFooter;
  }
  /**
   * @return bool
   */
  public function getUseFirstPageHeaderFooter()
  {
    return $this->useFirstPageHeaderFooter;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DocumentStyle::class, 'Google_Service_Docs_DocumentStyle');
