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

namespace Google\Service\CloudIAP;

class IapResource extends \Google\Collection
{
  protected $collection_key = 'locations';
  /**
   * The proto or JSON formatted expected next state of the resource, wrapped in
   * a google.protobuf.Any proto, against which the policy rules are evaluated.
   * Services not integrated with custom org policy can omit this field.
   * Services integrated with custom org policy must populate this field for all
   * requests where the API call changes the state of the resource. Custom org
   * policy backend uses these attributes to enforce custom org policies. For
   * create operations, GCP service is expected to pass resource from customer
   * request as is. For update/patch operations, GCP service is expected to
   * compute the next state with the patch provided by the user. See
   * go/federated-custom-org-policy-integration-guide for additional details.
   *
   * @var array[]
   */
  public $expectedNextState;
  /**
   * The service defined labels of the resource on which the conditions will be
   * evaluated. The semantics - including the key names - are vague to IAM. If
   * the effective condition has a reference to a `resource.labels[foo]`
   * construct, IAM consults with this map to retrieve the values associated
   * with `foo` key for Conditions evaluation. If the provided key is not found
   * in the labels map, the condition would evaluate to false. This field is in
   * limited use. If your intended use case is not expected to express
   * resource.labels attribute in IAM Conditions, leave this field empty. Before
   * planning on using this attribute please: * Read go/iam-conditions-labels-
   * comm and ensure your service can meet the data availability and management
   * requirements. * Talk to iam-conditions-eng@ about your use case.
   *
   * @var string[]
   */
  public $labels;
  /**
   * The locations of the resource. This field is used to determine whether the
   * request is compliant with Trust Boundaries. Usage: - Must not be empty for
   * services in-scope for Trust Boundaries. Once Trust Boundaries is GA, empty
   * values will cause the request to be rejected if customers enforce Trust
   * Boundaries on the parent CRM nodes. - For global resources: use a single
   * value of "global". - For regional/multi-regional resources: use name of the
   * GCP region(s) where the resource exists (e.g., ["us-east1", "us-west1"]).
   * For multi-regional resources specify the name of each GCP region in the
   * resource's multi-region. NOTE: Only GCP cloud region names are supported -
   * go/cloud-region-names.
   *
   * @var string[]
   */
  public $locations;
  /**
   * The **relative** name of the resource, which is the URI path of the
   * resource without the leading "/". See
   * https://cloud.google.com/iam/docs/conditions-resource-attributes#resource-
   * name for examples used by other GCP Services. This field is **required**
   * for services integrated with resource-attribute-based IAM conditions and/or
   * CustomOrgPolicy. This field requires special handling for parents-only
   * permissions such as `create` and `list`. See the document linked below for
   * further details. See go/iam-conditions-sig-g3#populate-resource-attributes
   * for specific details on populating this field.
   *
   * @var string
   */
  public $name;
  protected $nextStateOfTagsType = NextStateOfTags::class;
  protected $nextStateOfTagsDataType = '';
  /**
   * The name of the service this resource belongs to. It is configured using
   * the official_service_name of the Service as defined in service
   * configurations under //configs/cloud/resourcetypes. For example, the
   * official_service_name of cloud resource manager service is set as
   * 'cloudresourcemanager.googleapis.com' according to
   * //configs/cloud/resourcetypes/google/cloud/resourcemanager/prod.yaml This
   * field is **required** for services integrated with resource-attribute-based
   * IAM conditions and/or CustomOrgPolicy. This field requires special handling
   * for parents-only permissions such as `create` and `list`. See the document
   * linked below for further details. See go/iam-conditions-sig-g3#populate-
   * resource-attributes for specific details on populating this field.
   *
   * @var string
   */
  public $service;
  /**
   * The public resource type name of the resource. It is configured using the
   * official_name of the ResourceType as defined in service configurations
   * under //configs/cloud/resourcetypes. For example, the official_name for GCP
   * projects is set as 'cloudresourcemanager.googleapis.com/Project' according
   * to //configs/cloud/resourcetypes/google/cloud/resourcemanager/prod.yaml
   * This field is **required** for services integrated with resource-attribute-
   * based IAM conditions and/or CustomOrgPolicy. This field requires special
   * handling for parents-only permissions such as `create` and `list`. See the
   * document linked below for further details. See go/iam-conditions-
   * sig-g3#populate-resource-attributes for specific details on populating this
   * field.
   *
   * @var string
   */
  public $type;

