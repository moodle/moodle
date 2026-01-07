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

class UpdateImagePropertiesRequest extends \Google\Model
{
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `imageProperties` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the image outline color, set `fields` to
   * `"outline.outlineFill.solidFill.color"`. To reset a property to its default
   * value, include its field name in the field mask but leave the field itself
   * unset.
   *
   * @var string
   */
  public $fields;
  protected $imagePropertiesType = ImageProperties::class;
  protected $imagePropertiesDataType = '';
  /**
   * The object ID of the image the updates are applied to.
   *
   * @var string
   */
  public $objectId;

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root `imageProperties` is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update the image outline color, set `fields` to
   * `"outline.outlineFill.solidFill.color"`. To reset a property to its default
   * value, include its field name in the field mask but leave the field itself
   * unset.
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
   * The image properties to update.
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
   * The object ID of the image the updates are applied to.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateImagePropertiesRequest::class, 'Google_Service_Slides_UpdateImagePropertiesRequest');
