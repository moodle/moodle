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

namespace Google\Service\Apigee\Resource;

use Google\Service\Apigee\GoogleCloudApigeeV1Deployment;
use Google\Service\Apigee\GoogleProtobufEmpty;

/**
 * The "revisions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $apigeeService = new Google\Service\Apigee(...);
 *   $revisions = $apigeeService->organizations_environments_apis_revisions;
 *  </code>
 */
class OrganizationsEnvironmentsApisRevisions extends \Google\Service\Resource
{
  /**
   * Deploys a revision of an API proxy. If another revision of the same API proxy
   * revision is currently deployed, set the `override` parameter to `true` to
   * have this revision replace the currently deployed revision. You cannot invoke
   * an API proxy until it has been deployed to an environment. After you deploy
   * an API proxy revision, you cannot edit it. To edit the API proxy, you must
   * create and deploy a new revision. For a request path `organizations/{org}/env
   * ironments/{env}/apis/{api}/revisions/{rev}/deployments`, two permissions are
   * required: * `apigee.deployments.create` on the resource
   * `organizations/{org}/environments/{env}` * `apigee.proxyrevisions.deploy` on
   * the resource `organizations/{org}/apis/{api}/revisions/{rev}` All successful
   * API proxy deployments to Apigee are [zero-downtime
   * deployments](https://cloud.google.com/apigee/docs/api-platform/deploy/ui-
   * deploy-overview#zero-downtime-deployment). Apigee hybrid validates the
   * dependencies between shared flows and API proxies at deployment time. For
   * example, if the Flow Callout policy in an API proxy references a shared flow
   * that either doesn't exist or isn't deployed, the API proxy deployment fails.
   * (revisions.deploy)
   *
   * @param string $name Required. Name of the API proxy revision deployment in
   * the following format:
   * `organizations/{org}/environments/{env}/apis/{api}/revisions/{rev}` If the
   * API proxy resource being deployed has the `space` attribute set, IAM
   * permissions are checked differently . To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool override Flag that specifies whether the new deployment
   * replaces other deployed revisions of the API proxy in the environment. Set
   * `override` to `true` to replace other deployed revisions. By default,
   * `override` is `false` and the deployment is rejected if other revisions of
   * the API proxy are deployed in the environment.
   * @opt_param bool sequencedRollout Flag that specifies whether to enable
   * sequenced rollout. If set to `true`, the routing rules for this deployment
   * and the environment changes to add the deployment will be rolled out in a
   * safe order. This reduces the risk of downtime that could be caused by
   * changing the environment group's routing before the new destination for the
   * affected traffic is ready to receive it. This should only be necessary if the
   * new deployment will be capturing traffic from another environment under a
   * shared environment group or if traffic will be rerouted to a different
   * environment due to a base path removal. The generateDeployChangeReport API
   * may be used to examine routing changes before issuing the deployment request,
   * and its response will indicate if a sequenced rollout is recommended for the
   * deployment.
   * @opt_param string serviceAccount Google Cloud IAM service account. The
   * service account represents the identity of the deployed proxy, and determines
   * what permissions it has. The format must be
   * `{ACCOUNT_ID}@{PROJECT}.iam.gserviceaccount.com`.
   * @return GoogleCloudApigeeV1Deployment
   * @throws \Google\Service\Exception
   */
  public function deploy($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('deploy', [$params], GoogleCloudApigeeV1Deployment::class);
  }
  /**
   * Gets the deployment of an API proxy revision and actual state reported by
   * runtime pods. (revisions.getDeployments)
   *
   * @param string $name Required. Name representing an API proxy revision in an
   * environment in the following format:
   * `organizations/{org}/environments/{env}/apis/{api}/revisions/{rev}` If the
   * API proxy resource has the `space` attribute set, IAM permissions are checked
   * differently . To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   * @return GoogleCloudApigeeV1Deployment
   * @throws \Google\Service\Exception
   */
  public function getDeployments($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('getDeployments', [$params], GoogleCloudApigeeV1Deployment::class);
  }
  /**
   * Undeploys an API proxy revision from an environment. For a request path `orga
   * nizations/{org}/environments/{env}/apis/{api}/revisions/{rev}/deployments`,
   * two permissions are required: * `apigee.deployments.delete` on the resource
   * `organizations/{org}/environments/{env}` * `apigee.proxyrevisions.undeploy`
   * on the resource `organizations/{org}/apis/{api}/revisions/{rev}`
   * (revisions.undeploy)
   *
   * @param string $name Required. Name of the API proxy revision deployment in
   * the following format:
   * `organizations/{org}/environments/{env}/apis/{api}/revisions/{rev}` If the
   * API proxy resource has the `space` attribute set, IAM permissions are checked
   * differently . To learn more, read the [Apigee Spaces
   * Overview](https://cloud.google.com/apigee/docs/api-platform/system-
   * administration/spaces/apigee-spaces-overview).
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool sequencedRollout Flag that specifies whether to enable
   * sequenced rollout. If set to `true`, the environment group routing rules
   * corresponding to this deployment will be removed before removing the
   * deployment from the runtime. This is likely to be a rare use case; it is only
   * needed when the intended effect of undeploying this proxy is to cause the
   * traffic it currently handles to be rerouted to some other existing proxy in
   * the environment group. The GenerateUndeployChangeReport API may be used to
   * examine routing changes before issuing the undeployment request, and its
   * response will indicate if a sequenced rollout is recommended for the
   * undeployment.
   * @return GoogleProtobufEmpty
   * @throws \Google\Service\Exception
   */
  public function undeploy($name, $optParams = [])
  {
    $params = ['name' => $name];
    $params = array_merge($params, $optParams);
    return $this->call('undeploy', [$params], GoogleProtobufEmpty::class);
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OrganizationsEnvironmentsApisRevisions::class, 'Google_Service_Apigee_Resource_OrganizationsEnvironmentsApisRevisions');
