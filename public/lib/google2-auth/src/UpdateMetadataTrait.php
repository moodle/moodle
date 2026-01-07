<?php
/*
 * Copyright 2023 Google LLC
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Google\Auth;

/**
 * Provides shared methods for updating request metadata (request headers).
 *
 * Should implement {@see UpdateMetadataInterface} and {@see FetchAuthTokenInterface}.
 *
 * @internal
 */
trait UpdateMetadataTrait
{
    use MetricsTrait;

    /**
     * export a callback function which updates runtime metadata.
     *
     * @return callable updateMetadata function
     * @deprecated
     */
    public function getUpdateMetadataFunc()
    {
        return [$this, 'updateMetadata'];
    }

    /**
     * Updates metadata with the authorization token.
     *
     * @param array<mixed> $metadata metadata hashmap
     * @param string $authUri optional auth uri
     * @param callable|null $httpHandler callback which delivers psr7 request
     * @return array<mixed> updated metadata hashmap
     */
    public function updateMetadata(
        $metadata,
        $authUri = null,
        ?callable $httpHandler = null
    ) {
        $metadata_copy = $metadata;

        // We do need to set the service api usage metrics irrespective even if
        // the auth token is set because invoking this method with auth tokens
        // would mean the intention is to just explicitly set the metrics metadata.
        $metadata_copy = $this->applyServiceApiUsageMetrics($metadata_copy);

        if (isset($metadata_copy[self::AUTH_METADATA_KEY])) {
            // Auth metadata has already been set
            return $metadata_copy;
        }
        $result = $this->fetchAuthToken($httpHandler);
        if (isset($result['access_token'])) {
            $metadata_copy[self::AUTH_METADATA_KEY] = ['Bearer ' . $result['access_token']];
        } elseif (isset($result['id_token'])) {
            $metadata_copy[self::AUTH_METADATA_KEY] = ['Bearer ' . $result['id_token']];
        }
        return $metadata_copy;
    }
}
