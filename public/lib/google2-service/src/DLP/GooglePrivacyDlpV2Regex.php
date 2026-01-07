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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2Regex extends \Google\Collection
{
  protected $collection_key = 'groupIndexes';
  /**
   * The index of the submatch to extract as findings. When not specified, the
   * entire match is returned. No more than 3 may be included.
   *
   * @var int[]
   */
  public $groupIndexes;
  /**
   * Pattern defining the regular expression. Its syntax
   * (https://github.com/google/re2/wiki/Syntax) can be found under the
   * google/re2 repository on GitHub.
   *
   * @var string
   */
  public $pattern;

  /**
   * The index of the submatch to extract as findings. When not specified, the
   * entire match is returned. No more than 3 may be included.
   *
   * @param int[] $groupIndexes
   */
  public function setGroupIndexes($groupIndexes)
  {
    $this->groupIndexes = $groupIndexes;
  }
  /**
   * @return int[]
   */
  public function getGroupIndexes()
  {
    return $this->groupIndexes;
  }
  /**
   * Pattern defining the regular expression. Its syntax
   * (https://github.com/google/re2/wiki/Syntax) can be found under the
   * google/re2 repository on GitHub.
   *
   * @param string $pattern
   */
  public function setPattern($pattern)
  {
    $this->pattern = $pattern;
  }
  /**
   * @return string
   */
  public function getPattern()
  {
    return $this->pattern;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2Regex::class, 'Google_Service_DLP_GooglePrivacyDlpV2Regex');
