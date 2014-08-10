<?php

class SmartUI extends SmartUtil {

    const SMARTUI_WIDGET = "widget";
    const SMARTUI_DATATABLE = "datatable";
    const SMARTUI_BUTTON = "button";
    const SMARTUI_TAB = "tab";

    private static $_ui_calls = array("create", "set", "get");
    private $_track_start_time;

    private static $_uis = array();

    public static function register($name, $class) {
        self::$_uis[$name] = $class;
    }

	protected function _call($ui_member, $structure, $name, $args) {
        if (property_exists($structure, $name)) {
            if (!$args) return $structure->{$name};

            $value = null;
            $key = null;
            if (count($args) > 1 && is_array($structure->{$name})) {
                $key = $args[0];
                $value = $args[1];
                if (!is_string($key) && !is_int($key)) {
                    SmartUI::err("SmartUI structure property: $name must be string or int.");
                    return null;
                }
                
                $structure->{$name}[$key] = $value;

                if (isset($args[2]) && SmartUtil::is_closure($args[2])) {
                    //process callback
                    $callback = $args[2];
                    SmartUtil::run_callback($callback, array($ui_member));
                }

                return $ui_member;
            } else {
                if (isset($args[1]) && SmartUtil::is_closure($args[1])) {
                    $value = $args[0];
                    $structure->{$name} = $value;
                    $callback = $args[1];
                    SmartUtil::run_callback($callback, array($ui_member));
                    return $ui_member;
                } else if (is_array($structure->{$name}) && (is_string($args[0]) || is_int($args[0]))) {
                    $key = $args[0];
                    if (!is_string($key) && !is_int($key)) {
                        SmartUI::err("SmartUI property key: $key must be string or int.");
                        return null;
                    }
                    return $structure->{$name}[$key];
                } else {
                    $value = $args[0];
                    $structure->{$name} = $value;
                    return $ui_member;
                }
            }
        }

        SmartUI::err('Undefined structure property: '.$name);

        return null;
    }

    public function start_track() {
        $this->_track_start_time = microtime(true);
    }

    public function __call($name, $args) {
        $calls = explode('_', $name);
        if (!in_array($calls[0], self::$_ui_calls)) {
            self::err("Undefined call: $calls[0]");
            return null;
        }

        $ui_class = strtolower($calls[1]);

        if (isset(self::$_uis[$ui_class])) {
            $reflection = new ReflectionClass(self::$_uis[$ui_class]); 
            $new_ui = $args ? $reflection->newInstanceArgs($args) : $reflection->newInstance(); 

            $this->start_track();
            return $new_ui;
        }

        self::err("\"$ui_class\" is not a registered member of SmartUI: Class not found");
    }

    public function run_time($print = true) {
        $time_end = microtime(true);
        $execution_time = number_format($time_end - $this->_track_start_time, 4);
        if ($print) echo $execution_time.'s';
        else return $execution_time.'s';
    }

	public static function err($message = "SmartUI Error notice:") {
		$trace = debug_backtrace();
        trigger_error($message.' in '.$trace[0]['file'].' on line '.$trace[0]['line'], E_USER_NOTICE);
	}

    public static function get_progress($value, $type = '', $options = array()) {
        $real_value = str_replace('%', '', $value);
        $percent_value = $real_value.'%';

        $options_map = array(
            'transitional' => false,
            'class' => array(),
            'attr' => array(),
            'background' => '',
        );

        $new_options_map = parent::set_array_prop_def($options_map, $options, 'class');

        $classes = array();
        $classes[] = 'progress-bar';
        if ($type) $classes[] = 'progress-bar-'.$type;

        // add additional user classes
        if (is_array($new_options_map['class'])) {
            array_merge($classes, $new_options_map['class']);
        } else {
            $classes[] = $new_options_map['class'];
        }

        if ($new_options_map['background']) $classes[] = 'bg-color-'.$new_options_map['background'];

        $attrs = array();
        $attrs_html = array();

        if ($new_options_map['transitional']) 
            $attrs['aria-valuetransitiongoal'] = $real_value;
        else
            $attrs['style'] = 'width: '.$percent_value;


        // add additional user attributes
        if (is_array($new_options_map['attr'])) {
            array_merge($attrs, $new_options_map['attr']);
        } else {
            $attrs_html[] = $new_options_map['attr'];
        }
      
        foreach ($attrs as $attr => $attr_value) {
            $attrs_html[] = $attr.'="'.$attr_value.'"';
        }

        return'<div class="'.implode(' ', $classes).'" '.implode(' ', $attrs_html).'></div>';
    }

    public static function print_stack_progress($progress_bars, $base_options = array(), $return = false) {
        $options_map = array(
            'tooltip' => array(),
            'position' => 'left', // left, right, bottom (for vertical)
            'wide' => false,
            'size' => 'md', // sm, xs, md, xl, micro
            'striped' => false, // true or 'active'
            'vertical' => false
        );

        $new_options_map = parent::set_array_prop_def($options_map, $base_options, 'class');

        $container_classes = array();
        $container_classes[] = "progress";
        if ($new_options_map['vertical']) $container_classes[] = 'vertical';
        if ($new_options_map['position']) $container_classes[] = $new_options_map['position'];
        if ($new_options_map['wide']) $container_classes[] = 'wide-bar';
        if ($new_options_map['striped']) {
            $container_classes[] = 'progress-striped';
            if ($new_options_map['striped'] === 'active')
                $container_classes[] = 'active';
        }

        $container_classes[] = 'progress-'.$new_options_map['size'];

        $container_attrs = array();
        $container_attrs_html = array();

        if ($new_options_map['tooltip']) {
            $tooltip_prop =  array(
                'placement' => 'top',
                'title' => ''
            );
            $tooltip = $new_options_map['tooltip'];
            $new_tooltip_prop = parent::set_array_prop_def($tooltip_prop, $tooltip, 'title');
            $container_attrs['rel'] = 'tooltip';
            $container_attrs['data-original-title'] = $new_tooltip_prop['title'];
            $container_attrs['data-placement'] = $new_tooltip_prop['placement'];
        }

        foreach ($container_attrs as $container_attr => $attr_value) {
            $container_attrs_html[] = $container_attr.'="'.$attr_value.'"';
        }

        $result = '<div class="'.implode(' ', $container_classes).'" '.implode(' ', $container_attrs_html).'>';
        $result .= implode('', $progress_bars);
        $result .= '</div>';

        if ($return) return $result;
        else echo $result;
    }

