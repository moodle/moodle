export enum ContextLevel {
  System = 10,
  User = 30,
  CourseCategory = 40,
  Course = 50,
  Module = 70,
}

export interface Level {
  level: number;
  xprequired: number;
  description: string | null;
  name: string | null;
  badgeurl: string | null;
  badgeawardid?: number | null;
  popupmessage?: string | null;
}

export interface LevelsInfo {
  count: number;
  levels: Level[];
  algo: Omit<PointCalculationMethod, "method" | "incr"> &
    // Method and incr are not guaranteed to be present.
    Partial<Pick<PointCalculationMethod, "method" | "incr">> & {
      /** @deprecated No longer used. */
      enabled?: boolean;
    };
}

export interface PointCalculationMethod {
  method: "flat" | "linear" | "relative";
  base: number;
  coef: number; // Float. e.g. 1.2 = 20% increase.
  incr: number;
}

interface ResourceBase<TName extends string|number = string|number> {
  type?: string;
  name: TName; // A name uniquely identifying this resource.
  label: string;
  description?: string;
  isavailable?: boolean;
};

export interface ResourceItem<TName extends string|number = string|number> extends ResourceBase<TName> {}

export interface ResourceHeading<TName extends string|number = string|number> extends ResourceBase<TName> {
  type: 'header'
};

export type Resource<TName extends string|number = string|number> = ResourceBase<TName> | ResourceHeading<TName>;
