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

class ConversionStatus extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $conversionType = Conversion::class;
  protected $conversionDataType = '';
  protected $errorsType = ConversionError::class;
  protected $errorsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#conversionStatus".
   *
   * @var string
   */
  public $kind;

  /**
   * The original conversion that was inserted or updated.
   *
   * @param Conversion $conversion
   */
  public function setConversion(Conversion $conversion)
  {
    $this->conversion = $conversion;
  }
  /**
   * @return Conversion
   */
  public function getConversion()
  {
    return $this->conversion;
  }
  /**
   * A list of errors related to this conversion.
   *
   * @param ConversionError[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return ConversionError[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#conversionStatus".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConversionStatus::class, 'Google_Service_Dfareporting_ConversionStatus');
