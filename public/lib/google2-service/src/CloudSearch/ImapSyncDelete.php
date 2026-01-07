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

class ImapSyncDelete extends \Google\Model
{
  protected $mappingsType = FolderAttribute::class;
  protected $mappingsDataType = '';
  /**
   * @var string
   */
  public $msgId;

  /**
   * @param FolderAttribute
   */
  public function setMappings(FolderAttribute $mappings)
  {
    $this->mappings = $mappings;
  }
  /**
   * @return FolderAttribute
   */
  public function getMappings()
  {
    return $this->mappings;
  }
  /**
   * @param string
   */
  public function setMsgId($msgId)
  {
    $this->msgId = $msgId;
  }
  /**
   * @return string
   */
  public function getMsgId()
  {
    return $this->msgId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImapSyncDelete::class, 'Google_Service_CloudSearch_ImapSyncDelete');
