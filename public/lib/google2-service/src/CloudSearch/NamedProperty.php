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

namespace Google\Service\CloudSearch;

class NamedProperty extends \Google\Model
{
  /**
   * @var bool
   */
  public $booleanValue;
  protected $dateValuesType = DateValues::class;
  protected $dateValuesDataType = '';
  protected $doubleValuesType = DoubleValues::class;
  protected $doubleValuesDataType = '';
  protected $enumValuesType = EnumValues::class;
  protected $enumValuesDataType = '';
  protected $htmlValuesType = HtmlValues::class;
  protected $htmlValuesDataType = '';
  protected $integerValuesType = IntegerValues::class;
  protected $integerValuesDataType = '';
  /**
   * The name of the property. This name should correspond to the name of the
   * property that was registered for object definition in the schema. The
   * maximum allowable length for this property is 256 characters.
   *
   * @var string
   */
  public $name;
  protected $objectValuesType = ObjectValues::class;
  protected $objectValuesDataType = '';
  protected $textValuesType = TextValues::class;
  protected $textValuesDataType = '';
  protected $timestampValuesType = TimestampValues::class;
  protected $timestampValuesDataType = '';

  /**
   * @param bool $booleanValue
   */
  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }
  /**
   * @return bool
   */
  public function getBooleanValue()
  {
    return $this->booleanValue;
  }
  /**
   * @param DateValues $dateValues
   */
  public function setDateValues(DateValues $dateValues)
  {
    $this->dateValues = $dateValues;
  }
  /**
   * @return DateValues
   */
  public function getDateValues()
  {
    return $this->dateValues;
  }
  /**
   * @param DoubleValues $doubleValues
   */
  public function setDoubleValues(DoubleValues $doubleValues)
  {
    $this->doubleValues = $doubleValues;
  }
  /**
   * @return DoubleValues
   */
  public function getDoubleValues()
  {
    return $this->doubleValues;
  }
  /**
   * @param EnumValues $enumValues
   */
  public function setEnumValues(EnumValues $enumValues)
  {
    $this->enumValues = $enumValues;
  }
  /**
   * @return EnumValues
   */
  public function getEnumValues()
  {
    return $this->enumValues;
  }
  /**
   * @param HtmlValues $htmlValues
   */
  public function setHtmlValues(HtmlValues $htmlValues)
  {
    $this->htmlValues = $htmlValues;
  }
  /**
   * @return HtmlValues
   */
  public function getHtmlValues()
  {
    return $this->htmlValues;
  }
  /**
   * @param IntegerValues $integerValues
   */
  public function setIntegerValues(IntegerValues $integerValues)
  {
    $this->integerValues = $integerValues;
  }
  /**
   * @return IntegerValues
   */
  public function getIntegerValues()
  {
    return $this->integerValues;
  }
  /**
   * The name of the property. This name should correspond to the name of the
   * property that was registered for object definition in the schema. The
   * maximum allowable length for this property is 256 characters.
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
   * @param ObjectValues $objectValues
   */
  public function setObjectValues(ObjectValues $objectValues)
  {
    $this->objectValues = $objectValues;
  }
  /**
   * @return ObjectValues
   */
  public function getObjectValues()
  {
    return $this->objectValues;
  }
  /**
   * @param TextValues $textValues
   */
  public function setTextValues(TextValues $textValues)
  {
    $this->textValues = $textValues;
  }
  /**
   * @return TextValues
   */
  public function getTextValues()
  {
    return $this->textValues;
  }
  /**
   * @param TimestampValues $timestampValues
   */
  public function setTimestampValues(TimestampValues $timestampValues)
  {
    $this->timestampValues = $timestampValues;
  }
  /**
   * @return TimestampValues
   */
  public function getTimestampValues()
  {
    return $this->timestampValues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NamedProperty::class, 'Google_Service_CloudSearch_NamedProperty');
