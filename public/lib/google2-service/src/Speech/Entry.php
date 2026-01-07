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

namespace Google\Service\Speech;

class Entry extends \Google\Model
{
  /**
   * Whether the search is case sensitive.
   *
   * @var bool
   */
  public $caseSensitive;
  /**
   * What to replace with. Max length is 100 characters.
   *
   * @var string
   */
  public $replace;
  /**
   * What to replace. Max length is 100 characters.
   *
   * @var string
   */
  public $search;

  /**
   * Whether the search is case sensitive.
   *
   * @param bool $caseSensitive
   */
  public function setCaseSensitive($caseSensitive)
  {
    $this->caseSensitive = $caseSensitive;
  }
  /**
   * @return bool
   */
  public function getCaseSensitive()
  {
    return $this->caseSensitive;
  }
  /**
   * What to replace with. Max length is 100 characters.
   *
   * @param string $replace
   */
  public function setReplace($replace)
  {
    $this->replace = $replace;
  }
  /**
   * @return string
   */
  public function getReplace()
  {
    return $this->replace;
  }
  /**
   * What to replace. Max length is 100 characters.
   *
   * @param string $search
   */
  public function setSearch($search)
  {
    $this->search = $search;
  }
  /**
   * @return string
   */
  public function getSearch()
  {
    return $this->search;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Entry::class, 'Google_Service_Speech_Entry');
