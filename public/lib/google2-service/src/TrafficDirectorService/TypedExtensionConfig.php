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

class TypedExtensionConfig extends \Google\Model
{
  /**
   * The name of an extension. This is not used to select the extension, instead
   * it serves the role of an opaque identifier.
   *
   * @var string
   */
  public $name;
  /**
   * The typed config for the extension. The type URL will be used to identify
   * the extension. In the case that the type URL is *xds.type.v3.TypedStruct*
   * (or, for historical reasons, *udpa.type.v1.TypedStruct*), the inner type
   * URL of *TypedStruct* will be utilized. See the :ref:`extension
   * configuration overview ` for further details.
   *
   * @var array[]
   */
  public $typedConfig;

  /**
   * The name of an extension. This is not used to select the extension, instead
   * it serves the role of an opaque identifier.
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
   * The typed config for the extension. The type URL will be used to identify
   * the extension. In the case that the type URL is *xds.type.v3.TypedStruct*
   * (or, for historical reasons, *udpa.type.v1.TypedStruct*), the inner type
   * URL of *TypedStruct* will be utilized. See the :ref:`extension
   * configuration overview ` for further details.
   *
   * @param array[] $typedConfig
   */
  public function setTypedConfig($typedConfig)
  {
    $this->typedConfig = $typedConfig;
  }
  /**
   * @return array[]
   */
  public function getTypedConfig()
  {
    return $this->typedConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TypedExtensionConfig::class, 'Google_Service_TrafficDirectorService_TypedExtensionConfig');
