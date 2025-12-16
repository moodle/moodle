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

class Placeholder extends \Google\Model
{
  /**
   * Default value, signifies it is not a placeholder.
   */
  public const TYPE_NONE = 'NONE';
  /**
   * Body text.
   */
  public const TYPE_BODY = 'BODY';
  /**
   * Chart or graph.
   */
  public const TYPE_CHART = 'CHART';
  /**
   * Clip art image.
   */
  public const TYPE_CLIP_ART = 'CLIP_ART';
  /**
   * Title centered.
   */
  public const TYPE_CENTERED_TITLE = 'CENTERED_TITLE';
  /**
   * Diagram.
   */
  public const TYPE_DIAGRAM = 'DIAGRAM';
  /**
   * Date and time.
   */
  public const TYPE_DATE_AND_TIME = 'DATE_AND_TIME';
  /**
   * Footer text.
   */
  public const TYPE_FOOTER = 'FOOTER';
  /**
   * Header text.
   */
  public const TYPE_HEADER = 'HEADER';
  /**
   * Multimedia.
   */
  public const TYPE_MEDIA = 'MEDIA';
  /**
   * Any content type.
   */
  public const TYPE_OBJECT = 'OBJECT';
  /**
   * Picture.
   */
  public const TYPE_PICTURE = 'PICTURE';
  /**
   * Number of a slide.
   */
  public const TYPE_SLIDE_NUMBER = 'SLIDE_NUMBER';
  /**
   * Subtitle.
   */
  public const TYPE_SUBTITLE = 'SUBTITLE';
  /**
   * Table.
   */
  public const TYPE_TABLE = 'TABLE';
  /**
   * Slide title.
   */
  public const TYPE_TITLE = 'TITLE';
  /**
   * Slide image.
   */
  public const TYPE_SLIDE_IMAGE = 'SLIDE_IMAGE';
  /**
   * The index of the placeholder. If the same placeholder types are present in
   * the same page, they would have different index values.
   *
   * @var int
   */
  public $index;
  /**
   * The object ID of this shape's parent placeholder. If unset, the parent
   * placeholder shape does not exist, so the shape does not inherit properties
   * from any other shape.
   *
   * @var string
   */
  public $parentObjectId;
  /**
   * The type of the placeholder.
   *
   * @var string
   */
  public $type;

  /**
   * The index of the placeholder. If the same placeholder types are present in
   * the same page, they would have different index values.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * The object ID of this shape's parent placeholder. If unset, the parent
   * placeholder shape does not exist, so the shape does not inherit properties
   * from any other shape.
   *
   * @param string $parentObjectId
   */
  public function setParentObjectId($parentObjectId)
  {
    $this->parentObjectId = $parentObjectId;
  }
  /**
   * @return string
   */
  public function getParentObjectId()
  {
    return $this->parentObjectId;
  }
  /**
   * The type of the placeholder.
   *
   * Accepted values: NONE, BODY, CHART, CLIP_ART, CENTERED_TITLE, DIAGRAM,
   * DATE_AND_TIME, FOOTER, HEADER, MEDIA, OBJECT, PICTURE, SLIDE_NUMBER,
   * SUBTITLE, TABLE, TITLE, SLIDE_IMAGE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Placeholder::class, 'Google_Service_Slides_Placeholder');
