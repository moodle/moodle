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

namespace Google\Service\SecurityCommandCenter;

class ResourcePathNode extends \Google\Model
{
  /**
   * Node type is unspecified.
   */
  public const NODE_TYPE_RESOURCE_PATH_NODE_TYPE_UNSPECIFIED = 'RESOURCE_PATH_NODE_TYPE_UNSPECIFIED';
  /**
   * The node represents a Google Cloud organization.
   */
  public const NODE_TYPE_GCP_ORGANIZATION = 'GCP_ORGANIZATION';
  /**
   * The node represents a Google Cloud folder.
   */
  public const NODE_TYPE_GCP_FOLDER = 'GCP_FOLDER';
  /**
   * The node represents a Google Cloud project.
   */
  public const NODE_TYPE_GCP_PROJECT = 'GCP_PROJECT';
  /**
   * The node represents an AWS organization.
   */
  public const NODE_TYPE_AWS_ORGANIZATION = 'AWS_ORGANIZATION';
  /**
   * The node represents an AWS organizational unit.
   */
  public const NODE_TYPE_AWS_ORGANIZATIONAL_UNIT = 'AWS_ORGANIZATIONAL_UNIT';
  /**
   * The node represents an AWS account.
   */
  public const NODE_TYPE_AWS_ACCOUNT = 'AWS_ACCOUNT';
  /**
   * The node represents an Azure management group.
   */
  public const NODE_TYPE_AZURE_MANAGEMENT_GROUP = 'AZURE_MANAGEMENT_GROUP';
  /**
   * The node represents an Azure subscription.
   */
  public const NODE_TYPE_AZURE_SUBSCRIPTION = 'AZURE_SUBSCRIPTION';
  /**
   * The node represents an Azure resource group.
   */
  public const NODE_TYPE_AZURE_RESOURCE_GROUP = 'AZURE_RESOURCE_GROUP';
  /**
   * The display name of the resource this node represents.
   *
   * @var string
   */
  public $displayName;
  /**
   * The ID of the resource this node represents.
   *
   * @var string
   */
  public $id;
  /**
   * The type of resource this node represents.
   *
   * @var string
   */
  public $nodeType;

  /**
   * The display name of the resource this node represents.
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
   * The ID of the resource this node represents.
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
   * The type of resource this node represents.
   *
   * Accepted values: RESOURCE_PATH_NODE_TYPE_UNSPECIFIED, GCP_ORGANIZATION,
   * GCP_FOLDER, GCP_PROJECT, AWS_ORGANIZATION, AWS_ORGANIZATIONAL_UNIT,
   * AWS_ACCOUNT, AZURE_MANAGEMENT_GROUP, AZURE_SUBSCRIPTION,
   * AZURE_RESOURCE_GROUP
   *
   * @param self::NODE_TYPE_* $nodeType
   */
  public function setNodeType($nodeType)
  {
    $this->nodeType = $nodeType;
  }
  /**
   * @return self::NODE_TYPE_*
   */
  public function getNodeType()
  {
    return $this->nodeType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourcePathNode::class, 'Google_Service_SecurityCommandCenter_ResourcePathNode');
