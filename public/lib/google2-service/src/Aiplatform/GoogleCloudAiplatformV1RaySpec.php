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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1RaySpec extends \Google\Model
{
  /**
   * Optional. This will be used to indicate which resource pool will serve as
   * the Ray head node(the first node within that pool). Will use the machine
   * from the first workerpool as the head node by default if this field isn't
   * set.
   *
   * @var string
   */
  public $headNodeResourcePoolId;
  /**
   * Optional. Default image for user to choose a preferred ML framework (for
   * example, TensorFlow or Pytorch) by choosing from [Vertex prebuilt
   * images](https://cloud.google.com/vertex-ai/docs/training/pre-built-
   * containers). Either this or the resource_pool_images is required. Use this
   * field if you need all the resource pools to have the same Ray image.
   * Otherwise, use the {@code resource_pool_images} field.
   *
   * @var string
   */
  public $imageUri;
  protected $rayLogsSpecType = GoogleCloudAiplatformV1RayLogsSpec::class;
  protected $rayLogsSpecDataType = '';
  protected $rayMetricSpecType = GoogleCloudAiplatformV1RayMetricSpec::class;
  protected $rayMetricSpecDataType = '';
  /**
   * Optional. Required if image_uri isn't set. A map of resource_pool_id to
   * prebuild Ray image if user need to use different images for different
   * head/worker pools. This map needs to cover all the resource pool ids.
   * Example: { "ray_head_node_pool": "head image" "ray_worker_node_pool1":
   * "worker image" "ray_worker_node_pool2": "another worker image" }
   *
   * @var string[]
   */
  public $resourcePoolImages;

  /**
   * Optional. This will be used to indicate which resource pool will serve as
   * the Ray head node(the first node within that pool). Will use the machine
   * from the first workerpool as the head node by default if this field isn't
   * set.
   *
   * @param string $headNodeResourcePoolId
   */
  public function setHeadNodeResourcePoolId($headNodeResourcePoolId)
  {
    $this->headNodeResourcePoolId = $headNodeResourcePoolId;
  }
  /**
   * @return string
   */
  public function getHeadNodeResourcePoolId()
  {
    return $this->headNodeResourcePoolId;
  }
  /**
   * Optional. Default image for user to choose a preferred ML framework (for
   * example, TensorFlow or Pytorch) by choosing from [Vertex prebuilt
   * images](https://cloud.google.com/vertex-ai/docs/training/pre-built-
   * containers). Either this or the resource_pool_images is required. Use this
   * field if you need all the resource pools to have the same Ray image.
   * Otherwise, use the {@code resource_pool_images} field.
   *
   * @param string $imageUri
   */
  public function setImageUri($imageUri)
  {
    $this->imageUri = $imageUri;
  }
  /**
   * @return string
   */
  public function getImageUri()
  {
    return $this->imageUri;
  }
  /**
   * Optional. OSS Ray logging configurations.
   *
   * @param GoogleCloudAiplatformV1RayLogsSpec $rayLogsSpec
   */
  public function setRayLogsSpec(GoogleCloudAiplatformV1RayLogsSpec $rayLogsSpec)
  {
    $this->rayLogsSpec = $rayLogsSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1RayLogsSpec
   */
  public function getRayLogsSpec()
  {
    return $this->rayLogsSpec;
  }
  /**
   * Optional. Ray metrics configurations.
   *
   * @param GoogleCloudAiplatformV1RayMetricSpec $rayMetricSpec
   */
  public function setRayMetricSpec(GoogleCloudAiplatformV1RayMetricSpec $rayMetricSpec)
  {
    $this->rayMetricSpec = $rayMetricSpec;
  }
  /**
   * @return GoogleCloudAiplatformV1RayMetricSpec
   */
  public function getRayMetricSpec()
  {
    return $this->rayMetricSpec;
  }
  /**
   * Optional. Required if image_uri isn't set. A map of resource_pool_id to
   * prebuild Ray image if user need to use different images for different
   * head/worker pools. This map needs to cover all the resource pool ids.
   * Example: { "ray_head_node_pool": "head image" "ray_worker_node_pool1":
   * "worker image" "ray_worker_node_pool2": "another worker image" }
   *
   * @param string[] $resourcePoolImages
   */
  public function setResourcePoolImages($resourcePoolImages)
  {
    $this->resourcePoolImages = $resourcePoolImages;
  }
  /**
   * @return string[]
   */
  public function getResourcePoolImages()
  {
    return $this->resourcePoolImages;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RaySpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RaySpec');
