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

class GooglePrivacyDlpV2WordList extends \Google\Collection
{
  protected $collection_key = 'words';
  /**
   * Words or phrases defining the dictionary. The dictionary must contain at
   * least one phrase and every phrase must contain at least 2 characters that
   * are letters or digits. [required]
   *
   * @var string[]
   */
  public $words;

  /**
   * Words or phrases defining the dictionary. The dictionary must contain at
   * least one phrase and every phrase must contain at least 2 characters that
   * are letters or digits. [required]
   *
   * @param string[] $words
   */
  public function setWords($words)
  {
    $this->words = $words;
  }
  /**
   * @return string[]
   */
  public function getWords()
  {
    return $this->words;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2WordList::class, 'Google_Service_DLP_GooglePrivacyDlpV2WordList');
