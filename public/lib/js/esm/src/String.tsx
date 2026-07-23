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

import {Suspense, use, type ReactNode} from 'react';
import {getString, type StringParams} from './stringUtils';

export interface StringProps {
    identifier: string;
    component?: string;
    params?: StringParams;
}

function StringInner({identifier, component, params}: StringProps) {
    return <>{use(getString(identifier, component, params))}</>;
}

function String({children, identifier, component = 'core', params}: StringProps & {children?: ReactNode}) {
    return (
        <Suspense fallback={children ?? `${identifier}, ${component}`}>
            <StringInner identifier={identifier} component={component} params={params} />
        </Suspense>
    );
}

export default String;
