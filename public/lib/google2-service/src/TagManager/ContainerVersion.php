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

namespace Google\Service\TagManager;

class ContainerVersion extends \Google\Collection
{
  protected $collection_key = 'zone';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  protected $builtInVariableType = BuiltInVariable::class;
  protected $builtInVariableDataType = 'array';
  protected $clientType = Client::class;
  protected $clientDataType = 'array';
  protected $containerType = Container::class;
  protected $containerDataType = '';
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * The Container Version ID uniquely identifies the GTM Container Version.
   *
   * @var string
   */
  public $containerVersionId;
  protected $customTemplateType = CustomTemplate::class;
  protected $customTemplateDataType = 'array';
  /**
   * A value of true indicates this container version has been deleted.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Container version description.
   *
   * @var string
   */
  public $description;
  /**
   * The fingerprint of the GTM Container Version as computed at storage time.
   * This value is recomputed whenever the container version is modified.
   *
   * @var string
   */
  public $fingerprint;
  protected $folderType = Folder::class;
  protected $folderDataType = 'array';
  protected $gtagConfigType = GtagConfig::class;
  protected $gtagConfigDataType = 'array';
  /**
   * Container version display name.
   *
   * @var string
   */
  public $name;
  /**
   * GTM Container Version's API relative path.
   *
   * @var string
   */
  public $path;
  protected $tagType = Tag::class;
  protected $tagDataType = 'array';
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  protected $transformationType = Transformation::class;
  protected $transformationDataType = 'array';
  protected $triggerType = Trigger::class;
  protected $triggerDataType = 'array';
  protected $variableType = Variable::class;
  protected $variableDataType = 'array';
  protected $zoneType = Zone::class;
  protected $zoneDataType = 'array';

  /**
   * GTM Account ID.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The built-in variables in the container that this version was taken from.
   *
   * @param BuiltInVariable[] $builtInVariable
   */
  public function setBuiltInVariable($builtInVariable)
  {
    $this->builtInVariable = $builtInVariable;
  }
  /**
   * @return BuiltInVariable[]
   */
  public function getBuiltInVariable()
  {
    return $this->builtInVariable;
  }
  /**
   * The clients in the container that this version was taken from.
   *
   * @param Client[] $client
   */
  public function setClient($client)
  {
    $this->client = $client;
  }
  /**
   * @return Client[]
   */
  public function getClient()
  {
    return $this->client;
  }
  /**
   * The container that this version was taken from.
   *
   * @param Container $container
   */
  public function setContainer(Container $container)
  {
    $this->container = $container;
  }
  /**
   * @return Container
   */
  public function getContainer()
  {
    return $this->container;
  }
  /**
   * GTM Container ID.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * The Container Version ID uniquely identifies the GTM Container Version.
   *
   * @param string $containerVersionId
   */
  public function setContainerVersionId($containerVersionId)
  {
    $this->containerVersionId = $containerVersionId;
  }
  /**
   * @return string
   */
  public function getContainerVersionId()
  {
    return $this->containerVersionId;
  }
  /**
   * The custom templates in the container that this version was taken from.
   *
   * @param CustomTemplate[] $customTemplate
   */
  public function setCustomTemplate($customTemplate)
  {
    $this->customTemplate = $customTemplate;
  }
  /**
   * @return CustomTemplate[]
   */
  public function getCustomTemplate()
  {
    return $this->customTemplate;
  }
  /**
   * A value of true indicates this container version has been deleted.
   *
   * @param bool $deleted
   */
  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }
  /**
   * @return bool
   */
  public function getDeleted()
  {
    return $this->deleted;
  }
  /**
   * Container version description.
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
   * The fingerprint of the GTM Container Version as computed at storage time.
   * This value is recomputed whenever the container version is modified.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * The folders in the container that this version was taken from.
   *
   * @param Folder[] $folder
   */
  public function setFolder($folder)
  {
    $this->folder = $folder;
  }
  /**
   * @return Folder[]
   */
  public function getFolder()
  {
    return $this->folder;
  }
  /**
   * The Google tag configs in the container that this version was taken from.
   *
   * @param GtagConfig[] $gtagConfig
   */
  public function setGtagConfig($gtagConfig)
  {
    $this->gtagConfig = $gtagConfig;
  }
  /**
   * @return GtagConfig[]
   */
  public function getGtagConfig()
  {
    return $this->gtagConfig;
  }
  /**
   * Container version display name.
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
   * GTM Container Version's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * The tags in the container that this version was taken from.
   *
   * @param Tag[] $tag
   */
  public function setTag($tag)
  {
    $this->tag = $tag;
  }
  /**
   * @return Tag[]
   */
  public function getTag()
  {
    return $this->tag;
  }
  /**
   * Auto generated link to the tag manager UI
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
  /**
   * The transformations in the container that this version was taken from.
   *
   * @param Transformation[] $transformation
   */
  public function setTransformation($transformation)
  {
    $this->transformation = $transformation;
  }
  /**
   * @return Transformation[]
   */
  public function getTransformation()
  {
    return $this->transformation;
  }
  /**
   * The triggers in the container that this version was taken from.
   *
   * @param Trigger[] $trigger
   */
  public function setTrigger($trigger)
  {
    $this->trigger = $trigger;
  }
  /**
   * @return Trigger[]
   */
  public function getTrigger()
  {
    return $this->trigger;
  }
  /**
   * The variables in the container that this version was taken from.
   *
   * @param Variable[] $variable
   */
  public function setVariable($variable)
  {
    $this->variable = $variable;
  }
  /**
   * @return Variable[]
   */
  public function getVariable()
  {
    return $this->variable;
  }
  /**
   * The zones in the container that this version was taken from.
   *
   * @param Zone[] $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return Zone[]
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContainerVersion::class, 'Google_Service_TagManager_ContainerVersion');
