(function(){
    var Y;
    
    M.mod_lti = M.mod_lti || {};
    
    M.mod_lti.submissions = {
        init: function(yui3){
            if(yui3){
                Y = yui3;
            }
            
            this.setupTable();
            
            
        },
        
        setupTable: function(){
            var lti_submissions_table = YAHOO.util.Dom.get('lti_submissions_table');
    
            var dataSource = new YAHOO.util.DataSource(lti_submissions_table);

            var configuredColumns = [
                { key: "user", label: "User", sortable:true },
                { key: "date", label: "Submission Date", sortable:true, formatter: 'date' },
                { key: "grade", 
                  label: "Grade", 
                  sortable:true, 
                  formatter: function(cell, record, column, data){
                      cell.innerHTML = parseFloat(data).toFixed(1) + '%';
                  } 
                }
            ];

            dataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;
            dataSource.responseSchema = {
                fields: [
                    { key: "user" },
                    { key: "date", parser: "date" },
                    { key: "grade", parser: "number" },
                ]
            };

            new YAHOO.widget.DataTable("lti_submissions_table_container", configuredColumns, dataSource,
                {
                    sortedBy: {key:"date", dir:"desc"}
                }
            );
            
            Y.one('#lti_submissions_table_container').setStyle('display', '');
        }
    }
})();