    public static function print_progress($value, $type = '', $options = array(), $return = false) {
        $real_value = str_replace('%', '', $value);
        $percent_value = $real_value.'%';

        $options_map = array(
            'transitional' => false,
            'class' => array(),
            'attr' => array(),
            'background' => '',
            'tooltip' => array(),
            'position' => 'left', // left, right, bottom (for vertical)
            'wide' => false,
            'size' => 'md', // sm, xs, md, xl, micro
            'striped' => false, // true or 'active'
            'vertical' => false
        );

        $new_options_map = parent::set_array_prop_def($options_map, $options, 'class');

        $container_classes = array();
        $container_classes[] = "progress";
        if ($new_options_map['vertical']) $container_classes[] = 'vertical';
        if ($new_options_map['position']) $container_classes[] = $new_options_map['position'];
        if ($new_options_map['wide']) $container_classes[] = 'wide-bar';
        if ($new_options_map['striped']) {
            $container_classes[] = 'progress-striped';
            if ($new_options_map['striped'] === 'active')
                $container_classes[] = 'active';
        }

        $container_classes[] = 'progress-'.$new_options_map['size'];

        $container_attrs = array();
        $container_attrs_html = array();

        if ($new_options_map['tooltip']) {
            $tooltip_prop =  array(
                'placement' => 'top',
                'title' => $percent_value
            );
            $tooltip = $new_options_map['tooltip'];
            $new_tooltip_prop = parent::set_array_prop_def($tooltip_prop, $tooltip, 'title');
            $container_attrs['rel'] = 'tooltip';
            $container_attrs['data-original-title'] = $new_tooltip_prop['title'];
            $container_attrs['data-placement'] = $new_tooltip_prop['placement'];
        }

        foreach ($container_attrs as $container_attr => $attr_value) {
            $container_attrs_html[] = $container_attr.'="'.$attr_value.'"';
        }

        $result = '<div class="'.implode(' ', $container_classes).'" '.implode(' ', $container_attrs_html).'>';
        $result .= self::get_progress($value, $type, $options);
        $result .= '</div>';

        if ($return) return $result;
        else echo $result;
    }

    public static function print_alert($message, $type = 'info', $options = array(), $return = false) {
        $options_map = array(
            'closebutton' => true,
            'block' => false,
            'container' => 'div',
            'class' => array(),
            'fade_in' => true,
            'icon' => $type
        );

        $icon_map = array(
            'info' => 'fa-info',
            'warning' => 'fa-warning',
            'danger' => 'fa-times',
            'success' => 'fa-check'
        );
        $new_options_map = parent::set_array_prop_def($options_map, $options, 'class');
        $closebutton_html = $new_options_map['closebutton'] ? 
                        '<button class="close" data-dismiss="alert">
                            ×
                        </button>' : '';

        $classes = array();
        $classes[] = 'alert';
        $classes[] = 'alert-'.$type;
        if ($new_options_map['fade_in']) $classes[] = 'fade in';
        if ($new_options_map['block']) $classes[] = 'alert-block';

        $icon_html = $new_options_map['icon'] ? '<i class="fa-fw fa '.(isset($icon_map[$type]) ? $icon_map[$type] : 'fa-info').'"></i>' : '';

    	$result = '<div class="'.implode(' ', $classes).'">
                        '.$closebutton_html.'
                        '.$icon_html.'
                        '.$message.'
                    </div>';
    	if ($return) return $result;
    	else echo $result;
    }

    public static function print_dropdown($items, $multi_level = false, $return = false) {
        $get_property_value = self::_get_property_value_func();
        $items_html = '';
        foreach ($items as $item) {
            $item_html = '';
            $item_prop = array(
                'content' => '',
                'submenu' => array(),
                'class' => ''
            );

            $new_item_prop = $get_property_value($item, array(
                'if_array' => function($item) use ($item_prop) {
                    return SmartUtil::set_array_prop_def($item_prop, $item, 'content');
                },
                'if_closure' => function($item) use ($item_prop) {
                    return SmartUtil::set_closure_prop_def($item_prop, $item);
                },
                'if_other' => function($item) use ($item_prop) {
                    $item_prop['content'] = $item;
                    return $item_prop;
                }
            ));

            $classes = array();
            if ($new_item_prop['class'])
                $classes[] = $new_item_prop['class'];

            $content = $new_item_prop['content'];

            if ($new_item_prop['submenu']) {
                $content .= self::print_dropdown($new_item_prop['submenu'], false, true);
                $classes[] = 'dropdown-submenu';
            } else if ($content === '-') {
                $classes[] = 'divider';
            }

            $class = $classes ? ' class="'.trim(implode(' ', $classes)).'"' : '';

            $item_html = '<li'.$class.'>'.$content.'</li>';
            $items_html .= $item_html;
        }

        $result = '<ul class="dropdown-menu'.($multi_level ? ' multi-level' : '').'" role="menu">';
        $result .= $items_html;
        $result .= '</ul>';

        if ($return) return $result;
        else echo $result;
    }
}


?>