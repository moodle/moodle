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

namespace Google\Service\Translate;

class ListExamplesResponse extends \Google\Collection
{
  protected $collection_key = 'examples';
  protected $examplesType = Example::class;
  protected $examplesDataType = 'array';
  /**
   * A token to retrieve next page of results. Pass this token to the page_token
   * field in the ListExamplesRequest to obtain the corresponding page.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The sentence pairs.
   *
   * @param Example[] $examples
   */
  public function setExamples($examples)
  {
    $this->examples = $examples;
  }
  /**
   * @return Example[]
   */
  public function getExamples()
  {
    return $this->examples;
  }
  /**
   * A token to retrieve next page of results. Pass this token to the page_token
   * field in the ListExamplesRequest to obtain the corresponding page.
   *
   * @param string $nextPageToken
   */
  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }
  /**
   * @return string
   */
  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListExamplesResponse::class, 'Google_Service_Translate_ListExamplesResponse');
