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

namespace Google\Service\AndroidEnterprise;

class ManagedConfiguration extends \Google\Collection
{
  protected $collection_key = 'managedProperty';
  protected $configurationVariablesType = ConfigurationVariables::class;
  protected $configurationVariablesDataType = '';
  /**
   * Deprecated.
   *
   * @var string
   */
  public $kind;
  protected $managedPropertyType = ManagedProperty::class;
  protected $managedPropertyDataType = 'array';
  /**
   * The ID of the product that the managed configuration is for, e.g.
   * "app:com.google.android.gm".
   *
   * @var string
   */
  public $productId;

  /**
   * Contains the ID of the managed configuration profile and the set of
   * configuration variables (if any) defined for the user.
   *
   * @param ConfigurationVariables $configurationVariables
   */
  public function setConfigurationVariables(ConfigurationVariables $configurationVariables)
  {
    $this->configurationVariables = $configurationVariables;
  }
  /**
   * @return ConfigurationVariables
   */
  public function getConfigurationVariables()
  {
    return $this->configurationVariables;
  }
  /**
   * Deprecated.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The set of managed properties for this configuration.
   *
   * @param ManagedProperty[] $managedProperty
   */
  public function setManagedProperty($managedProperty)
  {
    $this->managedProperty = $managedProperty;
  }
  /**
   * @return ManagedProperty[]
   */
  public function getManagedProperty()
  {
    return $this->managedProperty;
  }
  /**
   * The ID of the product that the managed configuration is for, e.g.
   * "app:com.google.android.gm".
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ManagedConfiguration::class, 'Google_Service_AndroidEnterprise_ManagedConfiguration');
