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

namespace Google\Service\Baremetalsolution;

class GoogleCloudBaremetalsolutionV2ServerNetworkTemplateLogicalInterface extends \Google\Model
{
  /**
   * Unspecified value.
   */
  public const TYPE_INTERFACE_TYPE_UNSPECIFIED = 'INTERFACE_TYPE_UNSPECIFIED';
  /**
   * Bond interface type.
   */
  public const TYPE_BOND = 'BOND';
  /**
   * NIC interface type.
   */
  public const TYPE_NIC = 'NIC';
  /**
   * Interface name. This is not a globally unique identifier. Name is unique
   * only inside the ServerNetworkTemplate. This is of syntax or and forms part
   * of the network template name.
   *
   * @var string
   */
  public $name;
  /**
   * If true, interface must have network connected.
   *
   * @var bool
   */
  public $required;
  /**
   * Interface type.
   *
   * @var string
   */
  public $type;

  /**
   * Interface name. This is not a globally unique identifier. Name is unique
   * only inside the ServerNetworkTemplate. This is of syntax or and forms part
   * of the network template name.
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
   * If true, interface must have network connected.
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * Interface type.
   *
   * Accepted values: INTERFACE_TYPE_UNSPECIFIED, BOND, NIC
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBaremetalsolutionV2ServerNetworkTemplateLogicalInterface::class, 'Google_Service_Baremetalsolution_GoogleCloudBaremetalsolutionV2ServerNetworkTemplateLogicalInterface');
