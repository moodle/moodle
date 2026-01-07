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

namespace Google\Service\ServiceManagement;

class Api extends \Google\Collection
{
  /**
   * Syntax `proto2`.
   */
  public const SYNTAX_SYNTAX_PROTO2 = 'SYNTAX_PROTO2';
  /**
   * Syntax `proto3`.
   */
  public const SYNTAX_SYNTAX_PROTO3 = 'SYNTAX_PROTO3';
  /**
   * Syntax `editions`.
   */
  public const SYNTAX_SYNTAX_EDITIONS = 'SYNTAX_EDITIONS';
  protected $collection_key = 'options';
  /**
   * The source edition string, only valid when syntax is SYNTAX_EDITIONS.
   *
   * @var string
   */
  public $edition;
  protected $methodsType = Method::class;
  protected $methodsDataType = 'array';
  protected $mixinsType = Mixin::class;
  protected $mixinsDataType = 'array';
  /**
   * The fully qualified name of this interface, including package name followed
   * by the interface's simple name.
   *
   * @var string
   */
  public $name;
  protected $optionsType = Option::class;
  protected $optionsDataType = 'array';
  protected $sourceContextType = SourceContext::class;
  protected $sourceContextDataType = '';
  /**
   * The source syntax of the service.
   *
   * @var string
   */
  public $syntax;
  /**
   * A version string for this interface. If specified, must have the form
   * `major-version.minor-version`, as in `1.10`. If the minor version is
   * omitted, it defaults to zero. If the entire version field is empty, the
   * major version is derived from the package name, as outlined below. If the
   * field is not empty, the version in the package name will be verified to be
   * consistent with what is provided here. The versioning schema uses [semantic
   * versioning](http://semver.org) where the major version number indicates a
   * breaking change and the minor version an additive, non-breaking change.
   * Both version numbers are signals to users what to expect from different
   * versions, and should be carefully chosen based on the product plan. The
   * major version is also reflected in the package name of the interface, which
   * must end in `v`, as in `google.feature.v1`. For major versions 0 and 1, the
   * suffix can be omitted. Zero major versions must only be used for
   * experimental, non-GA interfaces.
   *
   * @var string
   */
  public $version;

  /**
   * The source edition string, only valid when syntax is SYNTAX_EDITIONS.
   *
   * @param string $edition
   */
  public function setEdition($edition)
  {
    $this->edition = $edition;
  }
  /**
   * @return string
   */
  public function getEdition()
  {
    return $this->edition;
  }
  /**
   * The methods of this interface, in unspecified order.
   *
   * @param Method[] $methods
   */
  public function setMethods($methods)
  {
    $this->methods = $methods;
  }
  /**
   * @return Method[]
   */
  public function getMethods()
  {
    return $this->methods;
  }
  /**
   * Included interfaces. See Mixin.
   *
   * @param Mixin[] $mixins
   */
  public function setMixins($mixins)
  {
    $this->mixins = $mixins;
  }
  /**
   * @return Mixin[]
   */
  public function getMixins()
  {
    return $this->mixins;
  }
  /**
   * The fully qualified name of this interface, including package name followed
   * by the interface's simple name.
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
   * Any metadata attached to the interface.
   *
   * @param Option[] $options
   */
  public function setOptions($options)
  {
    $this->options = $options;
  }
  /**
   * @return Option[]
   */
  public function getOptions()
  {
    return $this->options;
  }
  /**
   * Source context for the protocol buffer service represented by this message.
   *
   * @param SourceContext $sourceContext
   */
  public function setSourceContext(SourceContext $sourceContext)
  {
    $this->sourceContext = $sourceContext;
  }
  /**
   * @return SourceContext
   */
  public function getSourceContext()
  {
    return $this->sourceContext;
  }
  /**
   * The source syntax of the service.
   *
   * Accepted values: SYNTAX_PROTO2, SYNTAX_PROTO3, SYNTAX_EDITIONS
   *
   * @param self::SYNTAX_* $syntax
   */
  public function setSyntax($syntax)
  {
    $this->syntax = $syntax;
  }
  /**
   * @return self::SYNTAX_*
   */
  public function getSyntax()
  {
    return $this->syntax;
  }
  /**
   * A version string for this interface. If specified, must have the form
   * `major-version.minor-version`, as in `1.10`. If the minor version is
   * omitted, it defaults to zero. If the entire version field is empty, the
   * major version is derived from the package name, as outlined below. If the
   * field is not empty, the version in the package name will be verified to be
   * consistent with what is provided here. The versioning schema uses [semantic
   * versioning](http://semver.org) where the major version number indicates a
   * breaking change and the minor version an additive, non-breaking change.
   * Both version numbers are signals to users what to expect from different
   * versions, and should be carefully chosen based on the product plan. The
   * major version is also reflected in the package name of the interface, which
   * must end in `v`, as in `google.feature.v1`. For major versions 0 and 1, the
   * suffix can be omitted. Zero major versions must only be used for
   * experimental, non-GA interfaces.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Api::class, 'Google_Service_ServiceManagement_Api');
