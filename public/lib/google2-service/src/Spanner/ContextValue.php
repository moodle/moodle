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

namespace Google\Service\Spanner;

class ContextValue extends \Google\Model
{
  /**
   * Required default value.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Lowest severity level "Info".
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * Middle severity level "Warning".
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * Severity level signaling an error "Error"
   */
  public const SEVERITY_ERROR = 'ERROR';
  /**
   * Severity level signaling a non recoverable error "Fatal"
   */
  public const SEVERITY_FATAL = 'FATAL';
  protected $labelType = LocalizedString::class;
  protected $labelDataType = '';
  /**
   * The severity of this context.
   *
   * @var string
   */
  public $severity;
  /**
   * The unit of the context value.
   *
   * @var string
   */
  public $unit;
  /**
   * The value for the context.
   *
   * @var float
   */
  public $value;

  /**
   * The label for the context value. e.g. "latency".
   *
   * @param LocalizedString $label
   */
  public function setLabel(LocalizedString $label)
  {
    $this->label = $label;
  }
  /**
   * @return LocalizedString
   */
  public function getLabel()
  {
    return $this->label;
  }
  /**
   * The severity of this context.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFO, WARNING, ERROR, FATAL
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The unit of the context value.
   *
   * @param string $unit
   */
  public function setUnit($unit)
  {
    $this->unit = $unit;
  }
  /**
   * @return string
   */
  public function getUnit()
  {
    return $this->unit;
  }
  /**
   * The value for the context.
   *
   * @param float $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return float
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContextValue::class, 'Google_Service_Spanner_ContextValue');
