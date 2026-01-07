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

class PreState extends \Google\Collection
{
  protected $collection_key = 'syncIds';
  /**
   * @var string[]
   */
  public $labelIds;
  protected $messageKeyType = MultiKey::class;
  protected $messageKeyDataType = '';
  /**
   * @var string[]
   */
  public $syncIds;
  protected $threadKeyType = MultiKey::class;
  protected $threadKeyDataType = '';

  /**
   * @param string[]
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * @param MultiKey
   */
  public function setMessageKey(MultiKey $messageKey)
  {
    $this->messageKey = $messageKey;
  }
  /**
   * @return MultiKey
   */
  public function getMessageKey()
  {
    return $this->messageKey;
  }
  /**
   * @param string[]
   */
  public function setSyncIds($syncIds)
  {
    $this->syncIds = $syncIds;
  }
  /**
   * @return string[]
   */
  public function getSyncIds()
  {
    return $this->syncIds;
  }
  /**
   * @param MultiKey
   */
  public function setThreadKey(MultiKey $threadKey)
  {
    $this->threadKey = $threadKey;
  }
  /**
   * @return MultiKey
   */
  public function getThreadKey()
  {
    return $this->threadKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PreState::class, 'Google_Service_CloudSearch_PreState');
