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

namespace Google\Service\Slides;

class SubstringMatchCriteria extends \Google\Model
{
  /**
   * Indicates whether the search should respect case: - `True`: the search is
   * case sensitive. - `False`: the search is case insensitive.
   *
   * @var bool
   */
  public $matchCase;
  /**
   * Optional. True if the find value should be treated as a regular expression.
   * Any backslashes in the pattern should be escaped. - `True`: the search text
   * is treated as a regular expressions. - `False`: the search text is treated
   * as a substring for matching.
   *
   * @var bool
   */
  public $searchByRegex;
  /**
   * The text to search for in the shape or table.
   *
   * @var string
   */
  public $text;

  /**
   * Indicates whether the search should respect case: - `True`: the search is
   * case sensitive. - `False`: the search is case insensitive.
   *
   * @param bool $matchCase
   */
  public function setMatchCase($matchCase)
  {
    $this->matchCase = $matchCase;
  }
  /**
   * @return bool
   */
  public function getMatchCase()
  {
    return $this->matchCase;
  }
  /**
   * Optional. True if the find value should be treated as a regular expression.
   * Any backslashes in the pattern should be escaped. - `True`: the search text
   * is treated as a regular expressions. - `False`: the search text is treated
   * as a substring for matching.
   *
   * @param bool $searchByRegex
   */
  public function setSearchByRegex($searchByRegex)
  {
    $this->searchByRegex = $searchByRegex;
  }
  /**
   * @return bool
   */
  public function getSearchByRegex()
  {
    return $this->searchByRegex;
  }
  /**
   * The text to search for in the shape or table.
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
class_alias(SubstringMatchCriteria::class, 'Google_Service_Slides_SubstringMatchCriteria');
