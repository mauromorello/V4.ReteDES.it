// Arguments :
//  verb : 'GET'|'POST'
//  target : an optional opening target (a name, or "_blank"), defaults to "_self"
open = function(verb, url, data, target) {
  var form = document.createElement("form");
  form.action = url;
  form.method = verb;
  form.target = target || "_self";
  if (data) {
    for (var key in data) {
      var input = document.createElement("textarea");
      input.name = key;
      input.value = typeof data[key] === "object" ? JSON.stringify(data[key]) : data[key];
      form.appendChild(input);
    }
  }
  form.style.display = 'none';
  document.body.appendChild(form);
  form.submit();
};


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
function okWait(msg){
        $.smallBox({
                title : "ReteDES.it",
                content : msg + "<p class='text-align-right'><a href='javascript:void(0);' class='btn btn-default btn-sm'>Ok</a></p>",
                color : "#0074A7",
                //timeout: 8000,
                icon : "fa fa-bell swing animated"
            });
    }


$(document).on('click',".aggiungi_ditta",function(e){
            $.SmartMessageBox({
                title : "Aggiungi un nuovo fornitore ?",
                content : "Inserisci solo il suo nome, potrai poi fare tutte le altre operazioni successivamente.",
                buttons : "[Esci][Salva]",
                input : "text",
                placeholder : "Nome",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Salva"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/fornitori/_act.php",
                          dataType: 'json',
                          data: {act: "aggiungi_ditta", value : Value},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okWait(data.msg);
                                    location.replace("http://retegas.altervista.org/gas4/?#ajax_rd4/fornitori/scheda.php?id="+data.id);
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });

            e.preventDefault();
        })
$(document).on('click',".aggiungi_listino",function(e){
    var id_ditta = $(this).data('id_ditta');
    $.SmartMessageBox({
                title : "Aggiungi un nuovo listino ?",
                content : "Inserisci solo il suo nome, potrai poi fare tutte le altre operazioni successivamente.<br>I listini non compilati integralmente saranno cancellati entro qualche ora automaticamente.",
                buttons : "[Esci][Salva]",
                input : "text",
                placeholder : "Nome",
                inputValue: '',
            }, function(ButtonPress, Value) {

                if(ButtonPress=="Salva"){
                    $.ajax({
                          type: "POST",
                          url: "ajax_rd4/listini/_act.php",
                          dataType: 'json',
                          data: {act: "aggiungi_listino", value : Value, id:id_ditta},
                          context: document.body
                        }).done(function(data) {
                            if(data.result=="OK"){
                                    okWait(data.msg);
                                    location.replace("http://retegas.altervista.org/gas4/?#ajax_rd4/listini/listino.php?id="+data.id);
                            }else{
                                ko(data.msg)
                            ;}

                        });
                }
            });

            e.preventDefault();
        })
function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;
     document.body.innerHTML = printContents;
     window.print();
     document.body.innerHTML = originalContents;
}
function selectElementContents(el) {
    //selectElementContents( document.getElementById('table') );
    console.log("copying...")
    var body = document.body, range, sel;
    if (document.createRange && window.getSelection) {
        range = document.createRange();
        sel = window.getSelection();
        sel.removeAllRanges();
        try {
            range.selectNodeContents(el);
            sel.addRange(range);
        } catch (e) {
            range.selectNode(el);
            sel.addRange(range);
        }
    } else if (body.createTextRange) {
        range = body.createTextRange();
        range.moveToElementText(el);
        range.select();
    }
}