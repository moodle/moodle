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

class ExportAssetsRequest extends \Google\Collection
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
   * A list of asset types to take a snapshot for. For example:
   * "compute.googleapis.com/Disk". Regular expressions are also supported. For
   * example: * "compute.googleapis.com.*" snapshots resources whose asset type
   * starts with "compute.googleapis.com". * ".*Instance" snapshots resources
   * whose asset type ends with "Instance". * ".*Instance.*" snapshots resources
   * whose asset type contains "Instance". See
   * [RE2](https://github.com/google/re2/wiki/Syntax) for all supported regular
   * expression syntax. If the regular expression does not match any supported
   * asset type, an INVALID_ARGUMENT error will be returned. If specified, only
   * matching assets will be returned, otherwise, it will snapshot all asset
   * types. See [Introduction to Cloud Asset
   * Inventory](https://cloud.google.com/asset-inventory/docs/overview) for all
   * supported asset types.
   *
   * @var string[]
   */
  public $assetTypes;
  /**
   * Asset content type. If not specified, no content but the asset name will be
   * returned.
   *
   * @var string
   */
  public $contentType;
  protected $outputConfigType = OutputConfig::class;
  protected $outputConfigDataType = '';
  /**
   * Timestamp to take an asset snapshot. This can only be set to a timestamp
   * between the current time and the current time minus 35 days (inclusive). If
   * not specified, the current time will be used. Due to delays in resource
   * data collection and indexing, there is a volatile window during which
   * running the same query may get different results.
   *
   * @var string
   */
  public $readTime;
  /**
   * A list of relationship types to export, for example:
   * `INSTANCE_TO_INSTANCEGROUP`. This field should only be specified if
   * content_type=RELATIONSHIP. * If specified: it snapshots specified
   * relationships. It returns an error if any of the [relationship_types]
   * doesn't belong to the supported relationship types of the [asset_types] or
   * if any of the [asset_types] doesn't belong to the source types of the
   * [relationship_types]. * Otherwise: it snapshots the supported relationships
   * for all [asset_types] or returns an error if any of the [asset_types] has
   * no relationship support. An unspecified asset types field means all
   * supported asset_types. See [Introduction to Cloud Asset
   * Inventory](https://cloud.google.com/asset-inventory/docs/overview) for all
   * supported asset types and relationship types.
   *
   * @var string[]
   */
  public $relationshipTypes;

  /**
   * A list of asset types to take a snapshot for. For example:
   * "compute.googleapis.com/Disk". Regular expressions are also supported. For
   * example: * "compute.googleapis.com.*" snapshots resources whose asset type
   * starts with "compute.googleapis.com". * ".*Instance" snapshots resources
   * whose asset type ends with "Instance". * ".*Instance.*" snapshots resources
   * whose asset type contains "Instance". See
   * [RE2](https://github.com/google/re2/wiki/Syntax) for all supported regular
   * expression syntax. If the regular expression does not match any supported
   * asset type, an INVALID_ARGUMENT error will be returned. If specified, only
   * matching assets will be returned, otherwise, it will snapshot all asset
   * types. See [Introduction to Cloud Asset
   * Inventory](https://cloud.google.com/asset-inventory/docs/overview) for all
   * supported asset types.
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
   * Asset content type. If not specified, no content but the asset name will be
   * returned.
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
   * Required. Output configuration indicating where the results will be output
   * to.
   *
   * @param OutputConfig $outputConfig
   */
  public function setOutputConfig(OutputConfig $outputConfig)
  {
    $this->outputConfig = $outputConfig;
  }
  /**
   * @return OutputConfig
   */
  public function getOutputConfig()
  {
    return $this->outputConfig;
  }
  /**
   * Timestamp to take an asset snapshot. This can only be set to a timestamp
   * between the current time and the current time minus 35 days (inclusive). If
   * not specified, the current time will be used. Due to delays in resource
   * data collection and indexing, there is a volatile window during which
   * running the same query may get different results.
   *
   * @param string $readTime
   */
  public function setReadTime($readTime)
  {
    $this->readTime = $readTime;
  }
  /**
   * @return string
   */
  public function getReadTime()
  {
    return $this->readTime;
  }
  /**
   * A list of relationship types to export, for example:
   * `INSTANCE_TO_INSTANCEGROUP`. This field should only be specified if
   * content_type=RELATIONSHIP. * If specified: it snapshots specified
   * relationships. It returns an error if any of the [relationship_types]
   * doesn't belong to the supported relationship types of the [asset_types] or
   * if any of the [asset_types] doesn't belong to the source types of the
   * [relationship_types]. * Otherwise: it snapshots the supported relationships
   * for all [asset_types] or returns an error if any of the [asset_types] has
   * no relationship support. An unspecified asset types field means all
   * supported asset_types. See [Introduction to Cloud Asset
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
class_alias(ExportAssetsRequest::class, 'Google_Service_CloudAsset_ExportAssetsRequest');
