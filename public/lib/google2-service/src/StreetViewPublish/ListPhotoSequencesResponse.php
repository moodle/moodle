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

namespace Google\Service\StreetViewPublish;

class ListPhotoSequencesResponse extends \Google\Collection
{
  protected $collection_key = 'photoSequences';
  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;
  protected $photoSequencesType = Operation::class;
  protected $photoSequencesDataType = 'array';

  /**
   * Token to retrieve the next page of results, or empty if there are no more
   * results in the list.
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
  /**
   * List of photo sequences via Operation interface. The maximum number of
   * items returned is based on the pageSize field in the request. Each item in
   * the list can have three possible states, * `Operation.done` = false, if the
   * processing of PhotoSequence is not finished yet. * `Operation.done` = true
   * and `Operation.error` is populated, if there was an error in processing. *
   * `Operation.done` = true and `Operation.response` contains a PhotoSequence
   * message, In each sequence, only Id is populated.
   *
   * @param Operation[] $photoSequences
   */
  public function setPhotoSequences($photoSequences)
  {
    $this->photoSequences = $photoSequences;
  }
  /**
   * @return Operation[]
   */
  public function getPhotoSequences()
  {
    return $this->photoSequences;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListPhotoSequencesResponse::class, 'Google_Service_StreetViewPublish_ListPhotoSequencesResponse');
