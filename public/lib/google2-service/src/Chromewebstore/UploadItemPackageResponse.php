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

namespace Google\Service\Chromewebstore;

class UploadItemPackageResponse extends \Google\Model
{
  /**
   * The default value.
   */
  public const UPLOAD_STATE_UPLOAD_STATE_UNSPECIFIED = 'UPLOAD_STATE_UNSPECIFIED';
  /**
   * The upload succeeded.
   */
  public const UPLOAD_STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The upload is currently being processed.
   */
  public const UPLOAD_STATE_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * The upload failed.
   */
  public const UPLOAD_STATE_FAILED = 'FAILED';
  /**
   * Used as the value of `lastAsyncUploadState` in a `fetchStatus` response
   * indicating that an upload attempt was not found.
   */
  public const UPLOAD_STATE_NOT_FOUND = 'NOT_FOUND';
  /**
   * The extension version provided in the manifest of the uploaded package.
   * This will not be set if the upload is still in progress (`upload_state` is
   * `UPLOAD_IN_PROGRESS`).
   *
   * @var string
   */
  public $crxVersion;
  /**
   * Output only. The ID of the item the package was uploaded to.
   *
   * @var string
   */
  public $itemId;
  /**
   * The name of the item the package was uploaded to.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The state of the upload. If `upload_state` is
   * `UPLOAD_IN_PROGRESS`, you can poll for updates using the fetchStatus
   * method.
   *
   * @var string
   */
  public $uploadState;

  /**
   * The extension version provided in the manifest of the uploaded package.
   * This will not be set if the upload is still in progress (`upload_state` is
   * `UPLOAD_IN_PROGRESS`).
   *
   * @param string $crxVersion
   */
  public function setCrxVersion($crxVersion)
  {
    $this->crxVersion = $crxVersion;
  }
  /**
   * @return string
   */
  public function getCrxVersion()
  {
    return $this->crxVersion;
  }
  /**
   * Output only. The ID of the item the package was uploaded to.
   *
   * @param string $itemId
   */
  public function setItemId($itemId)
  {
    $this->itemId = $itemId;
  }
  /**
   * @return string
   */
  public function getItemId()
  {
    return $this->itemId;
  }
  /**
   * The name of the item the package was uploaded to.
   *
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
  /**
   * Output only. The state of the upload. If `upload_state` is
   * `UPLOAD_IN_PROGRESS`, you can poll for updates using the fetchStatus
   * method.
   *
   * Accepted values: UPLOAD_STATE_UNSPECIFIED, SUCCEEDED, IN_PROGRESS, FAILED,
   * NOT_FOUND
   *
   * @param self::UPLOAD_STATE_* $uploadState
   */
  public function setUploadState($uploadState)
  {
    $this->uploadState = $uploadState;
  }
  /**
   * @return self::UPLOAD_STATE_*
   */
  public function getUploadState()
  {
    return $this->uploadState;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UploadItemPackageResponse::class, 'Google_Service_Chromewebstore_UploadItemPackageResponse');
