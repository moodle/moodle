export type ActivityIconCategory = 'assessment' | 'collaboration' | 'communication' | 'interactive' | 'other' | 'resource';
export interface ActivityIconRegistryEntry {
    fileName: string;
    category: ActivityIconCategory;
}
export declare const activityIconRegistry: Record<string, ActivityIconRegistryEntry>;
export type ActivityIconName = keyof typeof activityIconRegistry;
export declare const activityIconNames: string[];
