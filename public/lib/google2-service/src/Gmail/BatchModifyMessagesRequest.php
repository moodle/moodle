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

namespace Google\Service\Gmail;

class BatchModifyMessagesRequest extends \Google\Collection
{
  protected $collection_key = 'removeLabelIds';
  /**
   * A list of label IDs to add to messages.
   *
   * @var string[]
   */
  public $addLabelIds;
  /**
   * The IDs of the messages to modify. There is a limit of 1000 ids per
   * request.
   *
   * @var string[]
   */
  public $ids;
  /**
   * A list of label IDs to remove from messages.
   *
   * @var string[]
   */
  public $removeLabelIds;

  /**
   * A list of label IDs to add to messages.
   *
   * @param string[] $addLabelIds
   */
  public function setAddLabelIds($addLabelIds)
  {
    $this->addLabelIds = $addLabelIds;
  }
  /**
   * @return string[]
   */
  public function getAddLabelIds()
  {
    return $this->addLabelIds;
  }
  /**
   * The IDs of the messages to modify. There is a limit of 1000 ids per
   * request.
   *
   * @param string[] $ids
   */
  public function setIds($ids)
  {
    $this->ids = $ids;
  }
  /**
   * @return string[]
   */
  public function getIds()
  {
    return $this->ids;
  }
  /**
   * A list of label IDs to remove from messages.
   *
   * @param string[] $removeLabelIds
   */
  public function setRemoveLabelIds($removeLabelIds)
  {
    $this->removeLabelIds = $removeLabelIds;
  }
  /**
   * @return string[]
   */
  public function getRemoveLabelIds()
  {
    return $this->removeLabelIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BatchModifyMessagesRequest::class, 'Google_Service_Gmail_BatchModifyMessagesRequest');
