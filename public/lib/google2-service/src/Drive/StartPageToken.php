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

namespace Google\Service\Drive;

class StartPageToken extends \Google\Model
{
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#startPageToken"`.
   *
   * @var string
   */
  public $kind;
  /**
   * The starting page token for listing future changes. The page token doesn't
   * expire.
   *
   * @var string
   */
  public $startPageToken;

  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"drive#startPageToken"`.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The starting page token for listing future changes. The page token doesn't
   * expire.
   *
   * @param string $startPageToken
   */
  public function setStartPageToken($startPageToken)
  {
    $this->startPageToken = $startPageToken;
  }
  /**
   * @return string
   */
  public function getStartPageToken()
  {
    return $this->startPageToken;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StartPageToken::class, 'Google_Service_Drive_StartPageToken');
