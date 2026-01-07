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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentEntityNormalizedValue extends \Google\Model
{
  protected $addressValueType = GoogleTypePostalAddress::class;
  protected $addressValueDataType = '';
  /**
   * Boolean value. Can be used for entities with binary values, or for
   * checkboxes.
   *
   * @var bool
   */
  public $booleanValue;
  protected $dateValueType = GoogleTypeDate::class;
  protected $dateValueDataType = '';
  protected $datetimeValueType = GoogleTypeDateTime::class;
  protected $datetimeValueDataType = '';
  /**
   * Float value.
   *
   * @var float
   */
  public $floatValue;
  /**
   * Integer value.
   *
   * @var int
   */
  public $integerValue;
  protected $moneyValueType = GoogleTypeMoney::class;
  protected $moneyValueDataType = '';
  /**
   * A signature - a graphical representation of a person's name, often used to
   * sign a document.
   *
   * @var bool
   */
  public $signatureValue;
  /**
   * Optional. An optional field to store a normalized string. For some entity
   * types, one of respective `structured_value` fields may also be populated.
   * Also not all the types of `structured_value` will be normalized. For
   * example, some processors may not generate `float` or `integer` normalized
   * text by default. Below are sample formats mapped to structured values. -
   * Money/Currency type (`money_value`) is in the ISO 4217 text format. - Date
   * type (`date_value`) is in the ISO 8601 text format. - Datetime type
   * (`datetime_value`) is in the ISO 8601 text format.
   *
   * @var string
   */
  public $text;

  /**
   * Postal address. See also: https://github.com/googleapis/googleapis/blob/mas
   * ter/google/type/postal_address.proto
   *
   * @param GoogleTypePostalAddress $addressValue
   */
  public function setAddressValue(GoogleTypePostalAddress $addressValue)
  {
    $this->addressValue = $addressValue;
  }
  /**
   * @return GoogleTypePostalAddress
   */
  public function getAddressValue()
  {
    return $this->addressValue;
  }
  /**
   * Boolean value. Can be used for entities with binary values, or for
   * checkboxes.
   *
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
   * Date value. Includes year, month, day. See also:
   * https://github.com/googleapis/googleapis/blob/master/google/type/date.proto
   *
   * @param GoogleTypeDate $dateValue
   */
  public function setDateValue(GoogleTypeDate $dateValue)
  {
    $this->dateValue = $dateValue;
  }
  /**
   * @return GoogleTypeDate
   */
  public function getDateValue()
  {
    return $this->dateValue;
  }
  /**
   * DateTime value. Includes date, time, and timezone. See also: https://github
   * .com/googleapis/googleapis/blob/master/google/type/datetime.proto
   *
   * @param GoogleTypeDateTime $datetimeValue
   */
  public function setDatetimeValue(GoogleTypeDateTime $datetimeValue)
  {
    $this->datetimeValue = $datetimeValue;
  }
  /**
   * @return GoogleTypeDateTime
   */
  public function getDatetimeValue()
  {
    return $this->datetimeValue;
  }
  /**
   * Float value.
   *
   * @param float $floatValue
   */
  public function setFloatValue($floatValue)
  {
    $this->floatValue = $floatValue;
  }
  /**
   * @return float
   */
  public function getFloatValue()
  {
    return $this->floatValue;
  }
  /**
   * Integer value.
   *
   * @param int $integerValue
   */
  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }
  /**
   * @return int
   */
  public function getIntegerValue()
  {
    return $this->integerValue;
  }
  /**
   * Money value. See also: https://github.com/googleapis/googleapis/blob/master
   * /google/type/money.proto
   *
   * @param GoogleTypeMoney $moneyValue
   */
  public function setMoneyValue(GoogleTypeMoney $moneyValue)
  {
    $this->moneyValue = $moneyValue;
  }
  /**
   * @return GoogleTypeMoney
   */
  public function getMoneyValue()
  {
    return $this->moneyValue;
  }
  /**
   * A signature - a graphical representation of a person's name, often used to
   * sign a document.
   *
   * @param bool $signatureValue
   */
  public function setSignatureValue($signatureValue)
  {
    $this->signatureValue = $signatureValue;
  }
  /**
   * @return bool
   */
  public function getSignatureValue()
  {
    return $this->signatureValue;
  }
  /**
   * Optional. An optional field to store a normalized string. For some entity
   * types, one of respective `structured_value` fields may also be populated.
   * Also not all the types of `structured_value` will be normalized. For
   * example, some processors may not generate `float` or `integer` normalized
   * text by default. Below are sample formats mapped to structured values. -
   * Money/Currency type (`money_value`) is in the ISO 4217 text format. - Date
   * type (`date_value`) is in the ISO 8601 text format. - Datetime type
   * (`datetime_value`) is in the ISO 8601 text format.
   *
   * @param string $text
   */
  public function setText($text)
  {
    $this->text = $text;
  }
  /**
   * @return string
   */
  public function getText()
  {
    return $this->text;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentEntityNormalizedValue::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentEntityNormalizedValue');
