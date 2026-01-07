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

class UpdateSlicerSpecRequest extends \Google\Model
{
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `SlicerSpec` is implied and should not be specified. A single "*"`
   * can be used as short-hand for listing every field.
   *
   * @var string
   */
  public $fields;
  /**
   * The id of the slicer to update.
   *
   * @var int
   */
  public $slicerId;
  protected $specType = SlicerSpec::class;
  protected $specDataType = '';

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `SlicerSpec` is implied and should not be specified. A single "*"`
   * can be used as short-hand for listing every field.
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
   * The id of the slicer to update.
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
   * The specification to apply to the slicer.
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
class_alias(UpdateSlicerSpecRequest::class, 'Google_Service_Sheets_UpdateSlicerSpecRequest');
