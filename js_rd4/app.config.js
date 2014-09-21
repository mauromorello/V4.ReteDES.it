/*         ______________________________________
  ________|                                      |_______
  \       |           SmartAdmin WebApp          |      /
   \      |      Copyright © 2014 MyOrange       |     /
   /      |______________________________________|     \
  /__________)                                (_________\

 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 * =======================================================================
 * SmartAdmin is FULLY owned and LICENSED by MYORANGE INC.
 * This script may NOT be RESOLD or REDISTRUBUTED under any
 * circumstances, and is only to be used with this purchased
 * copy of SmartAdmin Template.
 * =======================================================================
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 * =======================================================================
 * original filename: app.config.js
 * filesize: ??
 * author: Sunny (@bootstraphunt)
 * email: info@myorange.ca
 * =======================================================================
 *
 * APP CONFIGURATION (HTML/AJAX/PHP Versions ONLY)
 * Description: Enable / disable certain theme features here
 * GLOBAL: Your left nav in your app will no longer fire ajax calls, set
 * it to false for HTML version
 */
	$.navAsAjax = true;
/*
 * GLOBAL: Sound Config
 */
	$.sound_path = "sound/";
	$.sound_on = true;
/*
 * Impacts the responce rate of some of the responsive elements (lower
 * value affects CPU but improves speed)
 */
	var throttle_delay = 350,
/*
 * The rate at which the menu expands revealing child elements on click
 */
	menu_speed = 235,
/*
 * Turn on JarvisWidget functionality
 * dependency: js/jarviswidget/jarvis.widget.min.js
 */
	enableJarvisWidgets = true,
/*
 * Warning: Enabling mobile widgets could potentially crash your webApp
 * if you have too many widgets running at once
 * (must have enableJarvisWidgets = true)
 */
	enableMobileWidgets = false,
/*
 * Turn on fast click for mobile devices?
 * Enable this to activate fastclick plugin
 * dependency: js/plugin/fastclick/fastclick.js
 */
	fastClick = false,
/*
 * These elements are ignored during DOM object deletion for ajax version
 * It will delete all objects during page load with these exceptions:
 */
	ignore_key_elms = ["#header, #left-panel, #main, div.page-footer, #shortcut, #divSmallBoxes, #divMiniIcons, #divbigBoxes, #voiceModal, script"],
/*
 * VOICE COMMAND CONFIG
 * dependency: voicecommand.js
 */
	voice_command = false,
/*
 * Turns on speech without asking
 */
	voice_command_auto = false,
