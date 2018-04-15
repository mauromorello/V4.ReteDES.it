<?php
$skip_check=true;

//initilize the page
require_once("inc/init.php");

//require UI configuration (nav, ribbon, etc.)
require_once("inc/config.ui.php");

/*---------------- PHP Custom Scripts ---------

YOU CAN SET CONFIGURATION VARIABLES HERE BEFORE IT GOES TO NAV, RIBBON, ETC.
E.G. $page_title = "Custom Title" */

$page_title= "Login";


/* ---------------- END PHP Custom Scripts ------------- */


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
                <h1 style="font-size:48px;" class="Anaheim">rete<strong>DES</strong></h1>
                <h4 class="paragraph-header Anaheim ">Il gestionale artigianale<br>per i GAS e le reti di GAS</h4>
                <br><br>

            </div>
			<div class="col-xs-12 col-sm-12 col-md-7 col-lg-8 hidden-xs hidden-sm">
                <h1 style="font-size:72px;" class="Anaheim">rete<strong>DES</strong></h1>
                <div class="" style="">

					<div class="pull-left">
						<h3 class="paragraph-header Anaheim ">Il gestionale artigianale<br>per i GAS e le reti di GAS</h3>
                        <p class="margin-top-10" style="height:20px;"></p>
                    </div>
                    <img class="hidden-phone hidden-tablet pull-right" src="<?php echo ASSETS_URL; ?>/img_rd4/rd4_2017_320x320_trasparente.png" class="pull-right display-image" alt="" style="width:240px;">
                </div>
			</div>
            
		</div>
	</div>
</div>
<div class="clearfix"></div>

<div class="container row">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
        <div class="well well-lg">
            <h3><i class="fa fa-reply"></i> Disclaimer</h3>
            <p>
            <p>L'accesso e l'utilizzo del sito (di seguito, per brevità, anche "Sito" o "ReteDES", o "ReteGas.AP", nella sua versione precedente) accessibile all'URL:  https://www.retedes.it, o retegas.altervista.org, o da qualsiasi loro sottodominio, sono attività regolate, in ogni caso e in linea generale, dal presente Disclaimer Legale.<br>
                L'accesso e l'utilizzo di questo Sito da parte di qualsiasi navigatore presuppongono la consapevole presa visione ed integrale accettazione di questo Disclaimer Legale.
                Qualsiasi forma di interazione con il Sito, a partire dalla semplice navigazione in qualsiasi pagina, sin al suo utilizzo per partecipare e/o gestire ordini di materiale, si considera come esplicita accettazione di quanto contenuto nella presente pagina e conseguente impegno a manlevare e a tenere indenne ReteDES da qualsiasi rivendicazione o pretesa di terzi che dovesse derivare, direttamente o indirettamente, da un utilizzo improprio o illecito del Sito. Si consiglia, pertanto, un'attenta lettura di quanto di seguito riportato.
                ReteDES potrà modificare e/o aggiornare, in tutto o in parte, questo Disclaimer Legale. Le modifiche e/o gli aggiornamenti saranno notificati agli Utenti mediante specifica informazione sulla Home Page non appena adottati e saranno vincolanti non appena pubblicati sul Sito, in sostituzione della precedente versione.
                </p>
