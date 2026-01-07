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

class ResourceSearchResult extends \Google\Collection
{
  protected $collection_key = 'versionedResources';
  /**
   * The additional searchable attributes of this resource. The attributes may
   * vary from one resource type to another. Examples: `projectId` for Project,
   * `dnsName` for DNS ManagedZone. This field contains a subset of the resource
   * metadata fields that are returned by the List or Get APIs provided by the
   * corresponding Google Cloud service (e.g., Compute Engine). see [API
   * references and supported searchable
   * attributes](https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types) to see which fields are included. You can search values of these
   * fields through free text search. However, you should not consume the field
   * programically as the field names and values may change as the Google Cloud
   * service updates to a new incompatible API version. To search against the
   * `additional_attributes`: * Use a free text query to match the attributes
   * values. Example: to search `additional_attributes = { dnsName: "foobar" }`,
   * you can issue a query `foobar`.
   *
   * @var array[]
   */
  public $additionalAttributes;
  /**
   * The type of this resource. Example: `compute.googleapis.com/Disk`. To
   * search against the `asset_type`: * Specify the `asset_type` field in your
   * search request.
   *
   * @var string
   */
  public $assetType;
  protected $attachedResourcesType = AttachedResource::class;
  protected $attachedResourcesDataType = 'array';
  /**
   * The create timestamp of this resource, at which the resource was created.
   * The granularity is in seconds. Timestamp.nanos will always be 0. This field
   * is available only when the resource's Protobuf contains it. To search
   * against `create_time`: * Use a field query. - value in seconds since unix
   * epoch. Example: `createTime > 1609459200` - value in date string. Example:
   * `createTime > 2021-01-01` - value in date-time string (must be quoted).
   * Example: `createTime > "2021-01-01T00:00:00"`
   *
   * @var string
   */
  public $createTime;
  /**
   * One or more paragraphs of text description of this resource. Maximum length
   * could be up to 1M bytes. This field is available only when the resource's
   * Protobuf contains it. To search against the `description`: * Use a field
   * query. Example: `description:"important instance"` * Use a free text query.
   * Example: `"important instance"`
   *
   * @var string
   */
  public $description;
  /**
   * The display name of this resource. This field is available only when the
   * resource's Protobuf contains it. To search against the `display_name`: *
   * Use a field query. Example: `displayName:"My Instance"` * Use a free text
   * query. Example: `"My Instance"`
   *
   * @var string
   */
  public $displayName;
  protected $effectiveTagsType = EffectiveTagDetails::class;
  protected $effectiveTagsDataType = 'array';
  protected $enrichmentsType = AssetEnrichment::class;
  protected $enrichmentsDataType = 'array';
  /**
   * The folder(s) that this resource belongs to, in the form of
   * folders/{FOLDER_NUMBER}. This field is available when the resource belongs
   * to one or more folders. To search against `folders`: * Use a field query.
   * Example: `folders:(123 OR 456)` * Use a free text query. Example: `123` *
   * Specify the `scope` field as this folder in your search request.
   *
   * @var string[]
   */
  public $folders;
  /**
   * The Cloud KMS [CryptoKey](https://cloud.google.com/kms/docs/reference/rest/
   * v1/projects.locations.keyRings.cryptoKeys) name or [CryptoKeyVersion](https
   * ://cloud.google.com/kms/docs/reference/rest/v1/projects.locations.keyRings.
   * cryptoKeys.cryptoKeyVersions) name. This field only presents for the
   * purpose of backward compatibility. Use the `kms_keys` field to retrieve
   * Cloud KMS key information. This field is available only when the resource's
   * Protobuf contains it and will only be populated for [these resource
   * types](https://cloud.google.com/asset-inventory/docs/legacy-field-
   * names#resource_types_with_the_to_be_deprecated_kmskey_field) for backward
   * compatible purposes. To search against the `kms_key`: * Use a field query.
   * Example: `kmsKey:key` * Use a free text query. Example: `key`
   *
   * @deprecated
   * @var string
   */
  public $kmsKey;
  /**
   * The Cloud KMS [CryptoKey](https://cloud.google.com/kms/docs/reference/rest/
   * v1/projects.locations.keyRings.cryptoKeys) names or [CryptoKeyVersion](http
   * s://cloud.google.com/kms/docs/reference/rest/v1/projects.locations.keyRings
   * .cryptoKeys.cryptoKeyVersions) names. This field is available only when the
   * resource's Protobuf contains it. To search against the `kms_keys`: * Use a
   * field query. Example: `kmsKeys:key` * Use a free text query. Example: `key`
   *
   * @var string[]
   */
  public $kmsKeys;
  /**
   * User labels associated with this resource. See [Labelling and grouping
   * Google Cloud
   * resources](https://cloud.google.com/blog/products/gcp/labelling-and-
   * grouping-your-google-cloud-platform-resources) for more information. This
   * field is available only when the resource's Protobuf contains it. To search
   * against the `labels`: * Use a field query: - query on any label's key or
   * value. Example: `labels:prod` - query by a given label. Example:
   * `labels.env:prod` - query by a given label's existence. Example:
   * `labels.env:*` * Use a free text query. Example: `prod`
   *
   * @var string[]
   */
  public $labels;
  /**
   * Location can be `global`, regional like `us-east1`, or zonal like `us-
   * west1-b`. This field is available only when the resource's Protobuf
   * contains it. To search against the `location`: * Use a field query.
   * Example: `location:us-west*` * Use a free text query. Example: `us-west*`
   *
   * @var string
   */
  public $location;
  /**
   * The full resource name of this resource. Example: `//compute.googleapis.com
   * /projects/my_project_123/zones/zone1/instances/instance1`. See [Cloud Asset
   * Inventory Resource Name Format](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) for more information. To search
   * against the `name`: * Use a field query. Example: `name:instance1` * Use a
   * free text query. Example: `instance1`
   *
   * @var string
   */
  public $name;
  /**
   * Network tags associated with this resource. Like labels, network tags are a
   * type of annotations used to group Google Cloud resources. See [Labelling
   * Google Cloud
   * resources](https://cloud.google.com/blog/products/gcp/labelling-and-
   * grouping-your-google-cloud-platform-resources) for more information. This
   * field is available only when the resource's Protobuf contains it. To search
   * against the `network_tags`: * Use a field query. Example:
   * `networkTags:internal` * Use a free text query. Example: `internal`
   *
   * @var string[]
   */
  public $networkTags;
  /**
   * The organization that this resource belongs to, in the form of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the
   * resource belongs to an organization. To search against `organization`: *
   * Use a field query. Example: `organization:123` * Use a free text query.
   * Example: `123` * Specify the `scope` field as this organization in your
   * search request.
   *
   * @var string
   */
  public $organization;
  /**
   * The type of this resource's immediate parent, if there is one. To search
   * against the `parent_asset_type`: * Use a field query. Example:
   * `parentAssetType:"cloudresourcemanager.googleapis.com/Project"` * Use a
   * free text query. Example: `cloudresourcemanager.googleapis.com/Project`
   *
   * @var string
   */
  public $parentAssetType;
  /**
   * The full resource name of this resource's parent, if it has one. To search
   * against the `parent_full_resource_name`: * Use a field query. Example:
   * `parentFullResourceName:"project-name"` * Use a free text query. Example:
   * `project-name`
   *
   * @var string
   */
  public $parentFullResourceName;
  /**
   * The project that this resource belongs to, in the form of
   * projects/{PROJECT_NUMBER}. This field is available when the resource
   * belongs to a project. To search against `project`: * Use a field query.
   * Example: `project:12345` * Use a free text query. Example: `12345` *
   * Specify the `scope` field as this project in your search request.
   *
   * @var string
   */
  public $project;
  protected $relationshipsType = RelatedResources::class;
  protected $relationshipsDataType = 'map';
  /**
   * The actual content of Security Command Center security marks associated
   * with the asset. To search against SCC SecurityMarks field: * Use a field
   * query: - query by a given key value pair. Example:
   * `sccSecurityMarks.foo=bar` - query by a given key's existence. Example:
   * `sccSecurityMarks.foo:*`
   *
   * @var string[]
   */
  public $sccSecurityMarks;
  /**
   * The state of this resource. Different resources types have different state
   * definitions that are mapped from various fields of different resource
   * types. This field is available only when the resource's Protobuf contains
   * it. Example: If the resource is an instance provided by Compute Engine, its
   * state will include PROVISIONING, STAGING, RUNNING, STOPPING, SUSPENDING,
   * SUSPENDED, REPAIRING, and TERMINATED. See `status` definition in [API Refer
   * ence](https://cloud.google.com/compute/docs/reference/rest/v1/instances).
   * If the resource is a project provided by Resource Manager, its state will
   * include LIFECYCLE_STATE_UNSPECIFIED, ACTIVE, DELETE_REQUESTED and
   * DELETE_IN_PROGRESS. See `lifecycleState` definition in [API
   * Reference](https://cloud.google.com/resource-
   * manager/reference/rest/v1/projects). To search against the `state`: * Use a
   * field query. Example: `state:RUNNING` * Use a free text query. Example:
   * `RUNNING`
   *
   * @var string
   */
  public $state;
  /**
   * This field is only present for the purpose of backward compatibility. Use
   * the `tags` field instead. TagKey namespaced names, in the format of
   * {ORG_ID}/{TAG_KEY_SHORT_NAME}. To search against the `tagKeys`: * Use a
   * field query. Example: - `tagKeys:"123456789/env*"` -
   * `tagKeys="123456789/env"` - `tagKeys:"env"` * Use a free text query.
   * Example: - `env`
   *
   * @deprecated
   * @var string[]
   */
  public $tagKeys;
  /**
   * This field is only present for the purpose of backward compatibility. Use
   * the `tags` field instead. TagValue IDs, in the format of
   * tagValues/{TAG_VALUE_ID}. To search against the `tagValueIds`: * Use a
   * field query. Example: - `tagValueIds="tagValues/456"` * Use a free text
   * query. Example: - `456`
   *
   * @deprecated
   * @var string[]
   */
  public $tagValueIds;
  /**
   * This field is only present for the purpose of backward compatibility. Use
   * the `tags` field instead. TagValue namespaced names, in the format of
   * {ORG_ID}/{TAG_KEY_SHORT_NAME}/{TAG_VALUE_SHORT_NAME}. To search against the
   * `tagValues`: * Use a field query. Example: - `tagValues:"env"` -
   * `tagValues:"env/prod"` - `tagValues:"123456789/env/prod*"` -
   * `tagValues="123456789/env/prod"` * Use a free text query. Example: - `prod`
   *
   * @deprecated
   * @var string[]
   */
  public $tagValues;
  protected $tagsType = Tag::class;
  protected $tagsDataType = 'array';
  /**
   * The last update timestamp of this resource, at which the resource was last
   * modified or deleted. The granularity is in seconds. Timestamp.nanos will
   * always be 0. This field is available only when the resource's Protobuf
   * contains it. To search against `update_time`: * Use a field query. - value
   * in seconds since unix epoch. Example: `updateTime < 1609459200` - value in
   * date string. Example: `updateTime < 2021-01-01` - value in date-time string
   * (must be quoted). Example: `updateTime < "2021-01-01T00:00:00"`
   *
   * @var string
   */
  public $updateTime;
  protected $versionedResourcesType = VersionedResource::class;
  protected $versionedResourcesDataType = 'array';

