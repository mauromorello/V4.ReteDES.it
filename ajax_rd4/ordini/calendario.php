<?php require_once("inc/init.php"); $ui = new SmartUI;?>

<!-- row -->

<div class="row">

    <div class="col-sm-12 col-md-12 col-lg-3">
        <!-- new widget -->
        <div class="jarviswidget jarviswidget-color-blueDark">
            <header>
                <h2> Visualizza : </h2>
            </header>

            <!-- widget div-->
            <div>

                <div class="widget-body">
                    <!-- content goes here -->
                    <form class="smart-form padding-10">
                            <hr>
                            <section>
                                    <div class="row margin-top-5">
                                        <label class="label">Mostra gli ordini:</label>
                                            <label class="checkbox ">
                                                <input type="checkbox" name="show_aperti" checked="checked" id="show_aperti">
                                                <i></i>Aperti<span class="pull-right btn btn-xs btn-circle bg-color-greenLight"></span></label>

                                            <label class="checkbox">
                                                <input type="checkbox" name="show_futuri" id="show_futuri">
                                                <i></i>Futuri<span class="pull-right btn btn-xs btn-circle bg-color-blueLight"></span></label>
                                            <label class="checkbox">
                                                <input type="checkbox" name="show_chiusi" id="show_chiusi">
                                                <i></i>Chiusi<span class="pull-right btn btn-xs btn-circle bg-color-redLight"></span></label>
                                    </div>
                                </section>
                                <hr>
                                <section>
                                    <div class="row margin-top-5">
                                        <div class="btn-group-vertical btn-block btn-group-lg">
                                            <label class="label">Calendario:</label>
                                            <button type="button" class="btn btn-default" id="mt">
                                                Mensile
                                            </button>
                                            <button type="button" class="btn btn-default" id="ag">
                                                Settimanale
                                            </button>
                                            <button type="button" class="btn btn-default" id="td">
                                                Giornaliero
                                            </button>
                                        </div>
                                </div>
                            </section>
                            <hr>
                    </form>

                </div>

            </div>
            <!-- end widget div -->
        </div>
        <!-- end widget -->

    </div>
    <div class="col-sm-12 col-md-12 col-lg-9">

        <!-- new widget -->
        <div class="jarviswidget jarviswidget-color-blueDark">

            <header>
                <span class="widget-icon"> <i class="fa fa-calendar"></i> </span>
                <h2> Ordini in <?php echo _USER_GAS_NOME ?> </h2>
            </header>

            <!-- widget div-->
            <div>

                <div class="widget-body no-padding">
                    <!-- content goes here -->
                    <div class="widget-body-toolbar">

                        <div id="calendar-buttons">

                            <div class="btn-group">
                                <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-prev"><i class="fa fa-chevron-left"></i></a>
                                <a href="javascript:void(0)" class="btn btn-default btn-xs" id="btn-next"><i class="fa fa-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div id="calendar"></div>

                    <!-- end content -->
                </div>

            </div>
            <!-- end widget div -->
        </div>
        <!-- end widget -->

    </div>

</div>
<!-- end row -->
<div class="row">
    <div class="col-sm-12 col-md-12 col-lg-12">
    <?php echo help_render_html("ordini_calendario","Calendario ordini")?>
    </div>
</div>
<!-- end row -->

