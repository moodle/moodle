define(
    [
        'jquery',
        'tool_ally/main',
        'core/ajax',
        'tool_ally/vuecomp/th-Filter',
        'tool_ally/vuecomp/td-HTML',
        'tool_ally/vuecomp/td-LogDetails'

    ],
    function($, main, ajax, thFilter, tdHTML, tdLogDetails) {
    return {
        Vue: null,
        globalComponents: {}, // Additional global components to be registered by main bootstrapper.
        init: function() {

            var self = this;
            var logData = null;

            // Routing components.
            var dataTableTemplate = function(id) {
                return '<div id="' + id + '"><datatable v-bind="data">'
                    + '</datatable></div>';
            };

            var dumpList = { props: ['data'], template: dataTableTemplate('dt-allylog')};

            // Register th, td components.
            this.globalComponents.thFilter = thFilter;
            this.globalComponents.tdHTML = tdHTML;
            this.globalComponents.tdLogDetails = tdLogDetails;

            ajax.call([{
                methodname: 'tool_ally_get_logs',
                args: {
                    query: null
                }
            }])[0].then(function(data) {
                logData = data;
                var routes = [
                    {path: '/', redirect: '/logView'},
                    {path: '/logView', component: dumpList, props: {data: logData}},
                ];

                main.init({
                    data: {logData: logData},
                    routes: routes,

                    globalComponents: self.globalComponents,
                    watch: {
                        'logData.query': {
                            handler: function(query) {
                                ajax.call([{
                                    methodname: 'tool_ally_get_logs',
                                    args: {
                                        query: JSON.stringify(query)
                                    }
                                }])[0].then(function(data) {
                                    logData = data;
                                    // We don't need to update the columns or the query - just the data and pagination.
                                    // Updating the query would put us in a watch loop!
                                    self.Vue.logData.data = logData.data;
                                    self.Vue.logData.total = logData.total;
                                });
                            },
                            deep: true
                        }
                    }
                }).then(function(Vue) {
                    self.Vue = Vue;
                });

            });
        }
    };
});