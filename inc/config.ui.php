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
$page_nav_3 = array(
	"dashboard" => array(
		"title" => "Home",
		"url" => "ajax/dashboard.php",
		"icon" => "fa-home"
	),
	"smartui" => array(
		"title" => _USER_FULLNAME,
		"icon" => "fa-user",
		"sub" => array(
			"general" => array(
				'title' => 'General Elements',
				'icon' => 'fa-folder-open',
				'sub' => array(
					'alert' => array(
						'title' => 'Alerts',
						'url' => "ajax/smartui-alert.php"
					),
					'progress' => array(
						'title' => 'Progress',
						'url' => 'ajax/smartui-progress.php'
					)
				)
			),
			"carousel" => array(
				"title" => "Carousel",
				"url" => 'ajax/smartui-carousel.php'
			),
			"tab" => array(
				"title" => "Tab",
				"url" => 'ajax/smartui-tab.php'
			),
			"accordion" => array(
				"title" => "Accordion",
				"url" => 'ajax/smartui-accordion.php'
			),
			"widget" => array(
				'title' => "Widget",
				'url' => "ajax/smartui-widget.php"
			),
			"datatable" => array(
				"title" => "DataTable",
				"url" => "ajax/smartui-datatable.php"
			),
			"button" => array(
				"title" => "Button",
				"url" => "ajax/smartui-button.php"
			),
			'smartform' => array(
				'title' => 'Smart Form',
				'url' => 'ajax/smartui-form.php'
			)
		)
	),
	"inbox" => array(
		"title" => _USER_GAS_NOME,
		"url" => "ajax/inbox.php",
		"icon" => "fa-home",
		"label_htm" => '<span class="badge pull-right inbox-badge">14</span>',
	),
	"graphs" => array(
		"title" => _USER_DES_NOME,
		"icon" => "fa-bar-chart-o",
		"sub" => array(
			"flot" => array(
				"title" => "Flot Chart",
				"url" => "ajax/flot.php"
			),
			"morris" => array(
				"title" => "Morris Charts",
				"url" => "ajax/morris.php"
			),
			"inline" => array(
				"title" => "Inline Charts",
				"url" => "ajax/inline-charts.php"
			),
			"dygraphs" => array(
				"title" => "Dygraphs",
				"url" => "ajax/dygraphs.php",
				"label_htm" => ' <span class="badge pull-right inbox-badge bg-color-yellow">new</span>'
			)
		)
	),
	"tables" => array(
		"title" => "Tables",
		"icon" => "fa-table",
		"sub" => array(
			"normal" => array(
				"title" => "Normal Tables",
				"url" => "ajax/table.php"
			),
			"data" => array(
				"title" => "Data Tables",
				"url" => "ajax/datatables.php",
				"label_htm" => ' <span class="badge inbox-badge bg-color-greenLight">v1.10</span>'
			),
			"jqgrid" => array(
				"title" => "Jquery Grid",
				"url" => "ajax/jqgrid.php"
			)
		)
	),
	"forms" => array(
		"title" => "Forms",
		"icon" => "fa-pencil-square-o",
		"sub" => array(
			"smart_elements" => array(
				"title" => "Smart Form Elements",
				"url" => "ajax/form-elements.php"
			),
            "smart_layout" => array(
				"title" => "Smart Form Layouts",
				"url" => "ajax/form-templates.php"
			),
            "smart_validation" => array(
				"title" => "Smart Form Validation",
				"url" => "ajax/validation.php"
			),
            "bootstrap_forms" => array(
				"title" => "Bootstrap Form Elements",
				"url" => "ajax/bootstrap-forms.php"
			),
            "form_plugins" => array(
				"title" => "Form Plugins",
				"url" => "ajax/plugins.php"
			),
            "wizards" => array(
				"title" => "Wizards",
				"url" => "ajax/wizard.php"
			),
            "bootstrap_editors" => array(
				"title" => "Bootstrap Editors",
				"url" => "ajax/other-editors.php"
			),
            "dropzone" => array(
				"title" => "Dropzone",
				"url" => "ajax/dropzone.php"
			),
			"imagecrop" => array(
				"title" => "Image Cropping",
				"url" => "ajax/image-editor.php",
                "label_htm" => '<span class="badge pull-right inbox-badge bg-color-yellow">new</span>'
			)
		)
	),
    "ui_elements" => array(
        "title" => "UI Elements",
        "icon" => "fa-desktop",
        "sub" => array(
            "general" => array(
                "title" => "General Elements",
                "url" => "ajax/general-elements.php"
            ),
            "buttons" => array(
                "title" => "Buttons",
                "url" => "ajax/buttons.php"
            ),
            "icons" => array(
                "title" => "Icons",
                "sub" => array(
                    "fa" => array(
                        "title" => "Font Awesome",
                        "icon" => "fa-plane",
                        "url" => "ajax/fa.php"
                    ),
                    "glyph" => array(
                        "title" => "Glyph Icons",
                        "icon" => "fa-plane",
                        "url" => "ajax/glyph.php"
                    ),
                    "flags" => array(
                        "title" => "Flags",
                        "icon" => "fa-flag",
                        "url" => "ajax/flags.php"
                    )
                )
            ),
            "grid" => array(
                "title" => "Grid",
                "url" => "ajax/grid.php"
            ),
            "tree_view" => array(
                "title" => "Tree View",
                "url" => "ajax/treeview.php"
            ),
            "nestable_lists" => array(
                "title" => "Nestable Lists",
                "url" => "ajax/nestable-list.php"
            ),
            "jquery_ui" => array(
                "title" => "jQuery UI",
                "url" => "ajax/jqui.php"
            ),
            "typo" => array(
				"title" => "Typography",
				"url" => "ajax/typography.php"
			),
			"nav6" => array(
				"title" => "Six Level Menu",
				"sub" => array(
					"second_lvl" => array(
						"title" => "Item #2",
						"icon" => "fa-folder-open",
						"sub" => array(
							"third_lvl" => array(
								"title" => "Sub #2.1",
								"icon" => "fa-folder-open",
								"sub" => array(
									"file" => array(
										"title" => "Item #2.1.1",
										"icon" => "fa-file-text"
									),
									"fourth_lvl" => array(
										"title" => "Expand",
										"icon" => "fa-plus",
										"sub" => array(
											"file" => array(
												"title" => "File",
												"icon" => "fa-file-text"
											),
											"fifth_lvl" => array(
												"title" => "Delete",
												"icon" => "fa-trash-o"
											)
										)
									)
								)
							)
						)
					),
					"folder" => array(
						"title" => "Item #3",
						"icon" => "fa-folder-open",
						"sub" => array(
							"third_lvl" => array(
								"title" => "Sub #3.1",
								"icon" => "fa-folder-open",
								"sub" => array(
									"file1" => array(
										"title" => "File",
										"icon" => "fa-file-text"
									),
									"file2" => array(
										"title" => "File",
										"icon" => "fa-file-text"
									)
								)
							)
						)
					)
				)
			),
        )
    ),
    "cal" => array(
		"title" => "Calendar",
		"url" => "ajax/calendar.php",
		"icon" => "fa-calendar",
		"icon_badge" => "3"
	),
    "widgets" => array(
		"title" => "Widgets",
		"url" => "ajax/widgets.php",
		"icon" => "fa-list-alt"
	),
    "gallery" => array(
		"title" => "Gallery",
		"url" => "ajax/gallery.php",
		"icon" => "fa-picture-o"
	),
    "gmap_skins" => array(
		"title" => "GMap Skins",
		"url" => "ajax/gmap-xml.php",
		"icon" => "fa-map-marker",
        "label_htm" => '<span class="badge bg-color-greenLight pull-right inbox-badge">9</span>'
	),
	"misc" => array(
		"title" => "Miscellaneous",
		"icon" => "fa-windows",
		"sub" => array(
			"others" => array(
		        "title" => "Other Pages",
		        "icon" => "fa-file",
		        "sub" => array(
		            "forum" => array(
		                "title" => "Forum Layout",
		                "url" => "ajax/forum.php"
		            ),
		            "profile" => array(
		                "title" => "Profile",
		                "url" => "ajax/profile.php"
		            ),
		            "timeline" => array(
		                "title" => "Timeline",
		                "url" => "ajax/timeline.php"
		            )
		        )
		    ),
            "pricing_tables" => array(
				"title" => "Pricing Tables",
				"url" => "ajax/pricing-table.php"
			),
            "invoice" => array(
				"title" => "Invoice",
				"url" => "ajax/invoice.php"
			),
            "login" => array(
				"title" => "Login",
				"url" => APP_URL."/login.php",
				"url_target" => "_top"
			),
            "register" => array(
				"title" => "Register",
				"url" => APP_URL."/register.php",
				"url_target" => "_top"
			),
            "lock" => array(
				"title" => "Locked Screen",
				"url" => APP_URL."/lock.php",
				"url_target" => "_top"
			),
            "err_404" => array(
				"title" => "Error 404",
				"url" => "ajax/error404.php"
			),
            "err_500" => array(
				"title" => "Error 500",
				"url" => "ajax/error500.php"
			),
			"blank" => array(
				"title" => "Blank Page",
				"url" => "ajax/blank_.php"
			),
            "email_template" => array(
				"title" => "Email Template",
				"url" => "ajax/email-template.php"
			),
            "search" => array(
				"title" => "Search Page",
				"url" => "ajax/search.php"
			),
            "ck_editor" => array(
				"title" => "CK Editor",
				"url" => "ajax/ckeditor.php"
			),
		)
	),
	"smartint" => array(
		"title" => "SmartAdmin Intel",
		"icon" => "fa-cube txt-color-blue",
		"sub" => array(
			"difver" => array(
		        "title" => "Different Versions",
		        "icon" => "fa-stack-overflow",
		        "url" => "ajax/difver.php"
		    ),
		    "applayout" => array(
		        "title" => "App Settings",
		        "icon" => "fa-cube",
		        "url" => "ajax/applayout.php"
		    ),
		   "doc" => array(
		        "title" => "Documentation",
		        "icon" => "fa-book",
		        "url" => "http://bootstraphunter.com/smartadmin/BUGTRACK/track_/documentation/index.html",
		        "url_target"=> "_blank"
		    ),
		   "bugtrack" => array(
		        "title" => "Bug Tracker",
		        "icon" => "fa-bug",
		        "url" => "http://bootstraphunter.com/smartadmin/BUGTRACK/track_/",
		        "url_target"=> "_blank"
		    )
		)
	)
);

$gas_menu =  array(
    "title" => _USER_GAS_NOME,
    "url" => "ajax_rd4/gas_home.php",
    "icon" => "fa-home"
    );

$help_menu =  array(
    "title" => "Aiuto!",
    "url" => "ajax_rd4/help.php",
    "icon" => "fa-question"
);

$page_nav =array(   "dashboard" => array(
                    "title" => "Cruscotto",
                    "icon" => "fa-dashboard",
                    "url" => "ajax_rd4/home.php"),

                    "user_home" => array(
                    "title" => _USER_FULLNAME,
                    "icon" => "fa-user",
                    "sub" => array(
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
                ),
                "gas_menu" => $gas_menu,
                "help_menu" => $help_menu
            );




//configuration variables
$page_title = "";
$page_css = array();
$no_main_header = false; //set true for lock.php and login.php
$page_body_prop = array(); //optional properties for <body>
$page_html_prop = array(); //optional properties for <html>
?>