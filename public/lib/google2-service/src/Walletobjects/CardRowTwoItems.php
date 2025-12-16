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

class CardRowTwoItems extends \Google\Model
{
  protected $endItemType = TemplateItem::class;
  protected $endItemDataType = '';
  protected $startItemType = TemplateItem::class;
  protected $startItemDataType = '';

  /**
   * The item to be displayed at the end of the row. This item will be aligned
   * to the right.
   *
   * @param TemplateItem $endItem
   */
  public function setEndItem(TemplateItem $endItem)
  {
    $this->endItem = $endItem;
  }
  /**
   * @return TemplateItem
   */
  public function getEndItem()
  {
    return $this->endItem;
  }
  /**
   * The item to be displayed at the start of the row. This item will be aligned
   * to the left.
   *
   * @param TemplateItem $startItem
   */
  public function setStartItem(TemplateItem $startItem)
  {
    $this->startItem = $startItem;
  }
  /**
   * @return TemplateItem
   */
  public function getStartItem()
  {
    return $this->startItem;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CardRowTwoItems::class, 'Google_Service_Walletobjects_CardRowTwoItems');
