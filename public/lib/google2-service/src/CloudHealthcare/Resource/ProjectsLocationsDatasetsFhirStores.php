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

namespace Google\Service\CloudHealthcare\Resource;

use Google\Service\CloudHealthcare\ApplyAdminConsentsRequest;
use Google\Service\CloudHealthcare\ApplyConsentsRequest;
use Google\Service\CloudHealthcare\DeidentifyFhirStoreRequest;
use Google\Service\CloudHealthcare\ExplainDataAccessResponse;
use Google\Service\CloudHealthcare\ExportResourcesRequest;
use Google\Service\CloudHealthcare\FhirStore;
use Google\Service\CloudHealthcare\FhirStoreMetrics;
use Google\Service\CloudHealthcare\HealthcareEmpty;
use Google\Service\CloudHealthcare\HttpBody;
use Google\Service\CloudHealthcare\ImportResourcesRequest;
use Google\Service\CloudHealthcare\ListFhirStoresResponse;
use Google\Service\CloudHealthcare\Operation;
use Google\Service\CloudHealthcare\Policy;
use Google\Service\CloudHealthcare\RollbackFhirResourcesRequest;
use Google\Service\CloudHealthcare\SetIamPolicyRequest;
use Google\Service\CloudHealthcare\TestIamPermissionsRequest;
use Google\Service\CloudHealthcare\TestIamPermissionsResponse;

/**
 * The "fhirStores" collection of methods.
 * Typical usage is:
 *  <code>
 *   $healthcareService = new Google\Service\CloudHealthcare(...);
 *   $fhirStores = $healthcareService->projects_locations_datasets_fhirStores;
 *  </code>
 */
