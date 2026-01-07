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

namespace Google\Service\CloudSupport;

class DiffUploadRequest extends \Google\Model
{
  protected $checksumsInfoType = CompositeMedia::class;
  protected $checksumsInfoDataType = '';
  protected $objectInfoType = CompositeMedia::class;
  protected $objectInfoDataType = '';
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $objectVersion;

  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param CompositeMedia $checksumsInfo
   */
  public function setChecksumsInfo(CompositeMedia $checksumsInfo)
  {
    $this->checksumsInfo = $checksumsInfo;
  }
  /**
   * @return CompositeMedia
   */
  public function getChecksumsInfo()
  {
    return $this->checksumsInfo;
  }
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param CompositeMedia $objectInfo
   */
  public function setObjectInfo(CompositeMedia $objectInfo)
  {
    $this->objectInfo = $objectInfo;
  }
  /**
   * @return CompositeMedia
   */
  public function getObjectInfo()
  {
    return $this->objectInfo;
  }
  /**
   * # gdata.* are outside protos with mising documentation
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiffUploadRequest::class, 'Google_Service_CloudSupport_DiffUploadRequest');
