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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3DataStoreConnectionSignalsCitedSnippet extends \Google\Model
{
  protected $searchSnippetType = GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet::class;
  protected $searchSnippetDataType = '';
  /**
   * Index of the snippet in `search_snippets` field.
   *
   * @var int
   */
  public $snippetIndex;

  /**
   * Details of the snippet.
   *
   * @param GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet $searchSnippet
   */
  public function setSearchSnippet(GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet $searchSnippet)
  {
    $this->searchSnippet = $searchSnippet;
  }
  /**
   * @return GoogleCloudDialogflowCxV3DataStoreConnectionSignalsSearchSnippet
   */
  public function getSearchSnippet()
  {
    return $this->searchSnippet;
  }
  /**
   * Index of the snippet in `search_snippets` field.
   *
   * @param int $snippetIndex
   */
  public function setSnippetIndex($snippetIndex)
  {
    $this->snippetIndex = $snippetIndex;
  }
  /**
   * @return int
   */
  public function getSnippetIndex()
  {
    return $this->snippetIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3DataStoreConnectionSignalsCitedSnippet::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3DataStoreConnectionSignalsCitedSnippet');
