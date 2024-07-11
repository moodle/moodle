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
 * This file contains the JS code to support mod_subsection in versions of the app previous to 4.5.
 *
 * @copyright   2024 Dani Palou <dani@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

const context = this;

/**
 * Open a subsection.
 */
const openSubsection = async(module, courseId, siteId) => {
    const customData = context.CoreTextUtilsProvider.parseJSON(module.customdata);
    const pageParams = {
        sectionId: customData.sectionid,
    };

    if (
        (!siteId || siteId === context.CoreSitesProvider.getCurrentSiteId()) &&
        context.CoreCourseProvider.currentViewIsCourse(courseId)
    ) {
        context.CoreCourseProvider.selectCourseTab('', pageParams);
    } else {
        await context.CoreCourseHelperProvider.getAndOpenCourse(courseId, pageParams, siteId);
    }
};

/**
 * Handler to support mod_subsection in a course.
 */
class SubsectionModuleHandler {

    constructor() {
        this.name = 'PluginModSubsection';
        this.modName = 'subsection';
    }

    isEnabled() {
        return true;
    }

    getData(module) {
        return {
            icon: context.CoreCourseProvider.getModuleIconSrc(module.modname, module.modicon),
            title: module.name,
            action: async(event, module, courseId) => {
                try {
                    await openSubsection(module, courseId);
                } catch (error) {
                    context.CoreDomUtilsProvider.showErrorModalDefault(error, 'Error opening subsection.');
                }
            },
        };
    }
}

/**
 * Handler to support links to mod_subsection.
 */
class SubsectionLinkHandler extends this.CoreContentLinksHandlerBase {

    constructor() {
        super();

        this.name = 'PluginModSubsection';
        this.priority = 0;
        this.featureName = 'CoreCourseModuleDelegate_AddonModSubsection';
        this.pattern = new RegExp('/mod/subsection/view.php.*([&?]id=\\d+)');
    }

    getActions(siteIds, url, params, courseId) {
        return [{
            action: async(siteId) => {
                const modal = await context.CoreDomUtilsProvider.showModalLoading();
                const moduleId = Number(params.id);

                try {
                    // Get the module.
                    const module = await context.CoreCourseProvider.getModule(moduleId, courseId, undefined, true, false, siteId);

                    await openSubsection(module, module.course, siteId);
                } catch (error) {
                    context.CoreDomUtilsProvider.showErrorModalDefault(error, 'Error opening link.');
                } finally {
                    modal.dismiss();
                }
            },
        }];
    }

}

this.CoreCourseModuleDelegate.registerHandler(new SubsectionModuleHandler());
this.CoreContentLinksDelegate.registerHandler(new SubsectionLinkHandler());
