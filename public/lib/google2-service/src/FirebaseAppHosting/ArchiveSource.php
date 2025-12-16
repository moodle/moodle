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

namespace Google\Service\FirebaseAppHosting;

class ArchiveSource extends \Google\Model
{
  protected $authorType = SourceUserMetadata::class;
  protected $authorDataType = '';
  /**
   * Optional. An optional message that describes the uploaded version of the
   * source code.
   *
   * @var string
   */
  public $description;
  /**
   * Signed URL to an archive in a storage bucket.
   *
   * @var string
   */
  public $externalSignedUri;
  /**
   * Optional. Relative path in the archive.
   *
   * @var string
   */
  public $rootDirectory;
  /**
   * URI to an archive in Cloud Storage. The object must be a zipped (.zip) or
   * gzipped archive file (.tar.gz) containing source to deploy.
   *
   * @var string
   */
  public $userStorageUri;

  /**
   * Optional. The author contained in the metadata of a version control change.
   *
   * @param SourceUserMetadata $author
   */
  public function setAuthor(SourceUserMetadata $author)
  {
    $this->author = $author;
  }
  /**
   * @return SourceUserMetadata
   */
  public function getAuthor()
  {
    return $this->author;
  }
  /**
   * Optional. An optional message that describes the uploaded version of the
   * source code.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Signed URL to an archive in a storage bucket.
   *
   * @param string $externalSignedUri
   */
  public function setExternalSignedUri($externalSignedUri)
  {
    $this->externalSignedUri = $externalSignedUri;
  }
  /**
   * @return string
   */
  public function getExternalSignedUri()
  {
    return $this->externalSignedUri;
  }
  /**
   * Optional. Relative path in the archive.
   *
   * @param string $rootDirectory
   */
  public function setRootDirectory($rootDirectory)
  {
    $this->rootDirectory = $rootDirectory;
  }
  /**
   * @return string
   */
  public function getRootDirectory()
  {
    return $this->rootDirectory;
  }
  /**
   * URI to an archive in Cloud Storage. The object must be a zipped (.zip) or
   * gzipped archive file (.tar.gz) containing source to deploy.
   *
   * @param string $userStorageUri
   */
  public function setUserStorageUri($userStorageUri)
  {
    $this->userStorageUri = $userStorageUri;
  }
  /**
   * @return string
   */
  public function getUserStorageUri()
  {
    return $this->userStorageUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ArchiveSource::class, 'Google_Service_FirebaseAppHosting_ArchiveSource');
