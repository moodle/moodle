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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1AllowedValue extends \Google\Model
{
  /**
   * Optional. The detailed description of the allowed value.
   *
   * @var string
   */
  public $description;
  /**
   * Required. The display name of the allowed value.
   *
   * @var string
   */
  public $displayName;
  /**
   * Required. The ID of the allowed value. * If provided, the same will be
   * used. The service will throw an error if the specified id is already used
   * by another allowed value in the same attribute resource. * If not provided,
   * a system generated id derived from the display name will be used. In this
   * case, the service will handle conflict resolution by adding a system
   * generated suffix in case of duplicates. This value should be 4-63
   * characters, and valid characters are /a-z-/.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. When set to true, the allowed value cannot be updated or deleted
   * by the user. It can only be true for System defined attributes.
   *
   * @var bool
   */
  public $immutable;

  /**
   * Optional. The detailed description of the allowed value.
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
   * Required. The display name of the allowed value.
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
   * Required. The ID of the allowed value. * If provided, the same will be
   * used. The service will throw an error if the specified id is already used
   * by another allowed value in the same attribute resource. * If not provided,
   * a system generated id derived from the display name will be used. In this
   * case, the service will handle conflict resolution by adding a system
   * generated suffix in case of duplicates. This value should be 4-63
   * characters, and valid characters are /a-z-/.
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
   * Optional. When set to true, the allowed value cannot be updated or deleted
   * by the user. It can only be true for System defined attributes.
   *
   * @param bool $immutable
   */
  public function setImmutable($immutable)
  {
    $this->immutable = $immutable;
  }
  /**
   * @return bool
   */
  public function getImmutable()
  {
    return $this->immutable;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1AllowedValue::class, 'Google_Service_APIhub_GoogleCloudApihubV1AllowedValue');
