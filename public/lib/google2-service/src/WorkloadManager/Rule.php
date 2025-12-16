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

namespace Google\Service\WorkloadManager;

class Rule extends \Google\Collection
{
  /**
   * Not specified.
   */
  public const RULE_TYPE_RULE_TYPE_UNSPECIFIED = 'RULE_TYPE_UNSPECIFIED';
  /**
   * Baseline rules
   */
  public const RULE_TYPE_BASELINE = 'BASELINE';
  /**
   * Custom rules
   */
  public const RULE_TYPE_CUSTOM = 'CUSTOM';
  protected $collection_key = 'tags';
  /**
   * The CAI asset type of the rule is evaluating, for joined asset types, it
   * will be the corresponding primary asset types.
   *
   * @var string
   */
  public $assetType;
  /**
   * descrite rule in plain language
   *
   * @var string
   */
  public $description;
  /**
   * the name display in UI
   *
   * @var string
   */
  public $displayName;
  /**
   * the message template for rule
   *
   * @var string
   */
  public $errorMessage;
  /**
   * rule name
   *
   * @var string
   */
  public $name;
  /**
   * the primary category
   *
   * @var string
   */
  public $primaryCategory;
  /**
   * the remediation for the rule
   *
   * @var string
   */
  public $remediation;
  /**
   * Output only. the version of the rule
   *
   * @var string
   */
  public $revisionId;
  /**
   * The type of the rule.
   *
   * @var string
   */
  public $ruleType;
  /**
   * the secondary category
   *
   * @var string
   */
  public $secondaryCategory;
  /**
   * the severity of the rule
   *
   * @var string
   */
  public $severity;
  /**
   * List of user-defined tags
   *
   * @var string[]
   */
  public $tags;
  /**
   * the docuement url for the rule
   *
   * @var string
   */
  public $uri;

  /**
   * The CAI asset type of the rule is evaluating, for joined asset types, it
   * will be the corresponding primary asset types.
   *
   * @param string $assetType
   */
  public function setAssetType($assetType)
  {
    $this->assetType = $assetType;
  }
  /**
   * @return string
   */
  public function getAssetType()
  {
    return $this->assetType;
  }
  /**
   * descrite rule in plain language
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
   * the name display in UI
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * the message template for rule
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * rule name
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
   * the primary category
   *
   * @param string $primaryCategory
   */
  public function setPrimaryCategory($primaryCategory)
  {
    $this->primaryCategory = $primaryCategory;
  }
  /**
   * @return string
   */
  public function getPrimaryCategory()
  {
    return $this->primaryCategory;
  }
  /**
   * the remediation for the rule
   *
   * @param string $remediation
   */
  public function setRemediation($remediation)
  {
    $this->remediation = $remediation;
  }
  /**
   * @return string
   */
  public function getRemediation()
  {
    return $this->remediation;
  }
  /**
   * Output only. the version of the rule
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * The type of the rule.
   *
   * Accepted values: RULE_TYPE_UNSPECIFIED, BASELINE, CUSTOM
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
   * the secondary category
   *
   * @param string $secondaryCategory
   */
  public function setSecondaryCategory($secondaryCategory)
  {
    $this->secondaryCategory = $secondaryCategory;
  }
  /**
   * @return string
   */
  public function getSecondaryCategory()
  {
    return $this->secondaryCategory;
  }
  /**
   * the severity of the rule
   *
   * @param string $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return string
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * List of user-defined tags
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * the docuement url for the rule
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Rule::class, 'Google_Service_WorkloadManager_Rule');
