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

namespace Google\Service\WorkspaceEvents;

class AgentSkill extends \Google\Collection
{
  protected $collection_key = 'tags';
  /**
   * A human (or llm) readable description of the skill details and behaviors.
   *
   * @var string
   */
  public $description;
  /**
   * A set of example queries that this skill is designed to address. These
   * examples should help the caller to understand how to craft requests to the
   * agent to achieve specific goals. Example: ["I need a recipe for bread"]
   *
   * @var string[]
   */
  public $examples;
  /**
   * Unique identifier of the skill within this agent.
   *
   * @var string
   */
  public $id;
  /**
   * Possible input modalities supported.
   *
   * @var string[]
   */
  public $inputModes;
  /**
   * A human readable name for the skill.
   *
   * @var string
   */
  public $name;
  /**
   * Possible output modalities produced
   *
   * @var string[]
   */
  public $outputModes;
  protected $securityType = Security::class;
  protected $securityDataType = 'array';
  /**
   * A set of tags for the skill to enhance categorization/utilization. Example:
   * ["cooking", "customer support", "billing"]
   *
   * @var string[]
   */
  public $tags;

  /**
   * A human (or llm) readable description of the skill details and behaviors.
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
   * A set of example queries that this skill is designed to address. These
   * examples should help the caller to understand how to craft requests to the
   * agent to achieve specific goals. Example: ["I need a recipe for bread"]
   *
   * @param string[] $examples
   */
  public function setExamples($examples)
  {
    $this->examples = $examples;
  }
  /**
   * @return string[]
   */
  public function getExamples()
  {
    return $this->examples;
  }
  /**
   * Unique identifier of the skill within this agent.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Possible input modalities supported.
   *
   * @param string[] $inputModes
   */
  public function setInputModes($inputModes)
  {
    $this->inputModes = $inputModes;
  }
  /**
   * @return string[]
   */
  public function getInputModes()
  {
    return $this->inputModes;
  }
  /**
   * A human readable name for the skill.
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
   * Possible output modalities produced
   *
   * @param string[] $outputModes
   */
  public function setOutputModes($outputModes)
  {
    $this->outputModes = $outputModes;
  }
  /**
   * @return string[]
   */
  public function getOutputModes()
  {
    return $this->outputModes;
  }
  /**
   * protolint:disable REPEATED_FIELD_NAMES_PLURALIZED Security schemes
   * necessary for the agent to leverage this skill. As in the overall
   * AgentCard.security, this list represents a logical OR of security
   * requirement objects. Each object is a set of security schemes that must be
   * used together (a logical AND). protolint:enable
   * REPEATED_FIELD_NAMES_PLURALIZED
   *
   * @param Security[] $security
   */
  public function setSecurity($security)
  {
    $this->security = $security;
  }
  /**
   * @return Security[]
   */
  public function getSecurity()
  {
    return $this->security;
  }
  /**
   * A set of tags for the skill to enhance categorization/utilization. Example:
   * ["cooking", "customer support", "billing"]
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
class_alias(AgentSkill::class, 'Google_Service_WorkspaceEvents_AgentSkill');
