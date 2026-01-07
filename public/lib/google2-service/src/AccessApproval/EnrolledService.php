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

namespace Google\Service\AccessApproval;

class EnrolledService extends \Google\Model
{
  /**
   * Default value if not set, defaults to "BLOCK_ALL". This value is not
   * available to be set by the user, do not use.
   */
  public const ENROLLMENT_LEVEL_ENROLLMENT_LEVEL_UNSPECIFIED = 'ENROLLMENT_LEVEL_UNSPECIFIED';
  /**
   * Service is enrolled in Access Approval for all requests
   */
  public const ENROLLMENT_LEVEL_BLOCK_ALL = 'BLOCK_ALL';
  /**
   * The product for which Access Approval will be enrolled. Allowed values are
   * listed below (case-sensitive): * all * GA * Access Context Manager * Anthos
   * Identity Service * AlloyDB for PostgreSQL * Apigee * Application
   * Integration * App Hub * Artifact Registry * Anthos Service Mesh * Access
   * Transparency * BigQuery * Certificate Authority Service * Cloud Bigtable *
   * CCAI Assist and Knowledge * Cloud Dataflow * Cloud Dataproc * CEP Security
   * Gateway * Compliance Evaluation Service * Cloud Firestore * Cloud
   * Healthcare API * Chronicle * Cloud AI Companion Gateway - Titan * Google
   * Cloud Armor * Cloud Asset Inventory * Cloud Asset Search * Cloud Deploy *
   * Cloud DNS * Cloud Latency * Cloud Memorystore for Redis * CloudNet Control
   * * Cloud Riptide * Cloud Tasks * Cloud Trace * Cloud Data Transfer * Cloud
   * Composer * Integration Connectors * Contact Center AI Insights * Cloud
   * Pub/Sub * Cloud Run * Resource Manager * Cloud Spanner * Database Center *
   * Cloud Dataform * Cloud Data Fusion * Dataplex * Dialogflow Customer
   * Experience Edition * Cloud DLP * Document AI * Edge Container * Edge
   * Network * Cloud EKM * Eventarc * Firebase Data Connect * Firebase Rules *
   * App Engine * Cloud Build * Compute Engine * Cloud Functions (2nd Gen) *
   * Cloud Filestore * Cloud Interconnect * Cloud NetApp Volumes * Cloud Storage
   * * Generative AI App Builder * Google Kubernetes Engine * Backup for GKE API
   * * GKE Connect * GKE Hub * Hoverboard * Cloud HSM * Cloud Identity and
   * Access Management * Cloud Identity-Aware Proxy * Infrastructure Manager *
   * Identity Storage Service * Key Access Justifications * Cloud Key Management
   * Service * Cloud Logging * Looker (Google Cloud core) * Looker Studio *
   * Management Hub * Model Armor * Cloud Monitoring * Cloud NAT * Connectivity
   * Hub * External passthrough Network Load Balancer * OIDC One * Organization
   * Policy Service * Org Lifecycle * Persistent Disk * Parameter Manager *
   * Private Services Access * Regional Internal Application Load Balancer *
   * Storage Batch Operations * Cloud Security Command Center * Secure Source
   * Manager * Seeker * Service Provisioning * Speaker ID * Secret Manager *
   * Cloud SQL * Cloud Speech-to-Text * Traffic Director * Cloud Text-to-Speech
   * * USPS Andromeda * Vertex AI * Virtual Private Cloud (VPC) * VPC Access *
   * VPC Service Controls Troubleshooter * VPC virtnet * Cloud Workstations *
   * Web Risk Note: These values are supported as input for legacy purposes, but
   * will not be returned from the API. * all * ga-only *
   * appengine.googleapis.com * artifactregistry.googleapis.com *
   * bigquery.googleapis.com * bigtable.googleapis.com *
   * container.googleapis.com * cloudkms.googleapis.com *
   * cloudresourcemanager.googleapis.com * cloudsql.googleapis.com *
   * compute.googleapis.com * dataflow.googleapis.com * dataproc.googleapis.com
   * * dlp.googleapis.com * iam.googleapis.com * logging.googleapis.com *
   * orgpolicy.googleapis.com * pubsub.googleapis.com * spanner.googleapis.com *
   * secretmanager.googleapis.com * speakerid.googleapis.com *
   * storage.googleapis.com Calls to UpdateAccessApprovalSettings using 'all' or
   * any of the XXX.googleapis.com will be translated to the associated product
   * name ('all', 'App Engine', etc.). Note: 'all' will enroll the resource in
   * all products supported at both 'GA' and 'Preview' levels. More information
   * about levels of support is available at https://cloud.google.com/access-
   * approval/docs/supported-services
   *
   * @var string
   */
  public $cloudProduct;
  /**
   * The enrollment level of the service.
   *
   * @var string
   */
  public $enrollmentLevel;

