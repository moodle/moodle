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

namespace Google\Service\Dfareporting;

class RemarketingListShare extends \Google\Collection
{
  protected $collection_key = 'sharedAdvertiserIds';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#remarketingListShare".
   *
   * @var string
   */
  public $kind;
  /**
   * Remarketing list ID. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $remarketingListId;
  /**
   * Accounts that the remarketing list is shared with.
   *
   * @var string[]
   */
  public $sharedAccountIds;
  /**
   * Advertisers that the remarketing list is shared with.
   *
   * @var string[]
   */
  public $sharedAdvertiserIds;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#remarketingListShare".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Remarketing list ID. This is a read-only, auto-generated field.
   *
   * @param string $remarketingListId
   */
  public function setRemarketingListId($remarketingListId)
  {
    $this->remarketingListId = $remarketingListId;
  }
  /**
   * @return string
   */
  public function getRemarketingListId()
  {
    return $this->remarketingListId;
  }
  /**
   * Accounts that the remarketing list is shared with.
   *
   * @param string[] $sharedAccountIds
   */
  public function setSharedAccountIds($sharedAccountIds)
  {
    $this->sharedAccountIds = $sharedAccountIds;
  }
  /**
   * @return string[]
   */
  public function getSharedAccountIds()
  {
    return $this->sharedAccountIds;
  }
  /**
   * Advertisers that the remarketing list is shared with.
   *
   * @param string[] $sharedAdvertiserIds
   */
  public function setSharedAdvertiserIds($sharedAdvertiserIds)
  {
    $this->sharedAdvertiserIds = $sharedAdvertiserIds;
  }
  /**
   * @return string[]
   */
  public function getSharedAdvertiserIds()
  {
    return $this->sharedAdvertiserIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemarketingListShare::class, 'Google_Service_Dfareporting_RemarketingListShare');
