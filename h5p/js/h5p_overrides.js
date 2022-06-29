H5P._getLibraryPath = H5P.getLibraryPath;
H5P.getLibraryPath = function (library) {
    if (H5PIntegration.moodleLibraryPaths) {
        if (H5PIntegration.moodleLibraryPaths[library]) {
            return H5PIntegration.moodleLibraryPaths[library];
        }
    }
    return H5P._getLibraryPath(library);
};
H5P.findInstanceFromId = function (contentId) {
    if (!contentId) {
        return H5P.instances[0];
    }
    if (H5P.instances !== undefined) {
        for (var i = 0; i < H5P.instances.length; i++) {
            if (H5P.instances[i].contentId === contentId) {
                return H5P.instances[i];
            }
        }
    }
    return undefined;
};
H5P.getXAPIStatements = function (contentId, statement) {
    var statements = [];
    var instance = H5P.findInstanceFromId(contentId);
    if (!instance){
        return statements;
    }
    if (instance.getXAPIData == undefined) {
        var xAPIData = {
            statement: statement
        };
    } else {
        var xAPIData = instance.getXAPIData();
    }
    if (xAPIData.statement != undefined) {
        statements.push(xAPIData.statement);
    }
    if (xAPIData.children != undefined) {
        statements = statements.concat(xAPIData.children.map(a => a.statement));
    }
    return statements;
};
H5P.getMoodleComponent = function () {
    if (H5PIntegration.moodleComponent) {
        return H5PIntegration.moodleComponent;
    }
    return undefined;
};

/**
 * Set the actor. (Moved to overrides due to MDL-69467)
 */
H5P.XAPIEvent.prototype.setActor = function () {
    if (H5PIntegration.user !== undefined) {
        this.data.statement.actor = {
            'name': H5PIntegration.user.name,
            'objectType': 'Agent'
        };
        if (H5PIntegration.user.id !== undefined) {
            this.data.statement.actor.account = {
                'name': H5PIntegration.user.id,
                'homePage': H5PIntegration.siteUrl
            }
        } else if (H5PIntegration.user.mail !== undefined) {
            this.data.statement.actor.mbox = 'mailto:' + H5PIntegration.user.mail;
        }
    } else {
        var uuid;
        try {
            if (localStorage.H5PUserUUID) {
                uuid = localStorage.H5PUserUUID;
            } else {
                uuid = H5P.createUUID();
                localStorage.H5PUserUUID = uuid;
            }
        }
        catch (err) {
            // LocalStorage and Cookies are probably disabled. Do not track the user.
            uuid = 'not-trackable-' + H5P.createUUID();
        }
        this.data.statement.actor = {
            'account': {
                'name': uuid,
                'homePage': H5PIntegration.siteUrl
            },
            'objectType': 'Agent'
        };
    }
};