  /**
   * The product for which Access Approval will be enrolled. Allowed values are
   * listed below (case-sensitive): * all * GA * Access Context Manager * Anthos
   * Identity Service * AlloyDB for PostgreSQL * Apigee * Application
   * Integration * App Hub * Artifact Registry * Anthos Service Mesh * Access
   * Transparency * BigQuery * Certificate Authority Service * Cloud Bigtable *
   * CCAI Assist and Knowledge * Cloud Dataflow * Cloud Dataproc * CEP Security
   * Gateway * Compliance Evaluation Service * Cloud Firestore * Cloud
   * Healthcare API * Chronicle * Cloud AI Companion Gateway - Titan * Google
   * Cloud Armor * Cloud Asset Inventory * Cloud Asset Search * Cloud Deploy *
   * Cloud DNS * Cloud Latency * Cloud Memorystore for Redis * CloudNet Control
   * * Cloud Riptide * Cloud Tasks * Cloud Trace * Cloud Data Transfer * Cloud
   * Composer * Integration Connectors * Contact Center AI Insights * Cloud
   * Pub/Sub * Cloud Run * Resource Manager * Cloud Spanner * Database Center *
   * Cloud Dataform * Cloud Data Fusion * Dataplex * Dialogflow Customer
   * Experience Edition * Cloud DLP * Document AI * Edge Container * Edge
   * Network * Cloud EKM * Eventarc * Firebase Data Connect * Firebase Rules *
   * App Engine * Cloud Build * Compute Engine * Cloud Functions (2nd Gen) *
   * Cloud Filestore * Cloud Interconnect * Cloud NetApp Volumes * Cloud Storage
   * * Generative AI App Builder * Google Kubernetes Engine * Backup for GKE API
   * * GKE Connect * GKE Hub * Hoverboard * Cloud HSM * Cloud Identity and
   * Access Management * Cloud Identity-Aware Proxy * Infrastructure Manager *
   * Identity Storage Service * Key Access Justifications * Cloud Key Management
   * Service * Cloud Logging * Looker (Google Cloud core) * Looker Studio *
   * Management Hub * Model Armor * Cloud Monitoring * Cloud NAT * Connectivity
   * Hub * External passthrough Network Load Balancer * OIDC One * Organization
   * Policy Service * Org Lifecycle * Persistent Disk * Parameter Manager *
   * Private Services Access * Regional Internal Application Load Balancer *
   * Storage Batch Operations * Cloud Security Command Center * Secure Source
   * Manager * Seeker * Service Provisioning * Speaker ID * Secret Manager *
   * Cloud SQL * Cloud Speech-to-Text * Traffic Director * Cloud Text-to-Speech
   * * USPS Andromeda * Vertex AI * Virtual Private Cloud (VPC) * VPC Access *
   * VPC Service Controls Troubleshooter * VPC virtnet * Cloud Workstations *
   * Web Risk Note: These values are supported as input for legacy purposes, but
   * will not be returned from the API. * all * ga-only *
   * appengine.googleapis.com * artifactregistry.googleapis.com *
   * bigquery.googleapis.com * bigtable.googleapis.com *
   * container.googleapis.com * cloudkms.googleapis.com *
   * cloudresourcemanager.googleapis.com * cloudsql.googleapis.com *
   * compute.googleapis.com * dataflow.googleapis.com * dataproc.googleapis.com
   * * dlp.googleapis.com * iam.googleapis.com * logging.googleapis.com *
   * orgpolicy.googleapis.com * pubsub.googleapis.com * spanner.googleapis.com *
   * secretmanager.googleapis.com * speakerid.googleapis.com *
   * storage.googleapis.com Calls to UpdateAccessApprovalSettings using 'all' or
   * any of the XXX.googleapis.com will be translated to the associated product
   * name ('all', 'App Engine', etc.). Note: 'all' will enroll the resource in
   * all products supported at both 'GA' and 'Preview' levels. More information
   * about levels of support is available at https://cloud.google.com/access-
   * approval/docs/supported-services
   *
   * @param string $cloudProduct
   */
  public function setCloudProduct($cloudProduct)
  {
    $this->cloudProduct = $cloudProduct;
  }
  /**
   * @return string
   */
  public function getCloudProduct()
  {
    return $this->cloudProduct;
  }
  /**
   * The enrollment level of the service.
   *
   * Accepted values: ENROLLMENT_LEVEL_UNSPECIFIED, BLOCK_ALL
   *
   * @param self::ENROLLMENT_LEVEL_* $enrollmentLevel
   */
  public function setEnrollmentLevel($enrollmentLevel)
  {
    $this->enrollmentLevel = $enrollmentLevel;
  }
  /**
   * @return self::ENROLLMENT_LEVEL_*
   */
  public function getEnrollmentLevel()
  {
    return $this->enrollmentLevel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnrolledService::class, 'Google_Service_AccessApproval_EnrolledService');
