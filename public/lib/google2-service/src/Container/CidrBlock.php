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

namespace Google\Service\Container;

class CidrBlock extends \Google\Model
{
  /**
   * cidr_block must be specified in CIDR notation.
   *
   * @var string
   */
  public $cidrBlock;
  /**
   * display_name is an optional field for users to identify CIDR blocks.
   *
   * @var string
   */
  public $displayName;

  /**
   * cidr_block must be specified in CIDR notation.
   *
   * @param string $cidrBlock
   */
  public function setCidrBlock($cidrBlock)
  {
    $this->cidrBlock = $cidrBlock;
  }
  /**
   * @return string
   */
  public function getCidrBlock()
  {
    return $this->cidrBlock;
  }
  /**
   * display_name is an optional field for users to identify CIDR blocks.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CidrBlock::class, 'Google_Service_Container_CidrBlock');
