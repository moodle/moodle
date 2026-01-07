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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceAppliedCapabilities extends \Google\Model
{
  /**
   * Whether the user can read related applied metadata on items.
   *
   * @var bool
   */
  public $canRead;
  /**
   * Whether the user can use this choice in search queries.
   *
   * @var bool
   */
  public $canSearch;
  /**
   * Whether the user can select this choice on an item.
   *
   * @var bool
   */
  public $canSelect;

  /**
   * Whether the user can read related applied metadata on items.
   *
   * @param bool $canRead
   */
  public function setCanRead($canRead)
  {
    $this->canRead = $canRead;
  }
  /**
   * @return bool
   */
  public function getCanRead()
  {
    return $this->canRead;
  }
  /**
   * Whether the user can use this choice in search queries.
   *
   * @param bool $canSearch
   */
  public function setCanSearch($canSearch)
  {
    $this->canSearch = $canSearch;
  }
  /**
   * @return bool
   */
  public function getCanSearch()
  {
    return $this->canSearch;
  }
  /**
   * Whether the user can select this choice on an item.
   *
   * @param bool $canSelect
   */
  public function setCanSelect($canSelect)
  {
    $this->canSelect = $canSelect;
  }
  /**
   * @return bool
   */
  public function getCanSelect()
  {
    return $this->canSelect;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceAppliedCapabilities::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2FieldSelectionOptionsChoiceAppliedCapabilities');
