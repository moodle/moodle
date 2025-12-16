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

namespace Google\Service\CloudAsset;

class Feed extends \Google\Collection
{
  /**
   * Unspecified content type.
   */
  public const CONTENT_TYPE_CONTENT_TYPE_UNSPECIFIED = 'CONTENT_TYPE_UNSPECIFIED';
  /**
   * Resource metadata.
   */
  public const CONTENT_TYPE_RESOURCE = 'RESOURCE';
  /**
   * The actual IAM policy set on a resource.
   */
  public const CONTENT_TYPE_IAM_POLICY = 'IAM_POLICY';
  /**
   * The organization policy set on an asset.
   */
  public const CONTENT_TYPE_ORG_POLICY = 'ORG_POLICY';
  /**
   * The Access Context Manager policy set on an asset.
   */
  public const CONTENT_TYPE_ACCESS_POLICY = 'ACCESS_POLICY';
  /**
   * The runtime OS Inventory information.
   */
  public const CONTENT_TYPE_OS_INVENTORY = 'OS_INVENTORY';
  /**
   * The related resources.
   */
  public const CONTENT_TYPE_RELATIONSHIP = 'RELATIONSHIP';
  protected $collection_key = 'relationshipTypes';
  /**
   * A list of the full names of the assets to receive updates. You must specify
   * either or both of asset_names and asset_types. Only asset updates matching
   * specified asset_names or asset_types are exported to the feed. Example: `//
   * compute.googleapis.com/projects/my_project_123/zones/zone1/instances/instan
   * ce1`. For a list of the full names for supported asset types, see [Resource
   * name format](/asset-inventory/docs/resource-name-format).
   *
   * @var string[]
   */
  public $assetNames;
  /**
   * A list of types of the assets to receive updates. You must specify either
   * or both of asset_names and asset_types. Only asset updates matching
   * specified asset_names or asset_types are exported to the feed. Example:
   * `"compute.googleapis.com/Disk"` For a list of all supported asset types,
   * see [Supported asset types](/asset-inventory/docs/supported-asset-types).
   *
   * @var string[]
   */
  public $assetTypes;
  protected $conditionType = Expr::class;
  protected $conditionDataType = '';
  /**
   * Asset content type. If not specified, no content but the asset name and
   * type will be returned.
   *
   * @var string
   */
  public $contentType;
  protected $feedOutputConfigType = FeedOutputConfig::class;
  protected $feedOutputConfigDataType = '';
  /**
   * Required. The format will be projects/{project_number}/feeds/{client-
   * assigned_feed_identifier} or folders/{folder_number}/feeds/{client-
   * assigned_feed_identifier} or
   * organizations/{organization_number}/feeds/{client-assigned_feed_identifier}
   * The client-assigned feed identifier must be unique within the parent
   * project/folder/organization.
   *
   * @var string
   */
  public $name;
  /**
   * A list of relationship types to output, for example:
   * `INSTANCE_TO_INSTANCEGROUP`. This field should only be specified if
   * content_type=RELATIONSHIP. * If specified: it outputs specified
   * relationship updates on the [asset_names] or the [asset_types]. It returns
   * an error if any of the [relationship_types] doesn't belong to the supported
   * relationship types of the [asset_names] or [asset_types], or any of the
   * [asset_names] or the [asset_types] doesn't belong to the source types of
   * the [relationship_types]. * Otherwise: it outputs the supported
   * relationships of the types of [asset_names] and [asset_types] or returns an
   * error if any of the [asset_names] or the [asset_types] has no replationship
   * support. See [Introduction to Cloud Asset
   * Inventory](https://cloud.google.com/asset-inventory/docs/overview) for all
   * supported asset types and relationship types.
   *
   * @var string[]
   */
  public $relationshipTypes;

