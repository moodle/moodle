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

namespace Google\Service\Dfareporting;

class RemarketingValueAttribute extends \Google\Collection
{
  protected $collection_key = 'userAttributeIds';
  /**
   * Optional. Field ID in the element.
   *
   * @var int
   */
  public $fieldId;
  /**
   * Optional. Remarketing user attribute IDs for auto filtering.
   *
   * @var string[]
   */
  public $userAttributeIds;

  /**
   * Optional. Field ID in the element.
   *
   * @param int $fieldId
   */
  public function setFieldId($fieldId)
  {
    $this->fieldId = $fieldId;
  }
  /**
   * @return int
   */
  public function getFieldId()
  {
    return $this->fieldId;
  }
  /**
   * Optional. Remarketing user attribute IDs for auto filtering.
   *
   * @param string[] $userAttributeIds
   */
  public function setUserAttributeIds($userAttributeIds)
  {
    $this->userAttributeIds = $userAttributeIds;
  }
  /**
   * @return string[]
   */
  public function getUserAttributeIds()
  {
    return $this->userAttributeIds;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RemarketingValueAttribute::class, 'Google_Service_Dfareporting_RemarketingValueAttribute');
