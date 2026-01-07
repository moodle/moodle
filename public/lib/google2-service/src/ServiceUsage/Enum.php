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

namespace Google\Service\ServiceUsage;

class Enum extends \Google\Collection
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
  protected $enumvalueType = EnumValue::class;
  protected $enumvalueDataType = 'array';
  /**
   * Enum type name.
   *
   * @var string
   */
  public $name;
  protected $optionsType = Option::class;
  protected $optionsDataType = 'array';
  protected $sourceContextType = SourceContext::class;
  protected $sourceContextDataType = '';
  /**
   * The source syntax.
   *
   * @var string
   */
  public $syntax;

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
   * Enum value definitions.
   *
   * @param EnumValue[] $enumvalue
   */
  public function setEnumvalue($enumvalue)
  {
    $this->enumvalue = $enumvalue;
  }
  /**
   * @return EnumValue[]
   */
  public function getEnumvalue()
  {
    return $this->enumvalue;
  }
  /**
   * Enum type name.
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
   * Protocol buffer options.
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
   * The source context.
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
   * The source syntax.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Enum::class, 'Google_Service_ServiceUsage_Enum');
