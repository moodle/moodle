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

class AgentCard extends \Google\Collection
{
  protected $collection_key = 'skills';
  protected $additionalInterfacesType = AgentInterface::class;
  protected $additionalInterfacesDataType = 'array';
  protected $capabilitiesType = AgentCapabilities::class;
  protected $capabilitiesDataType = '';
  /**
   * protolint:enable REPEATED_FIELD_NAMES_PLURALIZED The set of interaction
   * modes that the agent supports across all skills. This can be overridden per
   * skill. Defined as mime types.
   *
   * @var string[]
   */
  public $defaultInputModes;
  /**
   * The mime types supported as outputs from this agent.
   *
   * @var string[]
   */
  public $defaultOutputModes;
  /**
   * A description of the agent's domain of action/solution space. Example:
   * "Agent that helps users with recipes and cooking."
   *
   * @var string
   */
  public $description;
  /**
   * A url to provide additional documentation about the agent.
   *
   * @var string
   */
  public $documentationUrl;
  /**
   * An optional URL to an icon for the agent.
   *
   * @var string
   */
  public $iconUrl;
  /**
   * A human readable name for the agent. Example: "Recipe Agent"
   *
   * @var string
   */
  public $name;
  /**
   * The transport of the preferred endpoint. If empty, defaults to JSONRPC.
   *
   * @var string
   */
  public $preferredTransport;
  /**
   * The version of the A2A protocol this agent supports.
   *
   * @var string
   */
  public $protocolVersion;
  protected $providerType = AgentProvider::class;
  protected $providerDataType = '';
  protected $securityType = Security::class;
  protected $securityDataType = 'array';
  protected $securitySchemesType = SecurityScheme::class;
  protected $securitySchemesDataType = 'map';
  protected $signaturesType = AgentCardSignature::class;
  protected $signaturesDataType = 'array';
  protected $skillsType = AgentSkill::class;
  protected $skillsDataType = 'array';
  /**
   * Whether the agent supports providing an extended agent card when the user
   * is authenticated, i.e. is the card from .well-known different than the card
   * from GetAgentCard.
   *
   * @var bool
   */
  public $supportsAuthenticatedExtendedCard;
  /**
   * A URL to the address the agent is hosted at. This represents the preferred
   * endpoint as declared by the agent.
   *
   * @var string
   */
  public $url;
  /**
   * The version of the agent. Example: "1.0.0"
   *
   * @var string
   */
  public $version;

