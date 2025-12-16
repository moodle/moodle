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

namespace Google\Service\Appengine;

class Application extends \Google\Collection
{
  /**
   * Database type is unspecified.
   */
  public const DATABASE_TYPE_DATABASE_TYPE_UNSPECIFIED = 'DATABASE_TYPE_UNSPECIFIED';
  /**
   * Cloud Datastore
   */
  public const DATABASE_TYPE_CLOUD_DATASTORE = 'CLOUD_DATASTORE';
  /**
   * Cloud Firestore Native
   */
  public const DATABASE_TYPE_CLOUD_FIRESTORE = 'CLOUD_FIRESTORE';
  /**
   * Cloud Firestore in Datastore Mode
   */
  public const DATABASE_TYPE_CLOUD_DATASTORE_COMPATIBILITY = 'CLOUD_DATASTORE_COMPATIBILITY';
  /**
   * Serving status is unspecified.
   */
  public const SERVING_STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Application is serving.
   */
  public const SERVING_STATUS_SERVING = 'SERVING';
  /**
   * Application has been disabled by the user.
   */
  public const SERVING_STATUS_USER_DISABLED = 'USER_DISABLED';
  /**
   * Application has been disabled by the system.
   */
  public const SERVING_STATUS_SYSTEM_DISABLED = 'SYSTEM_DISABLED';
  /**
   * Required by linter. Will work same as DEFAULT
   */
  public const SSL_POLICY_SSL_POLICY_UNSPECIFIED = 'SSL_POLICY_UNSPECIFIED';
  /**
   * DEFAULT is to allow all TLS versions and cipher suites supported by App
   * Engine
   */
  public const SSL_POLICY_DEFAULT = 'DEFAULT';
  /**
   * MODERN is to allow only TLS 1.2 and TLS 1.3 along with Modern cipher suites
   * only
   */
  public const SSL_POLICY_MODERN = 'MODERN';
  protected $collection_key = 'dispatchRules';
  /**
   * Google Apps authentication domain that controls which users can access this
   * application.Defaults to open access for any Google Account.
   *
   * @var string
   */
  public $authDomain;
  /**
   * Output only. Google Cloud Storage bucket that can be used for storing files
   * associated with this application. This bucket is associated with the
   * application and can be used by the gcloud deployment commands.@OutputOnly
   *
   * @var string
   */
  public $codeBucket;
  /**
   * The type of the Cloud Firestore or Cloud Datastore database associated with
   * this application.
   *
   * @var string
   */
  public $databaseType;
  /**
   * Output only. Google Cloud Storage bucket that can be used by this
   * application to store content.@OutputOnly
   *
   * @var string
   */
  public $defaultBucket;
  /**
   * Cookie expiration policy for this application.
   *
   * @var string
   */
  public $defaultCookieExpiration;
  /**
   * Output only. Hostname used to reach this application, as resolved by App
   * Engine.@OutputOnly
   *
   * @var string
   */
  public $defaultHostname;
  protected $dispatchRulesType = UrlDispatchRule::class;
  protected $dispatchRulesDataType = 'array';
  protected $featureSettingsType = FeatureSettings::class;
  protected $featureSettingsDataType = '';
  /**
   * Output only. The Google Container Registry domain used for storing managed
   * build docker images for this application.
   *
   * @var string
   */
  public $gcrDomain;
  /**
   * Additional Google Generated Customer Metadata, this field won't be provided
   * by default and can be requested by setting the IncludeExtraData field in
   * GetApplicationRequest
   *
   * @var array[]
   */
  public $generatedCustomerMetadata;
  protected $iapType = IdentityAwareProxy::class;
  protected $iapDataType = '';
  /**
   * Identifier of the Application resource. This identifier is equivalent to
   * the project ID of the Google Cloud Platform project where you want to
   * deploy your application. Example: myapp.
   *
   * @var string
   */
  public $id;
  /**
   * Location from which this application runs. Application instances run out of
   * the data centers in the specified location, which is also where all of the
   * application's end user content is stored.Defaults to us-central.View the
   * list of supported locations
   * (https://cloud.google.com/appengine/docs/locations).
   *
   * @var string
   */
  public $locationId;
  /**
   * @var string
   */
  public $name;
  /**
   * The service account associated with the application. This is the app-level
   * default identity. If no identity provided during create version, Admin API
   * will fallback to this one.
   *
   * @var string
   */
  public $serviceAccount;
  /**
   * Serving status of this application.
   *
   * @var string
   */
  public $servingStatus;
  /**
   * The SSL policy that will be applied to the application. If set to Modern it
   * will restrict traffic with TLS < 1.2 and allow only Modern Ciphers suite
   *
   * @var string
   */
  public $sslPolicy;