  /**
   * The additional searchable attributes of this resource. The attributes may
   * vary from one resource type to another. Examples: `projectId` for Project,
   * `dnsName` for DNS ManagedZone. This field contains a subset of the resource
   * metadata fields that are returned by the List or Get APIs provided by the
   * corresponding Google Cloud service (e.g., Compute Engine). see [API
   * references and supported searchable
   * attributes](https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types) to see which fields are included. You can search values of these
   * fields through free text search. However, you should not consume the field
   * programically as the field names and values may change as the Google Cloud
   * service updates to a new incompatible API version. To search against the
   * `additional_attributes`: * Use a free text query to match the attributes
   * values. Example: to search `additional_attributes = { dnsName: "foobar" }`,
   * you can issue a query `foobar`.
   *
   * @param array[] $additionalAttributes
   */
  public function setAdditionalAttributes($additionalAttributes)
  {
    $this->additionalAttributes = $additionalAttributes;
  }
  /**
   * @return array[]
   */
  public function getAdditionalAttributes()
  {
    return $this->additionalAttributes;
  }
  /**
   * The type of this resource. Example: `compute.googleapis.com/Disk`. To
   * search against the `asset_type`: * Specify the `asset_type` field in your
   * search request.
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
   * Attached resources of this resource. For example, an OSConfig Inventory is
   * an attached resource of a Compute Instance. This field is repeated because
   * a resource could have multiple attached resources. This
   * `attached_resources` field is not searchable. Some attributes of the
   * attached resources are exposed in `additional_attributes` field, so as to
   * allow users to search on them.
   *
   * @param AttachedResource[] $attachedResources
   */
  public function setAttachedResources($attachedResources)
  {
    $this->attachedResources = $attachedResources;
  }
  /**
   * @return AttachedResource[]
   */
  public function getAttachedResources()
  {
    return $this->attachedResources;
  }
  /**
   * The create timestamp of this resource, at which the resource was created.
   * The granularity is in seconds. Timestamp.nanos will always be 0. This field
   * is available only when the resource's Protobuf contains it. To search
   * against `create_time`: * Use a field query. - value in seconds since unix
   * epoch. Example: `createTime > 1609459200` - value in date string. Example:
   * `createTime > 2021-01-01` - value in date-time string (must be quoted).
   * Example: `createTime > "2021-01-01T00:00:00"`
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * One or more paragraphs of text description of this resource. Maximum length
   * could be up to 1M bytes. This field is available only when the resource's
   * Protobuf contains it. To search against the `description`: * Use a field
   * query. Example: `description:"important instance"` * Use a free text query.
   * Example: `"important instance"`
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
   * The display name of this resource. This field is available only when the
   * resource's Protobuf contains it. To search against the `display_name`: *
   * Use a field query. Example: `displayName:"My Instance"` * Use a free text
   * query. Example: `"My Instance"`
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
   * The effective tags on this resource. All of the tags that are both attached
   * to and inherited by a resource are collectively called the effective tags.
   * For more information, see [tag
   * inheritance](https://cloud.google.com/resource-manager/docs/tags/tags-
   * overview#inheritance). To search against the `effective_tags`: * Use a
   * field query. Example: - `effectiveTagKeys:"123456789/env*"` -
   * `effectiveTagKeys="123456789/env"` - `effectiveTagKeys:"env"` -
   * `effectiveTagKeyIds="tagKeys/123"` - `effectiveTagValues:"env"` -
   * `effectiveTagValues:"env/prod"` -
   * `effectiveTagValues:"123456789/env/prod*"` -
   * `effectiveTagValues="123456789/env/prod"` -
   * `effectiveTagValueIds="tagValues/456"`
   *
   * @param EffectiveTagDetails[] $effectiveTags
   */
  public function setEffectiveTags($effectiveTags)
  {
    $this->effectiveTags = $effectiveTags;
  }
  /**
   * @return EffectiveTagDetails[]
   */
  public function getEffectiveTags()
  {
    return $this->effectiveTags;
  }
  /**
   * Enrichments of the asset. Currently supported enrichment types with
   * SearchAllResources API: * RESOURCE_OWNERS The corresponding read masks in
   * order to get the enrichment: * enrichments.resource_owners The
   * corresponding required permissions: *
   * cloudasset.assets.searchEnrichmentResourceOwners Example query to get
   * resource owner enrichment: ``` scope: "projects/my-project" query: "name:
   * my-project" assetTypes: "cloudresourcemanager.googleapis.com/Project"
   * readMask: { paths: "asset_type" paths: "name" paths:
   * "enrichments.resource_owners" } ```
   *
   * @param AssetEnrichment[] $enrichments
   */
  public function setEnrichments($enrichments)
  {
    $this->enrichments = $enrichments;
  }
  /**
   * @return AssetEnrichment[]
   */
  public function getEnrichments()
  {
    return $this->enrichments;
  }
  /**
   * The folder(s) that this resource belongs to, in the form of
   * folders/{FOLDER_NUMBER}. This field is available when the resource belongs
   * to one or more folders. To search against `folders`: * Use a field query.
   * Example: `folders:(123 OR 456)` * Use a free text query. Example: `123` *
   * Specify the `scope` field as this folder in your search request.
   *
   * @param string[] $folders
   */
  public function setFolders($folders)
  {
    $this->folders = $folders;
  }
  /**
   * @return string[]
   */
  public function getFolders()
  {
    return $this->folders;
  }
  /**
   * The Cloud KMS [CryptoKey](https://cloud.google.com/kms/docs/reference/rest/
   * v1/projects.locations.keyRings.cryptoKeys) name or [CryptoKeyVersion](https
   * ://cloud.google.com/kms/docs/reference/rest/v1/projects.locations.keyRings.
   * cryptoKeys.cryptoKeyVersions) name. This field only presents for the
   * purpose of backward compatibility. Use the `kms_keys` field to retrieve
   * Cloud KMS key information. This field is available only when the resource's
   * Protobuf contains it and will only be populated for [these resource
   * types](https://cloud.google.com/asset-inventory/docs/legacy-field-
   * names#resource_types_with_the_to_be_deprecated_kmskey_field) for backward
   * compatible purposes. To search against the `kms_key`: * Use a field query.
   * Example: `kmsKey:key` * Use a free text query. Example: `key`
   *
   * @deprecated
   * @param string $kmsKey
   */
  public function setKmsKey($kmsKey)
  {
    $this->kmsKey = $kmsKey;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKmsKey()
  {
    return $this->kmsKey;
  }
  /**
   * The Cloud KMS [CryptoKey](https://cloud.google.com/kms/docs/reference/rest/
   * v1/projects.locations.keyRings.cryptoKeys) names or [CryptoKeyVersion](http
   * s://cloud.google.com/kms/docs/reference/rest/v1/projects.locations.keyRings
   * .cryptoKeys.cryptoKeyVersions) names. This field is available only when the
   * resource's Protobuf contains it. To search against the `kms_keys`: * Use a
   * field query. Example: `kmsKeys:key` * Use a free text query. Example: `key`
   *
   * @param string[] $kmsKeys
   */
  public function setKmsKeys($kmsKeys)
  {
    $this->kmsKeys = $kmsKeys;
  }
  /**
   * @return string[]
   */
  public function getKmsKeys()
  {
    return $this->kmsKeys;
  }
  /**
   * User labels associated with this resource. See [Labelling and grouping
   * Google Cloud
   * resources](https://cloud.google.com/blog/products/gcp/labelling-and-
   * grouping-your-google-cloud-platform-resources) for more information. This
   * field is available only when the resource's Protobuf contains it. To search
   * against the `labels`: * Use a field query: - query on any label's key or
   * value. Example: `labels:prod` - query by a given label. Example:
   * `labels.env:prod` - query by a given label's existence. Example:
   * `labels.env:*` * Use a free text query. Example: `prod`
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Location can be `global`, regional like `us-east1`, or zonal like `us-
   * west1-b`. This field is available only when the resource's Protobuf
   * contains it. To search against the `location`: * Use a field query.
   * Example: `location:us-west*` * Use a free text query. Example: `us-west*`
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The full resource name of this resource. Example: `//compute.googleapis.com
   * /projects/my_project_123/zones/zone1/instances/instance1`. See [Cloud Asset
   * Inventory Resource Name Format](https://cloud.google.com/asset-
   * inventory/docs/resource-name-format) for more information. To search
   * against the `name`: * Use a field query. Example: `name:instance1` * Use a
   * free text query. Example: `instance1`
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
   * Network tags associated with this resource. Like labels, network tags are a
   * type of annotations used to group Google Cloud resources. See [Labelling
   * Google Cloud
   * resources](https://cloud.google.com/blog/products/gcp/labelling-and-
   * grouping-your-google-cloud-platform-resources) for more information. This
   * field is available only when the resource's Protobuf contains it. To search
   * against the `network_tags`: * Use a field query. Example:
   * `networkTags:internal` * Use a free text query. Example: `internal`
   *
   * @param string[] $networkTags
   */
  public function setNetworkTags($networkTags)
  {
    $this->networkTags = $networkTags;
  }
  /**
   * @return string[]
   */
  public function getNetworkTags()
  {
    return $this->networkTags;
  }
  /**
   * The organization that this resource belongs to, in the form of
   * organizations/{ORGANIZATION_NUMBER}. This field is available when the
   * resource belongs to an organization. To search against `organization`: *
   * Use a field query. Example: `organization:123` * Use a free text query.
   * Example: `123` * Specify the `scope` field as this organization in your
   * search request.
   *
   * @param string $organization
   */
  public function setOrganization($organization)
  {
    $this->organization = $organization;
  }
  /**
   * @return string
   */
  public function getOrganization()
  {
    return $this->organization;
  }
  /**
   * The type of this resource's immediate parent, if there is one. To search
   * against the `parent_asset_type`: * Use a field query. Example:
   * `parentAssetType:"cloudresourcemanager.googleapis.com/Project"` * Use a
   * free text query. Example: `cloudresourcemanager.googleapis.com/Project`
   *
   * @param string $parentAssetType
   */
  public function setParentAssetType($parentAssetType)
  {
    $this->parentAssetType = $parentAssetType;
  }
  /**
   * @return string
   */
  public function getParentAssetType()
  {
    return $this->parentAssetType;
  }
  /**
   * The full resource name of this resource's parent, if it has one. To search
   * against the `parent_full_resource_name`: * Use a field query. Example:
   * `parentFullResourceName:"project-name"` * Use a free text query. Example:
   * `project-name`
   *
   * @param string $parentFullResourceName
   */
  public function setParentFullResourceName($parentFullResourceName)
  {
    $this->parentFullResourceName = $parentFullResourceName;
  }
  /**
   * @return string
   */
  public function getParentFullResourceName()
  {
    return $this->parentFullResourceName;
  }
  /**
   * The project that this resource belongs to, in the form of
   * projects/{PROJECT_NUMBER}. This field is available when the resource
   * belongs to a project. To search against `project`: * Use a field query.
   * Example: `project:12345` * Use a free text query. Example: `12345` *
   * Specify the `scope` field as this project in your search request.
   *
   * @param string $project
   */
  public function setProject($project)
  {
    $this->project = $project;
  }
  /**
   * @return string
   */
  public function getProject()
  {
    return $this->project;
  }
  /**
   * A map of related resources of this resource, keyed by the relationship
   * type. A relationship type is in the format of
   * {SourceType}_{ACTION}_{DestType}. Example: `DISK_TO_INSTANCE`,
   * `DISK_TO_NETWORK`, `INSTANCE_TO_INSTANCEGROUP`. See [supported relationship
   * types](https://cloud.google.com/asset-inventory/docs/supported-asset-
   * types#supported_relationship_types).
   *
   * @param RelatedResources[] $relationships
   */
  public function setRelationships($relationships)
  {
    $this->relationships = $relationships;
  }
  /**
   * @return RelatedResources[]
   */
  public function getRelationships()
  {
    return $this->relationships;
  }
  /**
   * The actual content of Security Command Center security marks associated
   * with the asset. To search against SCC SecurityMarks field: * Use a field
   * query: - query by a given key value pair. Example:
   * `sccSecurityMarks.foo=bar` - query by a given key's existence. Example:
   * `sccSecurityMarks.foo:*`
   *
   * @param string[] $sccSecurityMarks
   */
  public function setSccSecurityMarks($sccSecurityMarks)
  {
    $this->sccSecurityMarks = $sccSecurityMarks;
  }
  /**
   * @return string[]
   */
  public function getSccSecurityMarks()
  {
    return $this->sccSecurityMarks;
  }
  /**
   * The state of this resource. Different resources types have different state
   * definitions that are mapped from various fields of different resource
   * types. This field is available only when the resource's Protobuf contains
   * it. Example: If the resource is an instance provided by Compute Engine, its
   * state will include PROVISIONING, STAGING, RUNNING, STOPPING, SUSPENDING,
   * SUSPENDED, REPAIRING, and TERMINATED. See `status` definition in [API Refer
   * ence](https://cloud.google.com/compute/docs/reference/rest/v1/instances).
   * If the resource is a project provided by Resource Manager, its state will
   * include LIFECYCLE_STATE_UNSPECIFIED, ACTIVE, DELETE_REQUESTED and
   * DELETE_IN_PROGRESS. See `lifecycleState` definition in [API
   * Reference](https://cloud.google.com/resource-
   * manager/reference/rest/v1/projects). To search against the `state`: * Use a
   * field query. Example: `state:RUNNING` * Use a free text query. Example:
   * `RUNNING`
   *
   * @param string $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return string
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * This field is only present for the purpose of backward compatibility. Use
   * the `tags` field instead. TagKey namespaced names, in the format of
   * {ORG_ID}/{TAG_KEY_SHORT_NAME}. To search against the `tagKeys`: * Use a
   * field query. Example: - `tagKeys:"123456789/env*"` -
   * `tagKeys="123456789/env"` - `tagKeys:"env"` * Use a free text query.
   * Example: - `env`
   *
   * @deprecated
   * @param string[] $tagKeys
   */
  public function setTagKeys($tagKeys)
  {
    $this->tagKeys = $tagKeys;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getTagKeys()
  {
    return $this->tagKeys;
  }
  /**
   * This field is only present for the purpose of backward compatibility. Use
   * the `tags` field instead. TagValue IDs, in the format of
   * tagValues/{TAG_VALUE_ID}. To search against the `tagValueIds`: * Use a
   * field query. Example: - `tagValueIds="tagValues/456"` * Use a free text
   * query. Example: - `456`
   *
   * @deprecated
   * @param string[] $tagValueIds
   */
  public function setTagValueIds($tagValueIds)
  {
    $this->tagValueIds = $tagValueIds;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getTagValueIds()
  {
    return $this->tagValueIds;
  }
  /**
   * This field is only present for the purpose of backward compatibility. Use
   * the `tags` field instead. TagValue namespaced names, in the format of
   * {ORG_ID}/{TAG_KEY_SHORT_NAME}/{TAG_VALUE_SHORT_NAME}. To search against the
   * `tagValues`: * Use a field query. Example: - `tagValues:"env"` -
   * `tagValues:"env/prod"` - `tagValues:"123456789/env/prod*"` -
   * `tagValues="123456789/env/prod"` * Use a free text query. Example: - `prod`
   *
   * @deprecated
   * @param string[] $tagValues
   */
  public function setTagValues($tagValues)
  {
    $this->tagValues = $tagValues;
  }
  /**
   * @deprecated
   * @return string[]
   */
  public function getTagValues()
  {
    return $this->tagValues;
  }
  /**
   * The tags directly attached to this resource. To search against the `tags`:
   * * Use a field query. Example: - `tagKeys:"123456789/env*"` -
   * `tagKeys="123456789/env"` - `tagKeys:"env"` - `tagKeyIds="tagKeys/123"` -
   * `tagValues:"env"` - `tagValues:"env/prod"` -
   * `tagValues:"123456789/env/prod*"` - `tagValues="123456789/env/prod"` -
   * `tagValueIds="tagValues/456"` * Use a free text query. Example: -
   * `env/prod`
   *
   * @param Tag[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return Tag[]
   */
  public function getTags()
  {
    return $this->tags;
  }
  /**
   * The last update timestamp of this resource, at which the resource was last
   * modified or deleted. The granularity is in seconds. Timestamp.nanos will
   * always be 0. This field is available only when the resource's Protobuf
   * contains it. To search against `update_time`: * Use a field query. - value
   * in seconds since unix epoch. Example: `updateTime < 1609459200` - value in
   * date string. Example: `updateTime < 2021-01-01` - value in date-time string
   * (must be quoted). Example: `updateTime < "2021-01-01T00:00:00"`
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Versioned resource representations of this resource. This is repeated
   * because there could be multiple versions of resource representations during
   * version migration. This `versioned_resources` field is not searchable. Some
   * attributes of the resource representations are exposed in
   * `additional_attributes` field, so as to allow users to search on them.
   *
   * @param VersionedResource[] $versionedResources
   */
  public function setVersionedResources($versionedResources)
  {
    $this->versionedResources = $versionedResources;
  }
  /**
   * @return VersionedResource[]
   */
  public function getVersionedResources()
  {
    return $this->versionedResources;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceSearchResult::class, 'Google_Service_CloudAsset_ResourceSearchResult');
