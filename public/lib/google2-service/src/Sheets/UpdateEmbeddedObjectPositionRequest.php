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

namespace Google\Service\Sheets;

class UpdateEmbeddedObjectPositionRequest extends \Google\Model
{
  /**
   * The fields of OverlayPosition that should be updated when setting a new
   * position. Used only if newPosition.overlayPosition is set, in which case at
   * least one field must be specified. The root `newPosition.overlayPosition`
   * is implied and should not be specified. A single `"*"` can be used as
   * short-hand for listing every field.
   *
   * @var string
   */
  public $fields;
  protected $newPositionType = EmbeddedObjectPosition::class;
  protected $newPositionDataType = '';
  /**
   * The ID of the object to moved.
   *
   * @var int
   */
  public $objectId;

  /**
   * The fields of OverlayPosition that should be updated when setting a new
   * position. Used only if newPosition.overlayPosition is set, in which case at
   * least one field must be specified. The root `newPosition.overlayPosition`
   * is implied and should not be specified. A single `"*"` can be used as
   * short-hand for listing every field.
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
   * An explicit position to move the embedded object to. If newPosition.sheetId
   * is set, a new sheet with that ID will be created. If newPosition.newSheet
   * is set to true, a new sheet will be created with an ID that will be chosen
   * for you.
   *
   * @param EmbeddedObjectPosition $newPosition
   */
  public function setNewPosition(EmbeddedObjectPosition $newPosition)
  {
    $this->newPosition = $newPosition;
  }
  /**
   * @return EmbeddedObjectPosition
   */
  public function getNewPosition()
  {
    return $this->newPosition;
  }
  /**
   * The ID of the object to moved.
   *
   * @param int $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return int
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateEmbeddedObjectPositionRequest::class, 'Google_Service_Sheets_UpdateEmbeddedObjectPositionRequest');
