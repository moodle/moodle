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

class CustomValueField extends \Google\Model
{
  /**
   * Optional. Field ID in the element.
   *
   * @var int
   */
  public $fieldId;
  /**
   * Optional. Custom key used to match for auto filtering.
   *
   * @var string
   */
  public $requestKey;

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
   * Optional. Custom key used to match for auto filtering.
   *
   * @param string $requestKey
   */
  public function setRequestKey($requestKey)
  {
    $this->requestKey = $requestKey;
  }
  /**
   * @return string
   */
  public function getRequestKey()
  {
    return $this->requestKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomValueField::class, 'Google_Service_Dfareporting_CustomValueField');
