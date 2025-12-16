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

namespace Google\Service\SQLAdmin\Resource;

use Google\Service\SQLAdmin\DatabaseInstance;
use Google\Service\SQLAdmin\ExecuteSqlPayload;
use Google\Service\SQLAdmin\InstancesAcquireSsrsLeaseRequest;
use Google\Service\SQLAdmin\InstancesCloneRequest;
use Google\Service\SQLAdmin\InstancesDemoteMasterRequest;
use Google\Service\SQLAdmin\InstancesDemoteRequest;
use Google\Service\SQLAdmin\InstancesExportRequest;
use Google\Service\SQLAdmin\InstancesFailoverRequest;
use Google\Service\SQLAdmin\InstancesImportRequest;
use Google\Service\SQLAdmin\InstancesListEntraIdCertificatesResponse;
use Google\Service\SQLAdmin\InstancesListResponse;
use Google\Service\SQLAdmin\InstancesListServerCasResponse;
use Google\Service\SQLAdmin\InstancesListServerCertificatesResponse;
use Google\Service\SQLAdmin\InstancesPreCheckMajorVersionUpgradeRequest;
use Google\Service\SQLAdmin\InstancesReencryptRequest;
use Google\Service\SQLAdmin\InstancesRestoreBackupRequest;
use Google\Service\SQLAdmin\InstancesRotateEntraIdCertificateRequest;
use Google\Service\SQLAdmin\InstancesRotateServerCaRequest;
use Google\Service\SQLAdmin\InstancesRotateServerCertificateRequest;
use Google\Service\SQLAdmin\InstancesTruncateLogRequest;
use Google\Service\SQLAdmin\Operation;
use Google\Service\SQLAdmin\PointInTimeRestoreContext;
use Google\Service\SQLAdmin\SqlInstancesAcquireSsrsLeaseResponse;
use Google\Service\SQLAdmin\SqlInstancesExecuteSqlResponse;
use Google\Service\SQLAdmin\SqlInstancesReleaseSsrsLeaseResponse;

/**
 * The "instances" collection of methods.
 * Typical usage is:
 *  <code>
 *   $sqladminService = new Google\Service\SQLAdmin(...);
 *   $instances = $sqladminService->instances;
 *  </code>
 */
