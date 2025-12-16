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

class Paragraph extends \Google\Collection
{
  protected $collection_key = 'positionedObjectIds';
  protected $bulletType = Bullet::class;
  protected $bulletDataType = '';
  protected $elementsType = ParagraphElement::class;
  protected $elementsDataType = 'array';
  protected $paragraphStyleType = ParagraphStyle::class;
  protected $paragraphStyleDataType = '';
  /**
   * The IDs of the positioned objects tethered to this paragraph.
   *
   * @var string[]
   */
  public $positionedObjectIds;
  protected $suggestedBulletChangesType = SuggestedBullet::class;
  protected $suggestedBulletChangesDataType = 'map';
  protected $suggestedParagraphStyleChangesType = SuggestedParagraphStyle::class;
  protected $suggestedParagraphStyleChangesDataType = 'map';
  protected $suggestedPositionedObjectIdsType = ObjectReferences::class;
  protected $suggestedPositionedObjectIdsDataType = 'map';

  /**
   * The bullet for this paragraph. If not present, the paragraph does not
   * belong to a list.
   *
   * @param Bullet $bullet
   */
  public function setBullet(Bullet $bullet)
  {
    $this->bullet = $bullet;
  }
  /**
   * @return Bullet
   */
  public function getBullet()
  {
    return $this->bullet;
  }
  /**
   * The content of the paragraph, broken down into its component parts.
   *
   * @param ParagraphElement[] $elements
   */
  public function setElements($elements)
  {
    $this->elements = $elements;
  }
  /**
   * @return ParagraphElement[]
   */
  public function getElements()
  {
    return $this->elements;
  }
  /**
   * The style of this paragraph.
   *
   * @param ParagraphStyle $paragraphStyle
   */
  public function setParagraphStyle(ParagraphStyle $paragraphStyle)
  {
    $this->paragraphStyle = $paragraphStyle;
  }
  /**
   * @return ParagraphStyle
   */
  public function getParagraphStyle()
  {
    return $this->paragraphStyle;
  }
  /**
   * The IDs of the positioned objects tethered to this paragraph.
   *
   * @param string[] $positionedObjectIds
   */
  public function setPositionedObjectIds($positionedObjectIds)
  {
    $this->positionedObjectIds = $positionedObjectIds;
  }
  /**
   * @return string[]
   */
  public function getPositionedObjectIds()
  {
    return $this->positionedObjectIds;
  }
  /**
   * The suggested changes to this paragraph's bullet.
   *
   * @param SuggestedBullet[] $suggestedBulletChanges
   */
  public function setSuggestedBulletChanges($suggestedBulletChanges)
  {
    $this->suggestedBulletChanges = $suggestedBulletChanges;
  }
  /**
   * @return SuggestedBullet[]
   */
  public function getSuggestedBulletChanges()
  {
    return $this->suggestedBulletChanges;
  }
  /**
   * The suggested paragraph style changes to this paragraph, keyed by
   * suggestion ID.
   *
   * @param SuggestedParagraphStyle[] $suggestedParagraphStyleChanges
   */
  public function setSuggestedParagraphStyleChanges($suggestedParagraphStyleChanges)
  {
    $this->suggestedParagraphStyleChanges = $suggestedParagraphStyleChanges;
  }
  /**
   * @return SuggestedParagraphStyle[]
   */
  public function getSuggestedParagraphStyleChanges()
  {
    return $this->suggestedParagraphStyleChanges;
  }
  /**
   * The IDs of the positioned objects suggested to be attached to this
   * paragraph, keyed by suggestion ID.
   *
   * @param ObjectReferences[] $suggestedPositionedObjectIds
   */
  public function setSuggestedPositionedObjectIds($suggestedPositionedObjectIds)
  {
    $this->suggestedPositionedObjectIds = $suggestedPositionedObjectIds;
  }
  /**
   * @return ObjectReferences[]
   */
  public function getSuggestedPositionedObjectIds()
  {
    return $this->suggestedPositionedObjectIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Paragraph::class, 'Google_Service_Docs_Paragraph');
