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

namespace Google\Service\Meet;

class ListTranscriptsResponse extends \Google\Collection
{
  protected $collection_key = 'transcripts';
  /**
   * Token to be circulated back for further List call if current List doesn't
   * include all the transcripts. Unset if all transcripts are returned.
   *
   * @var string
   */
  public $nextPageToken;
  protected $transcriptsType = Transcript::class;
  protected $transcriptsDataType = 'array';

  /**
   * Token to be circulated back for further List call if current List doesn't
   * include all the transcripts. Unset if all transcripts are returned.
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
   * List of transcripts in one page.
   *
   * @param Transcript[] $transcripts
   */
  public function setTranscripts($transcripts)
  {
    $this->transcripts = $transcripts;
  }
  /**
   * @return Transcript[]
   */
  public function getTranscripts()
  {
    return $this->transcripts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListTranscriptsResponse::class, 'Google_Service_Meet_ListTranscriptsResponse');
