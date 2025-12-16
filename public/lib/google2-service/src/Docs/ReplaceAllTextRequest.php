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

class ReplaceAllTextRequest extends \Google\Model
{
  protected $containsTextType = SubstringMatchCriteria::class;
  protected $containsTextDataType = '';
  /**
   * The text that will replace the matched text.
   *
   * @var string
   */
  public $replaceText;
  protected $tabsCriteriaType = TabsCriteria::class;
  protected $tabsCriteriaDataType = '';

  /**
   * Finds text in the document matching this substring.
   *
   * @param SubstringMatchCriteria $containsText
   */
  public function setContainsText(SubstringMatchCriteria $containsText)
  {
    $this->containsText = $containsText;
  }
  /**
   * @return SubstringMatchCriteria
   */
  public function getContainsText()
  {
    return $this->containsText;
  }
  /**
   * The text that will replace the matched text.
   *
   * @param string $replaceText
   */
  public function setReplaceText($replaceText)
  {
    $this->replaceText = $replaceText;
  }
  /**
   * @return string
   */
  public function getReplaceText()
  {
    return $this->replaceText;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReplaceAllTextRequest::class, 'Google_Service_Docs_ReplaceAllTextRequest');
