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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceGcsBucketConfig extends \Google\Collection
{
  protected $collection_key = 'viewers';
  /**
   * @var string[]
   */
  public $admins;
  /**
   * Input/Output [Optional]. The name of a GCS bucket with max length of 63
   * chars. If not set, a random UUID will be generated as bucket name.
   *
   * @var string
   */
  public $bucketName;
  /**
   * Input/Output [Optional]. Only needed for per-entity tenant GCP resources.
   * During Deprovision API, the on-demand deletion will only cover the tenant
   * GCP resources with the specified entity name.
   *
   * @var string
   */
  public $entityName;
  /**
   * Input/Output [Optional]. The KMS key name or the KMS grant name used for
   * CMEK encryption. Only set this field when provisioning new GCS bucket. For
   * existing GCS bucket, this field will be ignored because CMEK re-encryption
   * is not supported.
   *
   * @var string
   */
  public $kmsKeyReference;
  /**
   * Input/Output [Optional]. Only needed when the content in bucket need to be
   * garbage collected within some amount of days.
   *
   * @var int
   */
  public $ttlDays;
  /**
   * Input/Output [Required]. IAM roles (viewer/admin) put on the bucket.
   *
   * @var string[]
   */
  public $viewers;

  /**
   * @param string[] $admins
   */
  public function setAdmins($admins)
  {
    $this->admins = $admins;
  }
  /**
   * @return string[]
   */
  public function getAdmins()
  {
    return $this->admins;
  }
  /**
   * Input/Output [Optional]. The name of a GCS bucket with max length of 63
   * chars. If not set, a random UUID will be generated as bucket name.
   *
   * @param string $bucketName
   */
  public function setBucketName($bucketName)
  {
    $this->bucketName = $bucketName;
  }
  /**
   * @return string
   */
  public function getBucketName()
  {
    return $this->bucketName;
  }
  /**
   * Input/Output [Optional]. Only needed for per-entity tenant GCP resources.
   * During Deprovision API, the on-demand deletion will only cover the tenant
   * GCP resources with the specified entity name.
   *
   * @param string $entityName
   */
  public function setEntityName($entityName)
  {
    $this->entityName = $entityName;
  }
  /**
   * @return string
   */
  public function getEntityName()
  {
    return $this->entityName;
  }
  /**
   * Input/Output [Optional]. The KMS key name or the KMS grant name used for
   * CMEK encryption. Only set this field when provisioning new GCS bucket. For
   * existing GCS bucket, this field will be ignored because CMEK re-encryption
   * is not supported.
   *
   * @param string $kmsKeyReference
   */
  public function setKmsKeyReference($kmsKeyReference)
  {
    $this->kmsKeyReference = $kmsKeyReference;
  }
  /**
   * @return string
   */
  public function getKmsKeyReference()
  {
    return $this->kmsKeyReference;
  }
  /**
   * Input/Output [Optional]. Only needed when the content in bucket need to be
   * garbage collected within some amount of days.
   *
   * @param int $ttlDays
   */
  public function setTtlDays($ttlDays)
  {
    $this->ttlDays = $ttlDays;
  }
  /**
   * @return int
   */
  public function getTtlDays()
  {
    return $this->ttlDays;
  }
  /**
   * Input/Output [Required]. IAM roles (viewer/admin) put on the bucket.
   *
   * @param string[] $viewers
   */
  public function setViewers($viewers)
  {
    $this->viewers = $viewers;
  }
  /**
   * @return string[]
   */
  public function getViewers()
  {
    return $this->viewers;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceGcsBucketConfig::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceGcsBucketConfig');
