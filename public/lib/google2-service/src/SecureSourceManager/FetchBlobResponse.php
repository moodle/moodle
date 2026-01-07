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

namespace Google\Service\SecureSourceManager;

class FetchBlobResponse extends \Google\Model
{
  /**
   * The content of the blob, encoded as base64.
   *
   * @var string
   */
  public $content;
  /**
   * The SHA-1 hash of the blob.
   *
   * @var string
   */
  public $sha;

  /**
   * The content of the blob, encoded as base64.
   *
   * @param string $content
   */
  public function setContent($content)
  {
    $this->content = $content;
  }
  /**
   * @return string
   */
  public function getContent()
  {
    return $this->content;
  }
  /**
   * The SHA-1 hash of the blob.
   *
   * @param string $sha
   */
  public function setSha($sha)
  {
    $this->sha = $sha;
  }
  /**
   * @return string
   */
  public function getSha()
  {
    return $this->sha;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FetchBlobResponse::class, 'Google_Service_SecureSourceManager_FetchBlobResponse');