/*
 * 	Sets the language to the default 'en-US'. (supports over 50 languages
 * 	by google)
 *
 *  Afrikaans         ['af-ZA']
 *  Bahasa Indonesia  ['id-ID']
 *  Bahasa Melayu     ['ms-MY']
 *  Català            ['ca-ES']
 *  Čeština           ['cs-CZ']
 *  Deutsch           ['de-DE']
 *  English           ['en-AU', 'Australia']
 *                    ['en-CA', 'Canada']
 *                    ['en-IN', 'India']
 *                    ['en-NZ', 'New Zealand']
 *                    ['en-ZA', 'South Africa']
 *                    ['en-GB', 'United Kingdom']
 *                    ['en-US', 'United States']
 *  Español           ['es-AR', 'Argentina']
 *                    ['es-BO', 'Bolivia']
 *                    ['es-CL', 'Chile']
 *                    ['es-CO', 'Colombia']
 *                    ['es-CR', 'Costa Rica']
 *                    ['es-EC', 'Ecuador']
 *                    ['es-SV', 'El Salvador']
 *                    ['es-ES', 'España']
 *                    ['es-US', 'Estados Unidos']
 *                    ['es-GT', 'Guatemala']
 *                    ['es-HN', 'Honduras']
 *                    ['es-MX', 'México']
 *                    ['es-NI', 'Nicaragua']
 *                    ['es-PA', 'Panamá']
 *                    ['es-PY', 'Paraguay']
 *                    ['es-PE', 'Perú']
 *                    ['es-PR', 'Puerto Rico']
 *                    ['es-DO', 'República Dominicana']
 *                    ['es-UY', 'Uruguay']
 *                    ['es-VE', 'Venezuela']
 *  Euskara           ['eu-ES']
 *  Français          ['fr-FR']
 *  Galego            ['gl-ES']
 *  Hrvatski          ['hr_HR']
 *  IsiZulu           ['zu-ZA']
 *  Íslenska          ['is-IS']
 *  Italiano          ['it-IT', 'Italia']
 *                    ['it-CH', 'Svizzera']
 *  Magyar            ['hu-HU']
 *  Nederlands        ['nl-NL']
 *  Norsk bokmål      ['nb-NO']
 *  Polski            ['pl-PL']
 *  Português         ['pt-BR', 'Brasil']
 *                    ['pt-PT', 'Portugal']
 *  Română            ['ro-RO']
 *  Slovenčina        ['sk-SK']
 *  Suomi             ['fi-FI']
 *  Svenska           ['sv-SE']
 *  Türkçe            ['tr-TR']
 *  български         ['bg-BG']
 *  Pусский           ['ru-RU']
 *  Српски            ['sr-RS']
 *  한국어          ['ko-KR']
 *  中文                            ['cmn-Hans-CN', '普通话 (中国大陆)']
 *                    ['cmn-Hans-HK', '普通话 (香港)']
 *                    ['cmn-Hant-TW', '中文 (台灣)']
 *                    ['yue-Hant-HK', '粵語 (香港)']
 *  日本語                         ['ja-JP']
 *  Lingua latīna     ['la']
 */
	voice_command_lang = 'it-IT',
/*
 * 	Use localstorage to remember on/off (best used with HTML Version)
 */
	voice_localStorage = false;
