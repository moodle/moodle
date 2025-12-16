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

namespace Google\Service\StreetViewPublish;

class UpdatePhotoRequest extends \Google\Model
{
  protected $photoType = Photo::class;
  protected $photoDataType = '';
  /**
   * Required. Mask that identifies fields on the photo metadata to update. If
   * not present, the old Photo metadata is entirely replaced with the new Photo
   * metadata in this request. The update fails if invalid fields are specified.
   * Multiple fields can be specified in a comma-delimited list. The following
   * fields are valid: * `pose.heading` * `pose.lat_lng_pair` * `pose.pitch` *
   * `pose.roll` * `pose.level` * `pose.altitude` * `connections` * `places` >
   * Note: When updateMask contains repeated fields, the entire set of repeated
   * values get replaced with the new contents. For example, if updateMask
   * contains `connections` and `UpdatePhotoRequest.photo.connections` is empty,
   * all connections are removed.
   *
   * @var string
   */
  public $updateMask;

  /**
   * Required. Photo object containing the new metadata.
   *
   * @param Photo $photo
   */
  public function setPhoto(Photo $photo)
  {
    $this->photo = $photo;
  }
  /**
   * @return Photo
   */
  public function getPhoto()
  {
    return $this->photo;
  }
  /**
   * Required. Mask that identifies fields on the photo metadata to update. If
   * not present, the old Photo metadata is entirely replaced with the new Photo
   * metadata in this request. The update fails if invalid fields are specified.
   * Multiple fields can be specified in a comma-delimited list. The following
   * fields are valid: * `pose.heading` * `pose.lat_lng_pair` * `pose.pitch` *
   * `pose.roll` * `pose.level` * `pose.altitude` * `connections` * `places` >
   * Note: When updateMask contains repeated fields, the entire set of repeated
   * values get replaced with the new contents. For example, if updateMask
   * contains `connections` and `UpdatePhotoRequest.photo.connections` is empty,
   * all connections are removed.
   *
   * @param string $updateMask
   */
  public function setUpdateMask($updateMask)
  {
    $this->updateMask = $updateMask;
  }
  /**
   * @return string
   */
  public function getUpdateMask()
  {
    return $this->updateMask;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpdatePhotoRequest::class, 'Google_Service_StreetViewPublish_UpdatePhotoRequest');
