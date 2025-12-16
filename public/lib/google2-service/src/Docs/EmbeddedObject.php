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

class EmbeddedObject extends \Google\Model
{
  /**
   * The description of the embedded object. The `title` and `description` are
   * both combined to display alt text.
   *
   * @var string
   */
  public $description;
  protected $embeddedDrawingPropertiesType = EmbeddedDrawingProperties::class;
  protected $embeddedDrawingPropertiesDataType = '';
  protected $embeddedObjectBorderType = EmbeddedObjectBorder::class;
  protected $embeddedObjectBorderDataType = '';
  protected $imagePropertiesType = ImageProperties::class;
  protected $imagePropertiesDataType = '';
  protected $linkedContentReferenceType = LinkedContentReference::class;
  protected $linkedContentReferenceDataType = '';
  protected $marginBottomType = Dimension::class;
  protected $marginBottomDataType = '';
  protected $marginLeftType = Dimension::class;
  protected $marginLeftDataType = '';
  protected $marginRightType = Dimension::class;
  protected $marginRightDataType = '';
  protected $marginTopType = Dimension::class;
  protected $marginTopDataType = '';
  protected $sizeType = Size::class;
  protected $sizeDataType = '';
  /**
   * The title of the embedded object. The `title` and `description` are both
   * combined to display alt text.
   *
   * @var string
   */
  public $title;

  /**
   * The description of the embedded object. The `title` and `description` are
   * both combined to display alt text.
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
   * The properties of an embedded drawing.
   *
   * @param EmbeddedDrawingProperties $embeddedDrawingProperties
   */
  public function setEmbeddedDrawingProperties(EmbeddedDrawingProperties $embeddedDrawingProperties)
  {
    $this->embeddedDrawingProperties = $embeddedDrawingProperties;
  }
  /**
   * @return EmbeddedDrawingProperties
   */
  public function getEmbeddedDrawingProperties()
  {
    return $this->embeddedDrawingProperties;
  }
  /**
   * The border of the embedded object.
   *
   * @param EmbeddedObjectBorder $embeddedObjectBorder
   */
  public function setEmbeddedObjectBorder(EmbeddedObjectBorder $embeddedObjectBorder)
  {
    $this->embeddedObjectBorder = $embeddedObjectBorder;
  }
  /**
   * @return EmbeddedObjectBorder
   */
  public function getEmbeddedObjectBorder()
  {
    return $this->embeddedObjectBorder;
  }
  /**
   * The properties of an image.
   *
   * @param ImageProperties $imageProperties
   */
  public function setImageProperties(ImageProperties $imageProperties)
  {
    $this->imageProperties = $imageProperties;
  }
  /**
   * @return ImageProperties
   */
  public function getImageProperties()
  {
    return $this->imageProperties;
  }
  /**
   * A reference to the external linked source content. For example, it contains
   * a reference to the source Google Sheets chart when the embedded object is a
   * linked chart. If unset, then the embedded object is not linked.
   *
   * @param LinkedContentReference $linkedContentReference
   */
  public function setLinkedContentReference(LinkedContentReference $linkedContentReference)
  {
    $this->linkedContentReference = $linkedContentReference;
  }
  /**
   * @return LinkedContentReference
   */
  public function getLinkedContentReference()
  {
    return $this->linkedContentReference;
  }
  /**
   * The bottom margin of the embedded object.
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
   * The left margin of the embedded object.
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
   * The right margin of the embedded object.
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
   * The top margin of the embedded object.
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
   * The visible size of the image after cropping.
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
   * The title of the embedded object. The `title` and `description` are both
   * combined to display alt text.
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
class_alias(EmbeddedObject::class, 'Google_Service_Docs_EmbeddedObject');
