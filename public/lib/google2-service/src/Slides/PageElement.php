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

class PageElement extends \Google\Model
{
  /**
   * The description of the page element. Combined with title to display alt
   * text. The field is not supported for Group elements.
   *
   * @var string
   */
  public $description;
  protected $elementGroupType = Group::class;
  protected $elementGroupDataType = '';
  protected $imageType = Image::class;
  protected $imageDataType = '';
  protected $lineType = Line::class;
  protected $lineDataType = '';
  /**
   * The object ID for this page element. Object IDs used by
   * google.apps.slides.v1.Page and google.apps.slides.v1.PageElement share the
   * same namespace.
   *
   * @var string
   */
  public $objectId;
  protected $shapeType = Shape::class;
  protected $shapeDataType = '';
  protected $sheetsChartType = SheetsChart::class;
  protected $sheetsChartDataType = '';
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  protected $speakerSpotlightType = SpeakerSpotlight::class;
  protected $speakerSpotlightDataType = '';
  protected $tableType = Table::class;
  protected $tableDataType = '';
  /**
   * The title of the page element. Combined with description to display alt
   * text. The field is not supported for Group elements.
   *
   * @var string
   */
  public $title;
  protected $transformType = AffineTransform::class;
  protected $transformDataType = '';
  protected $videoType = Video::class;
  protected $videoDataType = '';
  protected $wordArtType = WordArt::class;
  protected $wordArtDataType = '';

  /**
   * The description of the page element. Combined with title to display alt
   * text. The field is not supported for Group elements.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A collection of page elements joined as a single unit.
   *
   * @param Group $elementGroup
   */
  public function setElementGroup(Group $elementGroup)
  {
    $this->elementGroup = $elementGroup;
  }
  /**
   * @return Group
   */
  public function getElementGroup()
  {
    return $this->elementGroup;
  }
  /**
   * An image page element.
   *
   * @param Image $image
   */
  public function setImage(Image $image)
  {
    $this->image = $image;
  }
  /**
   * @return Image
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * A line page element.
   *
   * @param Line $line
   */
  public function setLine(Line $line)
  {
    $this->line = $line;
  }
  /**
   * @return Line
   */
  public function getLine()
  {
    return $this->line;
  }
  /**
   * The object ID for this page element. Object IDs used by
   * google.apps.slides.v1.Page and google.apps.slides.v1.PageElement share the
   * same namespace.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * A generic shape.
   *
   * @param Shape $shape
   */
  public function setShape(Shape $shape)
  {
    $this->shape = $shape;
  }
  /**
   * @return Shape
   */
  public function getShape()
  {
    return $this->shape;
  }
  /**
   * A linked chart embedded from Google Sheets. Unlinked charts are represented
   * as images.
   *
   * @param SheetsChart $sheetsChart
   */
  public function setSheetsChart(SheetsChart $sheetsChart)
  {
    $this->sheetsChart = $sheetsChart;
  }
  /**
   * @return SheetsChart
   */
  public function getSheetsChart()
  {
    return $this->sheetsChart;
  }
  /**
   * The size of the page element.
   *
   * @param Size $size
   */
  public function setSize(Size $size)
  {
    $this->size = $size;
  }
  /**
   * @return Size
   */
  public function getSize()
  {
    return $this->size;
  }
  /**
   * A Speaker Spotlight.
   *
   * @param SpeakerSpotlight $speakerSpotlight
   */
  public function setSpeakerSpotlight(SpeakerSpotlight $speakerSpotlight)
  {
    $this->speakerSpotlight = $speakerSpotlight;
  }
  /**
   * @return SpeakerSpotlight
   */
  public function getSpeakerSpotlight()
  {
    return $this->speakerSpotlight;
  }
  /**
   * A table page element.
   *
   * @param Table $table
   */
  public function setTable(Table $table)
  {
    $this->table = $table;
  }
  /**
   * @return Table
   */
  public function getTable()
  {
    return $this->table;
  }
  /**
   * The title of the page element. Combined with description to display alt
   * text. The field is not supported for Group elements.
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
  /**
   * The transform of the page element. The visual appearance of the page
   * element is determined by its absolute transform. To compute the absolute
   * transform, preconcatenate a page element's transform with the transforms of
   * all of its parent groups. If the page element is not in a group, its
   * absolute transform is the same as the value in this field. The initial
   * transform for the newly created Group is always the identity transform.
   *
   * @param AffineTransform $transform
   */
  public function setTransform(AffineTransform $transform)
  {
    $this->transform = $transform;
  }
  /**
   * @return AffineTransform
   */
  public function getTransform()
  {
    return $this->transform;
  }
  /**
   * A video page element.
   *
   * @param Video $video
   */
  public function setVideo(Video $video)
  {
    $this->video = $video;
  }
  /**
   * @return Video
   */
  public function getVideo()
  {
    return $this->video;
  }
  /**
   * A word art page element.
   *
   * @param WordArt $wordArt
   */
  public function setWordArt(WordArt $wordArt)
  {
    $this->wordArt = $wordArt;
  }
  /**
   * @return WordArt
   */
  public function getWordArt()
  {
    return $this->wordArt;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PageElement::class, 'Google_Service_Slides_PageElement');
