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

class GoogleCloudApihubV1AttributeValues extends \Google\Model
{
  /**
   * Output only. The name of the attribute. Format:
   * projects/{project}/locations/{location}/attributes/{attribute}
   *
   * @var string
   */
  public $attribute;
  protected $enumValuesType = GoogleCloudApihubV1EnumAttributeValues::class;
  protected $enumValuesDataType = '';
  protected $jsonValuesType = GoogleCloudApihubV1StringAttributeValues::class;
  protected $jsonValuesDataType = '';
  protected $stringValuesType = GoogleCloudApihubV1StringAttributeValues::class;
  protected $stringValuesDataType = '';
  protected $uriValuesType = GoogleCloudApihubV1StringAttributeValues::class;
  protected $uriValuesDataType = '';

  /**
   * Output only. The name of the attribute. Format:
   * projects/{project}/locations/{location}/attributes/{attribute}
   *
   * @param string $attribute
   */
  public function setAttribute($attribute)
  {
    $this->attribute = $attribute;
  }
  /**
   * @return string
   */
  public function getAttribute()
  {
    return $this->attribute;
  }
  /**
   * The attribute values associated with a resource in case attribute data type
   * is enum.
   *
   * @param GoogleCloudApihubV1EnumAttributeValues $enumValues
   */
  public function setEnumValues(GoogleCloudApihubV1EnumAttributeValues $enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return GoogleCloudApihubV1EnumAttributeValues
   */
  public function getEnumValues()
  {
    return $this->enumValues;
  }
  /**
   * The attribute values associated with a resource in case attribute data type
   * is JSON.
   *
   * @param GoogleCloudApihubV1StringAttributeValues $jsonValues
   */
  public function setJsonValues(GoogleCloudApihubV1StringAttributeValues $jsonValues)
  {
    $this->jsonValues = $jsonValues;
  }
  /**
   * @return GoogleCloudApihubV1StringAttributeValues
   */
  public function getJsonValues()
  {
    return $this->jsonValues;
  }
  /**
   * The attribute values associated with a resource in case attribute data type
   * is string.
   *
   * @param GoogleCloudApihubV1StringAttributeValues $stringValues
   */
  public function setStringValues(GoogleCloudApihubV1StringAttributeValues $stringValues)
  {
    $this->stringValues = $stringValues;
  }
  /**
   * @return GoogleCloudApihubV1StringAttributeValues
   */
  public function getStringValues()
  {
    return $this->stringValues;
  }
  /**
   * The attribute values associated with a resource in case attribute data type
   * is URL, URI or IP, like gs://bucket-name/object-name.
   *
   * @param GoogleCloudApihubV1StringAttributeValues $uriValues
   */
  public function setUriValues(GoogleCloudApihubV1StringAttributeValues $uriValues)
  {
    $this->uriValues = $uriValues;
  }
  /**
   * @return GoogleCloudApihubV1StringAttributeValues
   */
  public function getUriValues()
  {
    return $this->uriValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1AttributeValues::class, 'Google_Service_APIhub_GoogleCloudApihubV1AttributeValues');
