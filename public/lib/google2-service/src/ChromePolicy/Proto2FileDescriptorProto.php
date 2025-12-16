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

namespace Google\Service\ChromePolicy;

class Proto2FileDescriptorProto extends \Google\Collection
{
  protected $collection_key = 'optionDependency';
  /**
   * copybara:strip_begin TODO(b/297898292) Deprecate and remove this field in
   * favor of enums. copybara:strip_end
   *
   * @deprecated
   * @var string
   */
  public $editionDeprecated;
  protected $enumTypeType = Proto2EnumDescriptorProto::class;
  protected $enumTypeDataType = 'array';
  protected $messageTypeType = Proto2DescriptorProto::class;
  protected $messageTypeDataType = 'array';
  /**
   * file name, relative to root of source tree
   *
   * @var string
   */
  public $name;
  /**
   * Names of files imported by this file purely for the purpose of providing
   * option extensions. These are excluded from the dependency list above.
   *
   * @var string[]
   */
  public $optionDependency;
  /**
   * e.g. "foo", "foo.bar", etc.
   *
   * @var string
   */
  public $package;
  /**
   * The syntax of the proto file. The supported values are "proto2", "proto3",
   * and "editions". If `edition` is present, this value must be "editions".
   * WARNING: This field should only be used by protobuf plugins or special
   * cases like the proto compiler. Other uses are discouraged and developers
   * should rely on the protoreflect APIs for their client language.
   *
   * @var string
   */
  public $syntax;

  /**
   * copybara:strip_begin TODO(b/297898292) Deprecate and remove this field in
   * favor of enums. copybara:strip_end
   *
   * @deprecated
   * @param string $editionDeprecated
   */
  public function setEditionDeprecated($editionDeprecated)
  {
    $this->editionDeprecated = $editionDeprecated;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getEditionDeprecated()
  {
    return $this->editionDeprecated;
  }
  /**
   * @param Proto2EnumDescriptorProto[] $enumType
   */
  public function setEnumType($enumType)
  {
    $this->enumType = $enumType;
  }
  /**
   * @return Proto2EnumDescriptorProto[]
   */
  public function getEnumType()
  {
    return $this->enumType;
  }
  /**
   * All top-level definitions in this file.
   *
   * @param Proto2DescriptorProto[] $messageType
   */
  public function setMessageType($messageType)
  {
    $this->messageType = $messageType;
  }
  /**
   * @return Proto2DescriptorProto[]
   */
  public function getMessageType()
  {
    return $this->messageType;
  }
  /**
   * file name, relative to root of source tree
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
   * Names of files imported by this file purely for the purpose of providing
   * option extensions. These are excluded from the dependency list above.
   *
   * @param string[] $optionDependency
   */
  public function setOptionDependency($optionDependency)
  {
    $this->optionDependency = $optionDependency;
  }
  /**
   * @return string[]
   */
  public function getOptionDependency()
  {
    return $this->optionDependency;
  }
  /**
   * e.g. "foo", "foo.bar", etc.
   *
   * @param string $package
   */
  public function setPackage($package)
  {
    $this->package = $package;
  }
  /**
   * @return string
   */
  public function getPackage()
  {
    return $this->package;
  }
  /**
   * The syntax of the proto file. The supported values are "proto2", "proto3",
   * and "editions". If `edition` is present, this value must be "editions".
   * WARNING: This field should only be used by protobuf plugins or special
   * cases like the proto compiler. Other uses are discouraged and developers
   * should rely on the protoreflect APIs for their client language.
   *
   * @param string $syntax
   */
  public function setSyntax($syntax)
  {
    $this->syntax = $syntax;
  }
  /**
   * @return string
   */
  public function getSyntax()
  {
    return $this->syntax;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Proto2FileDescriptorProto::class, 'Google_Service_ChromePolicy_Proto2FileDescriptorProto');
