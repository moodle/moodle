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

class DiffUploadResponse extends \Google\Model
{
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @var string
   */
  public $objectVersion;
  protected $originalObjectType = CompositeMedia::class;
  protected $originalObjectDataType = '';

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
  /**
   * # gdata.* are outside protos with mising documentation
   *
   * @param CompositeMedia $originalObject
   */
  public function setOriginalObject(CompositeMedia $originalObject)
  {
    $this->originalObject = $originalObject;
  }
  /**
   * @return CompositeMedia
   */
  public function getOriginalObject()
  {
    return $this->originalObject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DiffUploadResponse::class, 'Google_Service_CloudSupport_DiffUploadResponse');