<p>
Dichiarazione di non-responsabilità , di autonomia decisionale e selezione degli accessi:<br>
ReteDES non assume alcuna responsabilità in ordine ad eventuali inesattezze, errori ed omissioni nelle informazioni pubblicate e/o gestite attraverso il suo software, ed in ogni caso si riserva il diritto di intervenire, in ogni momento, apportando correzioni, modifiche e migliorie al Sito, che potranno avvenire senza preavviso alcuno, limitando anche parzialmente o totalmente la sua accessibilità per il periodo tecnico necessario a tali operazioni. ReteDES potrà sospendere, integrare, modificare e/o monitorare l'attività di tutti i suoi utenti, nonché limitarne le operazioni o l'accesso a determinate pagine del sito senza essere tenuto ad avvertire i diretti interessati; ReteDES avrà completo potere censorio su tutti i dati inseriti dagli utenti, e potrà deciderne autonomamente ed incondizionatamente la loro modifica e/o cancellazione, totale o parziale, compresi quelli dell'account personale.. Potrà altresì insindacabilmente dare in delega tutte o solo parti di queste funzioni ad altri utenti ReteDES di sua scelta, o ad altri soggetti esterni anche non legati ai GAS partecipanti. ReteDES si riserva la facoltà di selezionare gli accessi degli utenti, (che siano già iscritti o che abbiano solamente fatto domanda di iscrizione) in base al browser utilizzato o a qualsiasi altro fattori tecnico od organizzativo, e in ogni caso NON GARANTISCE la piena funzionalità e/o la corretta visualizzazione delle pagine in caso di utilizzo di Browser diversi da Google Chrome.
</p>
<p>
Obblighi e responsabilità dell'Utente / Navigatore<br>
Il navigatore che accede al Sito, al fine di utilizzare i servizi che sono messi a disposizione , si impegna a non utilizzare il portale per scopi impropri e/o illeciti e, in particolare:
(i) ad utilizzare i Servizi esclusivamente per scopi leciti;
(ii) a non trasmettere file potenzialmente infetti da virus, di provenienza non sicura, ovvero file che in qualsiasi caso si possano considerare potenzialmente dannosi (a titolo esemplificativo, non esaustivo: virus, spyware, malicious code, trojan horse, ecc.);
(iii) a non compiere azioni che possano danneggiare, disattivare, sovraccaricare o compromettere la funzionalità del Portale, o interferire con l'utilizzo da parte di terzi.
(iv) a non utilizzare il sito con finalità promozionali, (sia esse palesi che non) volte deliberatamente allo sfruttamento commerciale del bacino di potenziali utenti della rete di contatti. A tal proposito ReteDes si riserva la facoltà di sospendere e/o cancellare qualsiasi utente che non rispetta questo punto, salvo accordi diversi intercorsi tra ReteDes stessa ed i responsabili dei GAS o dei DES ove questa situazione si presenta.
</p>
<p>
ReteDES, per quanto di propria competenza, precisa che<br>
- declina ogni responsabilità per le conseguenze che possano essere arrecate ad Utenti o terzi da possibili malfunzionamenti del Portale e per gli eventuali danni di qualsiasi natura in cui dovessero incorrere gli Utenti, compresi eventuali guasti, inesattezze, interruzioni della disponibilità o funzionalità del database sul quale il sito si appoggia;
Sono da considerarsi malfunzionamenti anche le possibili errate contabilizzazioni degli importi calcolati, le assegnazioni di merce, le rettifiche degli articoli e il calcolo delle percentuali sui costi riferite ad ogni utente, ed ogni altra operazione che dia dei risultati valori sia calcolati che semplicemente letti dal database, sia con output a video piuttosto che cartaceo.
- non è in alcuna misura responsabile delle informazioni che sono immesse dagli Utenti con particolare riferimento, ad esempio, alla veridicità, all'esaustività, alla non ingannevolezza, alla liceità dei dati riguardanti ditte, listini, articoli, ordini e movimenti di cassa - , né è posto a suo carico alcun onere di verifica e di controllo degli stessi;
- non è in alcuna misura responsabile dell'hosting e della conservazione dei dati del database, che sono affidati a www.altervista.org; In particolar modo i backup effettuati NON GARANTISCONO l'integrità e la persistenza nel tempo dei dati immessi dagli utenti.
</p>
<p>
Link in entrata e in uscita<br>
I collegamenti interattivi tra il Sito ed altri siti, gestiti da terzi, sono effettuati da ReteDES senza operare alcun controllo in merito alle informazioni, ai prodotti, ai servizi eventualmente offerti o alle politiche adottate in tali siti, né alcuna verifica della loro conformità alla normativa di volta in volta applicabile. Pertanto, ReteDES non assume alcuna responsabilità in merito al contenuto ed alle modalità tecnico-operative di tali siti esterni, in particolar modo per quanto riguarda gli aspetti di sicurezza informatica: a tale proposito, l’utente è tenuto a cautelarsi con idonei sistemi anti-virus, nonché informarsi accuratamente sulle policy ivi stabilite in materia.
Inoltre, ogni sito Internet “linkato” ha una propria privacy policy, diversa e autonoma rispetto a quella di ReteDes, rispetto alla quale quest'ultima non esercita alcuna verifica. I navigatori sono pregati quindi di visionare attentamente le politiche sulla riservatezza adottate nei siti dei terzi.
In ogni caso a fronte di eventuali comunicazioni da parte dei navigatori, sulla non affidabilità o sulla illegittimità di tali siti, ReteDES potrà, a proprio insindacabile giudizio, sospenderne il collegamento.
</p>
<p>
Proprietà del sito, diritti sui contenuti, limitazioni e divieti all'utilizzo, Copyleft<br>
Il codice originale sviluppato per Retegas è opensource, il che significa che è liberamente ed interamente a disposizione di chiunque voglia visionarlo, scaricarlo o utilizzarlo tutto o in parte per qualsiasi tipo di applicazione, privata o pubblica. Le parti non originali di codice usato, rispettanti le singole licenze, sono tutelate dalla normativa applicabile ad ognuna di esse. Ogni ulteriore informazione su queste parti di codice è fruibile e scaricabile dai rispettivi siti. Il software "reteDES", inteso come insieme delle singole parti originali e non, attualmente visibile e  in uso su www.retedes.it e www.retegas.info o su qualunque loro sottodominio (che costituiscono l'applicativo effettivamente visto dai singoli utenti che accettano questo disclaimer), è di proprietà di Mauro Morello, che di fatto ha registrato e ne mantiene il dominio (retegas.info e retedes.it) secondo le vigenti leggi italiane, il quale si riserva il diritto di integrare o meno modifiche e/o migliorie derivanti dalla parte opensource, o di delegare questo ed ogni altro tipo di incarico di progettazione, sviluppo, manutenzione, finanziamento o amministrazione del sito a personale di sua scelta. Mauro Morello quale attuale amministratore unico  potra' deciderne insindacabilmente le  forme di finanziamento piu' adatte al suo mantenimento. La documentazione, le immagini, i caratteri, la grafica, il software e altri contenuti del Sito e tutti i codici e format scripts per implementare le pagine visualizzate, sono di proprietà di Mauro Morello, tranne quelli di terzi ove indicato.
L'accesso al Sito non fornisce al navigatore il diritto di appropriarsi, né di riprodurre, di modificare, di distribuire, di ripubblicare, in alcuna forma anche parziale e con qualsiasi strumento , le informazioni in esso contenute, senza l'espressa autorizzazione scritta da parte di Mauro Morello e / o del terzo titolare dei relativi diritti di sfruttamento e/o di riproduzione.
</p>
<p>
Autorizzazione al trattamento dei dati personali<br>
Definizione di dati personali: Sono considerati dati personali (a titolo esemplificativo ma non esaustivo) il nome, l'indirizzo postale, il telefono, la mail, l'IP usato per la connessione, e tutti i dati raccolti in forma non anonima dal sito a seguito dell'attività dell'utente su di esso. Questi dati non verranno in nessun caso diffusi in rete o ceduti a terzi, fatti salvi eventuali obblighi di legge, necessità tecniche legate all'erogazione del servizio. Essendo reteDES per sua natura un social-network dedicato alla collaborazione tra i vari gas, parallelamente al massimo impegno e la cura per proteggere al meglio la privacy di tutti gli iscritti, è prevista la visibilità dei dati personali (tutti o una parte di essi) da parte altri utenti di reteDes, nei casi legati alla gestione degli ordini o alla condivisione di essi. I meccanismi di gestione e di condivisione sono spiegati nelle pagine di aiuto consultabili su wiki.retedes.it. Il titolare del trattamento è Mauro Morello. Il trattamento sarà realizzato con l'ausilio di strumenti informatici da parte del Titolare e degli operatori da questo incaricati. In qualsiasi momento sarà possibile richiedere gratuitamente la verifica, la cancellazione, la modifica dei propri dati, o ricevere l'elenco degli incaricati del trattamento, scrivendo una mail a info chiocciola retedes punto it.
</p>
</p>
</div>
</div>
</div>

<!-- END MAIN PANEL -->
<!-- ==========================CONTENT ENDS HERE ========================== -->

<?php
	//include required scripts
	include("inc/scripts.php");
?>

<?php
	//include footer
	include("inc/google-analytics.php");
?>