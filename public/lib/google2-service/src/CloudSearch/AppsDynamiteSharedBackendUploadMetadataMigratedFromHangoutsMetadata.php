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

namespace Google\Service\CloudSearch;

class AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadata extends \Google\Model
{
  protected $photoIdType = AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadataPhotoId::class;
  protected $photoIdDataType = '';
  /**
   * @var string
   */
  public $updateTimestampUsec;

  /**
   * @param AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadataPhotoId
   */
  public function setPhotoId(AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadataPhotoId $photoId)
  {
    $this->photoId = $photoId;
  }
  /**
   * @return AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadataPhotoId
   */
  public function getPhotoId()
  {
    return $this->photoId;
  }
  /**
   * @param string
   */
  public function setUpdateTimestampUsec($updateTimestampUsec)
  {
    $this->updateTimestampUsec = $updateTimestampUsec;
  }
  /**
   * @return string
   */
  public function getUpdateTimestampUsec()
  {
    return $this->updateTimestampUsec;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadata::class, 'Google_Service_CloudSearch_AppsDynamiteSharedBackendUploadMetadataMigratedFromHangoutsMetadata');
