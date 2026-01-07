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

namespace Google\Service\Walletobjects;

class ContentTypeInfo extends \Google\Model
{
  /**
   * Scotty's best guess of what the content type of the file is.
   *
   * @var string
   */
  public $bestGuess;
  /**
   * The content type of the file derived by looking at specific bytes (i.e.
   * "magic bytes") of the actual file.
   *
   * @var string
   */
  public $fromBytes;
  /**
   * The content type of the file derived from the file extension of the
   * original file name used by the client.
   *
   * @var string
   */
  public $fromFileName;
  /**
   * The content type of the file as specified in the request headers, multipart
   * headers, or RUPIO start request.
   *
   * @var string
   */
  public $fromHeader;
  /**
   * The content type of the file derived from the file extension of the URL
   * path. The URL path is assumed to represent a file name (which is typically
   * only true for agents that are providing a REST API).
   *
   * @var string
   */
  public $fromUrlPath;

  /**
   * Scotty's best guess of what the content type of the file is.
   *
   * @param string $bestGuess
   */
  public function setBestGuess($bestGuess)
  {
    $this->bestGuess = $bestGuess;
  }
  /**
   * @return string
   */
  public function getBestGuess()
  {
    return $this->bestGuess;
  }
  /**
   * The content type of the file derived by looking at specific bytes (i.e.
   * "magic bytes") of the actual file.
   *
   * @param string $fromBytes
   */
  public function setFromBytes($fromBytes)
  {
    $this->fromBytes = $fromBytes;
  }
  /**
   * @return string
   */
  public function getFromBytes()
  {
    return $this->fromBytes;
  }
  /**
   * The content type of the file derived from the file extension of the
   * original file name used by the client.
   *
   * @param string $fromFileName
   */
  public function setFromFileName($fromFileName)
  {
    $this->fromFileName = $fromFileName;
  }
  /**
   * @return string
   */
  public function getFromFileName()
  {
    return $this->fromFileName;
  }
  /**
   * The content type of the file as specified in the request headers, multipart
   * headers, or RUPIO start request.
   *
   * @param string $fromHeader
   */
  public function setFromHeader($fromHeader)
  {
    $this->fromHeader = $fromHeader;
  }
  /**
   * @return string
   */
  public function getFromHeader()
  {
    return $this->fromHeader;
  }
  /**
   * The content type of the file derived from the file extension of the URL
   * path. The URL path is assumed to represent a file name (which is typically
   * only true for agents that are providing a REST API).
   *
   * @param string $fromUrlPath
   */
  public function setFromUrlPath($fromUrlPath)
  {
    $this->fromUrlPath = $fromUrlPath;
  }
  /**
   * @return string
   */
  public function getFromUrlPath()
  {
    return $this->fromUrlPath;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContentTypeInfo::class, 'Google_Service_Walletobjects_ContentTypeInfo');