  /**
   * A list of the full names of the assets to receive updates. You must specify
   * either or both of asset_names and asset_types. Only asset updates matching
   * specified asset_names or asset_types are exported to the feed. Example: `//
   * compute.googleapis.com/projects/my_project_123/zones/zone1/instances/instan
   * ce1`. For a list of the full names for supported asset types, see [Resource
   * name format](/asset-inventory/docs/resource-name-format).
   *
   * @param string[] $assetNames
   */
  public function setAssetNames($assetNames)
  {
    $this->assetNames = $assetNames;
  }
  /**
   * @return string[]
   */
  public function getAssetNames()
  {
    return $this->assetNames;
  }
  /**
   * A list of types of the assets to receive updates. You must specify either
   * or both of asset_names and asset_types. Only asset updates matching
   * specified asset_names or asset_types are exported to the feed. Example:
   * `"compute.googleapis.com/Disk"` For a list of all supported asset types,
   * see [Supported asset types](/asset-inventory/docs/supported-asset-types).
   *
   * @param string[] $assetTypes
   */
  public function setAssetTypes($assetTypes)
  {
    $this->assetTypes = $assetTypes;
  }
  /**
   * @return string[]
   */
  public function getAssetTypes()
  {
    return $this->assetTypes;
  }
  /**
   * A condition which determines whether an asset update should be published.
   * If specified, an asset will be returned only when the expression evaluates
   * to true. When set, `expression` field in the `Expr` must be a valid [CEL
   * expression] (https://github.com/google/cel-spec) on a TemporalAsset with
   * name `temporal_asset`. Example: a Feed with expression
   * ("temporal_asset.deleted == true") will only publish Asset deletions. Other
   * fields of `Expr` are optional. See our [user
   * guide](https://cloud.google.com/asset-inventory/docs/monitoring-asset-
   * changes-with-condition) for detailed instructions.
   *
   * @param Expr $condition
   */
  public function setCondition(Expr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return Expr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * Asset content type. If not specified, no content but the asset name and
   * type will be returned.
   *
   * Accepted values: CONTENT_TYPE_UNSPECIFIED, RESOURCE, IAM_POLICY,
   * ORG_POLICY, ACCESS_POLICY, OS_INVENTORY, RELATIONSHIP
   *
   * @param self::CONTENT_TYPE_* $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return self::CONTENT_TYPE_*
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * Required. Feed output configuration defining where the asset updates are
   * published to.
   *
   * @param FeedOutputConfig $feedOutputConfig
   */
  public function setFeedOutputConfig(FeedOutputConfig $feedOutputConfig)
  {
    $this->feedOutputConfig = $feedOutputConfig;
  }
  /**
   * @return FeedOutputConfig
   */
  public function getFeedOutputConfig()
  {
    return $this->feedOutputConfig;
  }
  /**
   * Required. The format will be projects/{project_number}/feeds/{client-
   * assigned_feed_identifier} or folders/{folder_number}/feeds/{client-
   * assigned_feed_identifier} or
   * organizations/{organization_number}/feeds/{client-assigned_feed_identifier}
   * The client-assigned feed identifier must be unique within the parent
   * project/folder/organization.
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
   * A list of relationship types to output, for example:
   * `INSTANCE_TO_INSTANCEGROUP`. This field should only be specified if
   * content_type=RELATIONSHIP. * If specified: it outputs specified
   * relationship updates on the [asset_names] or the [asset_types]. It returns
   * an error if any of the [relationship_types] doesn't belong to the supported
   * relationship types of the [asset_names] or [asset_types], or any of the
   * [asset_names] or the [asset_types] doesn't belong to the source types of
   * the [relationship_types]. * Otherwise: it outputs the supported
   * relationships of the types of [asset_names] and [asset_types] or returns an
   * error if any of the [asset_names] or the [asset_types] has no replationship
   * support. See [Introduction to Cloud Asset
   * Inventory](https://cloud.google.com/asset-inventory/docs/overview) for all
   * supported asset types and relationship types.
   *
   * @param string[] $relationshipTypes
   */
  public function setRelationshipTypes($relationshipTypes)
  {
    $this->relationshipTypes = $relationshipTypes;
  }
  /**
   * @return string[]
   */
  public function getRelationshipTypes()
  {
    return $this->relationshipTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Feed::class, 'Google_Service_CloudAsset_Feed');
