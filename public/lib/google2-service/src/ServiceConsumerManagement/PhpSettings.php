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

namespace Google\Service\ServiceConsumerManagement;

class PhpSettings extends \Google\Model
{
  protected $commonType = CommonLanguageSettings::class;
  protected $commonDataType = '';
  /**
   * The package name to use in Php. Clobbers the php_namespace option set in
   * the protobuf. This should be used **only** by APIs who have already set the
   * language_settings.php.package_name" field in gapic.yaml. API teams should
   * use the protobuf php_namespace option where possible. Example of a YAML
   * configuration:: publishing: library_settings: php_settings:
   * library_package: Google\Cloud\PubSub\V1
   *
   * @var string
   */
  public $libraryPackage;

  /**
   * Some settings.
   *
   * @param CommonLanguageSettings $common
   */
  public function setCommon(CommonLanguageSettings $common)
  {
    $this->common = $common;
  }
  /**
   * @return CommonLanguageSettings
   */
  public function getCommon()
  {
    return $this->common;
  }
  /**
   * The package name to use in Php. Clobbers the php_namespace option set in
   * the protobuf. This should be used **only** by APIs who have already set the
   * language_settings.php.package_name" field in gapic.yaml. API teams should
   * use the protobuf php_namespace option where possible. Example of a YAML
   * configuration:: publishing: library_settings: php_settings:
   * library_package: Google\Cloud\PubSub\V1
   *
   * @param string $libraryPackage
   */
  public function setLibraryPackage($libraryPackage)
  {
    $this->libraryPackage = $libraryPackage;
  }
  /**
   * @return string
   */
  public function getLibraryPackage()
  {
    return $this->libraryPackage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PhpSettings::class, 'Google_Service_ServiceConsumerManagement_PhpSettings');
