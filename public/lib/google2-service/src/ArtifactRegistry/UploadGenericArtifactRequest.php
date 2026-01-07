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

namespace Google\Service\ArtifactRegistry;

class UploadGenericArtifactRequest extends \Google\Model
{
  /**
   * The name of the file of the generic artifact to be uploaded. E.g. `example-
   * file.zip` The filename is limited to letters, numbers, and url safe
   * characters, i.e. [a-zA-Z0-9-_.~@].
   *
   * @var string
   */
  public $filename;
  /**
   * The ID of the package of the generic artifact. If the package does not
   * exist, a new package will be created. The `package_id` should start and end
   * with a letter or number, only contain letters, numbers, hyphens,
   * underscores, and periods, and not exceed 256 characters.
   *
   * @var string
   */
  public $packageId;
  /**
   * The ID of the version of the generic artifact. If the version does not
   * exist, a new version will be created. The version_id must start and end
   * with a letter or number, can only contain lowercase letters, numbers, the
   * following characters [-.+~:], i.e.[a-z0-9-.+~:] and cannot exceed a total
   * of 128 characters. Creating a version called `latest` is not allowed.
   *
   * @var string
   */
  public $versionId;

  /**
   * The name of the file of the generic artifact to be uploaded. E.g. `example-
   * file.zip` The filename is limited to letters, numbers, and url safe
   * characters, i.e. [a-zA-Z0-9-_.~@].
   *
   * @param string $filename
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }
  /**
   * @return string
   */
  public function getFilename()
  {
    return $this->filename;
  }
  /**
   * The ID of the package of the generic artifact. If the package does not
   * exist, a new package will be created. The `package_id` should start and end
   * with a letter or number, only contain letters, numbers, hyphens,
   * underscores, and periods, and not exceed 256 characters.
   *
   * @param string $packageId
   */
  public function setPackageId($packageId)
  {
    $this->packageId = $packageId;
  }
  /**
   * @return string
   */
  public function getPackageId()
  {
    return $this->packageId;
  }
  /**
   * The ID of the version of the generic artifact. If the version does not
   * exist, a new version will be created. The version_id must start and end
   * with a letter or number, can only contain lowercase letters, numbers, the
   * following characters [-.+~:], i.e.[a-z0-9-.+~:] and cannot exceed a total
   * of 128 characters. Creating a version called `latest` is not allowed.
   *
   * @param string $versionId
   */
  public function setVersionId($versionId)
  {
    $this->versionId = $versionId;
  }
  /**
   * @return string
   */
  public function getVersionId()
  {
    return $this->versionId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UploadGenericArtifactRequest::class, 'Google_Service_ArtifactRegistry_UploadGenericArtifactRequest');
