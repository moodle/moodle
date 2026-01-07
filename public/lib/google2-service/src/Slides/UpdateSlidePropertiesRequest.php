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

class UpdateSlidePropertiesRequest extends \Google\Model
{
  /**
   * The fields that should be updated. At least one field must be specified.
   * The root 'slideProperties' is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update whether a slide is skipped, set `fields` to `"isSkipped"`. To reset
   * a property to its default value, include its field name in the field mask
   * but leave the field itself unset.
   *
   * @var string
   */
  public $fields;
  /**
   * The object ID of the slide the update is applied to.
   *
   * @var string
   */
  public $objectId;
  protected $slidePropertiesType = SlideProperties::class;
  protected $slidePropertiesDataType = '';

  /**
   * The fields that should be updated. At least one field must be specified.
   * The root 'slideProperties' is implied and should not be specified. A single
   * `"*"` can be used as short-hand for listing every field. For example to
   * update whether a slide is skipped, set `fields` to `"isSkipped"`. To reset
   * a property to its default value, include its field name in the field mask
   * but leave the field itself unset.
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
   * The object ID of the slide the update is applied to.
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
   * The slide properties to update.
   *
   * @param SlideProperties $slideProperties
   */
  public function setSlideProperties(SlideProperties $slideProperties)
  {
    $this->slideProperties = $slideProperties;
  }
  /**
   * @return SlideProperties
   */
  public function getSlideProperties()
  {
    return $this->slideProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdateSlidePropertiesRequest::class, 'Google_Service_Slides_UpdateSlidePropertiesRequest');
