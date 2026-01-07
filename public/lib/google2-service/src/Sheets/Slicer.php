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

class Slicer extends \Google\Model
{
  protected $positionType = EmbeddedObjectPosition::class;
  protected $positionDataType = '';
  /**
   * The ID of the slicer.
   *
   * @var int
   */
  public $slicerId;
  protected $specType = SlicerSpec::class;
  protected $specDataType = '';

  /**
   * The position of the slicer. Note that slicer can be positioned only on
   * existing sheet. Also, width and height of slicer can be automatically
   * adjusted to keep it within permitted limits.
   *
   * @param EmbeddedObjectPosition $position
   */
  public function setPosition(EmbeddedObjectPosition $position)
  {
    $this->position = $position;
  }
  /**
   * @return EmbeddedObjectPosition
   */
  public function getPosition()
  {
    return $this->position;
  }
  /**
   * The ID of the slicer.
   *
   * @param int $slicerId
   */
  public function setSlicerId($slicerId)
  {
    $this->slicerId = $slicerId;
  }
  /**
   * @return int
   */
  public function getSlicerId()
  {
    return $this->slicerId;
  }
  /**
   * The specification of the slicer.
   *
   * @param SlicerSpec $spec
   */
  public function setSpec(SlicerSpec $spec)
  {
    $this->spec = $spec;
  }
  /**
   * @return SlicerSpec
   */
  public function getSpec()
  {
    return $this->spec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Slicer::class, 'Google_Service_Sheets_Slicer');
