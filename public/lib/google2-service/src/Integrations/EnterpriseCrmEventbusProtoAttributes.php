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

class EnterpriseCrmEventbusProtoAttributes extends \Google\Collection
{
  public const DATA_TYPE_DATA_TYPE_UNSPECIFIED = 'DATA_TYPE_UNSPECIFIED';
  public const DATA_TYPE_EMAIL = 'EMAIL';
  public const DATA_TYPE_URL = 'URL';
  public const DATA_TYPE_CURRENCY = 'CURRENCY';
  public const DATA_TYPE_TIMESTAMP = 'TIMESTAMP';
  /**
   * Domain is a web url string with one top-level private domain and a suffix
   * (for example: google.com, walmart.com)
   */
  public const DATA_TYPE_DOMAIN_NAME = 'DOMAIN_NAME';
  public const SEARCHABLE_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * If yes, the parameter key and value will be full-text indexed. In a proto,
   * this value will propagate to all children whose searchable is unspecified.
   */
  public const SEARCHABLE_YES = 'YES';
  /**
   * If no, the parameter key and value will not be full-text indexed. In a
   * proto, this value will propagate to all children whose searchable is
   * unspecified.
   */
  public const SEARCHABLE_NO = 'NO';
  protected $collection_key = 'taskVisibility';
  /**
   * Things like URL, Email, Currency, Timestamp (rather than string, int64...)
   *
   * @var string
   */
  public $dataType;
  protected $defaultValueType = EnterpriseCrmEventbusProtoValueType::class;
  protected $defaultValueDataType = '';
  /**
   * Required for event execution. The validation will be done by the event bus
   * when the event is triggered.
   *
   * @var bool
   */
  public $isRequired;
  /**
   * Used to indicate if a ParameterEntry should be converted to ParamIndexes
   * for ST-Spanner full-text search. DEPRECATED: use searchable.
   *
   * @deprecated
   * @var bool
   */
  public $isSearchable;
  protected $logSettingsType = EnterpriseCrmEventbusProtoLogSettings::class;
  protected $logSettingsDataType = '';
  /**
   * True if this workflow parameter should be masked in the logs
   *
   * @var bool
   */
  public $masked;
  /**
   * Used to indicate if the ParameterEntry is a read only field or not.
   *
   * @var bool
   */
  public $readOnly;
  /**
   * @var string
   */
  public $searchable;
  /**
   * List of tasks that can view this property, if empty then all.
   *
   * @var string[]
   */
  public $taskVisibility;

  /**
   * Things like URL, Email, Currency, Timestamp (rather than string, int64...)
   *
   * Accepted values: DATA_TYPE_UNSPECIFIED, EMAIL, URL, CURRENCY, TIMESTAMP,
   * DOMAIN_NAME
   *
   * @param self::DATA_TYPE_* $dataType
   */
  public function setDataType($dataType)
  {
    $this->dataType = $dataType;
  }
  /**
   * @return self::DATA_TYPE_*
   */
  public function getDataType()
  {
    return $this->dataType;
  }
  /**
   * Used to define defaults.
   *
   * @param EnterpriseCrmEventbusProtoValueType $defaultValue
   */
  public function setDefaultValue(EnterpriseCrmEventbusProtoValueType $defaultValue)
  {
    $this->defaultValue = $defaultValue;
  }
  /**
   * @return EnterpriseCrmEventbusProtoValueType
   */
  public function getDefaultValue()
  {
    return $this->defaultValue;
  }
  /**
   * Required for event execution. The validation will be done by the event bus
   * when the event is triggered.
   *
   * @param bool $isRequired
   */
  public function setIsRequired($isRequired)
  {
    $this->isRequired = $isRequired;
  }
  /**
   * @return bool
   */
  public function getIsRequired()
  {
    return $this->isRequired;
  }
  /**
   * Used to indicate if a ParameterEntry should be converted to ParamIndexes
   * for ST-Spanner full-text search. DEPRECATED: use searchable.
   *
   * @deprecated
   * @param bool $isSearchable
   */
  public function setIsSearchable($isSearchable)
  {
    $this->isSearchable = $isSearchable;
  }
  /**
   * @deprecated
   * @return bool
   */
  public function getIsSearchable()
  {
    return $this->isSearchable;
  }
  /**
   * See
   *
   * @param EnterpriseCrmEventbusProtoLogSettings $logSettings
   */
  public function setLogSettings(EnterpriseCrmEventbusProtoLogSettings $logSettings)
  {
    $this->logSettings = $logSettings;
  }
  /**
   * @return EnterpriseCrmEventbusProtoLogSettings
   */
  public function getLogSettings()
  {
    return $this->logSettings;
  }
  /**
   * True if this workflow parameter should be masked in the logs
   *
   * @param bool $masked
   */
  public function setMasked($masked)
  {
    $this->masked = $masked;
  }
  /**
   * @return bool
   */
  public function getMasked()
  {
    return $this->masked;
  }
  /**
   * Used to indicate if the ParameterEntry is a read only field or not.
   *
   * @param bool $readOnly
   */
  public function setReadOnly($readOnly)
  {
    $this->readOnly = $readOnly;
  }
  /**
   * @return bool
   */
  public function getReadOnly()
  {
    return $this->readOnly;
  }
  /**
   * @param self::SEARCHABLE_* $searchable
   */
  public function setSearchable($searchable)
  {
    $this->searchable = $searchable;
  }
  /**
   * @return self::SEARCHABLE_*
   */
  public function getSearchable()
  {
    return $this->searchable;
  }
  /**
   * List of tasks that can view this property, if empty then all.
   *
   * @param string[] $taskVisibility
   */
  public function setTaskVisibility($taskVisibility)
  {
    $this->taskVisibility = $taskVisibility;
  }
  /**
   * @return string[]
   */
  public function getTaskVisibility()
  {
    return $this->taskVisibility;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoAttributes::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoAttributes');
