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

namespace Google\Service\FirebaseAppDistribution;

class GdataDiffUploadResponse extends \Google\Model
{
  /**
   * The object version of the object at the server. Must be included in the end
   * notification response. The version in the end notification response must
   * correspond to the new version of the object that is now stored at the
   * server, after the upload.
   *
   * @var string
   */
  public $objectVersion;
  protected $originalObjectType = GdataCompositeMedia::class;
  protected $originalObjectDataType = '';

  /**
   * The object version of the object at the server. Must be included in the end
   * notification response. The version in the end notification response must
   * correspond to the new version of the object that is now stored at the
   * server, after the upload.
   *
   * @param string $objectVersion
   */
  public function setObjectVersion($objectVersion)
  {
    $this->objectVersion = $objectVersion;
  }
  /**
   * @return string
   */
  public function getObjectVersion()
  {
    return $this->objectVersion;
  }
  /**
   * The location of the original file for a diff upload request. Must be filled
   * in if responding to an upload start notification.
   *
   * @param GdataCompositeMedia $originalObject
   */
  public function setOriginalObject(GdataCompositeMedia $originalObject)
  {
    $this->originalObject = $originalObject;
  }
  /**
   * @return GdataCompositeMedia
   */
  public function getOriginalObject()
  {
    return $this->originalObject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GdataDiffUploadResponse::class, 'Google_Service_FirebaseAppDistribution_GdataDiffUploadResponse');
