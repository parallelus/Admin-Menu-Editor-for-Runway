<?php

class Admin_Dashboard_Admin_Object extends Runway_Admin_Object {
	public $option_key;
	private $cm = false;

	function __construct($settings) {
		parent::__construct($settings);

		$this->option_key = $settings['option_key'];

		add_action( 'admin_head', array( $this, 'hook_menu' ) );
		// catching ajax-query and create $menu_settings array
		$menu_settings = array( 'menu' => array(), 'removed' => array() ); // contains base menu items and removed menu items

		if ( isset( $_REQUEST['reset'] ) ) {
			delete_option( $this->option_key );
		} else {
			if ( isset( $_POST['save'] ) ) {
				$menu_settings['menu']     = isset($_POST['menu'])? $_POST['menu'] : '';
				$menu_settings['removed']  = isset($_POST['removed'])? $_POST['removed'] : '';
				$menu_settings['imported'] = isset($_POST['imported'])? $_POST['imported'] : '';
				$menu_settings['order']    = isset($_POST['order'])? $_POST['order'] : '';
				$this->process_submit( $menu_settings );
			}
		}
	}

	function init($settings) {

	}

	function get_converted_menu() {

		global $menu, $submenu;

		if(!$this->cm)
			$this->cm = get_option( $this->option_key );

		if ( !$this->cm ) {
			$this->cm = array(
				'menu' => array(),
				'removed' => array(),
				'imported' => array(),
			);
		}
		$list = array();
		foreach ( $menu as $menuitem ) {
			$list[$menuitem[2]] = $menuitem;
			if ( isset( $submenu[$menuitem[2]] ) ) {
				$_menu[$menuitem[2]]['subitems'] = array();
				foreach ( $submenu[$menuitem[2]] as $subitem ) {
					$subitem['parent'] = $menuitem[2];
					$list[$menuitem[2] . '/' . $subitem[2]] = $subitem;
				}
			}
		}
		$to_adding_items = array_diff_key( (array)$list, (array)$this->cm['imported'] );
		foreach ( $to_adding_items as $item ) {

			if ( isset( $item['parent'] ) ) {
				$this->cm['menu'][$item['parent']]['subitems'][$item[2]] = $item;
			} else {
				$this->cm['menu'][$item[2]] = $item;
			}
		}
		$this->cm['imported'] = $list;
		echo '<table>';
		echo '<tr><td><pre>
		' . print_r( $this->cm['imported'], true ) . '
		</pre></td><td><pre>
		' . print_r( $list, true ) .'
		</pre></td></tr>';
		echo '</table>table>';
		$to_removing_items = array_diff_key( (array)$this->cm['imported'], (array)$list );
		return $this->cm;

	}

	// update menu-settings option. option_key => 'cm'
	function process_submit( $menu_settings = array() ) {

		if ( isset( $menu_settings ) && !empty( $menu_settings ) ) {
			update_option( $this->option_key, $menu_settings );
		}

	}

