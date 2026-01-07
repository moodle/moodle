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

namespace Google\Service\Walletobjects;

class BarcodeSectionDetail extends \Google\Model
{
  protected $fieldSelectorType = FieldSelector::class;
  protected $fieldSelectorDataType = '';

  /**
   * A reference to an existing text-based or image field to display.
   *
   * @param FieldSelector $fieldSelector
   */
  public function setFieldSelector(FieldSelector $fieldSelector)
  {
    $this->fieldSelector = $fieldSelector;
  }
  /**
   * @return FieldSelector
   */
  public function getFieldSelector()
  {
    return $this->fieldSelector;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BarcodeSectionDetail::class, 'Google_Service_Walletobjects_BarcodeSectionDetail');
