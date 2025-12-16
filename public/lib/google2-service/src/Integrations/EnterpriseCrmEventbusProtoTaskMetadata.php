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

class EnterpriseCrmEventbusProtoTaskMetadata extends \Google\Collection
{
  public const CATEGORY_UNSPECIFIED_CATEGORY = 'UNSPECIFIED_CATEGORY';
  public const CATEGORY_CUSTOM = 'CUSTOM';
  public const CATEGORY_FLOW_CONTROL = 'FLOW_CONTROL';
  public const CATEGORY_DATA_MANIPULATION = 'DATA_MANIPULATION';
  public const CATEGORY_SCRIPTING = 'SCRIPTING';
  public const CATEGORY_CONNECTOR = 'CONNECTOR';
  /**
   * Internal IP tasks that should not be available in the UI.
   */
  public const CATEGORY_HIDDEN = 'HIDDEN';
  /**
   * Tasks that are relevant to cloud systems teams and typically
   */
  public const CATEGORY_CLOUD_SYSTEMS = 'CLOUD_SYSTEMS';
  /**
   * Task entities that derive from a custom task template.
   */
  public const CATEGORY_CUSTOM_TASK_TEMPLATE = 'CUSTOM_TASK_TEMPLATE';
  /**
   * Category to show task recommendations
   */
  public const CATEGORY_TASK_RECOMMENDATIONS = 'TASK_RECOMMENDATIONS';
  /**
   * As per the default behavior, no validation will be run. Will not override
   * any option set in a Task.
   */
  public const DEFAULT_JSON_VALIDATION_OPTION_UNSPECIFIED_JSON_VALIDATION_OPTION = 'UNSPECIFIED_JSON_VALIDATION_OPTION';
  /**
   * Do not run any validation against JSON schemas.
   */
  public const DEFAULT_JSON_VALIDATION_OPTION_SKIP = 'SKIP';
  /**
   * Validate all potential input JSON parameters against schemas specified in
   * WorkflowParameters.
   */
  public const DEFAULT_JSON_VALIDATION_OPTION_PRE_EXECUTION = 'PRE_EXECUTION';
  /**
   * Validate all potential output JSON parameters against schemas specified in
   * WorkflowParameters.
   */
  public const DEFAULT_JSON_VALIDATION_OPTION_POST_EXECUTION = 'POST_EXECUTION';
  /**
   * Perform both PRE_EXECUTION and POST_EXECUTION validations.
   */
  public const DEFAULT_JSON_VALIDATION_OPTION_PRE_POST_EXECUTION = 'PRE_POST_EXECUTION';
  public const EXTERNAL_CATEGORY_UNSPECIFIED_EXTERNAL_CATEGORY = 'UNSPECIFIED_EXTERNAL_CATEGORY';
  public const EXTERNAL_CATEGORY_CORE = 'CORE';
  public const EXTERNAL_CATEGORY_CONNECTORS = 'CONNECTORS';
  /**
   * HTTP tasks, e.g. rest api call task
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_HTTP = 'EXTERNAL_HTTP';
  /**
   * Integration services, e.g. connector task
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_INTEGRATION_SERVICES = 'EXTERNAL_INTEGRATION_SERVICES';
  /**
   * Customer ations, e.g. email task
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_CUSTOMER_ACTIONS = 'EXTERNAL_CUSTOMER_ACTIONS';
  /**
   * Flow control, e.g. while loop task
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_FLOW_CONTROL = 'EXTERNAL_FLOW_CONTROL';
  /**
   * Workspace tasks, e.g. list drive task
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_WORKSPACE = 'EXTERNAL_WORKSPACE';
  /**
   * Security, e.g. kms related tasks
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_SECURITY = 'EXTERNAL_SECURITY';
  /**
   * Database operation tasks, e.g. read firestore info tasks
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_DATABASES = 'EXTERNAL_DATABASES';
  /**
   * Analytics tasks, e.g. dataflow creattion tasks
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_ANALYTICS = 'EXTERNAL_ANALYTICS';
  /**
   * BYOC tasks
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_BYOC = 'EXTERNAL_BYOC';
  /**
   * BYOT tasks
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_BYOT = 'EXTERNAL_BYOT';
  /**
   * AI related tasks.
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_ARTIFICIAL_INTELIGENCE = 'EXTERNAL_ARTIFICIAL_INTELIGENCE';
  /**
   * Data manipulation related tasks, e.g. data mapping task
   */
  public const EXTERNAL_CATEGORY_EXTERNAL_DATA_MANIPULATION = 'EXTERNAL_DATA_MANIPULATION';
  /**
   * Default value. Actual Task Status should always be set to either INACTIVE
   * or ACTIVE. If none is specified at runtime, it will be set to INACTIVE.
   */
  public const STATUS_UNSPECIFIED_STATUS = 'UNSPECIFIED_STATUS';
  /**
   * Still in-progress or incomplete, and not intended for use.
   */
  public const STATUS_DEFAULT_INACTIVE = 'DEFAULT_INACTIVE';
  /**
   * Available for use.
   */
  public const STATUS_ACTIVE = 'ACTIVE';
  public const SYSTEM_UNSPECIFIED_SYSTEM = 'UNSPECIFIED_SYSTEM';
  public const SYSTEM_GENERIC = 'GENERIC';
  public const SYSTEM_BUGANIZER = 'BUGANIZER';
  public const SYSTEM_SALESFORCE = 'SALESFORCE';
  public const SYSTEM_CLOUD_SQL = 'CLOUD_SQL';
  public const SYSTEM_PLX = 'PLX';
  public const SYSTEM_SHEETS = 'SHEETS';
  public const SYSTEM_GOOGLE_GROUPS = 'GOOGLE_GROUPS';
  public const SYSTEM_EMAIL = 'EMAIL';
  public const SYSTEM_SPANNER = 'SPANNER';
  public const SYSTEM_DATA_BRIDGE = 'DATA_BRIDGE';
  protected $collection_key = 'tags';
  /**
   * The new task name to replace the current task if it is deprecated.
   * Otherwise, it is the same as the current task name.
   *
   * @var string
   */
  public $activeTaskName;
  protected $adminsType = EnterpriseCrmEventbusProtoTaskMetadataAdmin::class;
  protected $adminsDataType = 'array';
  /**
   * @var string
   */
  public $category;
  /**
   * The Code Search link to the Task Java file.
   *
   * @var string
   */
  public $codeSearchLink;
  /**
   * Controls whether JSON workflow parameters are validated against provided
   * schemas before and/or after this task's execution.
   *
   * @var string
   */
  public $defaultJsonValidationOption;
  /**
   * Contains the initial configuration of the task with default values set. For
   * now, The string should be compatible to an ASCII-proto format.
   *
   * @var string
   */
  public $defaultSpec;
  /**
   * In a few sentences, describe the purpose and usage of the task.
   *
   * @var string
   */
  public $description;
  /**
   * The string name to show on the task list on the Workflow editor screen.
   * This should be a very short, one to two words name for the task. (e.g.
   * "Send Mail")
   *
   * @var string
   */
  public $descriptiveName;
  /**
   * Snippet of markdown documentation to embed in the RHP for this task.
   *
   * @var string
   */
  public $docMarkdown;
  /**
   * @var string
   */
  public $externalCategory;
  /**
   * Sequence with which the task in specific category to be displayed in task
   * discovery panel for external users.
   *
   * @var int
   */
  public $externalCategorySequence;
  /**
   * External-facing documention embedded in the RHP for this task.
   *
   * @var string
   */
  public $externalDocHtml;
  /**
   * Doc link for external-facing documentation (separate from g3doc).
   *
   * @var string
   */
  public $externalDocLink;
  /**
   * DEPRECATED: Use external_doc_html.
   *
   * @var string
   */
  public $externalDocMarkdown;
  /**
   * URL to the associated G3 Doc for the task if available
   *
   * @var string
   */
  public $g3DocLink;
  /**
   * URL to gstatic image icon for this task. This icon shows up on the task
   * list panel along with the task name in the Workflow Editor screen. Use the
   * 24p, 2x, gray color icon image format.
   *
   * @var string
   */
  public $iconLink;
  /**
   * The deprecation status of the current task. Default value is false;
   *
   * @var bool
   */
  public $isDeprecated;
  /**
   * The actual class name or the annotated name of the task. Task Author should
   * initialize this field with value from the getName() method of the Task
   * class.
   *
   * @var string
   */
  public $name;
  /**
   * External-facing documention for standalone IP in pantheon embedded in the
   * RHP for this task. Non null only if different from external_doc_html
   *
   * @var string
   */
  public $standaloneExternalDocHtml;
  /**
   * Allows author to indicate if the task is ready to use or not. If not set,
   * then it will default to INACTIVE.
   *
   * @var string
   */
  public $status;
  /**
   * @var string
   */
  public $system;
  /**
   * A set of tags that pertain to a particular task. This can be used to
   * improve the searchability of tasks with several names ("REST Caller" vs.
   * "Call REST Endpoint") or to help users find tasks based on related words.
   *
   * @var string[]
   */
  public $tags;

