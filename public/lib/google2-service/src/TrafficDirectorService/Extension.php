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

namespace Google\Service\TrafficDirectorService;

class Extension extends \Google\Collection
{
  protected $collection_key = 'typeUrls';
  /**
   * Category of the extension. Extension category names use reverse DNS
   * notation. For instance "envoy.filters.listener" for Envoy's built-in
   * listener filters or "com.acme.filters.http" for HTTP filters from acme.com
   * vendor. [#comment:
   *
   * @var string
   */
  public $category;
  /**
   * Indicates that the extension is present but was disabled via dynamic
   * configuration.
   *
   * @var bool
   */
  public $disabled;
  /**
   * This is the name of the Envoy filter as specified in the Envoy
   * configuration, e.g. envoy.filters.http.router, com.acme.widget.
   *
   * @var string
   */
  public $name;
  /**
   * [#not-implemented-hide:] Type descriptor of extension configuration proto.
   * [#comment:
   *
   * @deprecated
   * @var string
   */
  public $typeDescriptor;
  /**
   * Type URLs of extension configuration protos.
   *
   * @var string[]
   */
  public $typeUrls;
  protected $versionType = BuildVersion::class;
  protected $versionDataType = '';

  /**
   * Category of the extension. Extension category names use reverse DNS
   * notation. For instance "envoy.filters.listener" for Envoy's built-in
   * listener filters or "com.acme.filters.http" for HTTP filters from acme.com
   * vendor. [#comment:
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * Indicates that the extension is present but was disabled via dynamic
   * configuration.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * This is the name of the Envoy filter as specified in the Envoy
   * configuration, e.g. envoy.filters.http.router, com.acme.widget.
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
   * [#not-implemented-hide:] Type descriptor of extension configuration proto.
   * [#comment:
   *
   * @deprecated
   * @param string $typeDescriptor
   */
  public function setTypeDescriptor($typeDescriptor)
  {
    $this->typeDescriptor = $typeDescriptor;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getTypeDescriptor()
  {
    return $this->typeDescriptor;
  }
  /**
   * Type URLs of extension configuration protos.
   *
   * @param string[] $typeUrls
   */
  public function setTypeUrls($typeUrls)
  {
    $this->typeUrls = $typeUrls;
  }
  /**
   * @return string[]
   */
  public function getTypeUrls()
  {
    return $this->typeUrls;
  }
  /**
   * The version is a property of the extension and maintained independently of
   * other extensions and the Envoy API. This field is not set when extension
   * did not provide version information.
   *
   * @param BuildVersion $version
   */
  public function setVersion(BuildVersion $version)
  {
    $this->version = $version;
  }
  /**
   * @return BuildVersion
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Extension::class, 'Google_Service_TrafficDirectorService_Extension');
