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

class ReplaceNamedRangeContentRequest extends \Google\Model
{
  /**
   * The ID of the named range whose content will be replaced. If there is no
   * named range with the given ID a 400 bad request error is returned.
   *
   * @var string
   */
  public $namedRangeId;
  /**
   * The name of the NamedRanges whose content will be replaced. If there are
   * multiple named ranges with the given name, then the content of each one
   * will be replaced. If there are no named ranges with the given name, then
   * the request will be a no-op.
   *
   * @var string
   */
  public $namedRangeName;
  protected $tabsCriteriaType = TabsCriteria::class;
  protected $tabsCriteriaDataType = '';
  /**
   * Replaces the content of the specified named range(s) with the given text.
   *
   * @var string
   */
  public $text;

  /**
   * The ID of the named range whose content will be replaced. If there is no
   * named range with the given ID a 400 bad request error is returned.
   *
   * @param string $namedRangeId
   */
  public function setNamedRangeId($namedRangeId)
  {
    $this->namedRangeId = $namedRangeId;
  }
  /**
   * @return string
   */
  public function getNamedRangeId()
  {
    return $this->namedRangeId;
  }
  /**
   * The name of the NamedRanges whose content will be replaced. If there are
   * multiple named ranges with the given name, then the content of each one
   * will be replaced. If there are no named ranges with the given name, then
   * the request will be a no-op.
   *
   * @param string $namedRangeName
   */
  public function setNamedRangeName($namedRangeName)
  {
    $this->namedRangeName = $namedRangeName;
  }
  /**
   * @return string
   */
  public function getNamedRangeName()
  {
    return $this->namedRangeName;
  }
  /**
   * Optional. The criteria used to specify in which tabs the replacement
   * occurs. When omitted, the replacement applies to all tabs. In a document
   * containing a single tab: - If provided, must match the singular tab's ID. -
   * If omitted, the replacement applies to the singular tab. In a document
   * containing multiple tabs: - If provided, the replacement applies to the
   * specified tabs. - If omitted, the replacement applies to all tabs.
   *
   * @param TabsCriteria $tabsCriteria
   */
  public function setTabsCriteria(TabsCriteria $tabsCriteria)
  {
    $this->tabsCriteria = $tabsCriteria;
  }
  /**
   * @return TabsCriteria
   */
  public function getTabsCriteria()
  {
    return $this->tabsCriteria;
  }
  /**
   * Replaces the content of the specified named range(s) with the given text.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplaceNamedRangeContentRequest::class, 'Google_Service_Docs_ReplaceNamedRangeContentRequest');
