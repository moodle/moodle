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

namespace Google\Service\WorkspaceEvents;

class FilePart extends \Google\Model
{
  /**
   * @var string
   */
  public $fileWithBytes;
  /**
   * @var string
   */
  public $fileWithUri;
  /**
   * @var string
   */
  public $mimeType;
  /**
   * @var string
   */
  public $name;

  /**
   * @param string $fileWithBytes
   */
  public function setFileWithBytes($fileWithBytes)
  {
    $this->fileWithBytes = $fileWithBytes;
  }
  /**
   * @return string
   */
  public function getFileWithBytes()
  {
    return $this->fileWithBytes;
  }
  /**
   * @param string $fileWithUri
   */
  public function setFileWithUri($fileWithUri)
  {
    $this->fileWithUri = $fileWithUri;
  }
  /**
   * @return string
   */
  public function getFileWithUri()
  {
    return $this->fileWithUri;
  }
  /**
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FilePart::class, 'Google_Service_WorkspaceEvents_FilePart');
