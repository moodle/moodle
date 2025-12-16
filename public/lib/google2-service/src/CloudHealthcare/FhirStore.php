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

namespace Google\Service\CloudHealthcare;

class FhirStore extends \Google\Collection
{
  /**
   * No parsing behavior specified. This is the same as DISABLED for backwards
   * compatibility.
   */
  public const COMPLEX_DATA_TYPE_REFERENCE_PARSING_COMPLEX_DATA_TYPE_REFERENCE_PARSING_UNSPECIFIED = 'COMPLEX_DATA_TYPE_REFERENCE_PARSING_UNSPECIFIED';
  /**
   * References in complex data types are ignored.
   */
  public const COMPLEX_DATA_TYPE_REFERENCE_PARSING_DISABLED = 'DISABLED';
  /**
   * References in complex data types are parsed.
   */
  public const COMPLEX_DATA_TYPE_REFERENCE_PARSING_ENABLED = 'ENABLED';
  /**
   * Users must specify a version on store creation or an error is returned.
   */
  public const VERSION_VERSION_UNSPECIFIED = 'VERSION_UNSPECIFIED';
  /**
   * Draft Standard for Trial Use, [Release 2](https://www.hl7.org/fhir/DSTU2)
   */
  public const VERSION_DSTU2 = 'DSTU2';
  /**
   * Standard for Trial Use, [Release 3](https://www.hl7.org/fhir/STU3)
   */
  public const VERSION_STU3 = 'STU3';
  /**
   * [Release 4](https://www.hl7.org/fhir/R4)
   */
  public const VERSION_R4 = 'R4';
  /**
   * [Release 5](https://www.hl7.org/fhir/R5)
   */
  public const VERSION_R5 = 'R5';
  protected $collection_key = 'streamConfigs';
  protected $bulkExportGcsDestinationType = BulkExportGcsDestination::class;
  protected $bulkExportGcsDestinationDataType = '';
  /**
   * Optional. Enable parsing of references within complex FHIR data types such
   * as Extensions. If this value is set to ENABLED, then features like
   * referential integrity and Bundle reference rewriting apply to all
   * references. If this flag has not been specified the behavior of the FHIR
   * store will not change, references in complex data types will not be parsed.
   * New stores will have this value set to ENABLED after a notification period.
   * Warning: turning on this flag causes processing existing resources to fail
   * if they contain references to non-existent resources. Cannot be disabled in
   * R5.
   *
   * @var string
   */
  public $complexDataTypeReferenceParsing;
  protected $consentConfigType = ConsentConfig::class;
  protected $consentConfigDataType = '';
  /**
   * Optional. If true, overrides the default search behavior for this FHIR
   * store to `handling=strict` which returns an error for unrecognized search
   * parameters. If false, uses the FHIR specification default
   * `handling=lenient` which ignores unrecognized search parameters. The
   * handling can always be changed from the default on an individual API call
   * by setting the HTTP header `Prefer: handling=strict` or `Prefer:
   * handling=lenient`. Defaults to false.
   *
   * @var bool
   */
  public $defaultSearchHandlingStrict;
  /**
   * Immutable. Whether to disable referential integrity in this FHIR store.
   * This field is immutable after FHIR store creation. The default value is
   * false, meaning that the API enforces referential integrity and fails the
   * requests that result in inconsistent state in the FHIR store. When this
   * field is set to true, the API skips referential integrity checks.
   * Consequently, operations that rely on references, such as
   * GetPatientEverything, do not return all the results if broken references
   * exist.
   *
   * @var bool
   */
  public $disableReferentialIntegrity;
  /**
   * Immutable. Whether to disable resource versioning for this FHIR store. This
   * field can not be changed after the creation of FHIR store. If set to false,
   * all write operations cause historical versions to be recorded
   * automatically. The historical versions can be fetched through the history
   * APIs, but cannot be updated. If set to true, no historical versions are
   * kept. The server sends errors for attempts to read the historical versions.
   * Defaults to false.
   *
   * @var bool
   */
  public $disableResourceVersioning;
  /**
   * Optional. Whether this FHIR store has the [updateCreate
   * capability](https://www.hl7.org/fhir/capabilitystatement-
   * definitions.html#CapabilityStatement.rest.resource.updateCreate). This
   * determines if the client can use an Update operation to create a new
   * resource with a client-specified ID. If false, all IDs are server-assigned
   * through the Create operation and attempts to update a non-existent resource
   * return errors. It is strongly advised not to include or encode any
   * sensitive data such as patient identifiers in client-specified resource
   * IDs. Those IDs are part of the FHIR resource path recorded in Cloud audit
   * logs and Pub/Sub notifications. Those IDs can also be contained in
   * reference fields within other resources. Defaults to false.
   *
   * @var bool
   */
  public $enableUpdateCreate;
  /**
   * User-supplied key-value pairs used to organize FHIR stores. Label keys must
   * be between 1 and 63 characters long, have a UTF-8 encoding of maximum 128
   * bytes, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}{0,62} Label values are optional, must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. Resource name of the FHIR store, of the form `proj
   * ects/{project_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{fh
   * ir_store_id}`.
   *
   * @var string
   */
  public $name;
  protected $notificationConfigType = NotificationConfig::class;
  protected $notificationConfigDataType = '';
  protected $notificationConfigsType = FhirNotificationConfig::class;
  protected $notificationConfigsDataType = 'array';
  protected $streamConfigsType = StreamConfig::class;
  protected $streamConfigsDataType = 'array';
  protected $validationConfigType = ValidationConfig::class;
  protected $validationConfigDataType = '';
  /**
   * Required. Immutable. The FHIR specification version that this FHIR store
   * supports natively. This field is immutable after store creation. Requests
   * are rejected if they contain FHIR resources of a different version. Version
   * is required for every FHIR store.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. FHIR bulk export exports resources to the specified Cloud Storage
   * destination. A Cloud Storage destination is a URI for a Cloud Storage
   * directory where result files will be written. Only used in the spec-defined
   * bulk $export methods. The Cloud Healthcare Service Agent requires the
   * `roles/storage.objectAdmin` Cloud IAM role on the destination.
   *
   * @param BulkExportGcsDestination $bulkExportGcsDestination
   */
  public function setBulkExportGcsDestination(BulkExportGcsDestination $bulkExportGcsDestination)
  {
    $this->bulkExportGcsDestination = $bulkExportGcsDestination;
  }
  /**
   * @return BulkExportGcsDestination
   */
  public function getBulkExportGcsDestination()
  {
    return $this->bulkExportGcsDestination;
  }
  /**
   * Optional. Enable parsing of references within complex FHIR data types such
   * as Extensions. If this value is set to ENABLED, then features like
   * referential integrity and Bundle reference rewriting apply to all
   * references. If this flag has not been specified the behavior of the FHIR
   * store will not change, references in complex data types will not be parsed.
   * New stores will have this value set to ENABLED after a notification period.
   * Warning: turning on this flag causes processing existing resources to fail
   * if they contain references to non-existent resources. Cannot be disabled in
   * R5.
   *
   * Accepted values: COMPLEX_DATA_TYPE_REFERENCE_PARSING_UNSPECIFIED, DISABLED,
   * ENABLED
   *
   * @param self::COMPLEX_DATA_TYPE_REFERENCE_PARSING_* $complexDataTypeReferenceParsing
   */
  public function setComplexDataTypeReferenceParsing($complexDataTypeReferenceParsing)
  {
    $this->complexDataTypeReferenceParsing = $complexDataTypeReferenceParsing;
  }
  /**
   * @return self::COMPLEX_DATA_TYPE_REFERENCE_PARSING_*
   */
  public function getComplexDataTypeReferenceParsing()
  {
    return $this->complexDataTypeReferenceParsing;
  }
  /**
   * Optional. Specifies whether this store has consent enforcement. Not
   * available for DSTU2 FHIR version due to absence of Consent resources. Not
   * supported for R5 FHIR version.
   *
   * @param ConsentConfig $consentConfig
   */
  public function setConsentConfig(ConsentConfig $consentConfig)
  {
    $this->consentConfig = $consentConfig;
  }
  /**
   * @return ConsentConfig
   */
  public function getConsentConfig()
  {
    return $this->consentConfig;
  }
  /**
   * Optional. If true, overrides the default search behavior for this FHIR
   * store to `handling=strict` which returns an error for unrecognized search
   * parameters. If false, uses the FHIR specification default
   * `handling=lenient` which ignores unrecognized search parameters. The
   * handling can always be changed from the default on an individual API call
   * by setting the HTTP header `Prefer: handling=strict` or `Prefer:
   * handling=lenient`. Defaults to false.
   *
   * @param bool $defaultSearchHandlingStrict
   */
  public function setDefaultSearchHandlingStrict($defaultSearchHandlingStrict)
  {
    $this->defaultSearchHandlingStrict = $defaultSearchHandlingStrict;
  }
  /**
   * @return bool
   */
  public function getDefaultSearchHandlingStrict()
  {
    return $this->defaultSearchHandlingStrict;
  }
  /**
   * Immutable. Whether to disable referential integrity in this FHIR store.
   * This field is immutable after FHIR store creation. The default value is
   * false, meaning that the API enforces referential integrity and fails the
   * requests that result in inconsistent state in the FHIR store. When this
   * field is set to true, the API skips referential integrity checks.
   * Consequently, operations that rely on references, such as
   * GetPatientEverything, do not return all the results if broken references
   * exist.
   *
   * @param bool $disableReferentialIntegrity
   */
  public function setDisableReferentialIntegrity($disableReferentialIntegrity)
  {
    $this->disableReferentialIntegrity = $disableReferentialIntegrity;
  }
  /**
   * @return bool
   */
  public function getDisableReferentialIntegrity()
  {
    return $this->disableReferentialIntegrity;
  }
  /**
   * Immutable. Whether to disable resource versioning for this FHIR store. This
   * field can not be changed after the creation of FHIR store. If set to false,
   * all write operations cause historical versions to be recorded
   * automatically. The historical versions can be fetched through the history
   * APIs, but cannot be updated. If set to true, no historical versions are
   * kept. The server sends errors for attempts to read the historical versions.
   * Defaults to false.
   *
   * @param bool $disableResourceVersioning
   */
  public function setDisableResourceVersioning($disableResourceVersioning)
  {
    $this->disableResourceVersioning = $disableResourceVersioning;
  }
  /**
   * @return bool
   */
  public function getDisableResourceVersioning()
  {
    return $this->disableResourceVersioning;
  }
  /**
   * Optional. Whether this FHIR store has the [updateCreate
   * capability](https://www.hl7.org/fhir/capabilitystatement-
   * definitions.html#CapabilityStatement.rest.resource.updateCreate). This
   * determines if the client can use an Update operation to create a new
   * resource with a client-specified ID. If false, all IDs are server-assigned
   * through the Create operation and attempts to update a non-existent resource
   * return errors. It is strongly advised not to include or encode any
   * sensitive data such as patient identifiers in client-specified resource
   * IDs. Those IDs are part of the FHIR resource path recorded in Cloud audit
   * logs and Pub/Sub notifications. Those IDs can also be contained in
   * reference fields within other resources. Defaults to false.
   *
   * @param bool $enableUpdateCreate
   */
  public function setEnableUpdateCreate($enableUpdateCreate)
  {
    $this->enableUpdateCreate = $enableUpdateCreate;
  }
  /**
   * @return bool
   */
  public function getEnableUpdateCreate()
  {
    return $this->enableUpdateCreate;
  }
  /**
   * User-supplied key-value pairs used to organize FHIR stores. Label keys must
   * be between 1 and 63 characters long, have a UTF-8 encoding of maximum 128
   * bytes, and must conform to the following PCRE regular expression:
   * \p{Ll}\p{Lo}{0,62} Label values are optional, must be between 1 and 63
   * characters long, have a UTF-8 encoding of maximum 128 bytes, and must
   * conform to the following PCRE regular expression:
   * [\p{Ll}\p{Lo}\p{N}_-]{0,63} No more than 64 labels can be associated with a
   * given store.
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
   * Output only. Identifier. Resource name of the FHIR store, of the form `proj
   * ects/{project_id}/locations/{location}/datasets/{dataset_id}/fhirStores/{fh
   * ir_store_id}`.
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
   * Deprecated. Use `notification_configs` instead. If non-empty, publish all
   * resource modifications of this FHIR store to this destination. The Pub/Sub
   * message attributes contain a map with a string describing the action that
   * has triggered the notification. For example, "action":"CreateResource". Not
   * supported in R5. Use `notification_configs` instead.
   *
   * @deprecated
   * @param NotificationConfig $notificationConfig
   */
  public function setNotificationConfig(NotificationConfig $notificationConfig)
  {
    $this->notificationConfig = $notificationConfig;
  }
  /**
   * @deprecated
   * @return NotificationConfig
   */
  public function getNotificationConfig()
  {
    return $this->notificationConfig;
  }
  /**
   * Optional. Specifies where and whether to send notifications upon changes to
   * a FHIR store.
   *
   * @param FhirNotificationConfig[] $notificationConfigs
   */
  public function setNotificationConfigs($notificationConfigs)
  {
    $this->notificationConfigs = $notificationConfigs;
  }
  /**
   * @return FhirNotificationConfig[]
   */
  public function getNotificationConfigs()
  {
    return $this->notificationConfigs;
  }
  /**
   * Optional. A list of streaming configs that configure the destinations of
   * streaming export for every resource mutation in this FHIR store. Each store
   * is allowed to have up to 10 streaming configs. After a new config is added,
   * the next resource mutation is streamed to the new location in addition to
   * the existing ones. When a location is removed from the list, the server
   * stops streaming to that location. Before adding a new config, you must add
   * the required
   * [`bigquery.dataEditor`](https://cloud.google.com/bigquery/docs/access-
   * control#bigquery.dataEditor) role to your project's **Cloud Healthcare
   * Service Agent** [service
   * account](https://cloud.google.com/iam/docs/service-accounts). Some lag
   * (typically on the order of dozens of seconds) is expected before the
   * results show up in the streaming destination.
   *
   * @param StreamConfig[] $streamConfigs
   */
  public function setStreamConfigs($streamConfigs)
  {
    $this->streamConfigs = $streamConfigs;
  }
  /**
   * @return StreamConfig[]
   */
  public function getStreamConfigs()
  {
    return $this->streamConfigs;
  }
  /**
   * Optional. Configuration for how to validate incoming FHIR resources against
   * configured profiles.
   *
   * @param ValidationConfig $validationConfig
   */
  public function setValidationConfig(ValidationConfig $validationConfig)
  {
    $this->validationConfig = $validationConfig;
  }
  /**
   * @return ValidationConfig
   */
  public function getValidationConfig()
  {
    return $this->validationConfig;
  }
  /**
   * Required. Immutable. The FHIR specification version that this FHIR store
   * supports natively. This field is immutable after store creation. Requests
   * are rejected if they contain FHIR resources of a different version. Version
   * is required for every FHIR store.
   *
   * Accepted values: VERSION_UNSPECIFIED, DSTU2, STU3, R4, R5
   *
   * @param self::VERSION_* $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return self::VERSION_*
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FhirStore::class, 'Google_Service_CloudHealthcare_FhirStore');
