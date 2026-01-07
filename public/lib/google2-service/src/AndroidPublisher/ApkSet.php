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

namespace Google\Service\AndroidPublisher;

class ApkSet extends \Google\Collection
{
  protected $collection_key = 'apkDescription';
  protected $apkDescriptionType = ApkDescription::class;
  protected $apkDescriptionDataType = 'array';
  protected $moduleMetadataType = ModuleMetadata::class;
  protected $moduleMetadataDataType = '';

  /**
   * Description of the generated apks.
   *
   * @param ApkDescription[] $apkDescription
   */
  public function setApkDescription($apkDescription)
  {
    $this->apkDescription = $apkDescription;
  }
  /**
   * @return ApkDescription[]
   */
  public function getApkDescription()
  {
    return $this->apkDescription;
  }
  /**
   * Metadata about the module represented by this ApkSet
   *
   * @param ModuleMetadata $moduleMetadata
   */
  public function setModuleMetadata(ModuleMetadata $moduleMetadata)
  {
    $this->moduleMetadata = $moduleMetadata;
  }
  /**
   * @return ModuleMetadata
   */
  public function getModuleMetadata()
  {
    return $this->moduleMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApkSet::class, 'Google_Service_AndroidPublisher_ApkSet');
