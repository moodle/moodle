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

class CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule extends \Google\Collection
{
  protected $collection_key = 'childFields';
  protected $alignmentRuleType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule::class;
  protected $alignmentRuleDataType = '';
  protected $childFieldsType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField::class;
  protected $childFieldsDataType = 'array';
  protected $parentFieldType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField::class;
  protected $parentFieldDataType = '';

  /**
   * The alignment rule to apply to the child fields.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule $alignmentRule
   */
  public function setAlignmentRule(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule $alignmentRule)
  {
    $this->alignmentRule = $alignmentRule;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule
   */
  public function getAlignmentRule()
  {
    return $this->alignmentRule;
  }
  /**
   * The child fields to be aligned within the parent field.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField[] $childFields
   */
  public function setChildFields($childFields)
  {
    $this->childFields = $childFields;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField[]
   */
  public function getChildFields()
  {
    return $this->childFields;
  }
  /**
   * The full path of the parent field.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField $parentField
   */
  public function setParentField(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField $parentField)
  {
    $this->parentField = $parentField;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField
   */
  public function getParentField()
  {
    return $this->parentField;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule::class, 'Google_Service_Document_CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule');