  /**
   * Announcement of additional supported transports. Client can use any of the
   * supported transports.
   *
   * @param AgentInterface[] $additionalInterfaces
   */
  public function setAdditionalInterfaces($additionalInterfaces)
  {
    $this->additionalInterfaces = $additionalInterfaces;
  }
  /**
   * @return AgentInterface[]
   */
  public function getAdditionalInterfaces()
  {
    return $this->additionalInterfaces;
  }
  /**
   * A2A Capability set supported by the agent.
   *
   * @param AgentCapabilities $capabilities
   */
  public function setCapabilities(AgentCapabilities $capabilities)
  {
    $this->capabilities = $capabilities;
  }
  /**
   * @return AgentCapabilities
   */
  public function getCapabilities()
  {
    return $this->capabilities;
  }
  /**
   * protolint:enable REPEATED_FIELD_NAMES_PLURALIZED The set of interaction
   * modes that the agent supports across all skills. This can be overridden per
   * skill. Defined as mime types.
   *
   * @param string[] $defaultInputModes
   */
  public function setDefaultInputModes($defaultInputModes)
  {
    $this->defaultInputModes = $defaultInputModes;
  }
  /**
   * @return string[]
   */
  public function getDefaultInputModes()
  {
    return $this->defaultInputModes;
  }
  /**
   * The mime types supported as outputs from this agent.
   *
   * @param string[] $defaultOutputModes
   */
  public function setDefaultOutputModes($defaultOutputModes)
  {
    $this->defaultOutputModes = $defaultOutputModes;
  }
  /**
   * @return string[]
   */
  public function getDefaultOutputModes()
  {
    return $this->defaultOutputModes;
  }
  /**
   * A description of the agent's domain of action/solution space. Example:
   * "Agent that helps users with recipes and cooking."
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
   * A url to provide additional documentation about the agent.
   *
   * @param string $documentationUrl
   */
  public function setDocumentationUrl($documentationUrl)
  {
    $this->documentationUrl = $documentationUrl;
  }
  /**
   * @return string
   */
  public function getDocumentationUrl()
  {
    return $this->documentationUrl;
  }
  /**
   * An optional URL to an icon for the agent.
   *
   * @param string $iconUrl
   */
  public function setIconUrl($iconUrl)
  {
    $this->iconUrl = $iconUrl;
  }
  /**
   * @return string
   */
  public function getIconUrl()
  {
    return $this->iconUrl;
  }
  /**
   * A human readable name for the agent. Example: "Recipe Agent"
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
   * The transport of the preferred endpoint. If empty, defaults to JSONRPC.
   *
   * @param string $preferredTransport
   */
  public function setPreferredTransport($preferredTransport)
  {
    $this->preferredTransport = $preferredTransport;
  }
  /**
   * @return string
   */
  public function getPreferredTransport()
  {
    return $this->preferredTransport;
  }
  /**
   * The version of the A2A protocol this agent supports.
   *
   * @param string $protocolVersion
   */
  public function setProtocolVersion($protocolVersion)
  {
    $this->protocolVersion = $protocolVersion;
  }
  /**
   * @return string
   */
  public function getProtocolVersion()
  {
    return $this->protocolVersion;
  }
  /**
   * The service provider of the agent.
   *
   * @param AgentProvider $provider
   */
  public function setProvider(AgentProvider $provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return AgentProvider
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * protolint:disable REPEATED_FIELD_NAMES_PLURALIZED Security requirements for
   * contacting the agent. This list can be seen as an OR of ANDs. Each object
   * in the list describes one possible set of security requirements that must
   * be present on a request. This allows specifying, for example, "callers must
   * either use OAuth OR an API Key AND mTLS." Example: security { schemes {
   * key: "oauth" value { list: ["read"] } } } security { schemes { key: "api-
   * key" } schemes { key: "mtls" } }
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
   * The security scheme details used for authenticating with this agent.
   *
   * @param SecurityScheme[] $securitySchemes
   */
  public function setSecuritySchemes($securitySchemes)
  {
    $this->securitySchemes = $securitySchemes;
  }
  /**
   * @return SecurityScheme[]
   */
  public function getSecuritySchemes()
  {
    return $this->securitySchemes;
  }
  /**
   * JSON Web Signatures computed for this AgentCard.
   *
   * @param AgentCardSignature[] $signatures
   */
  public function setSignatures($signatures)
  {
    $this->signatures = $signatures;
  }
  /**
   * @return AgentCardSignature[]
   */
  public function getSignatures()
  {
    return $this->signatures;
  }
  /**
   * Skills represent a unit of ability an agent can perform. This may somewhat
   * abstract but represents a more focused set of actions that the agent is
   * highly likely to succeed at.
   *
   * @param AgentSkill[] $skills
   */
  public function setSkills($skills)
  {
    $this->skills = $skills;
  }
  /**
   * @return AgentSkill[]
   */
  public function getSkills()
  {
    return $this->skills;
  }
  /**
   * Whether the agent supports providing an extended agent card when the user
   * is authenticated, i.e. is the card from .well-known different than the card
   * from GetAgentCard.
   *
   * @param bool $supportsAuthenticatedExtendedCard
   */
  public function setSupportsAuthenticatedExtendedCard($supportsAuthenticatedExtendedCard)
  {
    $this->supportsAuthenticatedExtendedCard = $supportsAuthenticatedExtendedCard;
  }
  /**
   * @return bool
   */
  public function getSupportsAuthenticatedExtendedCard()
  {
    return $this->supportsAuthenticatedExtendedCard;
  }
  /**
   * A URL to the address the agent is hosted at. This represents the preferred
   * endpoint as declared by the agent.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
  /**
   * The version of the agent. Example: "1.0.0"
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgentCard::class, 'Google_Service_WorkspaceEvents_AgentCard');