class ProjectsLocationsDatasetsFhirStores extends \Google\Service\Resource
{
  /**
   * Applies the admin Consent resources for the FHIR store and reindexes the
   * underlying resources in the FHIR store according to the aggregate consents.
   * This method also updates the `consent_config.enforced_admin_consents` field
   * of the FhirStore unless `validate_only=true` in ApplyAdminConsentsRequest.
   * Any admin Consent resource change after this operation execution (including
   * deletion) requires you to call ApplyAdminConsents again for the change to
   * take effect. This method returns an Operation that can be used to track the
   * progress of the resources that were reindexed, by calling GetOperation. Upon
   * completion, the ApplyAdminConsentsResponse additionally contains the number
   * of resources that were reindexed. If at least one Consent resource contains
   * an error or fails be be enforced for any reason, the method returns an error
   * instead of an Operation. No resources will be reindexed and the
   * `consent_config.enforced_admin_consents` field will be unchanged. To enforce
   * a consent check for data access, `consent_config.access_enforced` must be set
   * to true for the FhirStore. FHIR Consent is not supported in DSTU2 or R5.
   * (fhirStores.applyAdminConsents)
   *
   * @param string $name Required. The name of the FHIR store to enforce, in the
   * format `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}/f
   * hirStores/{fhir_store_id}`.
   * @param ApplyAdminConsentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function applyAdminConsents($name, ApplyAdminConsentsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('applyAdminConsents', [$params], Operation::class);
  }
  /**
   * Apply the Consent resources for the FHIR store and reindex the underlying
   * resources in the FHIR store according to the aggregate consent. The aggregate
   * consent of the patient in scope in this request replaces any previous call of
   * this method. Any Consent resource change after this operation execution
   * (including deletion) requires you to call ApplyConsents again to have effect.
   * This method returns an Operation that can be used to track the progress of
   * the consent resources that were processed by calling GetOperation. Upon
   * completion, the ApplyConsentsResponse additionally contains the number of
   * resources that was reindexed. Errors are logged to Cloud Logging (see
   * [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)). To
   * enforce consent check for data access, `consent_config.access_enforced` must
   * be set to true for the FhirStore. FHIR Consent is not supported in DSTU2 or
   * R5. (fhirStores.applyConsents)
   *
   * @param string $name Required. The name of the FHIR store to enforce, in the
   * format `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}/f
   * hirStores/{fhir_store_id}`.
   * @param ApplyConsentsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function applyConsents($name, ApplyConsentsRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('applyConsents', [$params], Operation::class);
  }
  /**
   * Bulk exports a Group resource and resources in the member field, including
   * related resources for each Patient member. The export for each Patient is
   * identical to a GetPatientEverything request. Implements the FHIR
   * implementation guide [$export group of
   * patients](https://build.fhir.org/ig/HL7/bulk-data/export.html#endpoint---
   * group-of-patients). The following headers must be set in the request: *
   * `Accept`: specifies the format of the `OperationOutcome` response. Only
   * `application/fhir+json` is supported. * `Prefer`: specifies whether the
   * response is immediate or asynchronous. Must be to `respond-async` because
   * only asynchronous responses are supported. Specify the destination for the
   * server to write result files by setting the Cloud Storage location
   * bulk_export_gcs_destination on the FHIR store. URI of an existing Cloud
   * Storage directory where the server writes result files, in the format
   * gs://{bucket-id}/{path/to/destination/dir}. If there is no trailing slash,
   * the service appends one when composing the object path. The user is
   * responsible for creating the Cloud Storage bucket referenced. Supports the
   * following query parameters: * `_type`: string of comma-delimited FHIR
   * resource types. If provided, only resources of the specified type(s) are
   * exported. * `_since`: if provided, only resources updated after the specified
   * time are exported. * `_outputFormat`: optional, specify ndjson to export data
   * in NDJSON format. Exported file names use the format:
   * {export_id}_{resource_type}.ndjson. * `organizeOutputBy`: resource type to
   * organize the output by. Required and must be set to `Patient`. When
   * specified, output files are organized by instances of the specified resource
   * type, including the resource, referenced resources, and resources that
   * contain references to that resource. On success, the `Content-Location`
   * header of response is set to a URL that you can use to query the status of
   * the export. The URL is in the format `projects/{project_id}/locations/{locati
   * on_id}/datasets/{dataset_id}/fhirStores/{fhir_store_id}/operations/{export_id
   * }`. See get-fhir-operation-status for more information. Errors generated by
   * the FHIR store contain a JSON-encoded `OperationOutcome` resource describing
   * the reason for the error. (fhirStores.bulkExportGroup)
   *
   * @param string $name Required. Name of the Group resource that is exported, in
   * format `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}/f
   * hirStores/{fhir_store_id}/fhir/Group/{group_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string _since Optional. If provided, only resources updated after
   * this time are exported. The time uses the format YYYY-MM-
   * DDThh:mm:ss.sss+zz:zz. For example, `2015-02-07T13:28:17.239+02:00` or
   * `2017-01-01T00:00:00Z`. The time must be specified to the second and include
   * a time zone.
   * @opt_param string _type Optional. String of comma-delimited FHIR resource
   * types. If provided, only resources of the specified resource type(s) are
   * exported.
   * @opt_param string organizeOutputBy Required. The FHIR resource type used to
   * organize exported resources. Only supports "Patient". When organized by
   * Patient resource, output files are grouped as follows: * Patient file(s)
   * containing the Patient resources. Each Patient is sequentially followed by
   * all resources the Patient references, and all resources that reference the
   * Patient (equivalent to a GetPatientEverything request). * Individual files
   * grouped by resource type for resources in the Group's member field and the
   * Group resource itself. Resources may be duplicated across multiple Patients.
   * For example, if two Patient resources reference the same Organization
   * resource, it will appear twice, once after each Patient. The Group resource
   * from the request does not appear in the Patient files.
   * @opt_param string outputFormat Optional. Output format of the export. This
   * field is optional and only `application/fhir+ndjson` is supported.
   * @return HttpBody
   * @throws \Google\Service\Exception
   */
  public function bulkExportGroup($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('bulk-export-group', [$params], HttpBody::class);
  }
  /**
   * Creates a new FHIR store within the parent dataset. (fhirStores.create)
   *
   * @param string $parent Required. The name of the dataset this FHIR store
   * belongs to.
   * @param FhirStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string fhirStoreId Required. The ID of the FHIR store that is
   * being created. The string must match the following regex:
   * `[\p{L}\p{N}_\-\.]{1,256}`.
   * @return FhirStore
   * @throws \Google\Service\Exception
   */
  public function create($parent, FhirStore $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('create', [$params], FhirStore::class);
  }
  /**
   * De-identifies data from the source store and writes it to the destination
   * store. The metadata field type is OperationMetadata. If the request is
   * successful, the response field type is DeidentifyFhirStoreSummary. If errors
   * occur, error is set. Error details are also logged to Cloud Logging (see
   * [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
   * (fhirStores.deidentify)
   *
   * @param string $sourceStore Required. Source FHIR store resource name. For
   * example, `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}
   * /fhirStores/{fhir_store_id}`. R5 stores are not supported.
   * @param DeidentifyFhirStoreRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function deidentify($sourceStore, DeidentifyFhirStoreRequest $postBody, $optParams = [])
  {
    $params = ['sourceStore' => $sourceStore, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('deidentify', [$params], Operation::class);
  }
  /**
   * Deletes the specified FHIR store and removes all resources within it.
   * (fhirStores.delete)
   *
   * @param string $name Required. The resource name of the FHIR store to delete.
   * @param array $optParams Optional parameters.
   * @return HealthcareEmpty
   * @throws \Google\Service\Exception
   */
  public function delete($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], HealthcareEmpty::class);
  }
  /**
   * Explains all the permitted/denied actor, purpose and environment for a given
   * resource. FHIR Consent is not supported in DSTU2 or R5.
   * (fhirStores.explainDataAccess)
   *
   * @param string $name Required. The name of the FHIR store to enforce, in the
   * format `projects/{project_id}/locations/{location_id}/datasets/{dataset_id}/f
   * hirStores/{fhir_store_id}`.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string resourceId Required. The ID (`{resourceType}/{id}`) of the
   * resource to explain data access on.
   * @return ExplainDataAccessResponse
   * @throws \Google\Service\Exception
   */
  public function explainDataAccess($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('explainDataAccess', [$params], ExplainDataAccessResponse::class);
  }
  /**
   * Export resources from the FHIR store to the specified destination. This
   * method returns an Operation that can be used to track the status of the
   * export by calling GetOperation. To improve performance, it is recommended to
   * make the `type` filter as specific as possible, including only the resource
   * types that are absolutely needed. This minimizes the size of the initial
   * dataset to be processed and is the most effective way to improve performance.
   * While post-filters like `_since` are useful for refining results, they do not
   * speed up the initial data retrieval phase, which is primarily governed by the
   * `type` filter. Immediate fatal errors appear in the error field, errors are
   * also logged to Cloud Logging (see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
   * Otherwise, when the operation finishes, a detailed response of type
   * ExportResourcesResponse is returned in the response field. The metadata field
   * type for this operation is OperationMetadata. (fhirStores.export)
   *
   * @param string $name Required. The name of the FHIR store to export resource
   * from, in the format of `projects/{project_id}/locations/{location_id}/dataset
   * s/{dataset_id}/fhirStores/{fhir_store_id}`.
   * @param ExportResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function export($name, ExportResourcesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], Operation::class);
  }
  /**
   * Gets the configuration of the specified FHIR store. (fhirStores.get)
   *
   * @param string $name Required. The resource name of the FHIR store to get.
   * @param array $optParams Optional parameters.
   * @return FhirStore
   * @throws \Google\Service\Exception
   */
  public function get($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], FhirStore::class);
  }
  /**
   * Gets metrics associated with the FHIR store. (fhirStores.getFHIRStoreMetrics)
   *
   * @param string $name Required. The resource name of the FHIR store to get
   * metrics for.
   * @param array $optParams Optional parameters.
   * @return FhirStoreMetrics
   * @throws \Google\Service\Exception
   */
  public function getFHIRStoreMetrics($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getFHIRStoreMetrics', [$params], FhirStoreMetrics::class);
  }
  /**
   * Gets the access control policy for a resource. Returns an empty policy if the
   * resource exists and does not have a policy set. (fhirStores.getIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int options.requestedPolicyVersion Optional. The maximum policy
   * version that will be used to format the policy. Valid values are 0, 1, and 3.
   * Requests specifying an invalid value will be rejected. Requests for policies
   * with any conditional role bindings must specify version 3. Policies with no
   * conditional role bindings may specify any valid value or leave the field
   * unset. The policy in the response might use the policy version that you
   * specified, or it might use a lower policy version. For example, if you
   * specify version 3, but the policy has no conditional role bindings, the
   * response uses version 1. To learn which resources support conditions in their
   * IAM policies, see the [IAM
   * documentation](https://cloud.google.com/iam/help/conditions/resource-
   * policies).
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function getIamPolicy($resource, $optParams = [])
  {
    $params = ['resource' => $resource];
    $params = array_merge($params, $optParams);
    return $this->call('getIamPolicy', [$params], Policy::class);
  }
  /**
   * Imports resources to the FHIR store by loading data from the specified
   * sources. This method is optimized to load large quantities of data using
   * import semantics that ignore some FHIR store configuration options and are
   * not suitable for all use cases. It is primarily intended to load data into an
   * empty FHIR store that is not being used by other clients. In cases where this
   * method is not appropriate, consider using ExecuteBundle to load data. Every
   * resource in the input must contain a client-supplied ID. Each resource is
   * stored using the supplied ID regardless of the enable_update_create setting
   * on the FHIR store. It is strongly advised not to include or encode any
   * sensitive data such as patient identifiers in client-specified resource IDs.
   * Those IDs are part of the FHIR resource path recorded in Cloud Audit Logs and
   * Cloud Pub/Sub notifications. Those IDs can also be contained in reference
   * fields within other resources. The import process does not enforce
   * referential integrity, regardless of the disable_referential_integrity
   * setting on the FHIR store. This allows the import of resources with arbitrary
   * interdependencies without considering grouping or ordering, but if the input
   * data contains invalid references or if some resources fail to be imported,
   * the FHIR store might be left in a state that violates referential integrity.
   * The import process does not trigger Pub/Sub notification or BigQuery
   * streaming update, regardless of how those are configured on the FHIR store.
   * If a resource with the specified ID already exists, the most recent version
   * of the resource is overwritten without creating a new historical version,
   * regardless of the disable_resource_versioning setting on the FHIR store. If
   * transient failures occur during the import, it's possible that successfully
   * imported resources will be overwritten more than once. The import operation
   * is idempotent unless the input data contains multiple valid resources with
   * the same ID but different contents. In that case, after the import completes,
   * the store contains exactly one resource with that ID but there is no ordering
   * guarantee on which version of the contents it will have. The operation result
   * counters do not count duplicate IDs as an error and count one success for
   * each resource in the input, which might result in a success count larger than
   * the number of resources in the FHIR store. This often occurs when importing
   * data organized in bundles produced by Patient-everything where each bundle
   * contains its own copy of a resource such as Practitioner that might be
   * referred to by many patients. If some resources fail to import, for example
   * due to parsing errors, successfully imported resources are not rolled back.
   * The location and format of the input data is specified by the parameters in
   * ImportResourcesRequest. Note that if no format is specified, this method
   * assumes the `BUNDLE` format. When using the `BUNDLE` format this method
   * ignores the `Bundle.type` field, except that `history` bundles are rejected,
   * and does not apply any of the bundle processing semantics for batch or
   * transaction bundles. Unlike in ExecuteBundle, transaction bundles are not
   * executed as a single transaction and bundle-internal references are not
   * rewritten. The bundle is treated as a collection of resources to be written
   * as provided in `Bundle.entry.resource`, ignoring `Bundle.entry.request`. As
   * an example, this allows the import of `searchset` bundles produced by a FHIR
   * search or Patient-everything operation. This method returns an Operation that
   * can be used to track the status of the import by calling GetOperation.
   * Immediate fatal errors appear in the error field, errors are also logged to
   * Cloud Logging (see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
   * Otherwise, when the operation finishes, a detailed response of type
   * ImportResourcesResponse is returned in the response field. The metadata field
   * type for this operation is OperationMetadata. (fhirStores.import)
   *
   * @param string $name Required. The name of the FHIR store to import FHIR
   * resources to, in the format of `projects/{project_id}/locations/{location_id}
   * /datasets/{dataset_id}/fhirStores/{fhir_store_id}`.
   * @param ImportResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($name, ImportResourcesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Lists the FHIR stores in the given dataset.
   * (fhirStores.listProjectsLocationsDatasetsFhirStores)
   *
   * @param string $parent Required. Name of the dataset.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter Restricts stores returned to those matching a
   * filter. The following syntax is available: * A string field value can be
   * written as text inside quotation marks, for example `"query text"`. The only
   * valid relational operation for text fields is equality (`=`), where text is
   * searched within the field, rather than having the field be equal to the text.
   * For example, `"Comment = great"` returns messages with `great` in the comment
   * field. * A number field value can be written as an integer, a decimal, or an
   * exponential. The valid relational operators for number fields are the
   * equality operator (`=`), along with the less than/greater than operators
   * (`<`, `<=`, `>`, `>=`). Note that there is no inequality (`!=`) operator. You
   * can prepend the `NOT` operator to an expression to negate it. * A date field
   * value must be written in `yyyy-mm-dd` form. Fields with date and time use the
   * RFC3339 time format. Leading zeros are required for one-digit months and
   * days. The valid relational operators for date fields are the equality
   * operator (`=`) , along with the less than/greater than operators (`<`, `<=`,
   * `>`, `>=`). Note that there is no inequality (`!=`) operator. You can prepend
   * the `NOT` operator to an expression to negate it. * Multiple field query
   * expressions can be combined in one query by adding `AND` or `OR` operators
   * between the expressions. If a boolean operator appears within a quoted
   * string, it is not treated as special, it's just another part of the character
   * string to be matched. You can prepend the `NOT` operator to an expression to
   * negate it. Only filtering on labels is supported, for example
   * `labels.key=value`.
   * @opt_param int pageSize Limit on the number of FHIR stores to return in a
   * single response. If not specified, 100 is used. May not be larger than 1000.
   * @opt_param string pageToken The next_page_token value returned from the
   * previous List request, if any.
   * @return ListFhirStoresResponse
   * @throws \Google\Service\Exception
   */
  public function listProjectsLocationsDatasetsFhirStores($parent, $optParams = [])
  {
    $params = ['parent' => $parent];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], ListFhirStoresResponse::class);
  }
  /**
   * Updates the configuration of the specified FHIR store. (fhirStores.patch)
   *
   * @param string $name Output only. Identifier. Resource name of the FHIR store,
   * of the form `projects/{project_id}/locations/{location}/datasets/{dataset_id}
   * /fhirStores/{fhir_store_id}`.
   * @param FhirStore $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string updateMask Required. The update mask applies to the
   * resource. For the `FieldMask` definition, see
   * https://developers.google.com/protocol-
   * buffers/docs/reference/google.protobuf#fieldmask
   * @return FhirStore
   * @throws \Google\Service\Exception
   */
  public function patch($name, FhirStore $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], FhirStore::class);
  }
  /**
   * Rolls back resources from the FHIR store to the specified time. This method
   * returns an Operation that can be used to track the status of the rollback by
   * calling GetOperation. Immediate fatal errors appear in the error field,
   * errors are also logged to Cloud Logging (see [Viewing error logs in Cloud
   * Logging](https://cloud.google.com/healthcare/docs/how-tos/logging)).
   * Otherwise, when the operation finishes, a detailed response of type
   * RollbackFhirResourcesResponse is returned in the response field. The metadata
   * field type for this operation is OperationMetadata. (fhirStores.rollback)
   *
   * @param string $name Required. The name of the FHIR store to rollback, in the
   * format of
   * "projects/{project_id}/locations/{location_id}/datasets/{dataset_id}
   * /fhirStores/{fhir_store_id}".
   * @param RollbackFhirResourcesRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rollback($name, RollbackFhirResourcesRequest $postBody, $optParams = [])
  {
    $params = ['name' => $name, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rollback', [$params], Operation::class);
  }
  /**
   * Sets the access control policy on the specified resource. Replaces any
   * existing policy. Can return `NOT_FOUND`, `INVALID_ARGUMENT`, and
   * `PERMISSION_DENIED` errors. (fhirStores.setIamPolicy)
   *
   * @param string $resource REQUIRED: The resource for which the policy is being
   * specified. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param SetIamPolicyRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Policy
   * @throws \Google\Service\Exception
   */
  public function setIamPolicy($resource, SetIamPolicyRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('setIamPolicy', [$params], Policy::class);
  }
  /**
   * Returns permissions that a caller has on the specified resource. If the
   * resource does not exist, this will return an empty set of permissions, not a
   * `NOT_FOUND` error. Note: This operation is designed to be used for building
   * permission-aware UIs and command-line tools, not for authorization checking.
   * This operation may "fail open" without warning.
   * (fhirStores.testIamPermissions)
   *
   * @param string $resource REQUIRED: The resource for which the policy detail is
   * being requested. See [Resource
   * names](https://cloud.google.com/apis/design/resource_names) for the
   * appropriate value for this field.
   * @param TestIamPermissionsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return TestIamPermissionsResponse
   * @throws \Google\Service\Exception
   */
  public function testIamPermissions($resource, TestIamPermissionsRequest $postBody, $optParams = [])
  {
    $params = ['resource' => $resource, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('testIamPermissions', [$params], TestIamPermissionsResponse::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ProjectsLocationsDatasetsFhirStores::class, 'Google_Service_CloudHealthcare_Resource_ProjectsLocationsDatasetsFhirStores');
