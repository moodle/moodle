M.block_iomad_progress = {
    iomad_progressBarLast: new Array(),

    init: function (YUIObject, instances, users) {
        for (instance = 0; instance < instances.length; instance++) {
            for (user = 0; user < users.length; user++) {
                this.iomad_progressBarLast[instances[instance] + '-' + users[user]] = 'info';
            }
        }
    },

    showInfo: function (instance, user, id) {
        var last = this.iomad_progressBarLast[instance + '-' + user];
        document.getElementById('iomad_progressBarInfo' + instance + '-' + user + '-' + last).style.display = 'none';
        document.getElementById('iomad_progressBarInfo' + instance + '-' + user + '-' + id).style.display = 'block';
        this.iomad_progressBarLast[instance + '-' + user] = id;
    }
};
