<?php
$skip_check=true;

//initilize the page
require_once("inc/init.php");

//require UI configuration (nav, ribbon, etc.)
require_once("inc/config.ui.php");

/*---------------- PHP Custom Scripts ---------

YOU CAN SET CONFIGURATION VARIABLES HERE BEFORE IT GOES TO NAV, RIBBON, ETC.
E.G. $page_title = "Custom Title" */

$page_title = "Login";


/* ---------------- END PHP Custom Scripts ------------- */

//include header
//you can add your custom css in $page_css array.
//Note: all css files are inside css/ folder
$page_css[] = "your_style.css";
$no_main_header = true;
$page_body_prop = array("id"=>"extr-page", "class"=>"animated fadeInDown container");
include("inc/header.php");



?>
<!-- ==========================CONTENT STARTS HERE ========================== -->
<!-- possible classes: minified, no-right-panel, fixed-ribbon, fixed-header, fixed-width-->
<header id="header">
	<!--<span id="logo"></span>-->

	<div id="logo-group">
		<span id="logo"> <img src="<?php echo ASSETS_URL; ?>/img_rd4/logo_rd4.png" alt="reteDES.it"> </span>
	</div>
        <span id="extr-page-header-space"><a href="<?php echo APP_URL; ?>/login.php" class="btn btn-danger">Accedi</a> <a href="<?php echo APP_URL; ?>/register.php" class="btn btn-danger">Registrati</a></span>
</header>

<div id="" role="main" style="height:160px;">

	<!-- MAIN CONTENT -->
	<div id="content" class="container">

		<div class="row">
            <div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 hidden-md hidden-lg">
                <h1 style="font-size:48px;" class="Anaheim"><strong>V4</strong>.reteDES.it</h1>
                <h4 class="paragraph-header Anaheim ">Il gestionale artigianale<br>per i GAS e le reti di GAS</h4>
                <br><br>

            </div>
			<div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 hidden-xs hidden-sm">
                <h1 style="font-size:72px;" class="Anaheim"><strong>V4</strong>.reteDES.it</h1>
                <div class="" style="">

					<div class="pull-left login-desc-box-l">
						<h3 class="paragraph-header Anaheim ">Il gestionale artigianale<br>per i GAS e le reti di GAS</h3>
                        <p class="margin-top-10" style="height:20px;"></p>
                    </div>
                    <img class="hidden-phone hidden-tablet pull-right" src="<?php echo ASSETS_URL; ?>/img_rd4/RD_v3_320.png" class="pull-right display-image" alt="" style="width:240px;">
                </div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<div class="well well-lg"><h1>Alcune domande che di solito vengono in mente...</h1></div>
<div class="container row">
    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> Quali sono i princìpi fondanti?</h3>
            <p>reteDES.it è un gestionale il cui scopo unico è quello di agevolare per quanto possibile la parte più noiosa e frustrante dell'attività di un gas, cioè la parte contabile e amministrativa degli ordini. L'idea è quella di creare uno strumento che permetta di suddividere le responsabilità derivanti dalla gestione di un gas su più utenti possibili, in modo da non gravare sempre sulle solite persone, con la possibilità allo stesso tempo di coinvolgere attivamente anche gli utenti più pigri.
            </p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> Come funziona l'iscrizione?</h3>
            <p>Una volta inseriti i propri dati nella maschera iniziale, occorre attendere che l'account sia abilitato da un "gestore utenti" attivo presso il gas che si è scelto. In questo modo si può controllare l'accesso ad un pubblico di utenti solo veramente interessato, lasciando "pulito" il sito da account fasulli, trolls e tutto quanto di brutto può girare per internet.
            </p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> Come farò ad ordinare della merce?</h3>
            <p>Il sito NON vuole sconvolgere le abitudini di ogni gas. Per questo non esiste una risposta a questa domanda, nel senso che il gestionale permette una maggiore comodità, senza però influire su tutto quello che un GAS ha costruito nel frattempo, come logistica, organizzazione, gruppi ecc </p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> Come farò a gestire gli ordini?</h3>
            <p>Per gestire gli ordini ci sono un sacco di strumenti, nati tutti da richieste che hanno fatto i GAS nel corso di questi anni. Per cui probabilmente ci saranno molte funzioni che non interessano, ma allo stesso tempo troverete quella che vi farà lavorare come avete sempre fatto, solamente con più comodità.</p>
        </div>
    </div>

    <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6">
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> Cosa sono gli ordini condivisi?</h3>
            <p>Come dice il nome, reteDES gestisce "GAS e reti di GAS (Distretti di economia solidale), cioè raggruppamenti di gas che sporadicamente o continuativamente hanno la necessità o il piacere di gestire gli ordini in maniera condivisa. Questo significa che un GAS "A" può aprire un ordine di pasta e condividerlo con il gas "B" e "C",  in modo che assieme si possano raggiungere quantitativi importanti e facilitare le logistiche di distribuzione.</p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> C'è la gestione della cassa?</h3>
            <p>Si, la cassa è gestita. Però è da far notare che la cassa di reteDES NON SOSTITUISCE il metodo tradizionale con cui il vostro cassiere lavora. Sarà solo uno strumento per aiutare la gestione, con l'unico scopo di far risparmiare tempo ai poveri cassieri.
            </p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> Ci sono già ditte e listini inseriti?</h3>
            <p>Si, certo. Le ditte sono visibili a tutti, mentre i listini possono essere sia pubblici che privati. I listini possono essere gestiti da gruppi di utenti infra-gas, in modo da essere sempre aggiornati e pronti all'uso.
            </p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> C'è un sistema di avvisi?</h3>
            <p>Per gli avvisi tutto il sistema è basato sulla mail; Cerchiamo di essere il meno invasivi possibili, mandando solo mail che l'utente vuole ricevere.</p>
        </div>
        <div class="well">
            <h3><i class="fa fa-question-circle"></i> I miei dati sono pubblici?</h3>
            <p>I dati personali sono appunto "personali", quindi la loro diffusione è la più limitata possibile, nel senso che saranno visibili dal vostro gas, e dagli utenti con i quali condividerete gli ordini.</p>
        </div>
    </div>
</div>

<!-- END MAIN PANEL -->
<!-- ==========================CONTENT ENDS HERE ========================== -->

<?php
	//include required scripts
    
	include("inc/scripts.php");
?>

<!-- PAGE RELATED PLUGIN(S)
<script src="..."></script>-->

<script type="text/javascript">
</script>

<?php
	//include footer
	include("inc/google-analytics.php");
?>