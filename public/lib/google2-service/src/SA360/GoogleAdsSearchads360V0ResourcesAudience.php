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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesAudience extends \Google\Model
{
  /**
   * Description of this audience.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. ID of the audience.
   *
   * @var string
   */
  public $id;
  /**
   * Required. Name of the audience. It should be unique across all audiences.
   * It must have a minimum length of 1 and maximum length of 255.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. The resource name of the audience. Audience names have the form:
   * `customers/{customer_id}/audiences/{audience_id}`
   *
   * @var string
   */
  public $resourceName;

  /**
   * Description of this audience.
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
   * Output only. ID of the audience.
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
   * Required. Name of the audience. It should be unique across all audiences.
   * It must have a minimum length of 1 and maximum length of 255.
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
   * Immutable. The resource name of the audience. Audience names have the form:
   * `customers/{customer_id}/audiences/{audience_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesAudience::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesAudience');
