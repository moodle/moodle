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

namespace Google\Service\SQLAdmin;

class IpConfiguration extends \Google\Collection
{
  /**
   * CA mode is unspecified. It is effectively the same as
   * `GOOGLE_MANAGED_INTERNAL_CA`.
   */
  public const SERVER_CA_MODE_CA_MODE_UNSPECIFIED = 'CA_MODE_UNSPECIFIED';
  /**
   * Google-managed self-signed internal CA.
   */
  public const SERVER_CA_MODE_GOOGLE_MANAGED_INTERNAL_CA = 'GOOGLE_MANAGED_INTERNAL_CA';
  /**
   * Google-managed regional CA part of root CA hierarchy hosted on Google
   * Cloud's Certificate Authority Service (CAS).
   */
  public const SERVER_CA_MODE_GOOGLE_MANAGED_CAS_CA = 'GOOGLE_MANAGED_CAS_CA';
  /**
   * Customer-managed CA hosted on Google Cloud's Certificate Authority Service
   * (CAS).
   */
  public const SERVER_CA_MODE_CUSTOMER_MANAGED_CAS_CA = 'CUSTOMER_MANAGED_CAS_CA';
  /**
   * Unspecified: no automatic server certificate rotation.
   */
  public const SERVER_CERTIFICATE_ROTATION_MODE_SERVER_CERTIFICATE_ROTATION_MODE_UNSPECIFIED = 'SERVER_CERTIFICATE_ROTATION_MODE_UNSPECIFIED';
  /**
   * No automatic server certificate rotation. The user must [manage server
   * certificate rotation](/sql/docs/mysql/manage-ssl-instance#rotate-server-
   * certificate-cas) on their side.
   */
  public const SERVER_CERTIFICATE_ROTATION_MODE_NO_AUTOMATIC_ROTATION = 'NO_AUTOMATIC_ROTATION';
  /**
   * Automatic server certificate rotation during Cloud SQL scheduled
   * maintenance or self-service maintenance updates. Requires `server_ca_mode`
   * to be `GOOGLE_MANAGED_CAS_CA` or `CUSTOMER_MANAGED_CAS_CA`.
   */
  public const SERVER_CERTIFICATE_ROTATION_MODE_AUTOMATIC_ROTATION_DURING_MAINTENANCE = 'AUTOMATIC_ROTATION_DURING_MAINTENANCE';
  /**
   * The SSL mode is unknown.
   */
  public const SSL_MODE_SSL_MODE_UNSPECIFIED = 'SSL_MODE_UNSPECIFIED';
  /**
   * Allow non-SSL/non-TLS and SSL/TLS connections. For SSL connections to MySQL
   * and PostgreSQL, the client certificate isn't verified. When this value is
   * used, the legacy `require_ssl` flag must be false or cleared to avoid a
   * conflict between the values of the two flags.
   */
  public const SSL_MODE_ALLOW_UNENCRYPTED_AND_ENCRYPTED = 'ALLOW_UNENCRYPTED_AND_ENCRYPTED';
  /**
   * Only allow connections encrypted with SSL/TLS. For SSL connections to MySQL
   * and PostgreSQL, the client certificate isn't verified. When this value is
   * used, the legacy `require_ssl` flag must be false or cleared to avoid a
   * conflict between the values of the two flags.
   */
  public const SSL_MODE_ENCRYPTED_ONLY = 'ENCRYPTED_ONLY';
  /**
   * Only allow connections encrypted with SSL/TLS and with valid client
   * certificates. When this value is used, the legacy `require_ssl` flag must
   * be true or cleared to avoid the conflict between values of two flags.
   * PostgreSQL clients or users that connect using IAM database authentication
   * must use either the [Cloud SQL Auth
   * Proxy](https://cloud.google.com/sql/docs/postgres/connect-auth-proxy) or
   * [Cloud SQL Connectors](https://cloud.google.com/sql/docs/postgres/connect-
   * connectors) to enforce client identity verification. Only applicable to
   * MySQL and PostgreSQL. Not applicable to SQL Server.
   */
  public const SSL_MODE_TRUSTED_CLIENT_CERTIFICATE_REQUIRED = 'TRUSTED_CLIENT_CERTIFICATE_REQUIRED';
  protected $collection_key = 'customSubjectAlternativeNames';
  /**
   * The name of the allocated ip range for the private ip Cloud SQL instance.
   * For example: "google-managed-services-default". If set, the instance ip
   * will be created in the allocated range. The range name must comply with
   * [RFC 1035](https://tools.ietf.org/html/rfc1035). Specifically, the name
   * must be 1-63 characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?.`
   *
   * @var string
   */
  public $allocatedIpRange;
  protected $authorizedNetworksType = AclEntry::class;
  protected $authorizedNetworksDataType = 'array';
  /**
   * Optional. Custom Subject Alternative Name(SAN)s for a Cloud SQL instance.
   *
   * @var string[]
   */
  public $customSubjectAlternativeNames;
  /**
   * Controls connectivity to private IP instances from Google services, such as
   * BigQuery.
   *
   * @var bool
   */
  public $enablePrivatePathForGoogleCloudServices;
  /**
   * Whether the instance is assigned a public IP address or not.
   *
   * @var bool
   */
  public $ipv4Enabled;
  /**
   * The resource link for the VPC network from which the Cloud SQL instance is
   * accessible for private IP. For example,
   * `/projects/myProject/global/networks/default`. This setting can be updated,
   * but it cannot be removed after it is set.
   *
   * @var string
   */
  public $privateNetwork;
  protected $pscConfigType = PscConfig::class;
  protected $pscConfigDataType = '';
  /**
   * Use `ssl_mode` instead. Whether SSL/TLS connections over IP are enforced.
   * If set to false, then allow both non-SSL/non-TLS and SSL/TLS connections.
   * For SSL/TLS connections, the client certificate won't be verified. If set
   * to true, then only allow connections encrypted with SSL/TLS and with valid
   * client certificates. If you want to enforce SSL/TLS without enforcing the
   * requirement for valid client certificates, then use the `ssl_mode` flag
   * instead of the `require_ssl` flag.
   *
   * @var bool
   */
  public $requireSsl;
  /**
   * Specify what type of CA is used for the server certificate.
   *
   * @var string
   */
  public $serverCaMode;
  /**
   * Optional. The resource name of the server CA pool for an instance with
   * `CUSTOMER_MANAGED_CAS_CA` as the `server_ca_mode`. Format:
   * projects/{PROJECT}/locations/{REGION}/caPools/{CA_POOL_ID}
   *
   * @var string
   */
  public $serverCaPool;
  /**
   * Optional. Controls the automatic server certificate rotation feature. This
   * feature is disabled by default. When enabled, the server certificate will
   * be automatically rotated during Cloud SQL scheduled maintenance or self-
   * service maintenance updates up to six months before it expires. This
   * setting can only be set if server_ca_mode is either GOOGLE_MANAGED_CAS_CA
   * or CUSTOMER_MANAGED_CAS_CA.
   *
   * @var string
   */
  public $serverCertificateRotationMode;
  /**
   * Specify how SSL/TLS is enforced in database connections. If you must use
   * the `require_ssl` flag for backward compatibility, then only the following
   * value pairs are valid: For PostgreSQL and MySQL: *
   * `ssl_mode=ALLOW_UNENCRYPTED_AND_ENCRYPTED` and `require_ssl=false` *
   * `ssl_mode=ENCRYPTED_ONLY` and `require_ssl=false` *
   * `ssl_mode=TRUSTED_CLIENT_CERTIFICATE_REQUIRED` and `require_ssl=true` For
   * SQL Server: * `ssl_mode=ALLOW_UNENCRYPTED_AND_ENCRYPTED` and
   * `require_ssl=false` * `ssl_mode=ENCRYPTED_ONLY` and `require_ssl=true` The
   * value of `ssl_mode` has priority over the value of `require_ssl`. For
   * example, for the pair `ssl_mode=ENCRYPTED_ONLY` and `require_ssl=false`,
   * `ssl_mode=ENCRYPTED_ONLY` means accept only SSL connections, while
   * `require_ssl=false` means accept both non-SSL and SSL connections. In this
   * case, MySQL and PostgreSQL databases respect `ssl_mode` and accepts only
   * SSL connections.
   *
   * @var string
   */
  public $sslMode;

