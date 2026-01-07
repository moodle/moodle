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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1ApiProduct extends \Google\Collection
{
  /**
   * When quota is not explicitly defined for each operation(REST/GraphQL), the
   * limits set at product level will be used as a local counter for quota
   * evaluation by all the operations, independent of proxy association.
   */
  public const QUOTA_COUNTER_SCOPE_QUOTA_COUNTER_SCOPE_UNSPECIFIED = 'QUOTA_COUNTER_SCOPE_UNSPECIFIED';
  /**
   * When quota is not explicitly defined for each operation(REST/GraphQL), set
   * at product level will be used as a global counter for quota evaluation by
   * all the operations associated with a particular proxy.
   */
  public const QUOTA_COUNTER_SCOPE_PROXY = 'PROXY';
  /**
   * When quota is not explicitly defined for each operation(REST/GraphQL), the
   * limits set at product level will be used as a local counter for quota
   * evaluation by all the operations, independent of proxy association. This
   * behavior mimics the same as QUOTA_COUNTER_SCOPE_UNSPECIFIED.
   */
  public const QUOTA_COUNTER_SCOPE_OPERATION = 'OPERATION';
  /**
   * When quota is not explicitly defined for each operation(REST/GraphQL), the
   * limits set at product level will be used as a global counter for quota
   * evaluation by all the operations.
   */
  public const QUOTA_COUNTER_SCOPE_PRODUCT = 'PRODUCT';
  protected $collection_key = 'scopes';
  /**
   * Comma-separated list of API resources to be bundled in the API product. By
   * default, the resource paths are mapped from the `proxy.pathsuffix`
   * variable. The proxy path suffix is defined as the URI fragment following
   * the ProxyEndpoint base path. For example, if the `apiResources` element is
   * defined to be `/forecastrss` and the base path defined for the API proxy is
   * `/weather`, then only requests to `/weather/forecastrss` are permitted by
   * the API product. You can select a specific path, or you can select all
   * subpaths with the following wildcard: - `*`: Indicates that all sub-URIs
   * are included. - `` : Indicates that only URIs one level down are included.
   * By default, / supports the same resources as * as well as the base path
   * defined by the API proxy. For example, if the base path of the API proxy is
   * `/v1/weatherapikey`, then the API product supports requests to
   * `/v1/weatherapikey` and to any sub-URIs, such as
   * `/v1/weatherapikey/forecastrss`, `/v1/weatherapikey/region/CA`, and so on.
   * For more information, see Managing API products.
   *
   * @var string[]
   */
  public $apiResources;
  /**
   * Flag that specifies how API keys are approved to access the APIs defined by
   * the API product. If set to `manual`, the consumer key is generated and
   * returned in "pending" state. In this case, the API keys won't work until
   * they have been explicitly approved. If set to `auto`, the consumer key is
   * generated and returned in "approved" state and can be used immediately.
   * **Note:** Typically, `auto` is used to provide access to free or trial API
   * products that provide limited quota or capabilities.
   *
   * @var string
   */
  public $approvalType;
  protected $attributesType = GoogleCloudApigeeV1Attribute::class;
  protected $attributesDataType = 'array';
  /**
   * Response only. Creation time of this environment as milliseconds since
   * epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Description of the API product. Include key information about the API
   * product that is not captured by other fields.
   *
   * @var string
   */
  public $description;
  /**
   * Name displayed in the UI or developer portal to developers registering for
   * API access.
   *
   * @var string
   */
  public $displayName;
  /**
   * Comma-separated list of environment names to which the API product is
   * bound. Requests to environments that are not listed are rejected. By
   * specifying one or more environments, you can bind the resources listed in
   * the API product to a specific environment, preventing developers from
   * accessing those resources through API proxies deployed in another
   * environment. This setting is used, for example, to prevent resources
   * associated with API proxies in `prod` from being accessed by API proxies
   * deployed in `test`.
   *
   * @var string[]
   */
  public $environments;
  protected $graphqlOperationGroupType = GoogleCloudApigeeV1GraphQLOperationGroup::class;
  protected $graphqlOperationGroupDataType = '';
  protected $grpcOperationGroupType = GoogleCloudApigeeV1GrpcOperationGroup::class;
  protected $grpcOperationGroupDataType = '';
  /**
   * Response only. Modified time of this environment as milliseconds since
   * epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  protected $llmOperationGroupType = GoogleCloudApigeeV1LlmOperationGroup::class;
  protected $llmOperationGroupDataType = '';
  /**
   * Optional. Number of LLM tokens permitted per app by this API product for
   * the specified `llm_quota_interval` and `llm_quota_time_unit`. For example,
   * an `llm_quota` of 50,000, for an `llm_quota_interval` of 12 and an
   * `llm_quota_time_unit` of hours means 50,000 llm tokens are allowed to be
   * used every 12 hours.
   *
   * @var string
   */
  public $llmQuota;
  /**
   * Optional. Time interval over which the number of tokens from LLM responses
   * is calculated.
   *
   * @var string
   */
  public $llmQuotaInterval;
  /**
   * Optional. Time unit defined for the `llm_quota_interval`. Valid values
   * include `minute`, `hour`, `day`, or `month`.
   *
   * @var string
   */
  public $llmQuotaTimeUnit;
  /**
   * Internal name of the API product. Characters you can use in the name are
   * restricted to: `A-Z0-9._\-$ %`. **Note:** The internal name cannot be
   * edited when updating the API product.
   *
   * @var string
   */
  public $name;
  protected $operationGroupType = GoogleCloudApigeeV1OperationGroup::class;
  protected $operationGroupDataType = '';
  /**
   * Comma-separated list of API proxy names to which this API product is bound.
   * By specifying API proxies, you can associate resources in the API product
   * with specific API proxies, preventing developers from accessing those
   * resources through other API proxies. Apigee rejects requests to API proxies
   * that are not listed. **Note:** The API proxy names must already exist in
   * the specified environment as they will be validated upon creation.
   *
   * @var string[]
   */
  public $proxies;
  /**
   * Number of request messages permitted per app by this API product for the
   * specified `quotaInterval` and `quotaTimeUnit`. For example, a `quota` of
   * 50, for a `quotaInterval` of 12 and a `quotaTimeUnit` of hours means 50
   * requests are allowed every 12 hours.
   *
   * @var string
   */
  public $quota;
  /**
   * Scope of the quota decides how the quota counter gets applied and evaluate
   * for quota violation. If the Scope is set as PROXY, then all the operations
   * defined for the APIproduct that are associated with the same proxy will
   * share the same quota counter set at the APIproduct level, making it a
   * global counter at a proxy level. If the Scope is set as OPERATION, then
   * each operations get the counter set at the API product dedicated, making it
   * a local counter. Note that, the QuotaCounterScope applies only when an
   * operation does not have dedicated quota set for itself.
   *
   * @var string
   */
  public $quotaCounterScope;
  /**
   * Time interval over which the number of request messages is calculated.
   *
   * @var string
   */
  public $quotaInterval;
  /**
   * Time unit defined for the `quotaInterval`. Valid values include `minute`,
   * `hour`, `day`, or `month`.
   *
   * @var string
   */
  public $quotaTimeUnit;
  /**
   * Comma-separated list of OAuth scopes that are validated at runtime. Apigee
   * validates that the scopes in any access token presented match the scopes
   * defined in the OAuth policy associated with the API product.
   *
   * @var string[]
   */
  public $scopes;
  /**
   * Optional. The resource ID of the parent Space. If not set, the parent
   * resource will be the Organization. To learn how Spaces can be used to
   * manage resources, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   *
   * @var string
   */
  public $space;

  /**
   * Comma-separated list of API resources to be bundled in the API product. By
   * default, the resource paths are mapped from the `proxy.pathsuffix`
   * variable. The proxy path suffix is defined as the URI fragment following
   * the ProxyEndpoint base path. For example, if the `apiResources` element is
   * defined to be `/forecastrss` and the base path defined for the API proxy is
   * `/weather`, then only requests to `/weather/forecastrss` are permitted by
   * the API product. You can select a specific path, or you can select all
   * subpaths with the following wildcard: - `*`: Indicates that all sub-URIs
   * are included. - `` : Indicates that only URIs one level down are included.
   * By default, / supports the same resources as * as well as the base path
   * defined by the API proxy. For example, if the base path of the API proxy is
   * `/v1/weatherapikey`, then the API product supports requests to
   * `/v1/weatherapikey` and to any sub-URIs, such as
   * `/v1/weatherapikey/forecastrss`, `/v1/weatherapikey/region/CA`, and so on.
   * For more information, see Managing API products.
   *
   * @param string[] $apiResources
   */
  public function setApiResources($apiResources)
  {
    $this->apiResources = $apiResources;
  }
  /**
   * @return string[]
   */
  public function getApiResources()
  {
    return $this->apiResources;
  }
  /**
   * Flag that specifies how API keys are approved to access the APIs defined by
   * the API product. If set to `manual`, the consumer key is generated and
   * returned in "pending" state. In this case, the API keys won't work until
   * they have been explicitly approved. If set to `auto`, the consumer key is
   * generated and returned in "approved" state and can be used immediately.
   * **Note:** Typically, `auto` is used to provide access to free or trial API
   * products that provide limited quota or capabilities.
   *
   * @param string $approvalType
   */
  public function setApprovalType($approvalType)
  {
    $this->approvalType = $approvalType;
  }
  /**
   * @return string
   */
  public function getApprovalType()
  {
    return $this->approvalType;
  }
  /**
   * Array of attributes that may be used to extend the default API product
   * profile with customer-specific metadata. You can specify a maximum of 18
   * attributes. Use this property to specify the access level of the API
   * product as either `public`, `private`, or `internal`. Only products marked
   * `public` are available to developers in the Apigee developer portal. For
   * example, you can set a product to `internal` while it is in development and
   * then change access to `public` when it is ready to release on the portal.
   * API products marked as `private` do not appear on the portal, but can be
   * accessed by external developers.
   *
   * @param GoogleCloudApigeeV1Attribute[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return GoogleCloudApigeeV1Attribute[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Response only. Creation time of this environment as milliseconds since
   * epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Description of the API product. Include key information about the API
   * product that is not captured by other fields.
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
   * Name displayed in the UI or developer portal to developers registering for
   * API access.
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
   * Comma-separated list of environment names to which the API product is
   * bound. Requests to environments that are not listed are rejected. By
   * specifying one or more environments, you can bind the resources listed in
   * the API product to a specific environment, preventing developers from
   * accessing those resources through API proxies deployed in another
   * environment. This setting is used, for example, to prevent resources
   * associated with API proxies in `prod` from being accessed by API proxies
   * deployed in `test`.
   *
   * @param string[] $environments
   */
  public function setEnvironments($environments)
  {
    $this->environments = $environments;
  }
  /**
   * @return string[]
   */
  public function getEnvironments()
  {
    return $this->environments;
  }
  /**
   * Configuration used to group Apigee proxies or remote services with graphQL
   * operation name, graphQL operation type and quotas. This grouping allows us
   * to precisely set quota for a particular combination of graphQL name and
   * operation type for a particular proxy request. If graphQL name is not set,
   * this would imply quota will be applied on all graphQL requests matching the
   * operation type.
   *
   * @param GoogleCloudApigeeV1GraphQLOperationGroup $graphqlOperationGroup
   */
  public function setGraphqlOperationGroup(GoogleCloudApigeeV1GraphQLOperationGroup $graphqlOperationGroup)
  {
    $this->graphqlOperationGroup = $graphqlOperationGroup;
  }
  /**
   * @return GoogleCloudApigeeV1GraphQLOperationGroup
   */
  public function getGraphqlOperationGroup()
  {
    return $this->graphqlOperationGroup;
  }
  /**
   * Optional. Configuration used to group Apigee proxies with gRPC services and
   * method names. This grouping allows us to set quota for a particular proxy
   * with the gRPC service name and method. If a method name is not set, this
   * implies quota and authorization are applied to all gRPC methods implemented
   * by that proxy for that particular gRPC service.
   *
   * @param GoogleCloudApigeeV1GrpcOperationGroup $grpcOperationGroup
   */
  public function setGrpcOperationGroup(GoogleCloudApigeeV1GrpcOperationGroup $grpcOperationGroup)
  {
    $this->grpcOperationGroup = $grpcOperationGroup;
  }
  /**
   * @return GoogleCloudApigeeV1GrpcOperationGroup
   */
  public function getGrpcOperationGroup()
  {
    return $this->grpcOperationGroup;
  }
  /**
   * Response only. Modified time of this environment as milliseconds since
   * epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * Optional. Configuration used to group Apigee proxies with resources, method
   * types, LLM model and quotas. The resource refers to the resource URI
   * (excluding the base path). With this grouping, the API product creator is
   * able to fine-tune and give precise control over which REST methods have
   * access to specific resources, specific LLM model and how many calls can be
   * made (using the `quota` setting). **Note:** The `api_resources` setting
   * cannot be specified for both the API product and llm operation group;
   * otherwise the call will fail.
   *
   * @param GoogleCloudApigeeV1LlmOperationGroup $llmOperationGroup
   */
  public function setLlmOperationGroup(GoogleCloudApigeeV1LlmOperationGroup $llmOperationGroup)
  {
    $this->llmOperationGroup = $llmOperationGroup;
  }
  /**
   * @return GoogleCloudApigeeV1LlmOperationGroup
   */
  public function getLlmOperationGroup()
  {
    return $this->llmOperationGroup;
  }
  /**
   * Optional. Number of LLM tokens permitted per app by this API product for
   * the specified `llm_quota_interval` and `llm_quota_time_unit`. For example,
   * an `llm_quota` of 50,000, for an `llm_quota_interval` of 12 and an
   * `llm_quota_time_unit` of hours means 50,000 llm tokens are allowed to be
   * used every 12 hours.
   *
   * @param string $llmQuota
   */
  public function setLlmQuota($llmQuota)
  {
    $this->llmQuota = $llmQuota;
  }
  /**
   * @return string
   */
  public function getLlmQuota()
  {
    return $this->llmQuota;
  }
  /**
   * Optional. Time interval over which the number of tokens from LLM responses
   * is calculated.
   *
   * @param string $llmQuotaInterval
   */
  public function setLlmQuotaInterval($llmQuotaInterval)
  {
    $this->llmQuotaInterval = $llmQuotaInterval;
  }
  /**
   * @return string
   */
  public function getLlmQuotaInterval()
  {
    return $this->llmQuotaInterval;
  }
  /**
   * Optional. Time unit defined for the `llm_quota_interval`. Valid values
   * include `minute`, `hour`, `day`, or `month`.
   *
   * @param string $llmQuotaTimeUnit
   */
  public function setLlmQuotaTimeUnit($llmQuotaTimeUnit)
  {
    $this->llmQuotaTimeUnit = $llmQuotaTimeUnit;
  }
  /**
   * @return string
   */
  public function getLlmQuotaTimeUnit()
  {
    return $this->llmQuotaTimeUnit;
  }
  /**
   * Internal name of the API product. Characters you can use in the name are
   * restricted to: `A-Z0-9._\-$ %`. **Note:** The internal name cannot be
   * edited when updating the API product.
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
   * Configuration used to group Apigee proxies or remote services with
   * resources, method types, and quotas. The resource refers to the resource
   * URI (excluding the base path). With this grouping, the API product creator
   * is able to fine-tune and give precise control over which REST methods have
   * access to specific resources and how many calls can be made (using the
   * `quota` setting). **Note:** The `api_resources` setting cannot be specified
   * for both the API product and operation group; otherwise the call will fail.
   *
   * @param GoogleCloudApigeeV1OperationGroup $operationGroup
   */
  public function setOperationGroup(GoogleCloudApigeeV1OperationGroup $operationGroup)
  {
    $this->operationGroup = $operationGroup;
  }
  /**
   * @return GoogleCloudApigeeV1OperationGroup
   */
  public function getOperationGroup()
  {
    return $this->operationGroup;
  }
  /**
   * Comma-separated list of API proxy names to which this API product is bound.
   * By specifying API proxies, you can associate resources in the API product
   * with specific API proxies, preventing developers from accessing those
   * resources through other API proxies. Apigee rejects requests to API proxies
   * that are not listed. **Note:** The API proxy names must already exist in
   * the specified environment as they will be validated upon creation.
   *
   * @param string[] $proxies
   */
  public function setProxies($proxies)
  {
    $this->proxies = $proxies;
  }
  /**
   * @return string[]
   */
  public function getProxies()
  {
    return $this->proxies;
  }
  /**
   * Number of request messages permitted per app by this API product for the
   * specified `quotaInterval` and `quotaTimeUnit`. For example, a `quota` of
   * 50, for a `quotaInterval` of 12 and a `quotaTimeUnit` of hours means 50
   * requests are allowed every 12 hours.
   *
   * @param string $quota
   */
  public function setQuota($quota)
  {
    $this->quota = $quota;
  }
  /**
   * @return string
   */
  public function getQuota()
  {
    return $this->quota;
  }
  /**
   * Scope of the quota decides how the quota counter gets applied and evaluate
   * for quota violation. If the Scope is set as PROXY, then all the operations
   * defined for the APIproduct that are associated with the same proxy will
   * share the same quota counter set at the APIproduct level, making it a
   * global counter at a proxy level. If the Scope is set as OPERATION, then
   * each operations get the counter set at the API product dedicated, making it
   * a local counter. Note that, the QuotaCounterScope applies only when an
   * operation does not have dedicated quota set for itself.
   *
   * Accepted values: QUOTA_COUNTER_SCOPE_UNSPECIFIED, PROXY, OPERATION, PRODUCT
   *
   * @param self::QUOTA_COUNTER_SCOPE_* $quotaCounterScope
   */
  public function setQuotaCounterScope($quotaCounterScope)
  {
    $this->quotaCounterScope = $quotaCounterScope;
  }
  /**
   * @return self::QUOTA_COUNTER_SCOPE_*
   */
  public function getQuotaCounterScope()
  {
    return $this->quotaCounterScope;
  }
  /**
   * Time interval over which the number of request messages is calculated.
   *
   * @param string $quotaInterval
   */
  public function setQuotaInterval($quotaInterval)
  {
    $this->quotaInterval = $quotaInterval;
  }
  /**
   * @return string
   */
  public function getQuotaInterval()
  {
    return $this->quotaInterval;
  }
  /**
   * Time unit defined for the `quotaInterval`. Valid values include `minute`,
   * `hour`, `day`, or `month`.
   *
   * @param string $quotaTimeUnit
   */
  public function setQuotaTimeUnit($quotaTimeUnit)
  {
    $this->quotaTimeUnit = $quotaTimeUnit;
  }
  /**
   * @return string
   */
  public function getQuotaTimeUnit()
  {
    return $this->quotaTimeUnit;
  }
  /**
   * Comma-separated list of OAuth scopes that are validated at runtime. Apigee
   * validates that the scopes in any access token presented match the scopes
   * defined in the OAuth policy associated with the API product.
   *
   * @param string[] $scopes
   */
  public function setScopes($scopes)
  {
    $this->scopes = $scopes;
  }
  /**
   * @return string[]
   */
  public function getScopes()
  {
    return $this->scopes;
  }
  /**
   * Optional. The resource ID of the parent Space. If not set, the parent
   * resource will be the Organization. To learn how Spaces can be used to
   * manage resources, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   *
   * @param string $space
   */
  public function setSpace($space)
  {
    $this->space = $space;
  }
  /**
   * @return string
   */
  public function getSpace()
  {
    return $this->space;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1ApiProduct::class, 'Google_Service_Apigee_GoogleCloudApigeeV1ApiProduct');
