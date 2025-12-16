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

class UpdateSectionStyleRequest extends \Google\Model
{
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `section_style` is implied and must not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the left margin, set `fields` to `"margin_left"`.
   *
   * @var string
   */
  public $fields;
  protected $rangeType = Range::class;
  protected $rangeDataType = '';
  protected $sectionStyleType = SectionStyle::class;
  protected $sectionStyleDataType = '';

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `section_style` is implied and must not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the left margin, set `fields` to `"margin_left"`.
   *
   * @param string $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return string
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * The range overlapping the sections to style. Because section breaks can
   * only be inserted inside the body, the segment ID field must be empty.
   *
   * @param Range $range
   */
  public function setRange(Range $range)
  {
    $this->range = $range;
  }
  /**
   * @return Range
   */
  public function getRange()
  {
    return $this->range;
  }
  /**
   * The styles to be set on the section. Certain section style changes may
   * cause other changes in order to mirror the behavior of the Docs editor. See
   * the documentation of SectionStyle for more information.
   *
   * @param SectionStyle $sectionStyle
   */
  public function setSectionStyle(SectionStyle $sectionStyle)
  {
    $this->sectionStyle = $sectionStyle;
  }
  /**
   * @return SectionStyle
   */
  public function getSectionStyle()
  {
    return $this->sectionStyle;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSectionStyleRequest::class, 'Google_Service_Docs_UpdateSectionStyleRequest');
