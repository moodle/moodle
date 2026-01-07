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

namespace Google\Service;

use Google\Client;

/**
 * Service definition for Appengine (v1).
 *
 * <p>
 * Provisions and manages developers' App Engine applications.</p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://cloud.google.com/appengine/docs/admin-api/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Appengine extends \Google\Service
{
  /** View and manage your applications deployed on Google App Engine. */
  const APPENGINE_ADMIN =
      "https://www.googleapis.com/auth/appengine.admin";
  /** See, edit, configure, and delete your Google Cloud data and see the email address for your Google Account.. */
  const CLOUD_PLATFORM =
      "https://www.googleapis.com/auth/cloud-platform";
  /** View your data across Google Cloud services and see the email address of your Google Account. */
  const CLOUD_PLATFORM_READ_ONLY =
      "https://www.googleapis.com/auth/cloud-platform.read-only";

  public $apps;
  public $apps_authorizedCertificates;
  public $apps_authorizedDomains;
  public $apps_domainMappings;
  public $apps_firewall_ingressRules;
  public $apps_locations;
  public $apps_operations;
  public $apps_services;
  public $apps_services_versions;
  public $apps_services_versions_instances;
  public $projects_locations_applications;
  public $projects_locations_applications_authorizedCertificates;
  public $projects_locations_applications_authorizedDomains;
  public $projects_locations_applications_domainMappings;
  public $projects_locations_applications_services;
  public $projects_locations_applications_services_versions;
  public $rootUrlTemplate;

  /**
   * Constructs the internal representation of the Appengine service.
   *
   * @param Client|array $clientOrConfig The client used to deliver requests, or a
   *                                     config array to pass to a new Client instance.
   * @param string $rootUrl The root URL used for requests to the service.
   */
  public function __construct($clientOrConfig = [], $rootUrl = null)
  {
    parent::__construct($clientOrConfig);
    $this->rootUrl = $rootUrl ?: 'https://appengine.googleapis.com/';
    $this->rootUrlTemplate = $rootUrl ?: 'https://appengine.UNIVERSE_DOMAIN/';
    $this->servicePath = '';
    $this->batchPath = 'batch';
    $this->version = 'v1';
    $this->serviceName = 'appengine';

    $this->apps = new Appengine\Resource\Apps(
        $this,
        $this->serviceName,
        'apps',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/apps',
              'httpMethod' => 'POST',
              'parameters' => [],
            ],'get' => [
              'path' => 'v1/apps/{appsId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'includeExtraData' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'listRuntimes' => [
              'path' => 'v1/apps/{appsId}:listRuntimes',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'environment' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/apps/{appsId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'repair' => [
              'path' => 'v1/apps/{appsId}:repair',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_authorizedCertificates = new Appengine\Resource\AppsAuthorizedCertificates(
        $this,
        $this->serviceName,
        'authorizedCertificates',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/apps/{appsId}/authorizedCertificates',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/apps/{appsId}/authorizedCertificates/{authorizedCertificatesId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'authorizedCertificatesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/apps/{appsId}/authorizedCertificates/{authorizedCertificatesId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'authorizedCertificatesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/authorizedCertificates',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/apps/{appsId}/authorizedCertificates/{authorizedCertificatesId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'authorizedCertificatesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_authorizedDomains = new Appengine\Resource\AppsAuthorizedDomains(
        $this,
        $this->serviceName,
        'authorizedDomains',
        [
          'methods' => [
            'list' => [
              'path' => 'v1/apps/{appsId}/authorizedDomains',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_domainMappings = new Appengine\Resource\AppsDomainMappings(
        $this,
        $this->serviceName,
        'domainMappings',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/apps/{appsId}/domainMappings',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'overrideStrategy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'delete' => [
              'path' => 'v1/apps/{appsId}/domainMappings/{domainMappingsId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'domainMappingsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/apps/{appsId}/domainMappings/{domainMappingsId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'domainMappingsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/domainMappings',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/apps/{appsId}/domainMappings/{domainMappingsId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'domainMappingsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_firewall_ingressRules = new Appengine\Resource\AppsFirewallIngressRules(
        $this,
        $this->serviceName,
        'ingressRules',
        [
          'methods' => [
            'batchUpdate' => [
              'path' => 'v1/apps/{appsId}/firewall/ingressRules:batchUpdate',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'create' => [
              'path' => 'v1/apps/{appsId}/firewall/ingressRules',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/apps/{appsId}/firewall/ingressRules/{ingressRulesId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'ingressRulesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/apps/{appsId}/firewall/ingressRules/{ingressRulesId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'ingressRulesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/firewall/ingressRules',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'matchingAddress' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/apps/{appsId}/firewall/ingressRules/{ingressRulesId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'ingressRulesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_locations = new Appengine\Resource\AppsLocations(
        $this,
        $this->serviceName,
        'locations',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/apps/{appsId}/locations/{locationsId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/locations',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'extraLocationTypes' => [
                  'location' => 'query',
                  'type' => 'string',
                  'repeated' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_operations = new Appengine\Resource\AppsOperations(
        $this,
        $this->serviceName,
        'operations',
        [
          'methods' => [
            'get' => [
              'path' => 'v1/apps/{appsId}/operations/{operationsId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'operationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/operations',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'filter' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'returnPartialSuccess' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_services = new Appengine\Resource\AppsServices(
        $this,
        $this->serviceName,
        'services',
        [
          'methods' => [
            'delete' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/services',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'migrateTraffic' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_services_versions = new Appengine\Resource\AppsServicesVersions(
        $this,
        $this->serviceName,
        'versions',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'exportAppImage' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}:exportAppImage',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->apps_services_versions_instances = new Appengine\Resource\AppsServicesVersionsInstances(
        $this,
        $this->serviceName,
        'instances',
        [
          'methods' => [
            'debug' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}/instances/{instancesId}:debug',
              'httpMethod' => 'POST',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'instancesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}/instances/{instancesId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'instancesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}/instances/{instancesId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'instancesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/apps/{appsId}/services/{servicesId}/versions/{versionsId}/instances',
              'httpMethod' => 'GET',
              'parameters' => [
                'appsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_applications = new Appengine\Resource\ProjectsLocationsApplications(
        $this,
        $this->serviceName,
        'applications',
        [
          'methods' => [
            'patch' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_applications_authorizedCertificates = new Appengine\Resource\ProjectsLocationsApplicationsAuthorizedCertificates(
        $this,
        $this->serviceName,
        'authorizedCertificates',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/authorizedCertificates',
              'httpMethod' => 'POST',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'delete' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/authorizedCertificates/{authorizedCertificatesId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'authorizedCertificatesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/authorizedCertificates/{authorizedCertificatesId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'authorizedCertificatesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'list' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/authorizedCertificates',
              'httpMethod' => 'GET',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
                'view' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/authorizedCertificates/{authorizedCertificatesId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'authorizedCertificatesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_applications_authorizedDomains = new Appengine\Resource\ProjectsLocationsApplicationsAuthorizedDomains(
        $this,
        $this->serviceName,
        'authorizedDomains',
        [
          'methods' => [
            'list' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/authorizedDomains',
              'httpMethod' => 'GET',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_applications_domainMappings = new Appengine\Resource\ProjectsLocationsApplicationsDomainMappings(
        $this,
        $this->serviceName,
        'domainMappings',
        [
          'methods' => [
            'create' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/domainMappings',
              'httpMethod' => 'POST',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'overrideStrategy' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'delete' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/domainMappings/{domainMappingsId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'domainMappingsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'get' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/domainMappings/{domainMappingsId}',
              'httpMethod' => 'GET',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'domainMappingsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'list' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/domainMappings',
              'httpMethod' => 'GET',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'pageSize' => [
                  'location' => 'query',
                  'type' => 'integer',
                ],
                'pageToken' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],'patch' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/domainMappings/{domainMappingsId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'domainMappingsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_applications_services = new Appengine\Resource\ProjectsLocationsApplicationsServices(
        $this,
        $this->serviceName,
        'services',
        [
          'methods' => [
            'delete' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/services/{servicesId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'patch' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/services/{servicesId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'migrateTraffic' => [
                  'location' => 'query',
                  'type' => 'boolean',
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
    $this->projects_locations_applications_services_versions = new Appengine\Resource\ProjectsLocationsApplicationsServicesVersions(
        $this,
        $this->serviceName,
        'versions',
        [
          'methods' => [
            'delete' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/services/{servicesId}/versions/{versionsId}',
              'httpMethod' => 'DELETE',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'exportAppImage' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/services/{servicesId}/versions/{versionsId}:exportAppImage',
              'httpMethod' => 'POST',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
              ],
            ],'patch' => [
              'path' => 'v1/projects/{projectsId}/locations/{locationsId}/applications/{applicationsId}/services/{servicesId}/versions/{versionsId}',
              'httpMethod' => 'PATCH',
              'parameters' => [
                'projectsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'locationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'applicationsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'servicesId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'versionsId' => [
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ],
                'updateMask' => [
                  'location' => 'query',
                  'type' => 'string',
                ],
              ],
            ],
          ]
        ]
    );
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Appengine::class, 'Google_Service_Appengine');
