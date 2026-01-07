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

class WriteControl extends \Google\Model
{
  /**
   * The revision ID of the presentation required for the write request. If
   * specified and the required revision ID doesn't match the presentation's
   * current revision ID, the request is not processed and returns a 400 bad
   * request error. When a required revision ID is returned in a response, it
   * indicates the revision ID of the document after the request was applied.
   *
   * @var string
   */
  public $requiredRevisionId;

  /**
   * The revision ID of the presentation required for the write request. If
   * specified and the required revision ID doesn't match the presentation's
   * current revision ID, the request is not processed and returns a 400 bad
   * request error. When a required revision ID is returned in a response, it
   * indicates the revision ID of the document after the request was applied.
   *
   * @param string $requiredRevisionId
   */
  public function setRequiredRevisionId($requiredRevisionId)
  {
    $this->requiredRevisionId = $requiredRevisionId;
  }
  /**
   * @return string
   */
  public function getRequiredRevisionId()
  {
    return $this->requiredRevisionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WriteControl::class, 'Google_Service_Slides_WriteControl');
