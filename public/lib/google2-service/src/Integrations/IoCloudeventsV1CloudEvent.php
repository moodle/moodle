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

namespace Google\Service\Integrations;

class IoCloudeventsV1CloudEvent extends \Google\Model
{
  protected $attributesType = IoCloudeventsV1CloudEventCloudEventAttributeValue::class;
  protected $attributesDataType = 'map';
  /**
   * @var string
   */
  public $binaryData;
  /**
   * @var string
   */
  public $id;
  /**
   * @var array[]
   */
  public $protoData;
  /**
   * @var string
   */
  public $source;
  /**
   * @var string
   */
  public $specVersion;
  /**
   * @var string
   */
  public $textData;
  /**
   * @var string
   */
  public $type;

  /**
   * @param IoCloudeventsV1CloudEventCloudEventAttributeValue[]
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return IoCloudeventsV1CloudEventCloudEventAttributeValue[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * @param string
   */
  public function setBinaryData($binaryData)
  {
    $this->binaryData = $binaryData;
  }
  /**
   * @return string
   */
  public function getBinaryData()
  {
    return $this->binaryData;
  }
  /**
   * @param string
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
   * @param array[]
   */
  public function setProtoData($protoData)
  {
    $this->protoData = $protoData;
  }
  /**
   * @return array[]
   */
  public function getProtoData()
  {
    return $this->protoData;
  }
  /**
   * @param string
   */
  public function setSource($source)
  {
    $this->source = $source;
  }
  /**
   * @return string
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * @param string
   */
  public function setSpecVersion($specVersion)
  {
    $this->specVersion = $specVersion;
  }
  /**
   * @return string
   */
  public function getSpecVersion()
  {
    return $this->specVersion;
  }
  /**
   * @param string
   */
  public function setTextData($textData)
  {
    $this->textData = $textData;
  }
  /**
   * @return string
   */
  public function getTextData()
  {
    return $this->textData;
  }
  /**
   * @param string
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IoCloudeventsV1CloudEvent::class, 'Google_Service_Integrations_IoCloudeventsV1CloudEvent');
