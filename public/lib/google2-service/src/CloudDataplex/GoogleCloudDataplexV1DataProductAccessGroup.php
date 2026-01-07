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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProductAccessGroup extends \Google\Model
{
  /**
   * Optional. Description of the access group.
   *
   * @var string
   */
  public $description;
  /**
   * Required. User friendly display name of the access group. Eg. "Analyst",
   * "Developer", etc.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. Unique identifier of the access group within the Data Product.
   * User defined. Eg. "analyst", "developer", etc.
   *
   * @var string
   */
  public $id;
  protected $principalType = GoogleCloudDataplexV1DataProductPrincipal::class;
  protected $principalDataType = '';

  /**
   * Optional. Description of the access group.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Required. User friendly display name of the access group. Eg. "Analyst",
   * "Developer", etc.
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
  /**
   * Required. Unique identifier of the access group within the Data Product.
   * User defined. Eg. "analyst", "developer", etc.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The principal entity associated with this access group.
   *
   * @param GoogleCloudDataplexV1DataProductPrincipal $principal
   */
  public function setPrincipal(GoogleCloudDataplexV1DataProductPrincipal $principal)
  {
    $this->principal = $principal;
  }
  /**
   * @return GoogleCloudDataplexV1DataProductPrincipal
   */
  public function getPrincipal()
  {
    return $this->principal;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataProductAccessGroup::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProductAccessGroup');
