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

namespace Google\Service\DisplayVideo;

class NegativeKeywordList extends \Google\Model
{
  /**
   * Output only. The unique ID of the advertiser the negative keyword list
   * belongs to.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Required. The display name of the negative keyword list. Must be UTF-8
   * encoded with a maximum size of 255 bytes.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The resource name of the negative keyword list.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The unique ID of the negative keyword list. Assigned by the
   * system.
   *
   * @var string
   */
  public $negativeKeywordListId;
  /**
   * Output only. Number of line items that are directly targeting this negative
   * keyword list.
   *
   * @var string
   */
  public $targetedLineItemCount;

  /**
   * Output only. The unique ID of the advertiser the negative keyword list
   * belongs to.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Required. The display name of the negative keyword list. Must be UTF-8
   * encoded with a maximum size of 255 bytes.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The resource name of the negative keyword list.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The unique ID of the negative keyword list. Assigned by the
   * system.
   *
   * @param string $negativeKeywordListId
   */
  public function setNegativeKeywordListId($negativeKeywordListId)
  {
    $this->negativeKeywordListId = $negativeKeywordListId;
  }
  /**
   * @return string
   */
  public function getNegativeKeywordListId()
  {
    return $this->negativeKeywordListId;
  }
  /**
   * Output only. Number of line items that are directly targeting this negative
   * keyword list.
   *
   * @param string $targetedLineItemCount
   */
  public function setTargetedLineItemCount($targetedLineItemCount)
  {
    $this->targetedLineItemCount = $targetedLineItemCount;
  }
  /**
   * @return string
   */
  public function getTargetedLineItemCount()
  {
    return $this->targetedLineItemCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NegativeKeywordList::class, 'Google_Service_DisplayVideo_NegativeKeywordList');
