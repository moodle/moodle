M.core_ratings={

    Y : null,
    transaction : [],

    init : function(Y){
        this.Y = Y;
        Y.all('select.postratingmenu').each(this.attach_rating_events, this);

        //hide the submit buttons
        this.Y.all('input.postratingmenusubmit').setStyle('display', 'none');
    },

    attach_rating_events : function(selectnode) {
        selectnode.on('change', this.submit_rating, this, selectnode);
    },

    submit_rating : function(e, selectnode){
        var theinputs = selectnode.ancestor('form').all('.ratinginput')
        var thedata = [];

        var inputssize = theinputs.size();
        for ( var i=0; i<inputssize; i++ )
        {
            if(theinputs.item(i).get("name")!="returnurl") {//dont include return url for ajax requests
                thedata[theinputs.item(i).get("name")] = theinputs.item(i).get("value");
            }
        }
        
        this.Y.io.queue.stop();
        this.transaction.push({transaction:this.Y.io.queue(M.cfg.wwwroot+'/rating/rate_ajax.php', {
            method : 'POST',
            data : build_querystring(thedata),
            on : {
                complete : function(tid, outcome, args) {
                    try {
                        outcome = this.Y.JSON.parse(outcome.responseText);
                    } catch(e) {
                        //this.form.submit();
                        alert(outcome.responseText);
                    }
                    if(outcome.success){
                        //do nothing
                    }
                    else if (outcome.error){
                        //todo andrew put up an overlay or similar rather than an alert
                        alert(outcome.error);
                    }
                }
            },
            context : this,
            arguments : {
                //query : this.query.get('value')
            }
        }),complete:false,outcome:null});
        this.Y.io.queue.start();
    }
}