  /**
   * Google Apps authentication domain that controls which users can access this
   * application.Defaults to open access for any Google Account.
   *
   * @param string $authDomain
   */
  public function setAuthDomain($authDomain)
  {
    $this->authDomain = $authDomain;
  }
  /**
   * @return string
   */
  public function getAuthDomain()
  {
    return $this->authDomain;
  }
  /**
   * Output only. Google Cloud Storage bucket that can be used for storing files
   * associated with this application. This bucket is associated with the
   * application and can be used by the gcloud deployment commands.@OutputOnly
   *
   * @param string $codeBucket
   */
  public function setCodeBucket($codeBucket)
  {
    $this->codeBucket = $codeBucket;
  }
  /**
   * @return string
   */
  public function getCodeBucket()
  {
    return $this->codeBucket;
  }
  /**
   * The type of the Cloud Firestore or Cloud Datastore database associated with
   * this application.
   *
   * Accepted values: DATABASE_TYPE_UNSPECIFIED, CLOUD_DATASTORE,
   * CLOUD_FIRESTORE, CLOUD_DATASTORE_COMPATIBILITY
   *
   * @param self::DATABASE_TYPE_* $databaseType
   */
  public function setDatabaseType($databaseType)
  {
    $this->databaseType = $databaseType;
  }
  /**
   * @return self::DATABASE_TYPE_*
   */
  public function getDatabaseType()
  {
    return $this->databaseType;
  }
  /**
   * Output only. Google Cloud Storage bucket that can be used by this
   * application to store content.@OutputOnly
   *
   * @param string $defaultBucket
   */
  public function setDefaultBucket($defaultBucket)
  {
    $this->defaultBucket = $defaultBucket;
  }
  /**
   * @return string
   */
  public function getDefaultBucket()
  {
    return $this->defaultBucket;
  }
  /**
   * Cookie expiration policy for this application.
   *
   * @param string $defaultCookieExpiration
   */
  public function setDefaultCookieExpiration($defaultCookieExpiration)
  {
    $this->defaultCookieExpiration = $defaultCookieExpiration;
  }
  /**
   * @return string
   */
  public function getDefaultCookieExpiration()
  {
    return $this->defaultCookieExpiration;
  }
  /**
   * Output only. Hostname used to reach this application, as resolved by App
   * Engine.@OutputOnly
   *
   * @param string $defaultHostname
   */
  public function setDefaultHostname($defaultHostname)
  {
    $this->defaultHostname = $defaultHostname;
  }
  /**
   * @return string
   */
  public function getDefaultHostname()
  {
    return $this->defaultHostname;
  }
  /**
   * HTTP path dispatch rules for requests to the application that do not
   * explicitly target a service or version. Rules are order-dependent. Up to 20
   * dispatch rules can be supported.
   *
   * @param UrlDispatchRule[] $dispatchRules
   */
  public function setDispatchRules($dispatchRules)
  {
    $this->dispatchRules = $dispatchRules;
  }
  /**
   * @return UrlDispatchRule[]
   */
  public function getDispatchRules()
  {
    return $this->dispatchRules;
  }
  /**
   * The feature specific settings to be used in the application.
   *
   * @param FeatureSettings $featureSettings
   */
  public function setFeatureSettings(FeatureSettings $featureSettings)
  {
    $this->featureSettings = $featureSettings;
  }
  /**
   * @return FeatureSettings
   */
  public function getFeatureSettings()
  {
    return $this->featureSettings;
  }
  /**
   * Output only. The Google Container Registry domain used for storing managed
   * build docker images for this application.
   *
   * @param string $gcrDomain
   */
  public function setGcrDomain($gcrDomain)
  {
    $this->gcrDomain = $gcrDomain;
  }
  /**
   * @return string
   */
  public function getGcrDomain()
  {
    return $this->gcrDomain;
  }
  /**
   * Additional Google Generated Customer Metadata, this field won't be provided
   * by default and can be requested by setting the IncludeExtraData field in
   * GetApplicationRequest
   *
   * @param array[] $generatedCustomerMetadata
   */
  public function setGeneratedCustomerMetadata($generatedCustomerMetadata)
  {
    $this->generatedCustomerMetadata = $generatedCustomerMetadata;
  }
  /**
   * @return array[]
   */
  public function getGeneratedCustomerMetadata()
  {
    return $this->generatedCustomerMetadata;
  }
  /**
   * @param IdentityAwareProxy $iap
   */
  public function setIap(IdentityAwareProxy $iap)
  {
    $this->iap = $iap;
  }
  /**
   * @return IdentityAwareProxy
   */
  public function getIap()
  {
    return $this->iap;
  }
  /**
   * Identifier of the Application resource. This identifier is equivalent to
   * the project ID of the Google Cloud Platform project where you want to
   * deploy your application. Example: myapp.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Location from which this application runs. Application instances run out of
   * the data centers in the specified location, which is also where all of the
   * application's end user content is stored.Defaults to us-central.View the
   * list of supported locations
   * (https://cloud.google.com/appengine/docs/locations).
   *
   * @param string $locationId
   */
  public function setLocationId($locationId)
  {
    $this->locationId = $locationId;
  }
  /**
   * @return string
   */
  public function getLocationId()
  {
    return $this->locationId;
  }
  /**
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
   * The service account associated with the application. This is the app-level
   * default identity. If no identity provided during create version, Admin API
   * will fallback to this one.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Serving status of this application.
   *
   * Accepted values: UNSPECIFIED, SERVING, USER_DISABLED, SYSTEM_DISABLED
   *
   * @param self::SERVING_STATUS_* $servingStatus
   */
  public function setServingStatus($servingStatus)
  {
    $this->servingStatus = $servingStatus;
  }
  /**
   * @return self::SERVING_STATUS_*
   */
  public function getServingStatus()
  {
    return $this->servingStatus;
  }
  /**
   * The SSL policy that will be applied to the application. If set to Modern it
   * will restrict traffic with TLS < 1.2 and allow only Modern Ciphers suite
   *
   * Accepted values: SSL_POLICY_UNSPECIFIED, DEFAULT, MODERN
   *
   * @param self::SSL_POLICY_* $sslPolicy
   */
  public function setSslPolicy($sslPolicy)
  {
    $this->sslPolicy = $sslPolicy;
  }
  /**
   * @return self::SSL_POLICY_*
   */
  public function getSslPolicy()
  {
    return $this->sslPolicy;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Application::class, 'Google_Service_Appengine_Application');
