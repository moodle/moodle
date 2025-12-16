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

namespace Google\Service\BigQueryDataPolicyService;

class DataPolicy extends \Google\Collection
{
  /**
   * Default value for the data policy type. This should not be used.
   */
  public const DATA_POLICY_TYPE_DATA_POLICY_TYPE_UNSPECIFIED = 'DATA_POLICY_TYPE_UNSPECIFIED';
  /**
   * Used to create a data policy for data masking.
   */
  public const DATA_POLICY_TYPE_DATA_MASKING_POLICY = 'DATA_MASKING_POLICY';
  /**
   * Used to create a data policy for raw data access.
   */
  public const DATA_POLICY_TYPE_RAW_DATA_ACCESS_POLICY = 'RAW_DATA_ACCESS_POLICY';
  /**
   * Used to create a data policy for column-level security, without data
   * masking. This is deprecated in V2 api and only present to support GET and
   * LIST operations for V1 data policies in V2 api.
   */
  public const DATA_POLICY_TYPE_COLUMN_LEVEL_SECURITY_POLICY = 'COLUMN_LEVEL_SECURITY_POLICY';
  /**
   * Default value for the data policy version. This should not be used.
   */
  public const VERSION_VERSION_UNSPECIFIED = 'VERSION_UNSPECIFIED';
  /**
   * V1 data policy version. V1 Data Policies will be present in V2 List api
   * response, but can not be created/updated/deleted from V2 api.
   */
  public const VERSION_V1 = 'V1';
  /**
   * V2 data policy version.
   */
  public const VERSION_V2 = 'V2';
  protected $collection_key = 'grantees';
  protected $dataMaskingPolicyType = DataMaskingPolicy::class;
  protected $dataMaskingPolicyDataType = '';
  /**
   * Output only. User-assigned (human readable) ID of the data policy that
   * needs to be unique within a project. Used as {data_policy_id} in part of
   * the resource name.
   *
   * @var string
   */
  public $dataPolicyId;
  /**
   * Required. Type of data policy.
   *
   * @var string
   */
  public $dataPolicyType;
  /**
   * The etag for this Data Policy. This field is used for UpdateDataPolicy
   * calls. If Data Policy exists, this field is required and must match the
   * server's etag. It will also be populated in the response of GetDataPolicy,
   * CreateDataPolicy, and UpdateDataPolicy calls.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The list of IAM principals that have Fine Grained Access to the
   * underlying data goverened by this data policy. Uses the [IAM V2 principal
   * syntax](https://cloud.google.com/iam/docs/principal-identifiers#v2) Only
   * supports principal types users, groups, serviceaccounts, cloudidentity.
   * This field is supported in V2 Data Policy only. In case of V1 data policies
   * (i.e. verion = 1 and policy_tag is set), this field is not populated.
   *
   * @var string[]
   */
  public $grantees;
  /**
   * Identifier. Resource name of this data policy, in the format of `projects/{
   * project_number}/locations/{location_id}/dataPolicies/{data_policy_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Policy tag resource name, in the format of `projects/{project_
   * number}/locations/{location_id}/taxonomies/{taxonomy_id}/policyTags/{policy
   * Tag_id}`. policy_tag is supported only for V1 data policies.
   *
   * @var string
   */
  public $policyTag;
  /**
   * Output only. The version of the Data Policy resource.
   *
   * @var string
   */
  public $version;

  /**
   * Optional. The data masking policy that specifies the data masking rule to
   * use. It must be set if the data policy type is DATA_MASKING_POLICY.
   *
   * @param DataMaskingPolicy $dataMaskingPolicy
   */
  public function setDataMaskingPolicy(DataMaskingPolicy $dataMaskingPolicy)
  {
    $this->dataMaskingPolicy = $dataMaskingPolicy;
  }
  /**
   * @return DataMaskingPolicy
   */
  public function getDataMaskingPolicy()
  {
    return $this->dataMaskingPolicy;
  }
  /**
   * Output only. User-assigned (human readable) ID of the data policy that
   * needs to be unique within a project. Used as {data_policy_id} in part of
   * the resource name.
   *
   * @param string $dataPolicyId
   */
  public function setDataPolicyId($dataPolicyId)
  {
    $this->dataPolicyId = $dataPolicyId;
  }
  /**
   * @return string
   */
  public function getDataPolicyId()
  {
    return $this->dataPolicyId;
  }
  /**
   * Required. Type of data policy.
   *
   * Accepted values: DATA_POLICY_TYPE_UNSPECIFIED, DATA_MASKING_POLICY,
   * RAW_DATA_ACCESS_POLICY, COLUMN_LEVEL_SECURITY_POLICY
   *
   * @param self::DATA_POLICY_TYPE_* $dataPolicyType
   */
  public function setDataPolicyType($dataPolicyType)
  {
    $this->dataPolicyType = $dataPolicyType;
  }
  /**
   * @return self::DATA_POLICY_TYPE_*
   */
  public function getDataPolicyType()
  {
    return $this->dataPolicyType;
  }
  /**
   * The etag for this Data Policy. This field is used for UpdateDataPolicy
   * calls. If Data Policy exists, this field is required and must match the
   * server's etag. It will also be populated in the response of GetDataPolicy,
   * CreateDataPolicy, and UpdateDataPolicy calls.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The list of IAM principals that have Fine Grained Access to the
   * underlying data goverened by this data policy. Uses the [IAM V2 principal
   * syntax](https://cloud.google.com/iam/docs/principal-identifiers#v2) Only
   * supports principal types users, groups, serviceaccounts, cloudidentity.
   * This field is supported in V2 Data Policy only. In case of V1 data policies
   * (i.e. verion = 1 and policy_tag is set), this field is not populated.
   *
   * @param string[] $grantees
   */
  public function setGrantees($grantees)
  {
    $this->grantees = $grantees;
  }
  /**
   * @return string[]
   */
  public function getGrantees()
  {
    return $this->grantees;
  }
  /**
   * Identifier. Resource name of this data policy, in the format of `projects/{
   * project_number}/locations/{location_id}/dataPolicies/{data_policy_id}`.
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
   * Output only. Policy tag resource name, in the format of `projects/{project_
   * number}/locations/{location_id}/taxonomies/{taxonomy_id}/policyTags/{policy
   * Tag_id}`. policy_tag is supported only for V1 data policies.
   *
   * @param string $policyTag
   */
  public function setPolicyTag($policyTag)
  {
    $this->policyTag = $policyTag;
  }
  /**
   * @return string
   */
  public function getPolicyTag()
  {
    return $this->policyTag;
  }
  /**
   * Output only. The version of the Data Policy resource.
   *
   * Accepted values: VERSION_UNSPECIFIED, V1, V2
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
class_alias(DataPolicy::class, 'Google_Service_BigQueryDataPolicyService_DataPolicy');