class Instances extends \Google\Service\Resource
{
  /**
   * Lists all versions of EntraID certificates for the specified instance. There
   * can be up to three sets of certificates listed: the certificate that is
   * currently in use, a future that has been added but not yet used to sign a
   * certificate, and a certificate that has been rotated out.
   * (instances.ListEntraIdCertificates)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance ID. This does not
   * include the project ID.
   * @param array $optParams Optional parameters.
   * @return InstancesListEntraIdCertificatesResponse
   * @throws \Google\Service\Exception
   */
  public function ListEntraIdCertificates($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('ListEntraIdCertificates', [$params], InstancesListEntraIdCertificatesResponse::class);
  }
  /**
   * Lists all versions of server certificates and certificate authorities (CAs)
   * for the specified instance. There can be up to three sets of certs listed:
   * the certificate that is currently in use, a future that has been added but
   * not yet used to sign a certificate, and a certificate that has been rotated
   * out. For instances not using Certificate Authority Service (CAS) server CA,
   * use ListServerCas instead. (instances.ListServerCertificates)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance ID. This does not
   * include the project ID.
   * @param array $optParams Optional parameters.
   * @return InstancesListServerCertificatesResponse
   * @throws \Google\Service\Exception
   */
  public function ListServerCertificates($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('ListServerCertificates', [$params], InstancesListServerCertificatesResponse::class);
  }
  /**
   * Rotates the server certificate version to one previously added with the
   * addEntraIdCertificate method. (instances.RotateEntraIdCertificate)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance ID. This does not
   * include the project ID.
   * @param InstancesRotateEntraIdCertificateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function RotateEntraIdCertificate($project, $instance, InstancesRotateEntraIdCertificateRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('RotateEntraIdCertificate', [$params], Operation::class);
  }
  /**
   * Rotates the server certificate version to one previously added with the
   * addServerCertificate method. For instances not using Certificate Authority
   * Service (CAS) server CA, use RotateServerCa instead.
   * (instances.RotateServerCertificate)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance ID. This does not
   * include the project ID.
   * @param InstancesRotateServerCertificateRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function RotateServerCertificate($project, $instance, InstancesRotateServerCertificateRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('RotateServerCertificate', [$params], Operation::class);
  }
  /**
   * Acquire a lease for the setup of SQL Server Reporting Services (SSRS).
   * (instances.acquireSsrsLease)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance (Example: project-id).
   * @param string $instance Required. Cloud SQL instance ID. This doesn't include
   * the project ID. It's composed of lowercase letters, numbers, and hyphens, and
   * it must start with a letter. The total length must be 98 characters or less
   * (Example: instance-id).
   * @param InstancesAcquireSsrsLeaseRequest $postBody
   * @param array $optParams Optional parameters.
   * @return SqlInstancesAcquireSsrsLeaseResponse
   * @throws \Google\Service\Exception
   */
  public function acquireSsrsLease($project, $instance, InstancesAcquireSsrsLeaseRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('acquireSsrsLease', [$params], SqlInstancesAcquireSsrsLeaseResponse::class);
  }
  /**
   * Adds a new Entra ID certificate for the specified instance. If an Entra ID
   * certificate was previously added but never used in a certificate rotation,
   * this operation replaces that version. (instances.addEntraIdCertificate)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance ID. This does not
   * include the project ID.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function addEntraIdCertificate($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('addEntraIdCertificate', [$params], Operation::class);
  }
  /**
   * Adds a new trusted Certificate Authority (CA) version for the specified
   * instance. Required to prepare for a certificate rotation. If a CA version was
   * previously added but never used in a certificate rotation, this operation
   * replaces that version. There cannot be more than one CA version waiting to be
   * rotated in. For instances that have enabled Certificate Authority Service
   * (CAS) based server CA, use AddServerCertificate to add a new server
   * certificate. (instances.addServerCa)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function addServerCa($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('addServerCa', [$params], Operation::class);
  }
  /**
   * Add a new trusted server certificate version for the specified instance using
   * Certificate Authority Service (CAS) server CA. Required to prepare for a
   * certificate rotation. If a server certificate version was previously added
   * but never used in a certificate rotation, this operation replaces that
   * version. There cannot be more than one certificate version waiting to be
   * rotated in. For instances not using CAS server CA, use AddServerCa instead.
   * (instances.addServerCertificate)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function addServerCertificate($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('addServerCertificate', [$params], Operation::class);
  }
  /**
   * Creates a Cloud SQL instance as a clone of the source instance. Using this
   * operation might cause your instance to restart. (instances.cloneInstances)
   *
   * @param string $project Required. Project ID of the source as well as the
   * clone Cloud SQL instance.
   * @param string $instance Required. The ID of the Cloud SQL instance to be
   * cloned (source). This does not include the project ID.
   * @param InstancesCloneRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function cloneInstances($project, $instance, InstancesCloneRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('clone', [$params], Operation::class);
  }
  /**
   * Deletes a Cloud SQL instance. (instances.delete)
   *
   * @param string $project Project ID of the project that contains the instance
   * to be deleted.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool enableFinalBackup Flag to opt-in for final backup. By
   * default, it is turned off.
   * @opt_param string finalBackupDescription Optional. The description of the
   * final backup.
   * @opt_param string finalBackupExpiryTime Optional. Final Backup expiration
   * time. Timestamp in UTC of when this resource is considered expired.
   * @opt_param string finalBackupTtlDays Optional. Retention period of the final
   * backup.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function delete($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('delete', [$params], Operation::class);
  }
  /**
   * Demotes an existing standalone instance to be a Cloud SQL read replica for an
   * external database server. (instances.demote)
   *
   * @param string $project Required. ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance name.
   * @param InstancesDemoteRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function demote($project, $instance, InstancesDemoteRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('demote', [$params], Operation::class);
  }
  /**
   * Demotes the stand-alone instance to be a Cloud SQL read replica for an
   * external database server. (instances.demoteMaster)
   *
   * @param string $project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance name.
   * @param InstancesDemoteMasterRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function demoteMaster($project, $instance, InstancesDemoteMasterRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('demoteMaster', [$params], Operation::class);
  }
  /**
   * Execute SQL statements. (instances.executeSql)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Database instance ID. This does not include
   * the project ID.
   * @param ExecuteSqlPayload $postBody
   * @param array $optParams Optional parameters.
   * @return SqlInstancesExecuteSqlResponse
   * @throws \Google\Service\Exception
   */
  public function executeSql($project, $instance, ExecuteSqlPayload $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('executeSql', [$params], SqlInstancesExecuteSqlResponse::class);
  }
  /**
   * Exports data from a Cloud SQL instance to a Cloud Storage bucket as a SQL
   * dump or CSV file. (instances.export)
   *
   * @param string $project Project ID of the project that contains the instance
   * to be exported.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesExportRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function export($project, $instance, InstancesExportRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('export', [$params], Operation::class);
  }
  /**
   * Initiates a manual failover of a high availability (HA) primary instance to a
   * standby instance, which becomes the primary instance. Users are then rerouted
   * to the new primary. For more information, see the [Overview of high
   * availability](https://cloud.google.com/sql/docs/mysql/high-availability) page
   * in the Cloud SQL documentation. If using Legacy HA (MySQL only), this causes
   * the instance to failover to its failover replica instance.
   * (instances.failover)
   *
   * @param string $project ID of the project that contains the read replica.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesFailoverRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function failover($project, $instance, InstancesFailoverRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('failover', [$params], Operation::class);
  }
  /**
   * Retrieves a resource containing information about a Cloud SQL instance.
   * (instances.get)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Database instance ID. This does not include
   * the project ID.
   * @param array $optParams Optional parameters.
   * @return DatabaseInstance
   * @throws \Google\Service\Exception
   */
  public function get($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('get', [$params], DatabaseInstance::class);
  }
  /**
   * Imports data into a Cloud SQL instance from a SQL dump or CSV file in Cloud
   * Storage. (instances.import)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesImportRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function import($project, $instance, InstancesImportRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('import', [$params], Operation::class);
  }
  /**
   * Creates a new Cloud SQL instance. (instances.insert)
   *
   * @param string $project Project ID of the project to which the newly created
   * Cloud SQL instances should belong.
   * @param DatabaseInstance $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function insert($project, DatabaseInstance $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('insert', [$params], Operation::class);
  }
  /**
   * Lists instances under a given project. (instances.listInstances)
   *
   * @param string $project Project ID of the project for which to list Cloud SQL
   * instances.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string filter A filter expression that filters resources listed in
   * the response. The expression is in the form of field:value. For example,
   * 'instanceType:CLOUD_SQL_INSTANCE'. Fields can be nested as needed as per
   * their JSON representation, such as 'settings.userLabels.auto_start:true'.
   * Multiple filter queries are space-separated. For example. 'state:RUNNABLE
   * instanceType:CLOUD_SQL_INSTANCE'. By default, each expression is an AND
   * expression. However, you can include AND and OR expressions explicitly.
   * @opt_param string maxResults The maximum number of instances to return. The
   * service may return fewer than this value. If unspecified, at most 500
   * instances are returned. The maximum value is 1000; values above 1000 are
   * coerced to 1000.
   * @opt_param string pageToken A previously-returned page token representing
   * part of the larger set of results to view.
   * @return InstancesListResponse
   * @throws \Google\Service\Exception
   */
  public function listInstances($project, $optParams = [])
  {
    $params = ['project' => $project];
    $params = array_merge($params, $optParams);
    return $this->call('list', [$params], InstancesListResponse::class);
  }
  /**
   * Lists all of the trusted Certificate Authorities (CAs) for the specified
   * instance. There can be up to three CAs listed: the CA that was used to sign
   * the certificate that is currently in use, a CA that has been added but not
   * yet used to sign a certificate, and a CA used to sign a certificate that has
   * previously rotated out. (instances.listServerCas)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   * @return InstancesListServerCasResponse
   * @throws \Google\Service\Exception
   */
  public function listServerCas($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('listServerCas', [$params], InstancesListServerCasResponse::class);
  }
  /**
   * Partially updates settings of a Cloud SQL instance by merging the request
   * with the current configuration. This method supports patch semantics.
   * (instances.patch)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param DatabaseInstance $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function patch($project, $instance, DatabaseInstance $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('patch', [$params], Operation::class);
  }
  /**
   * Point in time restore for an instance managed by Google Cloud Backup and
   * Disaster Recovery. (instances.pointInTimeRestore)
   *
   * @param string $parent Required. The parent resource where you created this
   * instance. Format: projects/{project}
   * @param PointInTimeRestoreContext $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function pointInTimeRestore($parent, PointInTimeRestoreContext $postBody, $optParams = [])
  {
    $params = ['parent' => $parent, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('pointInTimeRestore', [$params], Operation::class);
  }
  /**
   * Execute MVU Pre-checks (instances.preCheckMajorVersionUpgrade)
   *
   * @param string $project Required. Project ID of the project that contains the
   * instance.
   * @param string $instance Required. Cloud SQL instance ID. This does not
   * include the project ID.
   * @param InstancesPreCheckMajorVersionUpgradeRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function preCheckMajorVersionUpgrade($project, $instance, InstancesPreCheckMajorVersionUpgradeRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('preCheckMajorVersionUpgrade', [$params], Operation::class);
  }
  /**
   * Promotes the read replica instance to be an independent Cloud SQL primary
   * instance. Using this operation might cause your instance to restart.
   * (instances.promoteReplica)
   *
   * @param string $project ID of the project that contains the read replica.
   * @param string $instance Cloud SQL read replica instance name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool failover Set to true to invoke a replica failover to the DR
   * replica. As part of replica failover, the promote operation attempts to add
   * the original primary instance as a replica of the promoted DR replica when
   * the original primary instance comes back online. If set to false or not
   * specified, then the original primary instance becomes an independent Cloud
   * SQL primary instance.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function promoteReplica($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('promoteReplica', [$params], Operation::class);
  }
  /**
   * Reencrypt CMEK instance with latest key version. (instances.reencrypt)
   *
   * @param string $project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesReencryptRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function reencrypt($project, $instance, InstancesReencryptRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('reencrypt', [$params], Operation::class);
  }
  /**
   * Release a lease for the setup of SQL Server Reporting Services (SSRS).
   * (instances.releaseSsrsLease)
   *
   * @param string $project Required. The project ID that contains the instance.
   * @param string $instance Required. The Cloud SQL instance ID. This doesn't
   * include the project ID. The instance ID contains lowercase letters, numbers,
   * and hyphens, and it must start with a letter. This ID can have a maximum
   * length of 98 characters.
   * @param array $optParams Optional parameters.
   * @return SqlInstancesReleaseSsrsLeaseResponse
   * @throws \Google\Service\Exception
   */
  public function releaseSsrsLease($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('releaseSsrsLease', [$params], SqlInstancesReleaseSsrsLeaseResponse::class);
  }
  /**
   * Deletes all client certificates and generates a new server SSL certificate
   * for the instance. (instances.resetSslConfig)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string mode Optional. Reset SSL mode to use.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function resetSslConfig($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('resetSslConfig', [$params], Operation::class);
  }
  /**
   * Restarts a Cloud SQL instance. (instances.restart)
   *
   * @param string $project Project ID of the project that contains the instance
   * to be restarted.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restart($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('restart', [$params], Operation::class);
  }
  /**
   * Restores a backup of a Cloud SQL instance. Using this operation might cause
   * your instance to restart. (instances.restoreBackup)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesRestoreBackupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function restoreBackup($project, $instance, InstancesRestoreBackupRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('restoreBackup', [$params], Operation::class);
  }
  /**
   * Rotates the server certificate to one signed by the Certificate Authority
   * (CA) version previously added with the addServerCA method. For instances that
   * have enabled Certificate Authority Service (CAS) based server CA, use
   * RotateServerCertificate to rotate the server certificate.
   * (instances.rotateServerCa)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesRotateServerCaRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function rotateServerCa($project, $instance, InstancesRotateServerCaRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('rotateServerCa', [$params], Operation::class);
  }
  /**
   * Starts the replication in the read replica instance. (instances.startReplica)
   *
   * @param string $project ID of the project that contains the read replica.
   * @param string $instance Cloud SQL read replica instance name.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function startReplica($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('startReplica', [$params], Operation::class);
  }
  /**
   * Stops the replication in the read replica instance. (instances.stopReplica)
   *
   * @param string $project ID of the project that contains the read replica.
   * @param string $instance Cloud SQL read replica instance name.
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function stopReplica($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('stopReplica', [$params], Operation::class);
  }
  /**
   * Switches over from the primary instance to the DR replica instance.
   * (instances.switchover)
   *
   * @param string $project ID of the project that contains the replica.
   * @param string $instance Cloud SQL read replica instance name.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string dbTimeout Optional. (MySQL and PostgreSQL only) Cloud SQL
   * instance operations timeout, which is a sum of all database operations.
   * Default value is 10 minutes and can be modified to a maximum value of 24
   * hours.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function switchover($project, $instance, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance];
    $params = array_merge($params, $optParams);
    return $this->call('switchover', [$params], Operation::class);
  }
  /**
   * Truncate MySQL general and slow query log tables MySQL only.
   * (instances.truncateLog)
   *
   * @param string $project Project ID of the Cloud SQL project.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param InstancesTruncateLogRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function truncateLog($project, $instance, InstancesTruncateLogRequest $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('truncateLog', [$params], Operation::class);
  }
  /**
   * Updates settings of a Cloud SQL instance. Using this operation might cause
   * your instance to restart. (instances.update)
   *
   * @param string $project Project ID of the project that contains the instance.
   * @param string $instance Cloud SQL instance ID. This does not include the
   * project ID.
   * @param DatabaseInstance $postBody
   * @param array $optParams Optional parameters.
   * @return Operation
   * @throws \Google\Service\Exception
   */
  public function update($project, $instance, DatabaseInstance $postBody, $optParams = [])
  {
    $params = ['project' => $project, 'instance' => $instance, 'postBody' => $postBody];
    $params = array_merge($params, $optParams);
    return $this->call('update', [$params], Operation::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instances::class, 'Google_Service_SQLAdmin_Resource_Instances');
