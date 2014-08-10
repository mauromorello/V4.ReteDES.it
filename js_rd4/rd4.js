function ok(msg){
        $.smallBox({
                            title : "ReteDES.it",
                            content : "<i class='fa fa-check'></i> <i>" + msg + "</i>",
                            color : "#0074A7",
                            iconSmall : "fa fa-thumbs-up bounce animated",
                            timeout : 4000
        });
    }

    function ko(msg){
        $.smallBox({
                            title : "Attenzione!",
                            content : "<i class='fa fa-warning'></i> <i>" + msg + "</i>",
                            color : "#FC8600",
                            iconSmall : "fa fa-thumbs-down bounce animated",
                            timeout : 4000
        });
}