  /**
   * The proto or JSON formatted expected next state of the resource, wrapped in
   * a google.protobuf.Any proto, against which the policy rules are evaluated.
   * Services not integrated with custom org policy can omit this field.
   * Services integrated with custom org policy must populate this field for all
   * requests where the API call changes the state of the resource. Custom org
   * policy backend uses these attributes to enforce custom org policies. For
   * create operations, GCP service is expected to pass resource from customer
   * request as is. For update/patch operations, GCP service is expected to
   * compute the next state with the patch provided by the user. See
   * go/federated-custom-org-policy-integration-guide for additional details.
   *
   * @param array[] $expectedNextState
   */
  public function setExpectedNextState($expectedNextState)
  {
    $this->expectedNextState = $expectedNextState;
  }
  /**
   * @return array[]
   */
  public function getExpectedNextState()
  {
    return $this->expectedNextState;
  }
  /**
   * The service defined labels of the resource on which the conditions will be
   * evaluated. The semantics - including the key names - are vague to IAM. If
   * the effective condition has a reference to a `resource.labels[foo]`
   * construct, IAM consults with this map to retrieve the values associated
   * with `foo` key for Conditions evaluation. If the provided key is not found
   * in the labels map, the condition would evaluate to false. This field is in
   * limited use. If your intended use case is not expected to express
   * resource.labels attribute in IAM Conditions, leave this field empty. Before
   * planning on using this attribute please: * Read go/iam-conditions-labels-
   * comm and ensure your service can meet the data availability and management
   * requirements. * Talk to iam-conditions-eng@ about your use case.
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
   * The locations of the resource. This field is used to determine whether the
   * request is compliant with Trust Boundaries. Usage: - Must not be empty for
   * services in-scope for Trust Boundaries. Once Trust Boundaries is GA, empty
   * values will cause the request to be rejected if customers enforce Trust
   * Boundaries on the parent CRM nodes. - For global resources: use a single
   * value of "global". - For regional/multi-regional resources: use name of the
   * GCP region(s) where the resource exists (e.g., ["us-east1", "us-west1"]).
   * For multi-regional resources specify the name of each GCP region in the
   * resource's multi-region. NOTE: Only GCP cloud region names are supported -
   * go/cloud-region-names.
   *
   * @param string[] $locations
   */
  public function setLocations($locations)
  {
    $this->locations = $locations;
  }
  /**
   * @return string[]
   */
  public function getLocations()
  {
    return $this->locations;
  }
  /**
   * The **relative** name of the resource, which is the URI path of the
   * resource without the leading "/". See
   * https://cloud.google.com/iam/docs/conditions-resource-attributes#resource-
   * name for examples used by other GCP Services. This field is **required**
   * for services integrated with resource-attribute-based IAM conditions and/or
   * CustomOrgPolicy. This field requires special handling for parents-only
   * permissions such as `create` and `list`. See the document linked below for
   * further details. See go/iam-conditions-sig-g3#populate-resource-attributes
   * for specific details on populating this field.
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
   * Used for calculating the next state of tags on the resource being passed
   * for Custom Org Policy enforcement. NOTE: Only one of the tags
   * representations (i.e. numeric or namespaced) should be populated. The input
   * tags will be converted to the same representation before the calculation.
   * This behavior intentionally may differ from other tags related fields in
   * CheckPolicy request, which may require both formats to be passed in.
   * IMPORTANT: If tags are unchanged, this field should not be set.
   *
   * @param NextStateOfTags $nextStateOfTags
   */
  public function setNextStateOfTags(NextStateOfTags $nextStateOfTags)
  {
    $this->nextStateOfTags = $nextStateOfTags;
  }
  /**
   * @return NextStateOfTags
   */
  public function getNextStateOfTags()
  {
    return $this->nextStateOfTags;
  }
  /**
   * The name of the service this resource belongs to. It is configured using
   * the official_service_name of the Service as defined in service
   * configurations under //configs/cloud/resourcetypes. For example, the
   * official_service_name of cloud resource manager service is set as
   * 'cloudresourcemanager.googleapis.com' according to
   * //configs/cloud/resourcetypes/google/cloud/resourcemanager/prod.yaml This
   * field is **required** for services integrated with resource-attribute-based
   * IAM conditions and/or CustomOrgPolicy. This field requires special handling
   * for parents-only permissions such as `create` and `list`. See the document
   * linked below for further details. See go/iam-conditions-sig-g3#populate-
   * resource-attributes for specific details on populating this field.
   *
   * @param string $service
   */
  public function setService($service)
  {
    $this->service = $service;
  }
  /**
   * @return string
   */
  public function getService()
  {
    return $this->service;
  }
  /**
   * The public resource type name of the resource. It is configured using the
   * official_name of the ResourceType as defined in service configurations
   * under //configs/cloud/resourcetypes. For example, the official_name for GCP
   * projects is set as 'cloudresourcemanager.googleapis.com/Project' according
   * to //configs/cloud/resourcetypes/google/cloud/resourcemanager/prod.yaml
   * This field is **required** for services integrated with resource-attribute-
   * based IAM conditions and/or CustomOrgPolicy. This field requires special
   * handling for parents-only permissions such as `create` and `list`. See the
   * document linked below for further details. See go/iam-conditions-
   * sig-g3#populate-resource-attributes for specific details on populating this
   * field.
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IapResource::class, 'Google_Service_CloudIAP_IapResource');
