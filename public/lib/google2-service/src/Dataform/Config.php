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

namespace Google\Service\Dataform;

class Config extends \Google\Model
{
  /**
   * Optional. The default KMS key that is used if no encryption key is provided
   * when a repository is created.
   *
   * @var string
   */
  public $defaultKmsKeyName;
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @var string
   */
  public $internalMetadata;
  /**
   * Identifier. The config name.
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The default KMS key that is used if no encryption key is provided
   * when a repository is created.
   *
   * @param string $defaultKmsKeyName
   */
  public function setDefaultKmsKeyName($defaultKmsKeyName)
  {
    $this->defaultKmsKeyName = $defaultKmsKeyName;
  }
  /**
   * @return string
   */
  public function getDefaultKmsKeyName()
  {
    return $this->defaultKmsKeyName;
  }
  /**
   * Output only. All the metadata information that is used internally to serve
   * the resource. For example: timestamps, flags, status fields, etc. The
   * format of this field is a JSON string.
   *
   * @param string $internalMetadata
   */
  public function setInternalMetadata($internalMetadata)
  {
    $this->internalMetadata = $internalMetadata;
  }
  /**
   * @return string
   */
  public function getInternalMetadata()
  {
    return $this->internalMetadata;
  }
  /**
   * Identifier. The config name.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Config::class, 'Google_Service_Dataform_Config');
