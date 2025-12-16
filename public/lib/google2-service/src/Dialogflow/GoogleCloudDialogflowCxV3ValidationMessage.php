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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3ValidationMessage extends \Google\Collection
{
  /**
   * Unspecified.
   */
  public const RESOURCE_TYPE_RESOURCE_TYPE_UNSPECIFIED = 'RESOURCE_TYPE_UNSPECIFIED';
  /**
   * Agent.
   */
  public const RESOURCE_TYPE_AGENT = 'AGENT';
  /**
   * Intent.
   */
  public const RESOURCE_TYPE_INTENT = 'INTENT';
  /**
   * Intent training phrase.
   */
  public const RESOURCE_TYPE_INTENT_TRAINING_PHRASE = 'INTENT_TRAINING_PHRASE';
  /**
   * Intent parameter.
   */
  public const RESOURCE_TYPE_INTENT_PARAMETER = 'INTENT_PARAMETER';
  /**
   * Multiple intents.
   */
  public const RESOURCE_TYPE_INTENTS = 'INTENTS';
  /**
   * Multiple training phrases.
   */
  public const RESOURCE_TYPE_INTENT_TRAINING_PHRASES = 'INTENT_TRAINING_PHRASES';
  /**
   * Entity type.
   */
  public const RESOURCE_TYPE_ENTITY_TYPE = 'ENTITY_TYPE';
  /**
   * Multiple entity types.
   */
  public const RESOURCE_TYPE_ENTITY_TYPES = 'ENTITY_TYPES';
  /**
   * Webhook.
   */
  public const RESOURCE_TYPE_WEBHOOK = 'WEBHOOK';
  /**
   * Flow.
   */
  public const RESOURCE_TYPE_FLOW = 'FLOW';
  /**
   * Page.
   */
  public const RESOURCE_TYPE_PAGE = 'PAGE';
  /**
   * Multiple pages.
   */
  public const RESOURCE_TYPE_PAGES = 'PAGES';
  /**
   * Transition route group.
   */
  public const RESOURCE_TYPE_TRANSITION_ROUTE_GROUP = 'TRANSITION_ROUTE_GROUP';
  /**
   * Agent transition route group.
   */
  public const RESOURCE_TYPE_AGENT_TRANSITION_ROUTE_GROUP = 'AGENT_TRANSITION_ROUTE_GROUP';
  /**
   * Unspecified.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * The agent doesn't follow Dialogflow best practices.
   */
  public const SEVERITY_INFO = 'INFO';
  /**
   * The agent may not behave as expected.
   */
  public const SEVERITY_WARNING = 'WARNING';
  /**
   * The agent may experience failures.
   */
  public const SEVERITY_ERROR = 'ERROR';
  protected $collection_key = 'resources';
  /**
   * The message detail.
   *
   * @var string
   */
  public $detail;
  protected $resourceNamesType = GoogleCloudDialogflowCxV3ResourceName::class;
  protected $resourceNamesDataType = 'array';
  /**
   * The type of the resources where the message is found.
   *
   * @var string
   */
  public $resourceType;
  /**
   * The names of the resources where the message is found.
   *
   * @deprecated
   * @var string[]
   */
  public $resources;
  /**
   * Indicates the severity of the message.
   *
   * @var string
   */
  public $severity;

  /**
   * The message detail.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The resource names of the resources where the message is found.
   *
   * @param GoogleCloudDialogflowCxV3ResourceName[] $resourceNames
   */
  public function setResourceNames($resourceNames)
  {
    $this->resourceNames = $resourceNames;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ResourceName[]
   */
  public function getResourceNames()
  {
    return $this->resourceNames;
  }
  /**
   * The type of the resources where the message is found.
   *
   * Accepted values: RESOURCE_TYPE_UNSPECIFIED, AGENT, INTENT,
   * INTENT_TRAINING_PHRASE, INTENT_PARAMETER, INTENTS, INTENT_TRAINING_PHRASES,
   * ENTITY_TYPE, ENTITY_TYPES, WEBHOOK, FLOW, PAGE, PAGES,
   * TRANSITION_ROUTE_GROUP, AGENT_TRANSITION_ROUTE_GROUP
   *
   * @param self::RESOURCE_TYPE_* $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return self::RESOURCE_TYPE_*
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * The names of the resources where the message is found.
   *
   * @deprecated
   * @param string[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * Indicates the severity of the message.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, INFO, WARNING, ERROR
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ValidationMessage::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ValidationMessage');
