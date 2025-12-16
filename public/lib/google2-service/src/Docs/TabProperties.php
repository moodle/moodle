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

namespace Google\Service\Docs;

class TabProperties extends \Google\Model
{
  /**
   * Optional. The emoji icon displayed with the tab. A valid emoji icon is
   * represented by a non-empty Unicode string. Any set of characters that don't
   * represent a single emoji is invalid. If an emoji is invalid, a 400 bad
   * request error is returned. If this value is unset or empty, the tab will
   * display the default tab icon.
   *
   * @var string
   */
  public $iconEmoji;
  /**
   * The zero-based index of the tab within the parent.
   *
   * @var int
   */
  public $index;
  /**
   * Output only. The depth of the tab within the document. Root-level tabs
   * start at 0.
   *
   * @var int
   */
  public $nestingLevel;
  /**
   * Optional. The ID of the parent tab. Empty when the current tab is a root-
   * level tab, which means it doesn't have any parents.
   *
   * @var string
   */
  public $parentTabId;
  /**
   * Output only. The ID of the tab. This field can't be changed.
   *
   * @var string
   */
  public $tabId;
  /**
   * The user-visible name of the tab.
   *
   * @var string
   */
  public $title;

  /**
   * Optional. The emoji icon displayed with the tab. A valid emoji icon is
   * represented by a non-empty Unicode string. Any set of characters that don't
   * represent a single emoji is invalid. If an emoji is invalid, a 400 bad
   * request error is returned. If this value is unset or empty, the tab will
   * display the default tab icon.
   *
   * @param string $iconEmoji
   */
  public function setIconEmoji($iconEmoji)
  {
    $this->iconEmoji = $iconEmoji;
  }
  /**
   * @return string
   */
  public function getIconEmoji()
  {
    return $this->iconEmoji;
  }
  /**
   * The zero-based index of the tab within the parent.
   *
   * @param int $index
   */
  public function setIndex($index)
  {
    $this->index = $index;
  }
  /**
   * @return int
   */
  public function getIndex()
  {
    return $this->index;
  }
  /**
   * Output only. The depth of the tab within the document. Root-level tabs
   * start at 0.
   *
   * @param int $nestingLevel
   */
  public function setNestingLevel($nestingLevel)
  {
    $this->nestingLevel = $nestingLevel;
  }
  /**
   * @return int
   */
  public function getNestingLevel()
  {
    return $this->nestingLevel;
  }
  /**
   * Optional. The ID of the parent tab. Empty when the current tab is a root-
   * level tab, which means it doesn't have any parents.
   *
   * @param string $parentTabId
   */
  public function setParentTabId($parentTabId)
  {
    $this->parentTabId = $parentTabId;
  }
  /**
   * @return string
   */
  public function getParentTabId()
  {
    return $this->parentTabId;
  }
  /**
   * Output only. The ID of the tab. This field can't be changed.
   *
   * @param string $tabId
   */
  public function setTabId($tabId)
  {
    $this->tabId = $tabId;
  }
  /**
   * @return string
   */
  public function getTabId()
  {
    return $this->tabId;
  }
  /**
   * The user-visible name of the tab.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TabProperties::class, 'Google_Service_Docs_TabProperties');