/*
 * Voice Commands
 * Defines all voice command variables and functions
 */
 	if (voice_command) {

		var commands = {

			'ordini aperti' : function() { $('nav a[href="ajax/dashboard.php"]').trigger("click"); },
			'show inbox' : function() { $('nav a[href="ajax/inbox.php"]').trigger("click"); },
			'show graphs' : function() { $('nav a[href="ajax/flot.php"]').trigger("click"); },
			'show flotchart' : function() { $('nav a[href="ajax/flot.php"]').trigger("click"); },
			'show morris chart' : function() { $('nav a[href="ajax/morris.php"]').trigger("click"); },
			'show inline chart' : function() { $('nav a[href="ajax/inline-charts.php"]').trigger("click"); },
			'show dygraphs' : function() { $('nav a[href="ajax/dygraphs.php"]').trigger("click"); },
			'show tables' : function() { $('nav a[href="ajax/table.php"]').trigger("click"); },
			'show data table' : function() { $('nav a[href="ajax/datatables.php"]').trigger("click"); },
			'show jquery grid' : function() { $('nav a[href="ajax/jqgrid.php"]').trigger("click"); },
			'show form' : function() { $('nav a[href="ajax/form-elements.php"]').trigger("click"); },
			'show form layouts' : function() { $('nav a[href="ajax/form-templates.php"]').trigger("click"); },
			'show form validation' : function() { $('nav a[href="ajax/validation.php"]').trigger("click"); },
			'show form elements' : function() { $('nav a[href="ajax/bootstrap-forms.php"]').trigger("click"); },
			'show form plugins' : function() { $('nav a[href="ajax/plugins.php"]').trigger("click"); },
			'show form wizards' : function() { $('nav a[href="ajax/wizards.php"]').trigger("click"); },
			'show bootstrap editor' : function() { $('nav a[href="ajax/other-editors.php"]').trigger("click"); },
			'show dropzone' : function() { $('nav a[href="ajax/dropzone.php"]').trigger("click"); },
			'show image cropping' : function() { $('nav a[href="ajax/image-editor.php"]').trigger("click"); },
			'show general elements' : function() { $('nav a[href="ajax/general-elements.php"]').trigger("click"); },
			'show buttons' : function() { $('nav a[href="ajax/buttons.php"]').trigger("click"); },
			'show fontawesome' : function() { $('nav a[href="ajax/fa.php"]').trigger("click"); },
			'show glyph icons' : function() { $('nav a[href="ajax/glyph.php"]').trigger("click"); },
			'show flags' : function() { $('nav a[href="ajax/flags.php"]').trigger("click"); },
			'show grid' : function() { $('nav a[href="ajax/grid.php"]').trigger("click"); },
			'show tree view' : function() { $('nav a[href="ajax/treeview.php"]').trigger("click"); },
			'show nestable lists' : function() { $('nav a[href="ajax/nestable-list.php"]').trigger("click"); },
			'show jquery U I' : function() { $('nav a[href="ajax/jqui.php"]').trigger("click"); },
			'show typography' : function() { $('nav a[href="ajax/typography.php"]').trigger("click"); },
			'show calendar' : function() { $('nav a[href="ajax/calendar.php"]').trigger("click"); },
			'show widgets' : function() { $('nav a[href="ajax/widgets.php"]').trigger("click"); },
			'show gallery' : function() { $('nav a[href="ajax/gallery.php"]').trigger("click"); },
			'show maps' : function() { $('nav a[href="ajax/gmap-xml.php"]').trigger("click"); },
			'show pricing tables' : function() { $('nav a[href="ajax/pricing-table.php"]').trigger("click"); },
			'show invoice' : function() { $('nav a[href="ajax/invoice.php"]').trigger("click"); },
			'show search page' : function() { $('nav a[href="ajax/search.php"]').trigger("click"); },
			'go back' :  function() { history.back(1); },
			'scroll up' : function () { $('html, body').animate({ scrollTop: 0 }, 100); },
			'scroll down' : function () { $('html, body').animate({ scrollTop: $(document).height() }, 100);},
			'hide navigation' : function() {
				if ($.root_.hasClass("container") && !$.root_.hasClass("menu-on-top")){
					$('span.minifyme').trigger("click");
				} else {
					$('#hide-menu > span > a').trigger("click");
				}
			},
			'show navigation' : function() {
				if ($.root_.hasClass("container") && !$.root_.hasClass("menu-on-top")){
					$('span.minifyme').trigger("click");
				} else {
					$('#hide-menu > span > a').trigger("click");
				}
			},
			'mute' : function() {
				$.sound_on = false;
				$.smallBox({
					title : "MUTE",
					content : "All sounds have been muted!",
					color : "#a90329",
					timeout: 4000,
					icon : "fa fa-volume-off"
				});
			},
			'sound on' : function() {
				$.sound_on = true;
				$.speechApp.playConfirmation();
				$.smallBox({
					title : "UNMUTE",
					content : "All sounds have been turned on!",
					color : "#40ac2b",
					sound_file: 'voice_alert',
					timeout: 5000,
					icon : "fa fa-volume-up"
				});
			},
			'stop' : function() {
				smartSpeechRecognition.abort();
				$.root_.removeClass("voice-command-active");
				$.smallBox({
					title : "VOICE COMMAND OFF",
					content : "Your voice commands has been successfully turned off. Click on the <i class='fa fa-microphone fa-lg fa-fw'></i> icon to turn it back on.",
					color : "#40ac2b",
					sound_file: 'voice_off',
					timeout: 8000,
					icon : "fa fa-microphone-slash"
				});
				if ($('#speech-btn .popover').is(':visible')) {
					$('#speech-btn .popover').fadeOut(250);
				}
			},
			'help' : function() {
				$('#voiceModal').removeData('modal').modal( { remote: "ajax/modal-content/modal-voicecommand.html", show: true } );
				if ($('#speech-btn .popover').is(':visible')) {
					$('#speech-btn .popover').fadeOut(250);
				}
			},
			'got it' : function() {
				$('#voiceModal').modal('hide');
			},
			'logout' : function() {
				$.speechApp.stop();
				window.location = $('#logout > span > a').attr("href");
			}
		};

	};
/*
 * END APP.CONFIG
 */







