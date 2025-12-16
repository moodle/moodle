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

class Abi extends \Google\Model
{
  /**
   * Unspecified abi.
   */
  public const ALIAS_UNSPECIFIED_CPU_ARCHITECTURE = 'UNSPECIFIED_CPU_ARCHITECTURE';
  /**
   * ARMEABI abi.
   */
  public const ALIAS_ARMEABI = 'ARMEABI';
  /**
   * ARMEABI_V7A abi.
   */
  public const ALIAS_ARMEABI_V7A = 'ARMEABI_V7A';
  /**
   * ARM64_V8A abi.
   */
  public const ALIAS_ARM64_V8A = 'ARM64_V8A';
  /**
   * X86 abi.
   */
  public const ALIAS_X86 = 'X86';
  /**
   * X86_64 abi.
   */
  public const ALIAS_X86_64 = 'X86_64';
  /**
   * RISCV64 abi.
   */
  public const ALIAS_RISCV64 = 'RISCV64';
  /**
   * Alias for an abi.
   *
   * @var string
   */
  public $alias;

  /**
   * Alias for an abi.
   *
   * Accepted values: UNSPECIFIED_CPU_ARCHITECTURE, ARMEABI, ARMEABI_V7A,
   * ARM64_V8A, X86, X86_64, RISCV64
   *
   * @param self::ALIAS_* $alias
   */
  public function setAlias($alias)
  {
    $this->alias = $alias;
  }
  /**
   * @return self::ALIAS_*
   */
  public function getAlias()
  {
    return $this->alias;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Abi::class, 'Google_Service_AndroidPublisher_Abi');