<script type="text/javascript">
    /* DO NOT REMOVE : GLOBAL FUNCTIONS!
     *
     * pageSetUp(); WILL CALL THE FOLLOWING FUNCTIONS
     *
     * // activate tooltips
     * $("[rel=tooltip]").tooltip();
     *
     * // activate popovers
     * $("[rel=popover]").popover();
     *
     * // activate popovers with hover states
     * $("[rel=popover-hover]").popover({ trigger: "hover" });
     *
     * // activate inline charts
     * runAllCharts();
     *
     * // setup widgets
     * setup_widgets_desktop();
     *
     * // run form elements
     * runAllForms();
     *
     ********************************
     *
     * pageSetUp() is needed whenever you load a page.
     * It initializes and checks for all basic elements of the page
     * and makes rendering easier.
     *
     */

    pageSetUp();


    var pagefunction = function() {

        <?php echo help_render_js("ordini_calendario")?>

        // full calendar

        var date = new Date();
        var d = date.getDate();
        var m = date.getMonth();
        var y = date.getFullYear();

        var hdr = {
            left: 'title',
            center: 'month,agendaWeek,agendaDay',
            right: 'prev,today,next'
        };

        $('#calendar').fullCalendar({

            header: hdr,

            dayNames:["Domenica","Lunedì","Martedì","Mercoledì","Giovedì","Venerdì","Sabato"],
            dayNamesShort:["Dom","Lun","Mar","Mer","Gio","Ven","Sab"],
            monthNames:["Gennaio","Febbraio","Marzo","Aprile","Maggio","Giugno","Luglio","Agosto","Settembre","Ottobre","Novembre","Dicembre"],
            monthNamesShort:["Gen","Feb","Mar","Apr","Mag","Giu","Lug","Ago","Set","Ott","Nov","Dic"],
            columnFormat:{month:"ddd",week:"ddd d/M",day:"dddd d/M"},
            titleFormat:{month:"MMMM yyyy",week:"d MMM[ yyyy]{ '&#8212;'d [ MMM] yyyy}",day:"dddd, d MMM, yyyy"},

            buttonText: {
                prev: '<i class="fa fa-chevron-left"></i>',
                next: '<i class="fa fa-chevron-right"></i>'
            },

            editable: false,
            droppable: false, // this allows things to be dropped onto the calendar !!!

            select: function (start, end, allDay) {
                var title = prompt('Event Title:');
                if (title) {
                    calendar.fullCalendar('renderEvent', {
                            title: title,
                            start: start,
                            end: end,
                            allDay: allDay
                        }//, true // make the event "stick"
                    );
                }
                calendar.fullCalendar('unselect');
            },

            events: {
                url: 'ajax_rd4/ordini/inc/cf.php',
                type: 'POST',
                data: {
                    a: 'something',
                    b: 'somethingelse'
                },
                error: function() {
                    ko('Problema nella ricezione dati :(');
                }
            },

            eventRender: function (event, element, icon) {
                if (!event.description == "") {
                    element.find('.fc-event-title').append("<br/><span class='ultra-light'>" + event.description +
                        "</span>");
                }
                if (!event.icon == "") {
                    element.find('.fc-event-title').append('<span>'+event.icon+'</span>');
                }

                //SE SONO UN GESTORE
                //if(event.gestore=="SI"){
                //    element.find('.fc-event-title').append("<i class='air air-bottom-right fa fa-star'></i>");
               // }

                if(event.ciccio=="C"){
                    if(!$('#show_chiusi').is(':checked')){
                            return false;
                    }
                }

                if(event.ciccio=="F"){
                    if(!$('#show_futuri').is(':checked')){
                            return false;
                    }
                }

                if(event.ciccio=="A"){
                    if(!$('#show_aperti').is(':checked')){
                            return false;
                    }
                }



            },

            eventClick: function(event) {
                    $(this).popover({html: true, placement : 'top', content : event.contenuto, title: event.title, container: 'body', viewport : 'body' });
                    $(this).popover('show');
            },


            dayClick: function(date, jsEvent, view) {

                $('#calendar').fullCalendar( 'changeView', 'agendaDay' );
                $('#calendar').fullCalendar( 'gotoDate', date );

            },

            windowResize: function (event, ui) {
                $('#calendar').fullCalendar('render');
            }
        });

        /* hide default buttons */
        $('.fc-header-right, .fc-header-center').hide();



        $('#calendar-buttons #btn-prev').click(function () {
            $('.fc-button-prev').click();
            return false;
        });

        $('#calendar-buttons #btn-next').click(function () {
            $('.fc-button-next').click();
            return false;
        });

        $('#calendar-buttons #btn-today').click(function () {
            $('.fc-button-today').click();
            return false;
        });

        $('#mt').click(function () {
            $('#calendar').fullCalendar('changeView', 'month');
        });

        $('#ag').click(function () {
            $('#calendar').fullCalendar('changeView', 'agendaWeek');
        });

        $('#td').click(function () {
            $('#calendar').fullCalendar('changeView', 'agendaDay');
        });

        $('#show_futuri').change(function() {
            $('#calendar').fullCalendar('render');
        });
        $('#show_aperti').change(function() {
            $('#calendar').fullCalendar('render');
        });
        $('#show_chiusi').change(function() {
            $('#calendar').fullCalendar('render');
        });

    };

    // end pagefunction

    // loadscript and run pagefunction

    loadScript("js/plugin/fullcalendar/jquery.fullcalendar.min.js",pagefunction);


</script>
