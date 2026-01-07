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

class UpdateLineCategoryRequest extends \Google\Model
{
  /**
   * Unspecified line category.
   */
  public const LINE_CATEGORY_LINE_CATEGORY_UNSPECIFIED = 'LINE_CATEGORY_UNSPECIFIED';
  /**
   * Straight connectors, including straight connector 1.
   */
  public const LINE_CATEGORY_STRAIGHT = 'STRAIGHT';
  /**
   * Bent connectors, including bent connector 2 to 5.
   */
  public const LINE_CATEGORY_BENT = 'BENT';
  /**
   * Curved connectors, including curved connector 2 to 5.
   */
  public const LINE_CATEGORY_CURVED = 'CURVED';
  /**
   * The line category to update to. The exact line type is determined based on
   * the category to update to and how it's routed to connect to other page
   * elements.
   *
   * @var string
   */
  public $lineCategory;
  /**
   * The object ID of the line the update is applied to. Only a line with a
   * category indicating it is a "connector" can be updated. The line may be
   * rerouted after updating its category.
   *
   * @var string
   */
  public $objectId;

  /**
   * The line category to update to. The exact line type is determined based on
   * the category to update to and how it's routed to connect to other page
   * elements.
   *
   * Accepted values: LINE_CATEGORY_UNSPECIFIED, STRAIGHT, BENT, CURVED
   *
   * @param self::LINE_CATEGORY_* $lineCategory
   */
  public function setLineCategory($lineCategory)
  {
    $this->lineCategory = $lineCategory;
  }
  /**
   * @return self::LINE_CATEGORY_*
   */
  public function getLineCategory()
  {
    return $this->lineCategory;
  }
  /**
   * The object ID of the line the update is applied to. Only a line with a
   * category indicating it is a "connector" can be updated. The line may be
   * rerouted after updating its category.
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
class_alias(UpdateLineCategoryRequest::class, 'Google_Service_Slides_UpdateLineCategoryRequest');
