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

namespace Google\Service\Forms;

class CreateItemResponse extends \Google\Collection
{
  protected $collection_key = 'questionId';
  /**
   * The ID of the created item.
   *
   * @var string
   */
  public $itemId;
  /**
   * The ID of the question created as part of this item, for a question group
   * it lists IDs of all the questions created for this item.
   *
   * @var string[]
   */
  public $questionId;

  /**
   * The ID of the created item.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * The ID of the question created as part of this item, for a question group
   * it lists IDs of all the questions created for this item.
   *
   * @param string[] $questionId
   */
  public function setQuestionId($questionId)
  {
    $this->questionId = $questionId;
  }
  /**
   * @return string[]
   */
  public function getQuestionId()
  {
    return $this->questionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateItemResponse::class, 'Google_Service_Forms_CreateItemResponse');
