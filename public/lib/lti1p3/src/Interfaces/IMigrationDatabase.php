<?php

namespace Packback\Lti1p3\Interfaces;

use Packback\Lti1p3\LtiMessageLaunch;

/**
 * This is an optional interface if an LTI 1.3 tool supports migrations
 * from LTI 1.1 compatible installations.
 *
 * To use this, just have whatever class you create that implements IDatabase
 * also implement this interface.
 */
interface IMigrationDatabase extends IDatabase
{
    /**
     * Using the LtiMessageLaunch return an array of matching LTI 1.1 keys
     *
     * @return array<\Packback\Lti1p3\Lti1p1Key>
     */
    public function findLti1p1Keys(LtiMessageLaunch $launch): array;

    /**
     * Given an LtiMessageLaunch, return true if this tool should migrate from 1.1 to 1.3
     */
    public function shouldMigrate(LtiMessageLaunch $launch): bool;

    /**
     * This method should create a 1.3 deployment in your DB based on the LtiMessageLaunch.
     * Previous to this, we validated the oauth_consumer_key_sign to ensure this migration
     * can safely occur.
     */
    public function migrateFromLti1p1(LtiMessageLaunch $launch): ?ILtiDeployment;
}
