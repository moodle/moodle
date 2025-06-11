<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * RedisCluster Cache Store - Add instance form
 *
 * @package   cachestore_rediscluster
 * @copyright 2013 Adam Durana
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot.'/cache/forms.php');

/**
 * Form for adding instance of RedisCluster Cache Store.
 *
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class cachestore_rediscluster_addinstance_form extends cachestore_addinstance_form {
    /**
     * Builds the form for creating an instance.
     */
    protected function configuration_definition() {
        $form = $this->_form;

        if (!class_exists('RedisCluster')) {
            return;
        }

        $form->addElement('text', 'server', get_string('server', 'cachestore_rediscluster'));
        $form->addHelpButton('server', 'server', 'cachestore_rediscluster');
        $form->addRule('server', get_string('required'), 'required');
        $form->setType('server', PARAM_TEXT);

        $form->addElement('text', 'serversecondary', get_string('serversecondary', 'cachestore_rediscluster'));
        $form->addHelpButton('serversecondary', 'serversecondary', 'cachestore_rediscluster');
        $form->addRule('serversecondary', get_string('required'), 'required');
        $form->setType('serversecondary', PARAM_TEXT);

        $form->addElement('text', 'prefix', get_string('prefix', 'cachestore_rediscluster'), ['size' => 16]);
        $form->setType('prefix', PARAM_TEXT); // We set to text but we have a rule to limit to alphanumext.
        $form->addHelpButton('prefix', 'prefix', 'cachestore_rediscluster');
        $form->addRule('prefix', get_string('prefixinvalid', 'cachestore_rediscluster'), 'regex', '#^[a-zA-Z0-9\-_]+$#');

        $opts = [
            RedisCluster::FAILOVER_NONE => get_string('failovernone', 'cachestore_rediscluster'),
            RedisCluster::FAILOVER_ERROR => get_string('failovererror', 'cachestore_rediscluster'),
            RedisCluster::FAILOVER_DISTRIBUTE => get_string('failoverdistribute', 'cachestore_rediscluster'),
        ];
        // Experimental setting, only add it if its available.
        // https://github.com/phpredis/phpredis/pull/1896 .
        if (defined('RedisCluster::FAILOVER_PREFERRED')) {
            $opts[RedisCluster::FAILOVER_PREFERRED] = get_string('failoverpreferred', 'cachestore_rediscluster');
        }
        $form->addElement('select', 'failover', get_string('failover', 'cachestore_rediscluster'), $opts);
        $form->addHelpButton('failover', 'failover', 'cachestore_rediscluster');
        $form->setDefault('failover', RedisCluster::FAILOVER_NONE);

        if (defined('RedisCluster::FAILOVER_PREFERRED')) {
            $form->addElement('text', 'preferrednodes', get_string('preferrednodes', 'cachestore_rediscluster'));
            $form->addHelpButton('preferrednodes', 'preferrednodes', 'cachestore_rediscluster');
            $form->disabledIf('preferrednodes', 'failover', 'neq', RedisCluster::FAILOVER_PREFERRED);
            $form->setType('preferrednodes', PARAM_TEXT);
            $form->setDefault('preferrednodes', '');
        }

        $form->addElement('checkbox', 'persist', get_string('persist', 'cachestore_rediscluster'));
        $form->addHelpButton('persist', 'persist', 'cachestore_rediscluster');
        $form->setDefault('persist', 0);
        $form->setType('persist', PARAM_BOOL);

        $form->addElement('text', 'timeout', get_string('timeout', 'cachestore_rediscluster'));
        $form->addHelpButton('timeout', 'timeout', 'cachestore_rediscluster');
        $form->setType('timeout', PARAM_FLOAT);
        $form->setDefault('timeout', '3.0');

        $form->addElement('text', 'readtimeout', get_string('readtimeout', 'cachestore_rediscluster'));
        $form->addHelpButton('readtimeout', 'readtimeout', 'cachestore_rediscluster');
        $form->setType('readtimeout', PARAM_FLOAT);
        $form->setDefault('readtimeout', '3.0');

        $opts = [
            Redis::SERIALIZER_PHP => get_string('serializerphp', 'cachestore_rediscluster'),
            Redis::SERIALIZER_IGBINARY => get_string('serializerigbinary', 'cachestore_rediscluster'),
        ];
        $form->addElement('select', 'serializer', get_string('serializer', 'cachestore_rediscluster'), $opts);
        $form->addHelpButton('serializer', 'serializer', 'cachestore_rediscluster');
        $form->setDefault('serializer', Redis::SERIALIZER_PHP);

        $opts = [
            Redis::COMPRESSION_NONE => get_string('compressionnone', 'cachestore_rediscluster'),
        ];

        if (defined('Redis::COMPRESSION_LZ4')) {
            $opts[Redis::COMPRESSION_LZ4] = get_string('compressionlz4', 'cachestore_rediscluster');
        }

        if (defined('Redis::COMPRESSION_LZF')) {
            $opts[Redis::COMPRESSION_LZF] = get_string('compressionlzf', 'cachestore_rediscluster');
        }
        if (defined('Redis::COMPRESSION_ZSTD')) {
            $opts[Redis::COMPRESSION_ZSTD] = get_string('compressionzstd', 'cachestore_rediscluster');
        }
        $form->addElement('select', 'compression', get_string('compression', 'cachestore_rediscluster'), $opts);
        $form->addHelpButton('compression', 'compression', 'cachestore_rediscluster');
        $form->setDefault('compression', Redis::COMPRESSION_NONE);

    }
}
