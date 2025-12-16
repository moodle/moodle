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

class SectionStyle extends \Google\Collection
{
  /**
   * An unspecified column separator style.
   */
  public const COLUMN_SEPARATOR_STYLE_COLUMN_SEPARATOR_STYLE_UNSPECIFIED = 'COLUMN_SEPARATOR_STYLE_UNSPECIFIED';
  /**
   * No column separator lines between columns.
   */
  public const COLUMN_SEPARATOR_STYLE_NONE = 'NONE';
  /**
   * Renders a column separator line between each column.
   */
  public const COLUMN_SEPARATOR_STYLE_BETWEEN_EACH_COLUMN = 'BETWEEN_EACH_COLUMN';
  /**
   * The content direction is unspecified.
   */
  public const CONTENT_DIRECTION_CONTENT_DIRECTION_UNSPECIFIED = 'CONTENT_DIRECTION_UNSPECIFIED';
  /**
   * The content goes from left to right.
   */
  public const CONTENT_DIRECTION_LEFT_TO_RIGHT = 'LEFT_TO_RIGHT';
  /**
   * The content goes from right to left.
   */
  public const CONTENT_DIRECTION_RIGHT_TO_LEFT = 'RIGHT_TO_LEFT';
  /**
   * The section type is unspecified.
   */
  public const SECTION_TYPE_SECTION_TYPE_UNSPECIFIED = 'SECTION_TYPE_UNSPECIFIED';
  /**
   * The section starts immediately after the last paragraph of the previous
   * section.
   */
  public const SECTION_TYPE_CONTINUOUS = 'CONTINUOUS';
  /**
   * The section starts on the next page.
   */
  public const SECTION_TYPE_NEXT_PAGE = 'NEXT_PAGE';
  protected $collection_key = 'columnProperties';
  protected $columnPropertiesType = SectionColumnProperties::class;
  protected $columnPropertiesDataType = 'array';
  /**
   * The style of column separators. This style can be set even when there's one
   * column in the section. When updating this property, setting a concrete
   * value is required. Unsetting this property results in a 400 bad request
   * error.
   *
   * @var string
   */
  public $columnSeparatorStyle;
  /**
   * The content direction of this section. If unset, the value defaults to
   * LEFT_TO_RIGHT. When updating this property, setting a concrete value is
   * required. Unsetting this property results in a 400 bad request error.
   *
   * @var string
   */
  public $contentDirection;
  /**
   * The ID of the default footer. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's default_footer_id. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @var string
   */
  public $defaultFooterId;
  /**
   * The ID of the default header. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's default_header_id. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @var string
   */
  public $defaultHeaderId;
  /**
   * The ID of the footer used only for even pages. If the value of
   * DocumentStyle's use_even_page_header_footer is true, this value is used for
   * the footers on even pages in the section. If it is false, the footers on
   * even pages use the default_footer_id. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's even_page_footer_id. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @var string
   */
  public $evenPageFooterId;
  /**
   * The ID of the header used only for even pages. If the value of
   * DocumentStyle's use_even_page_header_footer is true, this value is used for
   * the headers on even pages in the section. If it is false, the headers on
   * even pages use the default_header_id. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's even_page_header_id. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
   *
   * @var string
   */
  public $evenPageHeaderId;
  /**
   * The ID of the footer used only for the first page of the section. If
   * use_first_page_header_footer is true, this value is used for the footer on
   * the first page of the section. If it's false, the footer on the first page
   * of the section uses the default_footer_id. If unset, the value inherits
   * from the previous SectionBreak's SectionStyle. If the value is unset in the
   * first SectionBreak, it inherits from DocumentStyle's first_page_footer_id.
   * If DocumentMode is PAGELESS, this property will not be rendered. This
   * property is read-only.
   *
   * @var string
   */
  public $firstPageFooterId;
  /**
   * The ID of the header used only for the first page of the section. If
   * use_first_page_header_footer is true, this value is used for the header on
   * the first page of the section. If it's false, the header on the first page
   * of the section uses the default_header_id. If unset, the value inherits
   * from the previous SectionBreak's SectionStyle. If the value is unset in the
   * first SectionBreak, it inherits from DocumentStyle's first_page_header_id.
   * If DocumentMode is PAGELESS, this property will not be rendered. This
   * property is read-only.
   *
   * @var string
   */
  public $firstPageHeaderId;
  /**
   * Optional. Indicates whether to flip the dimensions of DocumentStyle's
   * page_size for this section, which allows changing the page orientation
   * between portrait and landscape. If unset, the value inherits from
   * DocumentStyle's flip_page_orientation. If DocumentMode is PAGELESS, this
   * property will not be rendered. When updating this property, setting a
   * concrete value is required. Unsetting this property results in a 400 bad
   * request error.
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
   * The page number from which to start counting the number of pages for this
   * section. If unset, page numbering continues from the previous section. If
   * the value is unset in the first SectionBreak, refer to DocumentStyle's
   * page_number_start. If DocumentMode is PAGELESS, this property will not be
   * rendered. When updating this property, setting a concrete value is
   * required. Unsetting this property results in a 400 bad request error.
   *
   * @var int
   */
  public $pageNumberStart;
  /**
   * Output only. The type of section.
   *
   * @var string
   */
  public $sectionType;
  /**
   * Indicates whether to use the first page header / footer IDs for the first
   * page of the section. If unset, it inherits from DocumentStyle's
   * use_first_page_header_footer for the first section. If the value is unset
   * for subsequent sectors, it should be interpreted as false. If DocumentMode
   * is PAGELESS, this property will not be rendered. When updating this
   * property, setting a concrete value is required. Unsetting this property
   * results in a 400 bad request error.
   *
   * @var bool
   */
  public $useFirstPageHeaderFooter;

