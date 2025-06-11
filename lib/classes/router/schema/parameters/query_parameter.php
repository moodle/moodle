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

namespace core\router\schema\parameters;

use core\exception\coding_exception;
use core\param;
use core\router\schema\parameter;
use core\router\schema\specification;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Routing query parameter for validation.
 *
 * @package    core
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class query_parameter extends parameter {
    /**
     * Query parameter constructor to override the location of the parameter.
     *
     * @param bool|null $allowreserved Determines whether the parameter value SHOULD allow reserved characters.
     * @param array ...$extra
     */
    public function __construct(
        /**
         * Determines whether the parameter value SHOULD allow reserved characters.
         *
         * As defined by [RFC3986], these characters are :/?#[]@!$&'()*+,;= to be included without percent-encoding.
         * This property only applies to parameters with an in value of query. The default value is false.
         *
         * @var bool|null
         */
        protected ?bool $allowreserved = null,
        ...$extra,
    ) {
        $extra['in'] = parameter::IN_QUERY;
        parent::__construct(...$extra);
    }

    /**
     * Validate query parameters.
     *
     * @param ServerRequestInterface $request
     * @param array $params
     * @return ServerRequestInterface
     * @throws coding_exception
     * @throws \ValueError
     */
    public function validate(
        ServerRequestInterface $request,
        array $params,
    ): ServerRequestInterface {
        if (array_key_exists($this->name, $params)) {
            // This parameter was specified. Validate it.
            if ($this->get_type() === param::BOOL) {
                match ($params[$this->name]) {
                    'true' => $params[$this->name] = 1,
                    'false' => $params[$this->name] = 0,
                    default => throw new \ValueError('Invalid boolean value.'),
                };
            }
            $this->type->validate_param($params[$this->name]);

            return $this->update_request_params(
                $request,
                array_merge(
                    $params,
                    [$this->name => $params[$this->name]],
                ),
            );
        }

        if ($this->required) {
            throw new coding_exception(
                "A required parameter {$this->name} was not provided and must be specified",
            );
        }

        if ($this->default !== null) {
            // This parameter is optional. Fill the default.
            return $this->update_request_params(
                $request,
                array_merge(
                    $params,
                    [$this->name => $this->default],
                ),
            );
        }

        // This parameter is optional and there is no default.
        // Fill a null value.
        return $this->update_request_params(
            $request,
            array_merge(
                $params,
                [$this->name => null],
            ),
        );
    }

    /**
     * Update the request parameters.
     *
     * @param ServerRequestInterface $request
     * @param array $params
     * @return ServerRequestInterface
     */
    protected function update_request_params(
        ServerRequestInterface $request,
        array $params,
    ): ServerRequestInterface {
        return $request->withQueryParams($params);
    }

    #[\Override]
    final public function get_openapi_description(
        specification $api,
        ?string $path = null,
    ): ?\stdClass {
        $data = parent::get_openapi_description($api, $path);

        if ($this->allowreserved) {
            // Determines whether the parameter value SHOULD allow reserved characters, as defined by [RFC3986]
            // :/?#[]@!$&'()*+,;=
            // to be included without percent-encoding.
            // This property only applies to parameters with an in value of query. The default value is false.
            $data->allowReserved = $this->allowreserved;
        }

        return $data;
    }
}
