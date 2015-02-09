<?php
require_once("inc/init.php");

$ui = new SmartUI;
$page_title = "Help ORDINI";


?>
<div class="inbox-nav-bar no-content-padding">
    <h1 class="page-title txt-color-blueDark"><i class="fa fa-fw fa-shopping-cart"></i> Gestione ORDINI! &nbsp;</h1>
</div>

<section id="widget-grid" class="margin-top-10">
    <div class="row">
        <!-- PRIMA COLONNA-->
        <article class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
            <?php echo help_render_html("help_ordini","Help ORDINI"); ?>
        </article>
    </div>
</section>
<div class="well well-sm">
            <!-- Timeline Content -->
            <div class="smart-timeline">
                <ul class="smart-timeline-list">

                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-truck"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>Per prima cosa...</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Si crea una ditta</strong></a></p>
                            <p>Le ditte sono inserite dagli utenti dei gas iscritti a ReteDES.it; Ogni gas ha dei suoi criteri per ammettere una ditta nel circuito, e una volta inserita rimane visibile a tutti.</p>
                            <p>Se si guarda la scheda di una ditta, si possono vedere anche i feedback della gente che ha ordinato merce da lei.</p>
                       </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-cube"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>successivamente...</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Si inserisce un listino</strong></a></p>
                            <p>Balah blah blah</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-cubes"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>in quel listino...</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Si inseriscono gli articoli;</strong></a></p>
                            <p>Balah blah blah</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>...finalmente</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Si programma un ordine!!</strong></a></p>
                            <p>Balah blah blah</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>..giunta l'ora X</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>L'ordine si apre</strong></a></p>
                            <p>Balah blah blah</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-shopping-cart"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>durante l'apertura</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>La gente acquista merce</strong></a></p>
                            <p>Balah blah blah</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-hand-o-up"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>ti aiuto !</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Gli utenti si offrono come aiutanti</strong></a></p>
                            <p>Balah blah blah</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-stop"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>si chiude!!</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>L'ordine chiude;</strong></a></p>
                            <p>Gli utenti non possono più aggiungere o togliere merce da quella ordinata</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-stop"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>ad ordine chiuso</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>La squadra dei referenti si mette al lavoro</strong></a></p>
                            <p>Compiono gli "aggiustamenti", una rettifica volta a far quadrare l'ordine, togliendo e aggiungendo dove serve merce alla lista della spesa degli utenti. In questa fase è operativo: il referente, i referenti extra, i supervisori.</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-paper-plane"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>un po' di pausa</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>L'ordine viene inoltrato al fornitore</strong></a></p>
                            <p>Per questo ci sono dei report appositi</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-truck"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>Eccola!</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>La merce ordinata arriva</strong></a></p>
                            <p>Per questo ci sono dei report appositi</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-gears"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>Il lavoro vero</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Si controlla il tutto</strong></a></p>
                            <p>A questo punto la merce è arrivata. I referenti, aiutati dalle persone che si sono offerte precedentemente, smistano tutto il materiale, e controllano se manca qualcosa.</p>
                            <p>Si fa quindi collimare il totale indicato da reteDES con il totale reale, usando gli strumenti di rettifica</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-check-square"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>Finito ?</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Si convalida l'ordine</strong></a></p>
                            <p>L'operazione di convalida serve a certificare da parte del referente che tutti gli importi assegnati agli utenti corrispondano alla realtà.</p>
                            <p>Una volta convalidato l'ordine, il cassiere può intervenire per gestire la cassa</p>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-bank"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>Soldi soldi soldi</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p><a href="javascript:void(0);"><strong>Il cassiere movimenta il credito degli utenti.</strong></a></p>
                            <p>Con l'ordine convalidato, il cassiere ufficializza il movimento dei crediti degli utenti.</p>
                        </div>
                    </li>
                </ul>
            </div>
            <!-- END Timeline Content -->

        </div>
<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        document.title = '<?php echo "ReteDES.it :: $page_title";?>';
        <?php echo help_render_js("help_ordini");?>
        //------------END HELP WIDGET




    };

    pagefunction();

</script>