  /**
   * The section's columns properties. If empty, the section contains one column
   * with the default properties in the Docs editor. A section can be updated to
   * have no more than 3 columns. When updating this property, setting a
   * concrete value is required. Unsetting this property will result in a 400
   * bad request error.
   *
   * @param SectionColumnProperties[] $columnProperties
   */
  public function setColumnProperties($columnProperties)
  {
    $this->columnProperties = $columnProperties;
  }
  /**
   * @return SectionColumnProperties[]
   */
  public function getColumnProperties()
  {
    return $this->columnProperties;
  }
  /**
   * The style of column separators. This style can be set even when there's one
   * column in the section. When updating this property, setting a concrete
   * value is required. Unsetting this property results in a 400 bad request
   * error.
   *
   * Accepted values: COLUMN_SEPARATOR_STYLE_UNSPECIFIED, NONE,
   * BETWEEN_EACH_COLUMN
   *
   * @param self::COLUMN_SEPARATOR_STYLE_* $columnSeparatorStyle
   */
  public function setColumnSeparatorStyle($columnSeparatorStyle)
  {
    $this->columnSeparatorStyle = $columnSeparatorStyle;
  }
  /**
   * @return self::COLUMN_SEPARATOR_STYLE_*
   */
  public function getColumnSeparatorStyle()
  {
    return $this->columnSeparatorStyle;
  }
  /**
   * The content direction of this section. If unset, the value defaults to
   * LEFT_TO_RIGHT. When updating this property, setting a concrete value is
   * required. Unsetting this property results in a 400 bad request error.
   *
   * Accepted values: CONTENT_DIRECTION_UNSPECIFIED, LEFT_TO_RIGHT,
   * RIGHT_TO_LEFT
   *
   * @param self::CONTENT_DIRECTION_* $contentDirection
   */
  public function setContentDirection($contentDirection)
  {
    $this->contentDirection = $contentDirection;
  }
  /**
   * @return self::CONTENT_DIRECTION_*
   */
  public function getContentDirection()
  {
    return $this->contentDirection;
  }
  /**
   * The ID of the default footer. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's default_footer_id. If
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
   * The ID of the default header. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's default_header_id. If
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
   * The ID of the footer used only for even pages. If the value of
   * DocumentStyle's use_even_page_header_footer is true, this value is used for
   * the footers on even pages in the section. If it is false, the footers on
   * even pages use the default_footer_id. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's even_page_footer_id. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
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
   * The ID of the header used only for even pages. If the value of
   * DocumentStyle's use_even_page_header_footer is true, this value is used for
   * the headers on even pages in the section. If it is false, the headers on
   * even pages use the default_header_id. If unset, the value inherits from the
   * previous SectionBreak's SectionStyle. If the value is unset in the first
   * SectionBreak, it inherits from DocumentStyle's even_page_header_id. If
   * DocumentMode is PAGELESS, this property will not be rendered. This property
   * is read-only.
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
   * The ID of the footer used only for the first page of the section. If
   * use_first_page_header_footer is true, this value is used for the footer on
   * the first page of the section. If it's false, the footer on the first page
   * of the section uses the default_footer_id. If unset, the value inherits
   * from the previous SectionBreak's SectionStyle. If the value is unset in the
   * first SectionBreak, it inherits from DocumentStyle's first_page_footer_id.
   * If DocumentMode is PAGELESS, this property will not be rendered. This
   * property is read-only.
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
   * The ID of the header used only for the first page of the section. If
   * use_first_page_header_footer is true, this value is used for the header on
   * the first page of the section. If it's false, the header on the first page
   * of the section uses the default_header_id. If unset, the value inherits
   * from the previous SectionBreak's SectionStyle. If the value is unset in the
   * first SectionBreak, it inherits from DocumentStyle's first_page_header_id.
   * If DocumentMode is PAGELESS, this property will not be rendered. This
   * property is read-only.
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
   * Optional. Indicates whether to flip the dimensions of DocumentStyle's
   * page_size for this section, which allows changing the page orientation
   * between portrait and landscape. If unset, the value inherits from
   * DocumentStyle's flip_page_orientation. If DocumentMode is PAGELESS, this
   * property will not be rendered. When updating this property, setting a
   * concrete value is required. Unsetting this property results in a 400 bad
   * request error.
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
   * The bottom page margin of the section. If unset, the value defaults to
   * margin_bottom from DocumentStyle. If DocumentMode is PAGELESS, this
   * property will not be rendered. When updating this property, setting a
   * concrete value is required. Unsetting this property results in a 400 bad
   * request error.
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
   * The footer margin of the section. If unset, the value defaults to
   * margin_footer from DocumentStyle. If updated,
   * use_custom_header_footer_margins is set to true on DocumentStyle. The value
   * of use_custom_header_footer_margins on DocumentStyle indicates if a footer
   * margin is being respected for this section If DocumentMode is PAGELESS,
   * this property will not be rendered. When updating this property, setting a
   * concrete value is required. Unsetting this property results in a 400 bad
   * request error.
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
   * The header margin of the section. If unset, the value defaults to
   * margin_header from DocumentStyle. If updated,
   * use_custom_header_footer_margins is set to true on DocumentStyle. The value
   * of use_custom_header_footer_margins on DocumentStyle indicates if a header
   * margin is being respected for this section. If DocumentMode is PAGELESS,
   * this property will not be rendered. When updating this property, setting a
   * concrete value is required. Unsetting this property results in a 400 bad
   * request error.
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
   * The left page margin of the section. If unset, the value defaults to
   * margin_left from DocumentStyle. Updating the left margin causes columns in
   * this section to resize. Since the margin affects column width, it's applied
   * before column properties. If DocumentMode is PAGELESS, this property will
   * not be rendered. When updating this property, setting a concrete value is
   * required. Unsetting this property results in a 400 bad request error.
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
   * The right page margin of the section. If unset, the value defaults to
   * margin_right from DocumentStyle. Updating the right margin causes columns
   * in this section to resize. Since the margin affects column width, it's
   * applied before column properties. If DocumentMode is PAGELESS, this
   * property will not be rendered. When updating this property, setting a
   * concrete value is required. Unsetting this property results in a 400 bad
   * request error.
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
   * The top page margin of the section. If unset, the value defaults to
   * margin_top from DocumentStyle. If DocumentMode is PAGELESS, this property
   * will not be rendered. When updating this property, setting a concrete value
   * is required. Unsetting this property results in a 400 bad request error.
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
   * The page number from which to start counting the number of pages for this
   * section. If unset, page numbering continues from the previous section. If
   * the value is unset in the first SectionBreak, refer to DocumentStyle's
   * page_number_start. If DocumentMode is PAGELESS, this property will not be
   * rendered. When updating this property, setting a concrete value is
   * required. Unsetting this property results in a 400 bad request error.
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
   * Output only. The type of section.
   *
   * Accepted values: SECTION_TYPE_UNSPECIFIED, CONTINUOUS, NEXT_PAGE
   *
   * @param self::SECTION_TYPE_* $sectionType
   */
  public function setSectionType($sectionType)
  {
    $this->sectionType = $sectionType;
  }
  /**
   * @return self::SECTION_TYPE_*
   */
  public function getSectionType()
  {
    return $this->sectionType;
  }
  /**
   * Indicates whether to use the first page header / footer IDs for the first
   * page of the section. If unset, it inherits from DocumentStyle's
   * use_first_page_header_footer for the first section. If the value is unset
   * for subsequent sectors, it should be interpreted as false. If DocumentMode
   * is PAGELESS, this property will not be rendered. When updating this
   * property, setting a concrete value is required. Unsetting this property
   * results in a 400 bad request error.
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
class_alias(SectionStyle::class, 'Google_Service_Docs_SectionStyle');
