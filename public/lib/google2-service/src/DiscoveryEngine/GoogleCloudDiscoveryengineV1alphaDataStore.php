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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1alphaDataStore extends \Google\Collection
{
  /**
   * Default value. For Spark and non-Spark non-configurable billing approach.
   */
  public const CONFIGURABLE_BILLING_APPROACH_CONFIGURABLE_BILLING_APPROACH_UNSPECIFIED = 'CONFIGURABLE_BILLING_APPROACH_UNSPECIFIED';
  /**
   * Use the subscription base + overage billing for indexing core for non
   * embedding storage.
   */
  public const CONFIGURABLE_BILLING_APPROACH_CONFIGURABLE_SUBSCRIPTION_INDEXING_CORE = 'CONFIGURABLE_SUBSCRIPTION_INDEXING_CORE';
  /**
   * Use the consumption pay-as-you-go billing for embedding storage add-on.
   */
  public const CONFIGURABLE_BILLING_APPROACH_CONFIGURABLE_CONSUMPTION_EMBEDDING = 'CONFIGURABLE_CONSUMPTION_EMBEDDING';
  /**
   * Default value.
   */
  public const CONTENT_CONFIG_CONTENT_CONFIG_UNSPECIFIED = 'CONTENT_CONFIG_UNSPECIFIED';
  /**
   * Only contains documents without any Document.content.
   */
  public const CONTENT_CONFIG_NO_CONTENT = 'NO_CONTENT';
  /**
   * Only contains documents with Document.content.
   */
  public const CONTENT_CONFIG_CONTENT_REQUIRED = 'CONTENT_REQUIRED';
  /**
   * The data store is used for public website search.
   */
  public const CONTENT_CONFIG_PUBLIC_WEBSITE = 'PUBLIC_WEBSITE';
  /**
   * The data store is used for workspace search. Details of workspace data
   * store are specified in the WorkspaceConfig.
   */
  public const CONTENT_CONFIG_GOOGLE_WORKSPACE = 'GOOGLE_WORKSPACE';
  /**
   * Value used when unset.
   */
  public const INDUSTRY_VERTICAL_INDUSTRY_VERTICAL_UNSPECIFIED = 'INDUSTRY_VERTICAL_UNSPECIFIED';
  /**
   * The generic vertical for documents that are not specific to any industry
   * vertical.
   */
  public const INDUSTRY_VERTICAL_GENERIC = 'GENERIC';
  /**
   * The media industry vertical.
   */
  public const INDUSTRY_VERTICAL_MEDIA = 'MEDIA';
  /**
   * The healthcare FHIR vertical.
   */
  public const INDUSTRY_VERTICAL_HEALTHCARE_FHIR = 'HEALTHCARE_FHIR';
  protected $collection_key = 'solutionTypes';
  /**
   * Immutable. Whether data in the DataStore has ACL information. If set to
   * `true`, the source data must have ACL. ACL will be ingested when data is
   * ingested by DocumentService.ImportDocuments methods. When ACL is enabled
   * for the DataStore, Document can't be accessed by calling
   * DocumentService.GetDocument or DocumentService.ListDocuments. Currently ACL
   * is only supported in `GENERIC` industry vertical with non-`PUBLIC_WEBSITE`
   * content config.
   *
   * @var bool
   */
  public $aclEnabled;
  protected $advancedSiteSearchConfigType = GoogleCloudDiscoveryengineV1alphaAdvancedSiteSearchConfig::class;
  protected $advancedSiteSearchConfigDataType = '';
  protected $billingEstimationType = GoogleCloudDiscoveryengineV1alphaDataStoreBillingEstimation::class;
  protected $billingEstimationDataType = '';
  protected $cmekConfigType = GoogleCloudDiscoveryengineV1alphaCmekConfig::class;
  protected $cmekConfigDataType = '';
  /**
   * Optional. Configuration for configurable billing approach. See
   *
   * @var string
   */
  public $configurableBillingApproach;
  /**
   * Output only. The timestamp when configurable_billing_approach was last
   * updated.
   *
   * @var string
   */
  public $configurableBillingApproachUpdateTime;
  /**
   * Immutable. The content config of the data store. If this field is unset,
   * the server behavior defaults to ContentConfig.NO_CONTENT.
   *
   * @var string
   */
  public $contentConfig;
  /**
   * Output only. Timestamp the DataStore was created at.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The id of the default Schema associated to this data store.
   *
   * @var string
   */
  public $defaultSchemaId;
  /**
   * Required. The data store display name. This field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
   *
   * @var string
   */
  public $displayName;
  protected $documentProcessingConfigType = GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig::class;
  protected $documentProcessingConfigDataType = '';
  protected $healthcareFhirConfigType = GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig::class;
  protected $healthcareFhirConfigDataType = '';
  /**
   * Immutable. The fully qualified resource name of the associated
   * IdentityMappingStore. This field can only be set for acl_enabled DataStores
   * with `THIRD_PARTY` or `GSUITE` IdP. Format: `projects/{project}/locations/{
   * location}/identityMappingStores/{identity_mapping_store}`.
   *
   * @var string
   */
  public $identityMappingStore;
  protected $idpConfigType = GoogleCloudDiscoveryengineV1alphaIdpConfig::class;
  protected $idpConfigDataType = '';
  /**
   * Immutable. The industry vertical that the data store registers.
   *
   * @var string
   */
  public $industryVertical;
  /**
   * Optional. If set, this DataStore is an Infobot FAQ DataStore.
   *
   * @var bool
   */
  public $isInfobotFaqDataStore;
  /**
   * Input only. The KMS key to be used to protect this DataStore at creation
   * time. Must be set for requests that need to comply with CMEK Org Policy
   * protections. If this field is set and processed successfully, the DataStore
   * will be protected by the KMS key, as indicated in the cmek_config field.
   *
   * @var string
   */
  public $kmsKeyName;
  protected $languageInfoType = GoogleCloudDiscoveryengineV1alphaLanguageInfo::class;
  protected $languageInfoDataType = '';
  /**
   * Immutable. Identifier. The full resource name of the data store. Format: `p
   * rojects/{project}/locations/{location}/collections/{collection_id}/dataStor
   * es/{data_store_id}`. This field must be a UTF-8 encoded string with a
   * length limit of 1024 characters.
   *
   * @var string
   */
  public $name;
  protected $naturalLanguageQueryUnderstandingConfigType = GoogleCloudDiscoveryengineV1alphaNaturalLanguageQueryUnderstandingConfig::class;
  protected $naturalLanguageQueryUnderstandingConfigDataType = '';
  protected $servingConfigDataStoreType = GoogleCloudDiscoveryengineV1alphaDataStoreServingConfigDataStore::class;
  protected $servingConfigDataStoreDataType = '';
  /**
   * The solutions that the data store enrolls. Available solutions for each
   * industry_vertical: * `MEDIA`: `SOLUTION_TYPE_RECOMMENDATION` and
   * `SOLUTION_TYPE_SEARCH`. * `SITE_SEARCH`: `SOLUTION_TYPE_SEARCH` is
   * automatically enrolled. Other solutions cannot be enrolled.
   *
   * @var string[]
   */
  public $solutionTypes;
  protected $startingSchemaType = GoogleCloudDiscoveryengineV1alphaSchema::class;
  protected $startingSchemaDataType = '';
  protected $workspaceConfigType = GoogleCloudDiscoveryengineV1alphaWorkspaceConfig::class;
  protected $workspaceConfigDataType = '';

  /**
   * Immutable. Whether data in the DataStore has ACL information. If set to
   * `true`, the source data must have ACL. ACL will be ingested when data is
   * ingested by DocumentService.ImportDocuments methods. When ACL is enabled
   * for the DataStore, Document can't be accessed by calling
   * DocumentService.GetDocument or DocumentService.ListDocuments. Currently ACL
   * is only supported in `GENERIC` industry vertical with non-`PUBLIC_WEBSITE`
   * content config.
   *
   * @param bool $aclEnabled
   */
  public function setAclEnabled($aclEnabled)
  {
    $this->aclEnabled = $aclEnabled;
  }
  /**
   * @return bool
   */
  public function getAclEnabled()
  {
    return $this->aclEnabled;
  }
  /**
   * Optional. Configuration for advanced site search.
   *
   * @param GoogleCloudDiscoveryengineV1alphaAdvancedSiteSearchConfig $advancedSiteSearchConfig
   */
  public function setAdvancedSiteSearchConfig(GoogleCloudDiscoveryengineV1alphaAdvancedSiteSearchConfig $advancedSiteSearchConfig)
  {
    $this->advancedSiteSearchConfig = $advancedSiteSearchConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaAdvancedSiteSearchConfig
   */
  public function getAdvancedSiteSearchConfig()
  {
    return $this->advancedSiteSearchConfig;
  }
  /**
   * Output only. Data size estimation for billing.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataStoreBillingEstimation $billingEstimation
   */
  public function setBillingEstimation(GoogleCloudDiscoveryengineV1alphaDataStoreBillingEstimation $billingEstimation)
  {
    $this->billingEstimation = $billingEstimation;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataStoreBillingEstimation
   */
  public function getBillingEstimation()
  {
    return $this->billingEstimation;
  }
  /**
   * Output only. CMEK-related information for the DataStore.
   *
   * @param GoogleCloudDiscoveryengineV1alphaCmekConfig $cmekConfig
   */
  public function setCmekConfig(GoogleCloudDiscoveryengineV1alphaCmekConfig $cmekConfig)
  {
    $this->cmekConfig = $cmekConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaCmekConfig
   */
  public function getCmekConfig()
  {
    return $this->cmekConfig;
  }
  /**
   * Optional. Configuration for configurable billing approach. See
   *
   * Accepted values: CONFIGURABLE_BILLING_APPROACH_UNSPECIFIED,
   * CONFIGURABLE_SUBSCRIPTION_INDEXING_CORE, CONFIGURABLE_CONSUMPTION_EMBEDDING
   *
   * @param self::CONFIGURABLE_BILLING_APPROACH_* $configurableBillingApproach
   */
  public function setConfigurableBillingApproach($configurableBillingApproach)
  {
    $this->configurableBillingApproach = $configurableBillingApproach;
  }
  /**
   * @return self::CONFIGURABLE_BILLING_APPROACH_*
   */
  public function getConfigurableBillingApproach()
  {
    return $this->configurableBillingApproach;
  }
  /**
   * Output only. The timestamp when configurable_billing_approach was last
   * updated.
   *
   * @param string $configurableBillingApproachUpdateTime
   */
  public function setConfigurableBillingApproachUpdateTime($configurableBillingApproachUpdateTime)
  {
    $this->configurableBillingApproachUpdateTime = $configurableBillingApproachUpdateTime;
  }
  /**
   * @return string
   */
  public function getConfigurableBillingApproachUpdateTime()
  {
    return $this->configurableBillingApproachUpdateTime;
  }
  /**
   * Immutable. The content config of the data store. If this field is unset,
   * the server behavior defaults to ContentConfig.NO_CONTENT.
   *
   * Accepted values: CONTENT_CONFIG_UNSPECIFIED, NO_CONTENT, CONTENT_REQUIRED,
   * PUBLIC_WEBSITE, GOOGLE_WORKSPACE
   *
   * @param self::CONTENT_CONFIG_* $contentConfig
   */
  public function setContentConfig($contentConfig)
  {
    $this->contentConfig = $contentConfig;
  }
  /**
   * @return self::CONTENT_CONFIG_*
   */
  public function getContentConfig()
  {
    return $this->contentConfig;
  }
  /**
   * Output only. Timestamp the DataStore was created at.
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
   * Output only. The id of the default Schema associated to this data store.
   *
   * @param string $defaultSchemaId
   */
  public function setDefaultSchemaId($defaultSchemaId)
  {
    $this->defaultSchemaId = $defaultSchemaId;
  }
  /**
   * @return string
   */
  public function getDefaultSchemaId()
  {
    return $this->defaultSchemaId;
  }
  /**
   * Required. The data store display name. This field must be a UTF-8 encoded
   * string with a length limit of 128 characters. Otherwise, an
   * INVALID_ARGUMENT error is returned.
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
   * Configuration for Document understanding and enrichment.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig $documentProcessingConfig
   */
  public function setDocumentProcessingConfig(GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig $documentProcessingConfig)
  {
    $this->documentProcessingConfig = $documentProcessingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDocumentProcessingConfig
   */
  public function getDocumentProcessingConfig()
  {
    return $this->documentProcessingConfig;
  }
  /**
   * Optional. Configuration for `HEALTHCARE_FHIR` vertical.
   *
   * @param GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig $healthcareFhirConfig
   */
  public function setHealthcareFhirConfig(GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig $healthcareFhirConfig)
  {
    $this->healthcareFhirConfig = $healthcareFhirConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaHealthcareFhirConfig
   */
  public function getHealthcareFhirConfig()
  {
    return $this->healthcareFhirConfig;
  }
  /**
   * Immutable. The fully qualified resource name of the associated
   * IdentityMappingStore. This field can only be set for acl_enabled DataStores
   * with `THIRD_PARTY` or `GSUITE` IdP. Format: `projects/{project}/locations/{
   * location}/identityMappingStores/{identity_mapping_store}`.
   *
   * @param string $identityMappingStore
   */
  public function setIdentityMappingStore($identityMappingStore)
  {
    $this->identityMappingStore = $identityMappingStore;
  }
  /**
   * @return string
   */
  public function getIdentityMappingStore()
  {
    return $this->identityMappingStore;
  }
  /**
   * Output only. Data store level identity provider config.
   *
   * @param GoogleCloudDiscoveryengineV1alphaIdpConfig $idpConfig
   */
  public function setIdpConfig(GoogleCloudDiscoveryengineV1alphaIdpConfig $idpConfig)
  {
    $this->idpConfig = $idpConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaIdpConfig
   */
  public function getIdpConfig()
  {
    return $this->idpConfig;
  }
  /**
   * Immutable. The industry vertical that the data store registers.
   *
   * Accepted values: INDUSTRY_VERTICAL_UNSPECIFIED, GENERIC, MEDIA,
   * HEALTHCARE_FHIR
   *
   * @param self::INDUSTRY_VERTICAL_* $industryVertical
   */
  public function setIndustryVertical($industryVertical)
  {
    $this->industryVertical = $industryVertical;
  }
  /**
   * @return self::INDUSTRY_VERTICAL_*
   */
  public function getIndustryVertical()
  {
    return $this->industryVertical;
  }
  /**
   * Optional. If set, this DataStore is an Infobot FAQ DataStore.
   *
   * @param bool $isInfobotFaqDataStore
   */
  public function setIsInfobotFaqDataStore($isInfobotFaqDataStore)
  {
    $this->isInfobotFaqDataStore = $isInfobotFaqDataStore;
  }
  /**
   * @return bool
   */
  public function getIsInfobotFaqDataStore()
  {
    return $this->isInfobotFaqDataStore;
  }
  /**
   * Input only. The KMS key to be used to protect this DataStore at creation
   * time. Must be set for requests that need to comply with CMEK Org Policy
   * protections. If this field is set and processed successfully, the DataStore
   * will be protected by the KMS key, as indicated in the cmek_config field.
   *
   * @param string $kmsKeyName
   */
  public function setKmsKeyName($kmsKeyName)
  {
    $this->kmsKeyName = $kmsKeyName;
  }
  /**
   * @return string
   */
  public function getKmsKeyName()
  {
    return $this->kmsKeyName;
  }
  /**
   * Language info for DataStore.
   *
   * @param GoogleCloudDiscoveryengineV1alphaLanguageInfo $languageInfo
   */
  public function setLanguageInfo(GoogleCloudDiscoveryengineV1alphaLanguageInfo $languageInfo)
  {
    $this->languageInfo = $languageInfo;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaLanguageInfo
   */
  public function getLanguageInfo()
  {
    return $this->languageInfo;
  }
  /**
   * Immutable. Identifier. The full resource name of the data store. Format: `p
   * rojects/{project}/locations/{location}/collections/{collection_id}/dataStor
   * es/{data_store_id}`. This field must be a UTF-8 encoded string with a
   * length limit of 1024 characters.
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
   * Optional. Configuration for Natural Language Query Understanding.
   *
   * @param GoogleCloudDiscoveryengineV1alphaNaturalLanguageQueryUnderstandingConfig $naturalLanguageQueryUnderstandingConfig
   */
  public function setNaturalLanguageQueryUnderstandingConfig(GoogleCloudDiscoveryengineV1alphaNaturalLanguageQueryUnderstandingConfig $naturalLanguageQueryUnderstandingConfig)
  {
    $this->naturalLanguageQueryUnderstandingConfig = $naturalLanguageQueryUnderstandingConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaNaturalLanguageQueryUnderstandingConfig
   */
  public function getNaturalLanguageQueryUnderstandingConfig()
  {
    return $this->naturalLanguageQueryUnderstandingConfig;
  }
  /**
   * Optional. Stores serving config at DataStore level.
   *
   * @param GoogleCloudDiscoveryengineV1alphaDataStoreServingConfigDataStore $servingConfigDataStore
   */
  public function setServingConfigDataStore(GoogleCloudDiscoveryengineV1alphaDataStoreServingConfigDataStore $servingConfigDataStore)
  {
    $this->servingConfigDataStore = $servingConfigDataStore;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaDataStoreServingConfigDataStore
   */
  public function getServingConfigDataStore()
  {
    return $this->servingConfigDataStore;
  }
  /**
   * The solutions that the data store enrolls. Available solutions for each
   * industry_vertical: * `MEDIA`: `SOLUTION_TYPE_RECOMMENDATION` and
   * `SOLUTION_TYPE_SEARCH`. * `SITE_SEARCH`: `SOLUTION_TYPE_SEARCH` is
   * automatically enrolled. Other solutions cannot be enrolled.
   *
   * @param string[] $solutionTypes
   */
  public function setSolutionTypes($solutionTypes)
  {
    $this->solutionTypes = $solutionTypes;
  }
  /**
   * @return string[]
   */
  public function getSolutionTypes()
  {
    return $this->solutionTypes;
  }
  /**
   * The start schema to use for this DataStore when provisioning it. If unset,
   * a default vertical specialized schema will be used. This field is only used
   * by CreateDataStore API, and will be ignored if used in other APIs. This
   * field will be omitted from all API responses including CreateDataStore API.
   * To retrieve a schema of a DataStore, use SchemaService.GetSchema API
   * instead. The provided schema will be validated against certain rules on
   * schema. Learn more from [this doc](https://cloud.google.com/generative-ai-
   * app-builder/docs/provide-schema).
   *
   * @param GoogleCloudDiscoveryengineV1alphaSchema $startingSchema
   */
  public function setStartingSchema(GoogleCloudDiscoveryengineV1alphaSchema $startingSchema)
  {
    $this->startingSchema = $startingSchema;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaSchema
   */
  public function getStartingSchema()
  {
    return $this->startingSchema;
  }
  /**
   * Config to store data store type configuration for workspace data. This must
   * be set when DataStore.content_config is set as
   * DataStore.ContentConfig.GOOGLE_WORKSPACE.
   *
   * @param GoogleCloudDiscoveryengineV1alphaWorkspaceConfig $workspaceConfig
   */
  public function setWorkspaceConfig(GoogleCloudDiscoveryengineV1alphaWorkspaceConfig $workspaceConfig)
  {
    $this->workspaceConfig = $workspaceConfig;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1alphaWorkspaceConfig
   */
  public function getWorkspaceConfig()
  {
    return $this->workspaceConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaDataStore::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaDataStore');
