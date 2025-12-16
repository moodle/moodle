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

class CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRule extends \Google\Model
{
  protected $childAlignmentRuleType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule::class;
  protected $childAlignmentRuleDataType = '';
  /**
   * Description of the validation rule. This has no use but for documentation
   *
   * @var string
   */
  public $description;
  protected $entityAlignmentRuleType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule::class;
  protected $entityAlignmentRuleDataType = '';
  protected $fieldOccurrencesType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldOccurrences::class;
  protected $fieldOccurrencesDataType = '';
  protected $fieldRegexType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldRegex::class;
  protected $fieldRegexDataType = '';
  protected $formValidationType = CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation::class;
  protected $formValidationDataType = '';
  /**
   * Name of the validation rule.
   *
   * @var string
   */
  public $name;
  /**
   * Unique identifier of the rule. Optional.
   *
   * @var string
   */
  public $ruleId;

  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule $childAlignmentRule
   */
  public function setChildAlignmentRule(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule $childAlignmentRule)
  {
    $this->childAlignmentRule = $childAlignmentRule;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleChildAlignmentRule
   */
  public function getChildAlignmentRule()
  {
    return $this->childAlignmentRule;
  }
  /**
   * Description of the validation rule. This has no use but for documentation
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule $entityAlignmentRule
   */
  public function setEntityAlignmentRule(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule $entityAlignmentRule)
  {
    $this->entityAlignmentRule = $entityAlignmentRule;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleEntityAlignmentRule
   */
  public function getEntityAlignmentRule()
  {
    return $this->entityAlignmentRule;
  }
  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldOccurrences $fieldOccurrences
   */
  public function setFieldOccurrences(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldOccurrences $fieldOccurrences)
  {
    $this->fieldOccurrences = $fieldOccurrences;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldOccurrences
   */
  public function getFieldOccurrences()
  {
    return $this->fieldOccurrences;
  }
  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldRegex $fieldRegex
   */
  public function setFieldRegex(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldRegex $fieldRegex)
  {
    $this->fieldRegex = $fieldRegex;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFieldRegex
   */
  public function getFieldRegex()
  {
    return $this->fieldRegex;
  }
  /**
   * @param CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation $formValidation
   */
  public function setFormValidation(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation $formValidation)
  {
    $this->formValidation = $formValidation;
  }
  /**
   * @return CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRuleFormValidation
   */
  public function getFormValidation()
  {
    return $this->formValidation;
  }
  /**
   * Name of the validation rule.
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
   * Unique identifier of the rule. Optional.
   *
   * @param string $ruleId
   */
  public function setRuleId($ruleId)
  {
    $this->ruleId = $ruleId;
  }
  /**
   * @return string
   */
  public function getRuleId()
  {
    return $this->ruleId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRule::class, 'Google_Service_Document_CloudAiDocumentaiLabHifiaToolsValidationValidatorInputValidationRule');
