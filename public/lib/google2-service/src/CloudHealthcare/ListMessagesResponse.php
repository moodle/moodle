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

namespace Google\Service\CloudHealthcare;

class ListMessagesResponse extends \Google\Collection
{
  protected $collection_key = 'hl7V2Messages';
  protected $hl7V2MessagesType = Message::class;
  protected $hl7V2MessagesDataType = 'array';
  /**
   * Token to retrieve the next page of results or empty if there are no more
   * results in the list.
   *
   * @var string
   */
  public $nextPageToken;

  /**
   * The returned Messages. Won't be more Messages than the value of page_size
   * in the request. See view for populated fields.
   *
   * @param Message[] $hl7V2Messages
   */
  public function setHl7V2Messages($hl7V2Messages)
  {
    $this->hl7V2Messages = $hl7V2Messages;
  }
  /**
   * @return Message[]
   */
  public function getHl7V2Messages()
  {
    return $this->hl7V2Messages;
  }
  /**
   * Token to retrieve the next page of results or empty if there are no more
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ListMessagesResponse::class, 'Google_Service_CloudHealthcare_ListMessagesResponse');