	function hook_menu() {

		global $menu, $submenu, $cm;

		if(!$this->cm)
			$this->cm = get_option( $this->option_key );
		$this->cm['menu'] = stripslashes_deep($this->cm['menu']);

		if ( !$this->cm ) {
			$this->cm = array(
				'menu' => array(),
				'removed' => array(),
				'imported' => array(),
				'order' => array()
			);
		}

		// TO DEVELOP. (RESET TO DEFAULT)
		$this->cm['original'] = get_original_menu_full_array( $menu, $submenu );

		update_option( $this->option_key, $this->cm );
		$list = array();
		$sortOrders = array(
			'downloads' => array(),
			'current-theme' => array( 'admin.php?page=options-builder&navigation=new-page' )
		);

		foreach ( $menu as $menuitem ) {
			if ( $menuitem[4] == 'wp-menu-separator' ) {
				$menuitem['source'] = __('Spacer', 'runway');
			} else {
				$menuitem['source'] = __('Default', 'runway');
			}

			$list[$menuitem[2]] = $menuitem;
			if ( isset( $submenu[$menuitem[2]] ) ) {
				$_menu[$menuitem[2]]['subitems'] = array();
				if ( array_key_exists( $menuitem[2], $sortOrders ) && count( $sortOrders[$menuitem[2]] ) ) {
					$order = $sortOrders[$menuitem[2]];
					foreach ( $submenu[$menuitem[2]] as $val ) {
						$tmp[$val[2]] = $val;
					}
					$submenu[$menuitem[2]] = array();
					foreach ( $order as $value ) {
						if(isset($submenu[$menuitem[2]]) && !empty($submenu[$menuitem[2]]))
							$submenu[$menuitem[2]][] = $tmp[$value];
						unset( $tmp[$value] );
					}
					if ( count( $tmp ) ) {
						$submenu[$menuitem[2]] = array_merge( $tmp, $submenu[$menuitem[2]] );
					}
				}
				foreach ( $submenu[$menuitem[2]] as $subitem ) {
					$subitem['source'] = __('Default', 'runway');
					$subitem['parent'] = $menuitem[2];
					$list[$menuitem[2] . '/' . $subitem[2]] = $subitem;
				}
			}
		}

		$this->cm['imported'] =	(isset($this->cm['imported']) )? $this->cm['imported'] : array();
		$to_adding_items =	array_diff_key( (array)$list, (array)$this->cm['imported'] );

		$top_item_names =	array();
		foreach ( $menu as $value ) {
			$top_item_names[$value[2]] = $value[0];
		}

		global $menu_items_from_theme;
		// items to remove wordpress counts
		$has_integer =	array( 'update-core.php', 'edit-comments.php', 'plugins.php' );
		$protected =	array( 'framework-options', 'current-theme', 'marketplace', 'downloads' );

		foreach ( $to_adding_items as $item ) {
			if ( isset( $item[2] ) ) {
				// remove wordpress counts
				if ( in_array( $item[2], $has_integer ) ) {
					$item[0] = trim( str_replace( range( 0, 9 ), '', $item[0] ) );
				}
				if ( in_array( $item[2], $protected ) || ( isset( $item['parent'] ) && in_array( $item['parent'], $protected ) ) ) {
					$item['is_protected'] = true;
				}
				$item['is_dynamic'] = false;
				if ( is_array( $menu_items_from_theme ) && in_array( $item[2], $menu_items_from_theme ) ) {
					$item['source'] = 'Theme/' . THEME_NAME . '/' . $top_item_names[$item['parent']];
					$item['is_protected'] = true;
					$item['is_dynamic'] =	true;
				}
				if ( isset( $item['parent'] ) ) {
					$this->cm['menu'][$item['parent']]['subitems'][] = $item;
					if ( $item['parent'] == 'current-theme' ) {
						foreach ( $this->cm['menu'][$item['parent']]['subitems'] as $sub_key => $sub ) {
							if ( $sub[2] == 'admin.php?page=options-builder&navigation=new-page' ) {
								unset( $this->cm['menu'][$item['parent']]['subitems'][$sub_key] );
								$list['current-theme/admin.php?page=options-builder&navigation=new-page']['is_protected'] = true;
								$list['current-theme/admin.php?page=options-builder&navigation=new-page']['is_dynamic'] =	false;
								$this->cm['menu'][$item['parent']]['subitems'][9999] = $list['current-theme/admin.php?page=options-builder&navigation=new-page'];
							}
						}
					}
				} else {
					$this->cm['menu'][$item[2]] = $item;
				}
			}
		}

		$to_removing_items = array_diff_key( (array)$this->cm['imported'], (array)$list );
		if ( count( $to_removing_items ) ){
			foreach ( $to_removing_items as $item ) {
				if ( isset( $item['parent'] ) ) {
					foreach ( $this->cm['menu'] as $key => $_item ) {
						if ( $_item[2] == $item['parent'] ) {
							foreach ( $_item['subitems'] as $_key => $subitem ) {
								if ( $subitem[2] == $item[2] ) {
									unset( $this->cm['menu'][$key]['subitems'][$_key] );
								}
							}
						}
					}
				} else {
					foreach ( $this->cm['menu'] as $key => $_item ) {
						if ( isset($item[2]) && $_item[2] == $item[2] )
						{
							unset( $this->cm['menu'][$key] );
						}
					}
				}
			}
		}

		$this->cm['imported'] = $list;
		// fix [0] empty element temp
		foreach ( (array)array_keys( $submenu ) as $parent ) {
			foreach ( $submenu[$parent] as $key => $value ) {
				if ( empty( $submenu[$parent][$key] ) )
					unset( $submenu[$parent][$key] );
			}
		}

		global $menu_items_icons;
		// rewrite wordpress menu by custom
		if ( isset( $this->cm['menu'] ) && !empty( $this->cm['menu'] ) ) {
			$menu =	array();
			$submenu = array();
			foreach ( $this->cm['menu'] as $tkey => $tvalue ) {
//                $tvalue[0] = stripslashes($tvalue[0]);
				if ( empty( $tvalue[4] ) ) {
					$tvalue[4] = 'menu-top';
				}
				if ( isset($tvalue['is_dynamic']) && $tvalue['is_dynamic'] == 'true' && isset( $menu_items_icons[$tvalue[2]] ) ) {
					if ( empty( $menu_items_icons[$tvalue[2]] ) ) {
						$tvalue[6] = admin_url('images/generic.png');
					} else {
						if ( strstr( $menu_items_icons[$tvalue[2]], 'http://' ) ) {
							$tvalue[6] = $menu_items_icons[$tvalue[2]];
						} else {
							$tvalue[4] .= ' ' .$menu_items_icons[$tvalue[2]];
						}
					}
				}
				if ( preg_match( '/index.php[\?]/', $tvalue[2] ) ) {
					$tvalue[2] = 'wp-admin/../'.$tvalue[2];
				}

				$menu[$tkey] = $tvalue;
				if ( isset( $tvalue['subitems'] ) ) {
					$submenu[$tvalue[2]] = $tvalue['subitems'];
					unset( $menu[$tkey]['subitems'] );
				}
			}
		}
		$cm = $this->cm;
		// external links
		foreach ( (array)array_keys( $submenu ) as $parent ) {
			foreach ( $submenu[$parent] as $key => $value ) {
				if ( preg_match( '/index.php[\?]/', $submenu[$parent][$key][2] ) ) {
					$submenu[$parent][$key][2] = 'wp-admin/../'.$submenu[$parent][$key][2];
				}
				if ( strstr( $submenu[$parent][$key][2], 'http:' ) || strstr( $submenu[$parent][$key][2], 'https:' ) ) {
					$submenu[$parent][$key][2] = str_replace( '/', '//', $submenu[$parent][$key][2] );
				}
			}
		}
	}

	// Add hooks & crooks
	function add_actions() {



	}

	function after_settings_init() {
		/* nothing */
	}

	function validate_sumbission() {

		$this->process_submit();

		$_POST['index'] = 'admin_dashboard';

		// If all is OK
		return true;

	}

	function load_objects( $data = array() ) {

		$this->data = $this->load_objects();
		return $this->data;
	}

}

/**
 * Function to get merged wordpress real menu and submenu
 *
 * @param array   $wp_menu
 * @param array   $wp_submenu
 * @return array
 */
function get_original_menu_full_array( $wp_menu = array(), $wp_submenu = array() ) {

	$merged = array(); $new_items = array();
	foreach ( $wp_menu as $key => $value ) {
		$merged[$key] = $value;
		if ( $value[4] != 'wp-menu-separator' ) {
			foreach ( $wp_submenu as $subkey => $sub_value ) {
				if ( $value[2] == $subkey ) {
					if ( is_array( $sub_value ) ) {
						foreach ( $sub_value as $k => $v ) {
							$merged[$key]['subitems'][$k] = $v;
						}
					}
				}
			}
		}
	}
	return $merged;

}


?>