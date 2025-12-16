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

class DynamicRules extends \Google\Collection
{
  /**
   * The rotation type is unknown. This value is unused.
   */
  public const ROTATION_TYPE_ROTATION_TYPE_UNKNOWN = 'ROTATION_TYPE_UNKNOWN';
  /**
   * The rotation type is random. It is the default value.
   */
  public const ROTATION_TYPE_RANDOM = 'RANDOM';
  /**
   * The rotation type is optimized.
   */
  public const ROTATION_TYPE_OPTIMIZED = 'OPTIMIZED';
  /**
   * The rotation type is weighted.
   */
  public const ROTATION_TYPE_WEIGHTED = 'WEIGHTED';
  /**
   * The rule type is unknown. This value is unused.
   */
  public const RULE_TYPE_RULE_SET_TYPE_UNKNOWN = 'RULE_SET_TYPE_UNKNOWN';
  /**
   * The rule type is open, all feed rows are eligible for selection. This is
   * the default value.
   */
  public const RULE_TYPE_OPEN = 'OPEN';
  /**
   * The rule type is auto, the feed rows are eligible for selection based on
   * the automatic rules.
   */
  public const RULE_TYPE_AUTO = 'AUTO';
  /**
   * The rule type is custom, the feed rows are eligible for selection based on
   * the custom rules.
   */
  public const RULE_TYPE_CUSTOM = 'CUSTOM';
  /**
   * The rule type is proximity targeting, the feed rows are eligible for
   * selection based on the proximity targeting rules.
   */
  public const RULE_TYPE_PROXIMITY_TARGETING = 'PROXIMITY_TARGETING';
  protected $collection_key = 'remarketingValueAttributes';
  /**
   * Optional. List of field IDs in this element that should be auto-targeted.
   * Applicable when rule type is AUTO.
   *
   * @var int[]
   */
  public $autoTargetedFieldIds;
  protected $customRulesType = CustomRule::class;
  protected $customRulesDataType = 'array';
  protected $customValueFieldsType = CustomValueField::class;
  protected $customValueFieldsDataType = 'array';
  protected $proximityFilterType = ProximityFilter::class;
  protected $proximityFilterDataType = '';
  protected $remarketingValueAttributesType = RemarketingValueAttribute::class;
  protected $remarketingValueAttributesDataType = 'array';
  /**
   * Optional. The rotation type to select from eligible rows. Rotation type
   * only apply when the filtering rule results in more than one eligible rows.
   *
   * @var string
   */
  public $rotationType;
  /**
   * Optional. The type of the rule, the default value is OPEN.
   *
   * @var string
   */
  public $ruleType;
  /**
   * Optional. The field ID for the feed that will be used for weighted
   * rotation, only applicable when rotation type is WEIGHTED.
   *
   * @var int
   */
  public $weightFieldId;

  /**
   * Optional. List of field IDs in this element that should be auto-targeted.
   * Applicable when rule type is AUTO.
   *
   * @param int[] $autoTargetedFieldIds
   */
  public function setAutoTargetedFieldIds($autoTargetedFieldIds)
  {
    $this->autoTargetedFieldIds = $autoTargetedFieldIds;
  }
  /**
   * @return int[]
   */
  public function getAutoTargetedFieldIds()
  {
    return $this->autoTargetedFieldIds;
  }
  /**
   * Optional. The custom rules of the dynamic feed, only applicable when rule
   * type is CUSTOM.
   *
   * @param CustomRule[] $customRules
   */
  public function setCustomRules($customRules)
  {
    $this->customRules = $customRules;
  }
  /**
   * @return CustomRule[]
   */
  public function getCustomRules()
  {
    return $this->customRules;
  }
  /**
   * Optional. Mapping between field ID and custom key that are used to match
   * for auto filtering.
   *
   * @param CustomValueField[] $customValueFields
   */
  public function setCustomValueFields($customValueFields)
  {
    $this->customValueFields = $customValueFields;
  }
  /**
   * @return CustomValueField[]
   */
  public function getCustomValueFields()
  {
    return $this->customValueFields;
  }
  /**
   * Optional. The proximity targeting rules of the dynamic feed, only
   * applicable when rule type is PROXIMITY_TARGETING.
   *
   * @param ProximityFilter $proximityFilter
   */
  public function setProximityFilter(ProximityFilter $proximityFilter)
  {
    $this->proximityFilter = $proximityFilter;
  }
  /**
   * @return ProximityFilter
   */
  public function getProximityFilter()
  {
    return $this->proximityFilter;
  }
  /**
   * Optional. The link between an element field ID and a list of user attribute
   * IDs.
   *
   * @param RemarketingValueAttribute[] $remarketingValueAttributes
   */
  public function setRemarketingValueAttributes($remarketingValueAttributes)
  {
    $this->remarketingValueAttributes = $remarketingValueAttributes;
  }
  /**
   * @return RemarketingValueAttribute[]
   */
  public function getRemarketingValueAttributes()
  {
    return $this->remarketingValueAttributes;
  }
  /**
   * Optional. The rotation type to select from eligible rows. Rotation type
   * only apply when the filtering rule results in more than one eligible rows.
   *
   * Accepted values: ROTATION_TYPE_UNKNOWN, RANDOM, OPTIMIZED, WEIGHTED
   *
   * @param self::ROTATION_TYPE_* $rotationType
   */
  public function setRotationType($rotationType)
  {
    $this->rotationType = $rotationType;
  }
  /**
   * @return self::ROTATION_TYPE_*
   */
  public function getRotationType()
  {
    return $this->rotationType;
  }
  /**
   * Optional. The type of the rule, the default value is OPEN.
   *
   * Accepted values: RULE_SET_TYPE_UNKNOWN, OPEN, AUTO, CUSTOM,
   * PROXIMITY_TARGETING
   *
   * @param self::RULE_TYPE_* $ruleType
   */
  public function setRuleType($ruleType)
  {
    $this->ruleType = $ruleType;
  }
  /**
   * @return self::RULE_TYPE_*
   */
  public function getRuleType()
  {
    return $this->ruleType;
  }
  /**
   * Optional. The field ID for the feed that will be used for weighted
   * rotation, only applicable when rotation type is WEIGHTED.
   *
   * @param int $weightFieldId
   */
  public function setWeightFieldId($weightFieldId)
  {
    $this->weightFieldId = $weightFieldId;
  }
  /**
   * @return int
   */
  public function getWeightFieldId()
  {
    return $this->weightFieldId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DynamicRules::class, 'Google_Service_Dfareporting_DynamicRules');
