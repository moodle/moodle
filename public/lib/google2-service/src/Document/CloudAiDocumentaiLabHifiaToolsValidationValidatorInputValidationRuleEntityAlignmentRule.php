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

class CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule extends \Google\Collection
{
  protected $collection_key = 'fields';
  protected $alignmentRuleType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleAlignmentRule::class;
  protected $alignmentRuleDataType = '';
  protected $fieldsType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField::class;
  protected $fieldsDataType = 'array';

  /**
   * The alignment rule to apply to the fields.
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
   * The fields to be aligned.
   *
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleField[]
   */
  public function getFields()
  {
    return $this->fields;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule::class, 'Google_Service_Document_CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule');
