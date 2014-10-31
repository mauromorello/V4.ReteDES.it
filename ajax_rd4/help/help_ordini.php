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
                            <img src="img/avatars/sunny.png" width="32" height="32">
                        </div>
                        <div class="smart-timeline-time">
                            <small>just now</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Trip to Adalaskar</strong></a>
                            </p>
                            <p>
                                Check out my tour to Adalaskar
                            </p>
                            <p>
                                <a href="javascript:void(0);" class="btn btn-xs btn-primary"><i class="fa fa-file"></i> Read the post</a>
                            </p>
                            <img src="img/superbox/superbox-thumb-4.jpg" alt="img" width="150">



                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-file-text"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>1 min ago</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <strong>Meeting invite for "GENERAL GNU" [<a href="javascript:void(0);"><i>Go to my calendar</i></a>]</strong>
                            </p>

                            <div class="well well-sm display-inline">
                                <p>Will you be able to attend the meeting - <strong> 10:00 am</strong> tomorrow?</p>
                                <button class="btn btn-xs btn-default">Confirm Attendance</button>
                            </div>

                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon bg-color-greenDark">
                            <i class="fa fa-bar-chart-o"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>5 hrs ago</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <strong class="txt-color-greenDark">24hrs User Feed</strong>
                            </p>

                            <div class="sparkline" data-sparkline-type="compositeline" data-sparkline-spotradius-top="5" data-sparkline-color-top="#3a6965" data-sparkline-line-width-top="3" data-sparkline-color-bottom="#2b5c59" data-sparkline-spot-color="#2b5c59" data-sparkline-minspot-color-top="#97bfbf" data-sparkline-maxspot-color-top="#c2cccc" data-sparkline-highlightline-color-top="#cce8e4" data-sparkline-highlightspot-color-top="#9dbdb9" data-sparkline-width="170px" data-sparkline-height="40px" data-sparkline-line-val="[6,4,7,8,4,3,2,2,5,6,7,4,1,5,7,9,9,8,7,6]" data-sparkline-bar-val="[4,1,5,7,9,9,8,7,6,6,4,7,8,4,3,2,2,5,6,7]"><canvas width="170" height="40" style="display: inline-block; width: 170px; height: 40px; vertical-align: top;"></canvas></div>

                            <br>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-user"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>yesterday</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Update user information</strong></a>
                            </p>
                            <p>
                                Curabitur ullamcorper ultricies nisi. Nam eget dui. Etiam rhoncus. Maecenas tempus, tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit vel, luctus pulvinar, hendrerit id, lorem. Maecenas nec odio et ante tincidunt tempus. Donec vitae sapien ut libero venenatis faucibus.
                            </p>

                            Tellus eget condimentum rhoncus, sem quam semper libero, sit amet adipiscing sem neque sed ipsum. Nam quam nunc, blandit

                            <ul class="list-inline">
                                <li>
                                    <img src="img/superbox/superbox-thumb-6.jpg" alt="img" width="50">
                                </li>
                                <li>
                                    <img src="img/superbox/superbox-thumb-5.jpg" alt="img" width="50">
                                </li>
                                <li>
                                    <img src="img/superbox/superbox-thumb-7.jpg" alt="img" width="50">
                                </li>
                            </ul>
                        </div>
                    </li>
                    <li>
                        <div class="smart-timeline-icon">
                            <i class="fa fa-pencil"></i>
                        </div>
                        <div class="smart-timeline-time">
                            <small>12 Mar, 2013</small>
                        </div>
                        <div class="smart-timeline-content">
                            <p>
                                <a href="javascript:void(0);"><strong>Nabi Resource Report</strong></a>
                            </p>
                            <p>
                                Ean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim. Aliquam lorem ante, dapibus in, viverra quis.
                            </p>
                            <a href="javascript:void(0);" class="btn btn-xs btn-default">Read more</a>
                        </div>
                    </li>
                    <li class="text-center">
                        <a href="javascript:void(0)" class="btn btn-sm btn-default"><i class="fa fa-arrow-down text-muted"></i> LOAD MORE</a>
                    </li>
                </ul>
            </div>
            <!-- END Timeline Content -->

        </div>
<script type="text/javascript">
    pageSetUp();

    var pagefunction = function(){

        //------------HELP WIDGET
        <?php echo help_render_js("help_ordini");?>
        //------------END HELP WIDGET




    };

    pagefunction();

</script>
