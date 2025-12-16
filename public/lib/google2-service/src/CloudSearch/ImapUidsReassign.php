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

namespace Google\Service\CloudSearch;

class ImapUidsReassign extends \Google\Collection
{
  protected $collection_key = 'messageId';
  /**
   * @var string
   */
  public $labelId;
  /**
   * @var string[]
   */
  public $messageId;

  /**
   * @param string
   */
  public function setLabelId($labelId)
  {
    $this->labelId = $labelId;
  }
  /**
   * @return string
   */
  public function getLabelId()
  {
    return $this->labelId;
  }
  /**
   * @param string[]
   */
  public function setMessageId($messageId)
  {
    $this->messageId = $messageId;
  }
  /**
   * @return string[]
   */
  public function getMessageId()
  {
    return $this->messageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImapUidsReassign::class, 'Google_Service_CloudSearch_ImapUidsReassign');
