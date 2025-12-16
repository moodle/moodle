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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataAttributeBinding extends \Google\Collection
{
  protected $collection_key = 'paths';
  /**
   * Optional. List of attributes to be associated with the resource, provided
   * in the form: projects/{project}/locations/{location}/dataTaxonomies/{dataTa
   * xonomy}/attributes/{data_attribute_id}
   *
   * @var string[]
   */
  public $attributes;
  /**
   * Output only. The time when the DataAttributeBinding was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Description of the DataAttributeBinding.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. User friendly display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. Etags must be used when calling the
   * DeleteDataAttributeBinding and the UpdateDataAttributeBinding method.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. User-defined labels for the DataAttributeBinding.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The relative resource name of the Data Attribute Binding, of
   * the form: projects/{project_number}/locations/{location}/dataAttributeBindi
   * ngs/{data_attribute_binding_id}
   *
   * @var string
   */
  public $name;
  protected $pathsType = GoogleCloudDataplexV1DataAttributeBindingPath::class;
  protected $pathsDataType = 'array';
  /**
   * Optional. Immutable. The resource name of the resource that is associated
   * to attributes. Presently, only entity resource is supported in the form: pr
   * ojects/{project}/locations/{location}/lakes/{lake}/zones/{zone}/entities/{e
   * ntity_id} Must belong in the same project and region as the attribute
   * binding, and there can only exist one active binding for a resource.
   *
   * @var string
   */
  public $resource;
  /**
   * Output only. System generated globally unique ID for the
   * DataAttributeBinding. This ID will be different if the DataAttributeBinding
   * is deleted and re-created with the same name.
   *
   * @var string
   */
  public $uid;
  /**
   * Output only. The time when the DataAttributeBinding was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. List of attributes to be associated with the resource, provided
   * in the form: projects/{project}/locations/{location}/dataTaxonomies/{dataTa
   * xonomy}/attributes/{data_attribute_id}
   *
   * @param string[] $attributes
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * Output only. The time when the DataAttributeBinding was created.
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
   * Optional. Description of the DataAttributeBinding.
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
   * Optional. User friendly display name.
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
   * This checksum is computed by the server based on the value of other fields,
   * and may be sent on update and delete requests to ensure the client has an
   * up-to-date value before proceeding. Etags must be used when calling the
   * DeleteDataAttributeBinding and the UpdateDataAttributeBinding method.
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
   * Optional. User-defined labels for the DataAttributeBinding.
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
   * Output only. The relative resource name of the Data Attribute Binding, of
   * the form: projects/{project_number}/locations/{location}/dataAttributeBindi
   * ngs/{data_attribute_binding_id}
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
   * Optional. The list of paths for items within the associated resource (eg.
   * columns and partitions within a table) along with attribute bindings.
   *
   * @param GoogleCloudDataplexV1DataAttributeBindingPath[] $paths
   */
  public function setPaths($paths)
  {
    $this->paths = $paths;
  }
  /**
   * @return GoogleCloudDataplexV1DataAttributeBindingPath[]
   */
  public function getPaths()
  {
    return $this->paths;
  }
  /**
   * Optional. Immutable. The resource name of the resource that is associated
   * to attributes. Presently, only entity resource is supported in the form: pr
   * ojects/{project}/locations/{location}/lakes/{lake}/zones/{zone}/entities/{e
   * ntity_id} Must belong in the same project and region as the attribute
   * binding, and there can only exist one active binding for a resource.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Output only. System generated globally unique ID for the
   * DataAttributeBinding. This ID will be different if the DataAttributeBinding
   * is deleted and re-created with the same name.
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Output only. The time when the DataAttributeBinding was last updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDataplexV1DataAttributeBinding::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataAttributeBinding');
