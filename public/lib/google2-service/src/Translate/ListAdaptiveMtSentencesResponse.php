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

class ListAdaptiveMtSentencesResponse extends \Google\Collection
{
  protected $collection_key = 'adaptiveMtSentences';
  protected $adaptiveMtSentencesType = AdaptiveMtSentence::class;
  protected $adaptiveMtSentencesDataType = 'array';
  /**
   * Optional.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * Output only. The list of AdaptiveMtSentences.
   *
   * @param AdaptiveMtSentence[] $adaptiveMtSentences
   */
  public function setAdaptiveMtSentences($adaptiveMtSentences)
  {
    $this->adaptiveMtSentences = $adaptiveMtSentences;
  }
  /**
   * @return AdaptiveMtSentence[]
   */
  public function getAdaptiveMtSentences()
  {
    return $this->adaptiveMtSentences;
  }
  /**
   * Optional.
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
class_alias(ListAdaptiveMtSentencesResponse::class, 'Google_Service_Translate_ListAdaptiveMtSentencesResponse');
