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

class PositionedObject extends \Google\Collection
{
  protected $collection_key = 'suggestedDeletionIds';
  /**
   * The ID of this positioned object.
   *
   * @var string
   */
  public $objectId;
  protected $positionedObjectPropertiesType = PositionedObjectProperties::class;
  protected $positionedObjectPropertiesDataType = '';
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @var string[]
   */
  public $suggestedDeletionIds;
  /**
   * The suggested insertion ID. If empty, then this is not a suggested
   * insertion.
   *
   * @var string
   */
  public $suggestedInsertionId;
  protected $suggestedPositionedObjectPropertiesChangesType = SuggestedPositionedObjectProperties::class;
  protected $suggestedPositionedObjectPropertiesChangesDataType = 'map';

  /**
   * The ID of this positioned object.
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
   * The properties of this positioned object.
   *
   * @param PositionedObjectProperties $positionedObjectProperties
   */
  public function setPositionedObjectProperties(PositionedObjectProperties $positionedObjectProperties)
  {
    $this->positionedObjectProperties = $positionedObjectProperties;
  }
  /**
   * @return PositionedObjectProperties
   */
  public function getPositionedObjectProperties()
  {
    return $this->positionedObjectProperties;
  }
  /**
   * The suggested deletion IDs. If empty, then there are no suggested deletions
   * of this content.
   *
   * @param string[] $suggestedDeletionIds
   */
  public function setSuggestedDeletionIds($suggestedDeletionIds)
  {
    $this->suggestedDeletionIds = $suggestedDeletionIds;
  }
  /**
   * @return string[]
   */
  public function getSuggestedDeletionIds()
  {
    return $this->suggestedDeletionIds;
  }
  /**
   * The suggested insertion ID. If empty, then this is not a suggested
   * insertion.
   *
   * @param string $suggestedInsertionId
   */
  public function setSuggestedInsertionId($suggestedInsertionId)
  {
    $this->suggestedInsertionId = $suggestedInsertionId;
  }
  /**
   * @return string
   */
  public function getSuggestedInsertionId()
  {
    return $this->suggestedInsertionId;
  }
  /**
   * The suggested changes to the positioned object properties, keyed by
   * suggestion ID.
   *
   * @param SuggestedPositionedObjectProperties[] $suggestedPositionedObjectPropertiesChanges
   */
  public function setSuggestedPositionedObjectPropertiesChanges($suggestedPositionedObjectPropertiesChanges)
  {
    $this->suggestedPositionedObjectPropertiesChanges = $suggestedPositionedObjectPropertiesChanges;
  }
  /**
   * @return SuggestedPositionedObjectProperties[]
   */
  public function getSuggestedPositionedObjectPropertiesChanges()
  {
    return $this->suggestedPositionedObjectPropertiesChanges;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PositionedObject::class, 'Google_Service_Docs_PositionedObject');