  /**
   * The name of the allocated ip range for the private ip Cloud SQL instance.
   * For example: "google-managed-services-default". If set, the instance ip
   * will be created in the allocated range. The range name must comply with
   * [RFC 1035](https://tools.ietf.org/html/rfc1035). Specifically, the name
   * must be 1-63 characters long and match the regular expression
   * `[a-z]([-a-z0-9]*[a-z0-9])?.`
   *
   * @param string $allocatedIpRange
   */
  public function setAllocatedIpRange($allocatedIpRange)
  {
    $this->allocatedIpRange = $allocatedIpRange;
  }
  /**
   * @return string
   */
  public function getAllocatedIpRange()
  {
    return $this->allocatedIpRange;
  }
  /**
   * The list of external networks that are allowed to connect to the instance
   * using the IP. In 'CIDR' notation, also known as 'slash' notation (for
   * example: `157.197.200.0/24`).
   *
   * @param AclEntry[] $authorizedNetworks
   */
  public function setAuthorizedNetworks($authorizedNetworks)
  {
    $this->authorizedNetworks = $authorizedNetworks;
  }
  /**
   * @return AclEntry[]
   */
  public function getAuthorizedNetworks()
  {
    return $this->authorizedNetworks;
  }
  /**
   * Optional. Custom Subject Alternative Name(SAN)s for a Cloud SQL instance.
   *
   * @param string[] $customSubjectAlternativeNames
   */
  public function setCustomSubjectAlternativeNames($customSubjectAlternativeNames)
  {
    $this->customSubjectAlternativeNames = $customSubjectAlternativeNames;
  }
  /**
   * @return string[]
   */
  public function getCustomSubjectAlternativeNames()
  {
    return $this->customSubjectAlternativeNames;
  }
  /**
   * Controls connectivity to private IP instances from Google services, such as
   * BigQuery.
   *
   * @param bool $enablePrivatePathForGoogleCloudServices
   */
  public function setEnablePrivatePathForGoogleCloudServices($enablePrivatePathForGoogleCloudServices)
  {
    $this->enablePrivatePathForGoogleCloudServices = $enablePrivatePathForGoogleCloudServices;
  }
  /**
   * @return bool
   */
  public function getEnablePrivatePathForGoogleCloudServices()
  {
    return $this->enablePrivatePathForGoogleCloudServices;
  }
  /**
   * Whether the instance is assigned a public IP address or not.
   *
   * @param bool $ipv4Enabled
   */
  public function setIpv4Enabled($ipv4Enabled)
  {
    $this->ipv4Enabled = $ipv4Enabled;
  }
  /**
   * @return bool
   */
  public function getIpv4Enabled()
  {
    return $this->ipv4Enabled;
  }
  /**
   * The resource link for the VPC network from which the Cloud SQL instance is
   * accessible for private IP. For example,
   * `/projects/myProject/global/networks/default`. This setting can be updated,
   * but it cannot be removed after it is set.
   *
   * @param string $privateNetwork
   */
  public function setPrivateNetwork($privateNetwork)
  {
    $this->privateNetwork = $privateNetwork;
  }
  /**
   * @return string
   */
  public function getPrivateNetwork()
  {
    return $this->privateNetwork;
  }
  /**
   * PSC settings for this instance.
   *
   * @param PscConfig $pscConfig
   */
  public function setPscConfig(PscConfig $pscConfig)
  {
    $this->pscConfig = $pscConfig;
  }
  /**
   * @return PscConfig
   */
  public function getPscConfig()
  {
    return $this->pscConfig;
  }
  /**
   * Use `ssl_mode` instead. Whether SSL/TLS connections over IP are enforced.
   * If set to false, then allow both non-SSL/non-TLS and SSL/TLS connections.
   * For SSL/TLS connections, the client certificate won't be verified. If set
   * to true, then only allow connections encrypted with SSL/TLS and with valid
   * client certificates. If you want to enforce SSL/TLS without enforcing the
   * requirement for valid client certificates, then use the `ssl_mode` flag
   * instead of the `require_ssl` flag.
   *
   * @param bool $requireSsl
   */
  public function setRequireSsl($requireSsl)
  {
    $this->requireSsl = $requireSsl;
  }
  /**
   * @return bool
   */
  public function getRequireSsl()
  {
    return $this->requireSsl;
  }
  /**
   * Specify what type of CA is used for the server certificate.
   *
   * Accepted values: CA_MODE_UNSPECIFIED, GOOGLE_MANAGED_INTERNAL_CA,
   * GOOGLE_MANAGED_CAS_CA, CUSTOMER_MANAGED_CAS_CA
   *
   * @param self::SERVER_CA_MODE_* $serverCaMode
   */
  public function setServerCaMode($serverCaMode)
  {
    $this->serverCaMode = $serverCaMode;
  }
  /**
   * @return self::SERVER_CA_MODE_*
   */
  public function getServerCaMode()
  {
    return $this->serverCaMode;
  }
  /**
   * Optional. The resource name of the server CA pool for an instance with
   * `CUSTOMER_MANAGED_CAS_CA` as the `server_ca_mode`. Format:
   * projects/{PROJECT}/locations/{REGION}/caPools/{CA_POOL_ID}
   *
   * @param string $serverCaPool
   */
  public function setServerCaPool($serverCaPool)
  {
    $this->serverCaPool = $serverCaPool;
  }
  /**
   * @return string
   */
  public function getServerCaPool()
  {
    return $this->serverCaPool;
  }
  /**
   * Optional. Controls the automatic server certificate rotation feature. This
   * feature is disabled by default. When enabled, the server certificate will
   * be automatically rotated during Cloud SQL scheduled maintenance or self-
   * service maintenance updates up to six months before it expires. This
   * setting can only be set if server_ca_mode is either GOOGLE_MANAGED_CAS_CA
   * or CUSTOMER_MANAGED_CAS_CA.
   *
   * Accepted values: SERVER_CERTIFICATE_ROTATION_MODE_UNSPECIFIED,
   * NO_AUTOMATIC_ROTATION, AUTOMATIC_ROTATION_DURING_MAINTENANCE
   *
   * @param self::SERVER_CERTIFICATE_ROTATION_MODE_* $serverCertificateRotationMode
   */
  public function setServerCertificateRotationMode($serverCertificateRotationMode)
  {
    $this->serverCertificateRotationMode = $serverCertificateRotationMode;
  }
  /**
   * @return self::SERVER_CERTIFICATE_ROTATION_MODE_*
   */
  public function getServerCertificateRotationMode()
  {
    return $this->serverCertificateRotationMode;
  }
  /**
   * Specify how SSL/TLS is enforced in database connections. If you must use
   * the `require_ssl` flag for backward compatibility, then only the following
   * value pairs are valid: For PostgreSQL and MySQL: *
   * `ssl_mode=ALLOW_UNENCRYPTED_AND_ENCRYPTED` and `require_ssl=false` *
   * `ssl_mode=ENCRYPTED_ONLY` and `require_ssl=false` *
   * `ssl_mode=TRUSTED_CLIENT_CERTIFICATE_REQUIRED` and `require_ssl=true` For
   * SQL Server: * `ssl_mode=ALLOW_UNENCRYPTED_AND_ENCRYPTED` and
   * `require_ssl=false` * `ssl_mode=ENCRYPTED_ONLY` and `require_ssl=true` The
   * value of `ssl_mode` has priority over the value of `require_ssl`. For
   * example, for the pair `ssl_mode=ENCRYPTED_ONLY` and `require_ssl=false`,
   * `ssl_mode=ENCRYPTED_ONLY` means accept only SSL connections, while
   * `require_ssl=false` means accept both non-SSL and SSL connections. In this
   * case, MySQL and PostgreSQL databases respect `ssl_mode` and accepts only
   * SSL connections.
   *
   * Accepted values: SSL_MODE_UNSPECIFIED, ALLOW_UNENCRYPTED_AND_ENCRYPTED,
   * ENCRYPTED_ONLY, TRUSTED_CLIENT_CERTIFICATE_REQUIRED
   *
   * @param self::SSL_MODE_* $sslMode
   */
  public function setSslMode($sslMode)
  {
    $this->sslMode = $sslMode;
  }
  /**
   * @return self::SSL_MODE_*
   */
  public function getSslMode()
  {
    return $this->sslMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IpConfiguration::class, 'Google_Service_SQLAdmin_IpConfiguration');
