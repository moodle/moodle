import { ReactElement } from 'react';
export type IconElement = ReactElement<'i' | 'svg'>;
export declare const isIconElement: (el: unknown) => el is IconElement;
