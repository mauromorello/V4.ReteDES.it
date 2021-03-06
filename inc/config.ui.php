<?php

//CONFIGURATION for SmartAdmin UI

//ribbon breadcrumbs config
//array("Display Name" => "URL");
$breadcrumbs = array(
	"Home" => APP_URL
);

/*navigation array config

ex:
"dashboard" => array(
	"title" => "Display Title",
	"url" => "http://yoururl.com",
	"url_target" => "_blank",
	"icon" => "fa-home",
	"label_htm" => "<span>Add your custom label/badge html here</span>",
	"sub" => array() //contains array of sub items with the same format as the parent
)

*/

if(_USER_PERMISSIONS & perm::puo_gestire_retegas){
    $amministra_menu = array(
                        "title" => "Ammimistra",
                        "url" => "ajax_rd4/admin/admin.php",
                        "icon" => "fa-unlock"
                        );

}else{
    $amministra_menu = array("title" => "Ammimistra",
                             "icon" => "fa-lock");
}
if(_USER_GAS_USA_CASSA){
    if(_USER_PERMISSIONS & perm::puo_gestire_la_cassa){
    $cassa_menu = array(
                        "title" => "Cassa",
                        "icon" => "fa-bank",
                        "sub" => array(
                                                "cassa_home" => array(
                                                    'title' => 'Riepilogo',
                                                    "url" => "ajax_rd4/cassa/cassa_home.php"),


                                                "cassa_richieste" => array(
                                                    'title' => 'Richieste carico',
                                                    "url" => "ajax_rd4/cassa/richieste.php")
                                        )
                    );
    }else{
        $cassa_menu = array("title" => "Cassa","icon" => "fa-lock");
    }
}else{
    $cassa_menu = array("title" => "Cassa",
                             "icon" => "fa-lock");
}
$user_menu = array(
                    "title" => _USER_FULLNAME,
                    "icon" => "fa-user",
                    "sub" => array(

                            "amministra_menu" => $amministra_menu,

                            "useranagrafica" => array(
                                'title' => 'Anagrafica',
                                "url" => "ajax_rd4/user/anagrafiche.php"),

                            "usercassa" => array(
                                'title' => 'Cassa',
                                "url" => "ajax_rd4/user/miacassa.php"),

                            "useramici" => array(
                                "title" => "Amici",
                                'url' => 'ajax_rd4/user/amici.php'),

                            "userimpostazioni" => array(
                                        "title" => "Impostazioni",
                                        'url' => 'ajax_rd4/user/impostazioni.php')
                    )
                );

$gas_menu = array(
    "title" => _USER_GAS_NOME,
    "icon" => "fa-home",
    "sub" => array(
                            "gas_home" => array(
                                'title' => 'Scheda',
                                "url" => "ajax_rd4/gas/gas_home.php"),


                            "gas_utenti" => array(
                                'title' => 'Utenti',
                                "url" => "ajax_rd4/gas/gas_utenti.php")
                    )
);

$help_menu =  array(



    "title" => "Aiuto!",
    "icon" => "fa-question",
    "sub" => array(
                            "help_inizio" => array(
                                'title' => 'un nuovo inizio',
                                'icon' => 'fa-smile-o',
                                "url" => "ajax_rd4/help/help_inizio.php"),

                            "help_ordini" => array(
                                'title' => 'Ordini',
                                "url" => "ajax_rd4/help/help_ordini.php"),

                            "help_cronologia" => array(
                                'title' => 'Cronologia Help',
                                'icon' => 'fa-lock',
                                "url" => "ajax_rd4/help/cronologia_help.php"),

                            "help_gas" => array(
                                'title' => 'GAS & Utenti',
                                "url" => "ajax_rd4/help/help_gas.php"),

                            "help_sviluppo" => array(
                                "title" => "Sviluppatori",
                                'url' => 'ajax_rd4/help/help_developer.php')
                    )
);

$fornitori_menu =  array(



    "title" => "Fornitori",
    "icon" => "fa-truck",
    "sub" => array(
                            "fornitori_tutti" => array(
                                'title' => 'Tutte le ditte',
                                "url" => "ajax_rd4/fornitori/tutti_fornitori.php"),


                            "fornitori_miei" => array(
                                'title' => 'Le mie ditte',
                                "icon" => "fa-star",
                                "url" => "ajax_rd4/fornitori/miei_fornitori.php"),

                           "listini_miei" => array(
                                'title' => 'I miei listini',
                                "icon" => "fa-star",
                                "url" => "ajax_rd4/listini/miei.php")

                    )
);

if(_USER_PERMISSIONS & perm::puo_creare_ordini){
    $ordini_menu_nuovo = array("title" => "nuovo",
                                "icon" => "fa-star",
                                "url" => "ajax_rd4/ordini/nuovo.php");
}else{
   $ordini_menu_nuovo = array("title" => "nuovo",
                            "icon" => "fa-lock");
}

$ordini_menu =  array(

    "title" => "Ordini",
    "icon" => "fa-shopping-cart",
    "sub" => array(
                                "ordini_calendario" => array(
                                'title' => 'calendario',
                                'icon' => 'fa-calendar',
                                "url" => "ajax_rd4/ordini/calendario.php"),
                                "ordini_nuovo" => $ordini_menu_nuovo
                    )
);




$page_nav =array(   "dashboard" => array(
                            "title" => "Cruscotto",
                            "icon" => "fa-dashboard",
                            "url" => "ajax_rd4/home.php"),
                    "cassa_menu" => $cassa_menu,
                    "user_home" => $user_menu,

                    "gas_menu" => $gas_menu,
                    "ordini_menu" => $ordini_menu,
                    "fornitori_menu" => $fornitori_menu,
                    "help_menu" => $help_menu
            );




//configuration variables
$page_title = "";
$page_css = array();
$no_main_header = false; //set true for lock.php and login.php
$page_body_prop = array(); //optional properties for <body>
$page_html_prop = array(); //optional properties for <html>
?>