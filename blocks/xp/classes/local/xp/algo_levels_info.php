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
 * Levels.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace block_xp\local\xp;

use block_xp\local\factory\level_factory;
use coding_exception;
use context;

/**
 * Levels with algorithm computation support.
 *
 * @package    block_xp
 * @copyright  2017 Frédéric Massart
 * @author     Frédéric Massart <fred@branchup.tech>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class algo_levels_info implements levels_info, levels_info_with_algo {

    /** Default number of levels. */
    const DEFAULT_COUNT = 10;
    /** Default base for XP algo. */
    const DEFAULT_BASE = 120;
    /** Default coef for XP algo. */
    const DEFAULT_COEF = 1.3;
    /** Default incr for XP algo. */
    const DEFAULT_INCR = 40;
    /** Default method for XP algo. */
    const DEFAULT_METHOD = 'relative';

    /** @var array The initial data. */
    protected $data;
    /** @var int Number of levels. */
    protected $count;
    /** @var level[] The levels. */
    protected $levels;

    /** @var int Base XP */
    protected $base;
    /** @var float Coef */
    protected $coef;
    /** @var int Incr */
    protected $incr = self::DEFAULT_INCR;
    /** @var string Method */
    protected $method = self::DEFAULT_METHOD;

    /** @var badge_url_resolver|null The badge URL resolver. */
    protected $resolver;
    /** @var level_factory|null The level factory. */
    protected $levelfactory;
    /** @var array The metadata keys. */
    protected $metadatakeys = [];

    /** @var bool Used algo? Deprecated since Level Up XP 3.15 without replacement. */
    protected $usealgo = false;

    /**
     * Constructor.
     *
     * @param array $data Array containing both keys 'xp' and 'desc'. Indexes should start at 1.
     * @param badge_url_resolver $resolver The server resolving badge URLs if any.
     * @param level_factory $levelfactory The level factory.
     */
    public function __construct(array $data, badge_url_resolver $resolver = null, level_factory $levelfactory = null) {
        $this->data = $data;

        if (!empty($data['algo'])) {
            $this->base = isset($data['algo']['base']) ? max(1, (int) $data['algo']['base']) : static::DEFAULT_BASE;
            $this->coef = isset($data['algo']['coef']) ? max(1, (float) $data['algo']['coef']) : static::DEFAULT_COEF;
            $this->incr = isset($data['algo']['incr']) ? max(0, (int) $data['algo']['incr']) : static::DEFAULT_INCR;
            $this->method = $data['algo']['method'];
        } else {
            $this->base = max(1, (int) ($data['base'] ?? static::DEFAULT_BASE));
            $this->coef = max(1, (float) ($data['coef'] ?? static::DEFAULT_COEF));
        }

        // For legacy reasons, if we do not know the method we fall back onto what was the equivalent
        // to the previous algorithm, which is the relative method.
        if (!in_array($this->method, ['flat', 'linear', 'relative'])) {
            $this->method = self::DEFAULT_METHOD;
        }

        $this->resolver = $resolver;
        $this->levelfactory = $levelfactory;
        $this->metadatakeys = array_diff(array_keys($this->data), ['v', 'xp', 'algo']);
    }

    /**
     * XP Base.
     *
     * @return int
     */
    public function get_base() {
        return $this->base;
    }

    /**
     * XP coef.
     *
     * @return float
     */
    public function get_coef() {
        return $this->coef;
    }

    /**
     * XP incr.
     *
     * @return int
     */
    public function get_incr() {
        return $this->incr;
    }

    /**
     * XP method.
     *
     * @return string
     */
    public function get_method() {
        return $this->method;
    }

    /**
     * Get the number of levels.
     *
     * @return int
     */
    public function get_count() {
        $this->load();
        return $this->count;
    }

    /**
     * Get the level.
     *
     * @param int $level The level as a number.
     * @return level
     */
    public function get_level($level) {
        $this->load();
        if (!isset($this->levels[$level])) {
            throw new coding_exception('Invalid level: ' . $level);
        }
        return $this->levels[$level];
    }

    /**
     * Get the level for a certain amount of experience points.
     *
     * @param int $xp The experience points.
     * @return level
     */
    public function get_level_from_xp($xp) {
        $this->load();
        for ($i = $this->get_count(); $i > 0; $i--) {
            $level = $this->levels[$i];
            if ($level->get_xp_required() <= $xp) {
                return $level;
            }
        }
        return $level;
    }

    /**
     * Get the levels.
     *
     * @return level[]
     */
    public function get_levels() {
        $this->load();
        return $this->levels;
    }

    /**
     * Whether the algo was used.
     *
     * @return bool
     * @deprecated Since Level Up XP 3.15 without replacement.
     */
    public function get_use_algo() {
        return $this->usealgo;
    }

    // @codingStandardsIgnoreLine.
    #[\ReturnTypeWillChange]
    /**
     * Serialize that thing.
     *
     * @return array
     */
    public function jsonSerialize() { // @codingStandardsIgnoreLine.
        return [];
    }

    /**
     * Get a level's metadata.
     *
     * @param int $level The level number.
     * @return array Of metadata.
     */
    protected function get_level_metadata($level) {
        return array_reduce($this->metadatakeys, function($carry, $name) use ($level) {
            $carry[$name] = isset($this->data[$name]) ? $this->data[$name][$level] ?? null : null;
            return $carry;
        }, []);
    }

    /**
     * Load the levels.
     */
    protected function load() {
        if ($this->levels !== null) {
            return;
        }

        $data = $this->data;
        $resolver = $this->resolver;
        $leveln = 1;

        $levels = array_reduce(array_keys($this->data['xp']), function($carry, $key) use ($resolver, $data, &$leveln) {
            $level = $leveln++;

            if ($this->levelfactory) {
                $obj = $this->levelfactory->make_level(
                    $level,
                    $data['xp'][$key],
                    $this->get_level_metadata($level),
                    $resolver
                );

            } else {
                // Legacy implementation.
                $desc = isset($data['desc'][$key]) ? $data['desc'][$key] : null;
                $name = isset($data['name'][$key]) ? $data['name'][$key] : null;
                if (!$resolver) {
                    $obj = new described_level($level, $data['xp'][$key], $desc, $name);
                } else {
                    $obj = new badged_level($level, $data['xp'][$key], $desc, $resolver, $name);
                }
            }

            $carry[$level] = $obj;
            return $carry;
        }, []);

        $this->levels = $levels;
        $this->count = count($this->levels);
    }

    /**
     * Make levels from the defaults.
     *
     * @param badge_url_resolver $resolver The badge URL resolver.
     * @param level_factory $levelfactory The level factory.
     * @return self
     */
    public static function make_from_defaults(badge_url_resolver $resolver = null, level_factory $levelfactory = null) {
        return new self([
            'v' => 2,
            'algo' => [
                'method' => self::DEFAULT_METHOD,
                'base' => self::DEFAULT_BASE,
                'coef' => self::DEFAULT_COEF,
                'incr' => self::DEFAULT_INCR,
            ],
            // Version 2 does not index points by level, version 1 used to.
            'xp' => array_values(self::get_xp_with_algo(self::DEFAULT_COUNT, self::DEFAULT_BASE, self::DEFAULT_COEF,
                self::DEFAULT_METHOD, self::DEFAULT_INCR)),
            'name' => [],
            'desc' => [],
        ], $resolver, $levelfactory);
    }

    /**
     * Get the levels and their XP based on a simple algorithm.
     *
     * @param int $levelcount The number of levels.
     * @param int $base The base XP required.
     * @param float $coef The coefficient between levels.
     * @param string $method The method.
     * @param int $incr The incr value.
     * @return array level => xp required.
     */
    public static function get_xp_with_algo($levelcount, $base, $coef, $method = self::DEFAULT_METHOD,
            $incr = self::DEFAULT_INCR) {

        if ($method === 'flat') {
            $list = [];
            for ($i = 1; $i <= $levelcount; $i++) {
                $list[$i] = $base * ($i - 1);
            }
            return $list;
        }

        if ($method === 'linear') {
            $list = [];
            for ($i = 1; $i <= $levelcount; $i++) {
                if ($i == 1) {
                    $list[$i] = 0;
                } else if ($i == 2) {
                    $list[$i] = $base;
                } else {
                    $list[$i] = $list[$i - 1] + $base + ($i - 2) * $incr;
                }
            }
            return $list;
        }

        // Relative method.
        $list = [];
        for ($i = 1; $i <= $levelcount; $i++) {
            if ($i == 1) {
                $list[$i] = 0;
            } else if ($i == 2) {
                $list[$i] = $base;
            } else {

                // Before XP 3.15, the calculation used to be base + round(prevLevel * coef),
                // but in the UI we had switched to the values below (since XP 3.11). So for consistency
                // we're now using the same method here. This change will only affect default levels in
                // the admin, that have never been edited before, and new courses. The difference caused
                // is only +1 point from level 5, to +4 points at level 10, so not dramatic.
                if ($coef <= 1) {
                    $list[$i] = $base * ($i - 1);
                } else {
                    $list[$i] = round($base * ((1 - $coef ** ($i - 1)) / (1 - $coef)));
                }
            }
        }
        return $list;
    }

}
