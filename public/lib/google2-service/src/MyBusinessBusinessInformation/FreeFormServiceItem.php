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

namespace Google\Service\MyBusinessBusinessInformation;

class FreeFormServiceItem extends \Google\Model
{
  /**
   * Required. This field represents the category name (i.e. the category's
   * stable ID). The `category` and `service_type_id` should match the possible
   * combinations provided in the `Category` message.
   *
   * @var string
   */
  public $category;
  protected $labelType = Label::class;
  protected $labelDataType = '';

  /**
   * Required. This field represents the category name (i.e. the category's
   * stable ID). The `category` and `service_type_id` should match the possible
   * combinations provided in the `Category` message.
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Required. Language-tagged labels for the item. We recommend that item names
   * be 140 characters or less, and descriptions 250 characters or less. This
   * field should only be set if the input is a custom service item.
   * Standardized service types should be updated via service_type_id.
   *
   * @param Label $label
   */
  public function setLabel(Label $label)
  {
    $this->label = $label;
  }
  /**
   * @return Label
   */
  public function getLabel()
  {
    return $this->label;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FreeFormServiceItem::class, 'Google_Service_MyBusinessBusinessInformation_FreeFormServiceItem');
