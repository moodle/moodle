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

class LabelAdded extends \Google\Collection
{
  protected $collection_key = 'messageKeys';
  /**
   * @var string
   */
  public $labelId;
  /**
   * @var string
   */
  public $labelName;
  protected $messageKeysType = MultiKey::class;
  protected $messageKeysDataType = 'array';
  /**
   * @var string
   */
  public $syncId;

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
   * @param string
   */
  public function setLabelName($labelName)
  {
    $this->labelName = $labelName;
  }
  /**
   * @return string
   */
  public function getLabelName()
  {
    return $this->labelName;
  }
  /**
   * @param MultiKey[]
   */
  public function setMessageKeys($messageKeys)
  {
    $this->messageKeys = $messageKeys;
  }
  /**
   * @return MultiKey[]
   */
  public function getMessageKeys()
  {
    return $this->messageKeys;
  }
  /**
   * @param string
   */
  public function setSyncId($syncId)
  {
    $this->syncId = $syncId;
  }
  /**
   * @return string
   */
  public function getSyncId()
  {
    return $this->syncId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LabelAdded::class, 'Google_Service_CloudSearch_LabelAdded');
