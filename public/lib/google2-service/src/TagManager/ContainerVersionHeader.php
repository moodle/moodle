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

class ContainerVersionHeader extends \Google\Model
{
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
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
  /**
   * A value of true indicates this container version has been deleted.
   *
   * @var bool
   */
  public $deleted;
  /**
   * Container version display name.
   *
   * @var string
   */
  public $name;
  /**
   * Number of clients in the container version.
   *
   * @var string
   */
  public $numClients;
  /**
   * Number of custom templates in the container version.
   *
   * @var string
   */
  public $numCustomTemplates;
  /**
   * Number of Google tag configs in the container version.
   *
   * @var string
   */
  public $numGtagConfigs;
  /**
   * Number of tags in the container version.
   *
   * @var string
   */
  public $numTags;
  /**
   * Number of transformations in the container version.
   *
   * @var string
   */
  public $numTransformations;
  /**
   * Number of triggers in the container version.
   *
   * @var string
   */
  public $numTriggers;
  /**
   * Number of variables in the container version.
   *
   * @var string
   */
  public $numVariables;
  /**
   * Number of zones in the container version.
   *
   * @var string
   */
  public $numZones;
  /**
   * GTM Container Version's API relative path.
   *
   * @var string
   */
  public $path;

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
   * Number of clients in the container version.
   *
   * @param string $numClients
   */
  public function setNumClients($numClients)
  {
    $this->numClients = $numClients;
  }
  /**
   * @return string
   */
  public function getNumClients()
  {
    return $this->numClients;
  }
  /**
   * Number of custom templates in the container version.
   *
   * @param string $numCustomTemplates
   */
  public function setNumCustomTemplates($numCustomTemplates)
  {
    $this->numCustomTemplates = $numCustomTemplates;
  }
  /**
   * @return string
   */
  public function getNumCustomTemplates()
  {
    return $this->numCustomTemplates;
  }
  /**
   * Number of Google tag configs in the container version.
   *
   * @param string $numGtagConfigs
   */
  public function setNumGtagConfigs($numGtagConfigs)
  {
    $this->numGtagConfigs = $numGtagConfigs;
  }
  /**
   * @return string
   */
  public function getNumGtagConfigs()
  {
    return $this->numGtagConfigs;
  }
  /**
   * Number of tags in the container version.
   *
   * @param string $numTags
   */
  public function setNumTags($numTags)
  {
    $this->numTags = $numTags;
  }
  /**
   * @return string
   */
  public function getNumTags()
  {
    return $this->numTags;
  }
  /**
   * Number of transformations in the container version.
   *
   * @param string $numTransformations
   */
  public function setNumTransformations($numTransformations)
  {
    $this->numTransformations = $numTransformations;
  }
  /**
   * @return string
   */
  public function getNumTransformations()
  {
    return $this->numTransformations;
  }
  /**
   * Number of triggers in the container version.
   *
   * @param string $numTriggers
   */
  public function setNumTriggers($numTriggers)
  {
    $this->numTriggers = $numTriggers;
  }
  /**
   * @return string
   */
  public function getNumTriggers()
  {
    return $this->numTriggers;
  }
  /**
   * Number of variables in the container version.
   *
   * @param string $numVariables
   */
  public function setNumVariables($numVariables)
  {
    $this->numVariables = $numVariables;
  }
  /**
   * @return string
   */
  public function getNumVariables()
  {
    return $this->numVariables;
  }
  /**
   * Number of zones in the container version.
   *
   * @param string $numZones
   */
  public function setNumZones($numZones)
  {
    $this->numZones = $numZones;
  }
  /**
   * @return string
   */
  public function getNumZones()
  {
    return $this->numZones;
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContainerVersionHeader::class, 'Google_Service_TagManager_ContainerVersionHeader');
