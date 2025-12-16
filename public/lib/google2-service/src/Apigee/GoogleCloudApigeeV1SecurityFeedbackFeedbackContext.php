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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityFeedbackFeedbackContext extends \Google\Collection
{
  /**
   * Unspecified attribute.
   */
  public const ATTRIBUTE_ATTRIBUTE_UNSPECIFIED = 'ATTRIBUTE_UNSPECIFIED';
  /**
   * Values will be a list of environments.
   */
  public const ATTRIBUTE_ATTRIBUTE_ENVIRONMENTS = 'ATTRIBUTE_ENVIRONMENTS';
  /**
   * Values will be a list of IP addresses. This could be either IPv4 or IPv6.
   */
  public const ATTRIBUTE_ATTRIBUTE_IP_ADDRESS_RANGES = 'ATTRIBUTE_IP_ADDRESS_RANGES';
  protected $collection_key = 'values';
  /**
   * Required. The attribute the user is providing feedback about.
   *
   * @var string
   */
  public $attribute;
  /**
   * Required. The values of the attribute the user is providing feedback about.
   *
   * @var string[]
   */
  public $values;

  /**
   * Required. The attribute the user is providing feedback about.
   *
   * Accepted values: ATTRIBUTE_UNSPECIFIED, ATTRIBUTE_ENVIRONMENTS,
   * ATTRIBUTE_IP_ADDRESS_RANGES
   *
   * @param self::ATTRIBUTE_* $attribute
   */
  public function setAttribute($attribute)
  {
    $this->attribute = $attribute;
  }
  /**
   * @return self::ATTRIBUTE_*
   */
  public function getAttribute()
  {
    return $this->attribute;
  }
  /**
   * Required. The values of the attribute the user is providing feedback about.
   *
   * @param string[] $values
   */
  public function setValues($values)
  {
    $this->values = $values;
  }
  /**
   * @return string[]
   */
  public function getValues()
  {
    return $this->values;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityFeedbackFeedbackContext::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityFeedbackFeedbackContext');
