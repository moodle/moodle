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

namespace Google\Service\YouTube;

class LiveChatNewSponsorDetails extends \Google\Model
{
  /**
   * If the viewer just had upgraded from a lower level. For viewers that were
   * not members at the time of purchase, this field is false.
   *
   * @var bool
   */
  public $isUpgrade;
  /**
   * The name of the Level that the viewer just had joined. The Level names are
   * defined by the YouTube channel offering the Membership. In some situations
   * this field isn't filled.
   *
   * @var string
   */
  public $memberLevelName;

  /**
   * If the viewer just had upgraded from a lower level. For viewers that were
   * not members at the time of purchase, this field is false.
   *
   * @param bool $isUpgrade
   */
  public function setIsUpgrade($isUpgrade)
  {
    $this->isUpgrade = $isUpgrade;
  }
  /**
   * @return bool
   */
  public function getIsUpgrade()
  {
    return $this->isUpgrade;
  }
  /**
   * The name of the Level that the viewer just had joined. The Level names are
   * defined by the YouTube channel offering the Membership. In some situations
   * this field isn't filled.
   *
   * @param string $memberLevelName
   */
  public function setMemberLevelName($memberLevelName)
  {
    $this->memberLevelName = $memberLevelName;
  }
  /**
   * @return string
   */
  public function getMemberLevelName()
  {
    return $this->memberLevelName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiveChatNewSponsorDetails::class, 'Google_Service_YouTube_LiveChatNewSponsorDetails');
