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

namespace Google\Service\ServiceManagement;

class ConfigChange extends \Google\Collection
{
  /**
   * No value was provided.
   */
  public const CHANGE_TYPE_CHANGE_TYPE_UNSPECIFIED = 'CHANGE_TYPE_UNSPECIFIED';
  /**
   * The changed object exists in the 'new' service configuration, but not in
   * the 'old' service configuration.
   */
  public const CHANGE_TYPE_ADDED = 'ADDED';
  /**
   * The changed object exists in the 'old' service configuration, but not in
   * the 'new' service configuration.
   */
  public const CHANGE_TYPE_REMOVED = 'REMOVED';
  /**
   * The changed object exists in both service configurations, but its value is
   * different.
   */
  public const CHANGE_TYPE_MODIFIED = 'MODIFIED';
  protected $collection_key = 'advices';
  protected $advicesType = Advice::class;
  protected $advicesDataType = 'array';
  /**
   * The type for this change, either ADDED, REMOVED, or MODIFIED.
   *
   * @var string
   */
  public $changeType;
  /**
   * Object hierarchy path to the change, with levels separated by a '.'
   * character. For repeated fields, an applicable unique identifier field is
   * used for the index (usually selector, name, or id). For maps, the term
   * 'key' is used. If the field has no unique identifier, the numeric index is
   * used. Examples: -
   * visibility.rules[selector=="google.LibraryService.ListBooks"].restriction -
   * quota.metric_rules[selector=="google"].metric_costs[key=="reads"].value -
   * logging.producer_destinations[0]
   *
   * @var string
   */
  public $element;
  /**
   * Value of the changed object in the new Service configuration, in JSON
   * format. This field will not be populated if ChangeType == REMOVED.
   *
   * @var string
   */
  public $newValue;
  /**
   * Value of the changed object in the old Service configuration, in JSON
   * format. This field will not be populated if ChangeType == ADDED.
   *
   * @var string
   */
  public $oldValue;

  /**
   * Collection of advice provided for this change, useful for determining the
   * possible impact of this change.
   *
   * @param Advice[] $advices
   */
  public function setAdvices($advices)
  {
    $this->advices = $advices;
  }
  /**
   * @return Advice[]
   */
  public function getAdvices()
  {
    return $this->advices;
  }
  /**
   * The type for this change, either ADDED, REMOVED, or MODIFIED.
   *
   * Accepted values: CHANGE_TYPE_UNSPECIFIED, ADDED, REMOVED, MODIFIED
   *
   * @param self::CHANGE_TYPE_* $changeType
   */
  public function setChangeType($changeType)
  {
    $this->changeType = $changeType;
  }
  /**
   * @return self::CHANGE_TYPE_*
   */
  public function getChangeType()
  {
    return $this->changeType;
  }
  /**
   * Object hierarchy path to the change, with levels separated by a '.'
   * character. For repeated fields, an applicable unique identifier field is
   * used for the index (usually selector, name, or id). For maps, the term
   * 'key' is used. If the field has no unique identifier, the numeric index is
   * used. Examples: -
   * visibility.rules[selector=="google.LibraryService.ListBooks"].restriction -
   * quota.metric_rules[selector=="google"].metric_costs[key=="reads"].value -
   * logging.producer_destinations[0]
   *
   * @param string $element
   */
  public function setElement($element)
  {
    $this->element = $element;
  }
  /**
   * @return string
   */
  public function getElement()
  {
    return $this->element;
  }
  /**
   * Value of the changed object in the new Service configuration, in JSON
   * format. This field will not be populated if ChangeType == REMOVED.
   *
   * @param string $newValue
   */
  public function setNewValue($newValue)
  {
    $this->newValue = $newValue;
  }
  /**
   * @return string
   */
  public function getNewValue()
  {
    return $this->newValue;
  }
  /**
   * Value of the changed object in the old Service configuration, in JSON
   * format. This field will not be populated if ChangeType == ADDED.
   *
   * @param string $oldValue
   */
  public function setOldValue($oldValue)
  {
    $this->oldValue = $oldValue;
  }
  /**
   * @return string
   */
  public function getOldValue()
  {
    return $this->oldValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConfigChange::class, 'Google_Service_ServiceManagement_ConfigChange');