  /**
   * The new task name to replace the current task if it is deprecated.
   * Otherwise, it is the same as the current task name.
   *
   * @param string $activeTaskName
   */
  public function setActiveTaskName($activeTaskName)
  {
    $this->activeTaskName = $activeTaskName;
  }
  /**
   * @return string
   */
  public function getActiveTaskName()
  {
    return $this->activeTaskName;
  }
  /**
   * @param EnterpriseCrmEventbusProtoTaskMetadataAdmin[] $admins
   */
  public function setAdmins($admins)
  {
    $this->admins = $admins;
  }
  /**
   * @return EnterpriseCrmEventbusProtoTaskMetadataAdmin[]
   */
  public function getAdmins()
  {
    return $this->admins;
  }
  /**
   * @param self::CATEGORY_* $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return self::CATEGORY_*
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The Code Search link to the Task Java file.
   *
   * @param string $codeSearchLink
   */
  public function setCodeSearchLink($codeSearchLink)
  {
    $this->codeSearchLink = $codeSearchLink;
  }
  /**
   * @return string
   */
  public function getCodeSearchLink()
  {
    return $this->codeSearchLink;
  }
  /**
   * Controls whether JSON workflow parameters are validated against provided
   * schemas before and/or after this task's execution.
   *
   * Accepted values: UNSPECIFIED_JSON_VALIDATION_OPTION, SKIP, PRE_EXECUTION,
   * POST_EXECUTION, PRE_POST_EXECUTION
   *
   * @param self::DEFAULT_JSON_VALIDATION_OPTION_* $defaultJsonValidationOption
   */
  public function setDefaultJsonValidationOption($defaultJsonValidationOption)
  {
    $this->defaultJsonValidationOption = $defaultJsonValidationOption;
  }
  /**
   * @return self::DEFAULT_JSON_VALIDATION_OPTION_*
   */
  public function getDefaultJsonValidationOption()
  {
    return $this->defaultJsonValidationOption;
  }
  /**
   * Contains the initial configuration of the task with default values set. For
   * now, The string should be compatible to an ASCII-proto format.
   *
   * @param string $defaultSpec
   */
  public function setDefaultSpec($defaultSpec)
  {
    $this->defaultSpec = $defaultSpec;
  }
  /**
   * @return string
   */
  public function getDefaultSpec()
  {
    return $this->defaultSpec;
  }
  /**
   * In a few sentences, describe the purpose and usage of the task.
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
   * The string name to show on the task list on the Workflow editor screen.
   * This should be a very short, one to two words name for the task. (e.g.
   * "Send Mail")
   *
   * @param string $descriptiveName
   */
  public function setDescriptiveName($descriptiveName)
  {
    $this->descriptiveName = $descriptiveName;
  }
  /**
   * @return string
   */
  public function getDescriptiveName()
  {
    return $this->descriptiveName;
  }
  /**
   * Snippet of markdown documentation to embed in the RHP for this task.
   *
   * @param string $docMarkdown
   */
  public function setDocMarkdown($docMarkdown)
  {
    $this->docMarkdown = $docMarkdown;
  }
  /**
   * @return string
   */
  public function getDocMarkdown()
  {
    return $this->docMarkdown;
  }
  /**
   * @param self::EXTERNAL_CATEGORY_* $externalCategory
   */
  public function setExternalCategory($externalCategory)
  {
    $this->externalCategory = $externalCategory;
  }
  /**
   * @return self::EXTERNAL_CATEGORY_*
   */
  public function getExternalCategory()
  {
    return $this->externalCategory;
  }
  /**
   * Sequence with which the task in specific category to be displayed in task
   * discovery panel for external users.
   *
   * @param int $externalCategorySequence
   */
  public function setExternalCategorySequence($externalCategorySequence)
  {
    $this->externalCategorySequence = $externalCategorySequence;
  }
  /**
   * @return int
   */
  public function getExternalCategorySequence()
  {
    return $this->externalCategorySequence;
  }
  /**
   * External-facing documention embedded in the RHP for this task.
   *
   * @param string $externalDocHtml
   */
  public function setExternalDocHtml($externalDocHtml)
  {
    $this->externalDocHtml = $externalDocHtml;
  }
  /**
   * @return string
   */
  public function getExternalDocHtml()
  {
    return $this->externalDocHtml;
  }
  /**
   * Doc link for external-facing documentation (separate from g3doc).
   *
   * @param string $externalDocLink
   */
  public function setExternalDocLink($externalDocLink)
  {
    $this->externalDocLink = $externalDocLink;
  }
  /**
   * @return string
   */
  public function getExternalDocLink()
  {
    return $this->externalDocLink;
  }
  /**
   * DEPRECATED: Use external_doc_html.
   *
   * @param string $externalDocMarkdown
   */
  public function setExternalDocMarkdown($externalDocMarkdown)
  {
    $this->externalDocMarkdown = $externalDocMarkdown;
  }
  /**
   * @return string
   */
  public function getExternalDocMarkdown()
  {
    return $this->externalDocMarkdown;
  }
  /**
   * URL to the associated G3 Doc for the task if available
   *
   * @param string $g3DocLink
   */
  public function setG3DocLink($g3DocLink)
  {
    $this->g3DocLink = $g3DocLink;
  }
  /**
   * @return string
   */
  public function getG3DocLink()
  {
    return $this->g3DocLink;
  }
  /**
   * URL to gstatic image icon for this task. This icon shows up on the task
   * list panel along with the task name in the Workflow Editor screen. Use the
   * 24p, 2x, gray color icon image format.
   *
   * @param string $iconLink
   */
  public function setIconLink($iconLink)
  {
    $this->iconLink = $iconLink;
  }
  /**
   * @return string
   */
  public function getIconLink()
  {
    return $this->iconLink;
  }
  /**
   * The deprecation status of the current task. Default value is false;
   *
   * @param bool $isDeprecated
   */
  public function setIsDeprecated($isDeprecated)
  {
    $this->isDeprecated = $isDeprecated;
  }
  /**
   * @return bool
   */
  public function getIsDeprecated()
  {
    return $this->isDeprecated;
  }
  /**
   * The actual class name or the annotated name of the task. Task Author should
   * initialize this field with value from the getName() method of the Task
   * class.
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
   * External-facing documention for standalone IP in pantheon embedded in the
   * RHP for this task. Non null only if different from external_doc_html
   *
   * @param string $standaloneExternalDocHtml
   */
  public function setStandaloneExternalDocHtml($standaloneExternalDocHtml)
  {
    $this->standaloneExternalDocHtml = $standaloneExternalDocHtml;
  }
  /**
   * @return string
   */
  public function getStandaloneExternalDocHtml()
  {
    return $this->standaloneExternalDocHtml;
  }
  /**
   * Allows author to indicate if the task is ready to use or not. If not set,
   * then it will default to INACTIVE.
   *
   * Accepted values: UNSPECIFIED_STATUS, DEFAULT_INACTIVE, ACTIVE
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * @param self::SYSTEM_* $system
   */
  public function setSystem($system)
  {
    $this->system = $system;
  }
  /**
   * @return self::SYSTEM_*
   */
  public function getSystem()
  {
    return $this->system;
  }
  /**
   * A set of tags that pertain to a particular task. This can be used to
   * improve the searchability of tasks with several names ("REST Caller" vs.
   * "Call REST Endpoint") or to help users find tasks based on related words.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoTaskMetadata::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoTaskMetadata');
