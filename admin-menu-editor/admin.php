<?php
if( isset( $_GET['reset'] ) && $_GET['reset'] ) {
	require_once( get_template_directory().'/extensions/admin-menu-editor/templates/reset-confirmation.php' );
	die();
}
?>

<?php global $admin_Dashboard_Admin, $admin_dashboard_settings, $menu, $submenu, $wp_roles, $orig_menu, $orig_sub_menu, $wp_roles, $cm; ?>
<?php include_once FRAMEWORK_DIR.'extensions/admin-menu-editor/js/admin-js.php'; ?>

<div class="debug" style="width: 100%"></div>
<div class='update' id='message-success'></div>

<div id='dialog'>
	<select id='check-default' style="width: 295px;"></select>
</div>

<div class="templates" style="display: none;">
<!-- Dialog select list items template -->
<script id="dialog-content-tmpl" type="text/x-jquery-tmpl">
	<option data-0="${item[0]}"
			data-1="${item[1]}"
			data-2="${item[2]}"
			data-3="${item[3]}"
			data-4="${item[4]}"
			data-5="${item[5]}"
			data-6="${item[6]}" value="${item[0]}">${item[0]}</option>
</script>
<!--  Menu items template  -->
<script id="menu-item-tmpl" type="text/x-jquery-tmpl">
	<li id="" class="menu-item menu-item-custom pending"
		data-0="${item[0]}"
		data-1="${item[1]}"
		data-2="${item[2]}"
		data-3="${item[3]}"
		data-4="${item[4]}"
		data-5="${item[5]}"
		data-6="${item[6]}"
		data-is_protected="${item['is_protected']}"
		data-is_dynamic="${item['is_dynamic']}">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title">${item[0]}</span>
				<span class="item-controls">
					<span class="item-type">{{if item['source'] === undefined}}<?php echo __( 'Custom', 'runway' ); ?>{{else}}${item['source']}{{/if}}</span>
					<a class="item-edit" id="" title="<?php echo __( 'Edit Menu Item', 'runway' ); ?>" href="#" onclick="return false;"><?php echo __( 'Edit Menu Item', 'runway' ); ?></a>
				</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-21" style="display: none; ">
			<p class="field-name name name-thin">
				<label for="edit-menu-item-title-21">
					<?php echo __( 'Name', 'runway' ); ?><br>
					<input type="text" id="edit-menu-item-title-21" style="width:97%;" class="widefat edit-menu-item-title" name="menu-item-title[21]" value="${item[0]}" {{if item['is_protected']}}disabled="disabled"{{/if}}>
				</label>
			</p>
			<p class="field-permissions permissions permissions-wide">
				<label for="edit-menu-item-permissions-21">
					<?php echo __('Permissions', 'runway'); ?><br>
					<select id='edit-menu-item-permissions-21' style="width:97%;" {{if item['is_protected']}}disabled="disabled"{{/if}}>
						<?php   global $wp_roles;
								foreach ( $wp_roles->roles[administrator][capabilities] as $cap => $name ):
									$cap_name = str_replace( '_', ' ', $cap ); ?>
									<option value="<?php echo $cap ?>" <?php echo "{{if item[1] == '$cap'}}selected='true'{{/if}}"; ?>><?php echo ucfirst( $cap_name ); ?></option>
						<?php 	endforeach; ?>
					</select><br>
					<span class="description"><?php echo __('Capability to access this page.', 'runway'); ?></span>
				</label>
			</p>
			<p class="field-url description description-wide">
				<label for="edit-menu-item-url-21">
					<?php echo __( 'URL', 'runway' ); ?><br>
					<input type="text" id="edit-menu-item-url-21" class="widefat code edit-menu-item-url" name="menu-item-url[21]" value="${item[2]}" {{if item['is_protected']}}disabled="disabled"{{/if}}>
				</label>
			</p>

			<div class="menu-item-actions description-wide submitbox">
				<a class="item-delete submitdelete deletion" id="" href="#"><?php echo __( 'Remove', 'runway' ); ?></a>
				<span class="meta-sep"> | </span>
				<a class="item-cancel submitcancel" id="cancel-21" href="#"><?php echo __( 'Cancel', 'runway' ); ?></a>
				<span class="meta-sep"> | </span>
				<a class="default-settings submitcancel" id="cancel-21" href="#"><?php echo __( 'Get settings from...', 'runway' ); ?></a>
			</div>
			<div style="clear: both;"></div>

		</div>
		<ul class="menu-item-transport"></ul>
	</li>
</script>
<!--  Menu spacers template -->
<script id="menu-spacer-tmpl" type="text/x-jquery-tmpl">
	<li id="" class="menu-item menu-item-custom pending"
		data-0="${item[0]}"
		data-1="${item[1]}"
		data-2="${item[2]}"
		data-3="${item[3]}"
		data-4="${item[4]}"
		data-5="${item[5]}"
		data-6="${item[6]}">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title"><?php echo __( 'Spacer', 'runway' ); ?></span>
				<span class="item-controls">
					<span class="item-type">{{if item['source'] === undefined}}<?php echo __( 'Custom', 'runway' ); ?>{{else}}${item['source']}{{/if}}</span>
					<a class="item-edit" id="" title="<?php echo __( 'Edit Menu Item', 'runway' ); ?>" href="#" onclick="return false;"><?php echo __( 'Edit Menu Item', 'runway' ); ?></a>
				</span>
			</dt>
		</dl>

		<div class="menu-item-settings" id="menu-item-settings-21" style="display: none; ">
			<div class="menu-item-actions description-wide submitbox">
				<a class="item-delete submitdelete deletion" id="" href="#"><?php echo __( 'Remove', 'runway' ); ?></a> <span class="meta-sep"> | </span> <a class="item-cancel submitcancel" id="cancel-21" href="<?php admin_url( 'nav-menus.php?edit-menu-item=21&amp;cancel=1346668427#menu-item-settings-21' ); ?>"><?php echo __( 'Cancel', 'runway' ); ?></a>
			</div>

			<div style="clear: both;"></div>

		</div><!-- .menu-item-settings-->
		<ul class="menu-item-transport"></ul>
	</li>
	</script>
<!--  Deleted menu items template  -->
<script id="delete-item-tmpl" type="text/x-jquery-tmpl">
	<li class="menu-item menu-item-custom pending menu-item-depth-0" style="display: list-item; position: relative; "
		data-0="${item[0]}"
		data-1="${item[1]}"
		data-2="${item[2]}"
		data-3="${item[3]}"
		data-4="${item[4]}"
		data-5="${item[5]}"
		data-6="${item[6]}">
		<dl class="menu-item-bar">
			<dt class="menu-item-handle">
				<span class="item-title">${item[0]}</span>
				<span class="item-controls">
					<a class="item-restore" id="restore-delete-item" title="restore" href=""><?php echo __( 'Restore', 'runway' ); ?></a>
					<a class="item-edit" id="" title="<?php echo __('Edit Menu Item', 'runway'); ?>" href="#" onclick="return false;"><?php echo __( 'Edit Menu Item', 'runway' ); ?></a>
				</span>
			</dt>
		</dl>
		<div class="menu-item-settings" id="menu-item-settings-21" style="display: none; ">
			<p class="field-name name name-thin">
				<label for="edit-menu-item-title-21">
					<?php echo __( 'Name', 'runway' ); ?><br>
					<input type="text" id="edit-menu-item-title-21" style="width:97%;" class="widefat edit-menu-item-title" name="menu-item-title[21]" value="${item[0]}">
				</label>
			</p>
			<p class="field-permissions permissions permissions-wide">
				<label for="edit-menu-item-permissions-21">
					<?php echo __( 'Permissions', 'runway' ); ?><br>
					<select id='edit-menu-item-permissions-21' style="width:97%;">
						<?php 	global $wp_roles;
								foreach ( $wp_roles->roles[administrator][capabilities] as $cap => $name ):
									$cap_name = str_replace( '_', ' ', $cap ); ?>
									<option value="<?php echo $cap ?>" <?php echo "{{if item[1] == '$cap'}}selected='true'{{/if}}"; ?>><?php echo ucfirst( $cap_name ); ?></option>
						<?php 	endforeach; ?>
					</select><br>
					<span class="description"><?php echo __( 'Capability to access this page.', 'runway' ); ?></span>
				</label>
			</p>
			<p class="field-url description description-wide">
				<label for="edit-menu-item-url-21">
					<?php echo __( 'URL', 'runway' ); ?><br>
					<input type="text" id="edit-menu-item-url-21" class="widefat code edit-menu-item-url" name="menu-item-url[21]" value="${item[2]}">
				</label>
			</p>
			<div class="menu-item-actions description-wide submitbox">
				<a class="item-delete submitdelete deletion" id="" href="#"><?php echo __( 'Remove', 'runway' ); ?></a>
				<span class="meta-sep"> | </span>
				<a class="item-cancel submitcancel" id="cancel-21" href="#"><?php echo __( 'Cancel', 'runway' ); ?></a>
			</div>
			<div style="clear: both;"></div>
		</div>
		<ul class="menu-item-transport"></ul>
	</li>
</script>
</div>

<div id="nav-menus-frame">
	<div id="menu-settings-column" class="metabox-holder">
		<div id="side-sortables" class="meta-box-sortables ui-sortable">

			<div id="nav-menu-theme-locations" class="postbox ">
				<div class="handlediv" title="<?php echo __( 'Click to toggle', 'runway' ); ?>">
					<br>
				</div>
				<h3 class="hndle"><span><?php echo __( 'New menu items', 'runway' ); ?></span></h3>
				<div class="inside">
					<ul class="new-items">
						<li class="menu-item menu-item-custom pending menu-item-template-to-clone">
							<dl class="menu-item-bar">
								<dt class="menu-item-handle">
									<span class="item-title"><?php echo __( 'Menu item', 'runway' ); ?></span>
								</dt>
							</dl>
						</li>
						<li class="menu-item menu-item-custom pending menu-spacer-template">
							<dl class="menu-item-bar">
								<dt class="menu-item-handle">
									<span class="item-title"><?php echo __( 'Spacer', 'runway' ); ?></span>
									<span class="item-controls">
										<span class="item-type"><?php echo __( 'Custom', 'runway' ); ?></span>
									</span>
								</dt>
							</dl>
						</li>
					</ul>
				</div>
			</div>

			<div id="add-custom-links" class="postbox ">
				<div class="handlediv" title="<?php echo __( 'Click to toggle', 'runway' ); ?>">
					<br>
				</div>
				<h3 class="hndle"><span><?php echo __( 'Deleted menu items', 'runway' ); ?></span></h3>
				<div class="inside">
					<ul class="deleted-menu-items" id="deleted-menu">

					</ul>
				</div>
			</div>

		</div>
	</div>

	<div id="menu-management-liquid" class="nav-menus-php">
		<div id="menu-management">
			<div class="menu-edit">

					<div id="nav-menu-header">
						<div id="submitpost" class="submitbox">
							<div class="major-publishing-actions">
								<button class="button reset-menu"><?php echo __( 'Reset to default', 'runway' ); ?></button>
								<button class="button-primary ajax-save"><?php echo __( 'Save menu', 'runway' ); ?></button>
							</div>
						</div>
					</div>

					<div id="post-body">
						<div id="post-body-content">
							<div class="ui-sortable dynamic_pages_sortable">
								<ul class="menu ui-sortable" id="menu-to-edit"></ul>
							</div>

						</div>
					</div>

					<div id="nav-menu-footer">
						<div class="major-publishing-actions">
							<div class="publishing-action">
								<button class="button reset-menu"><?php echo __( 'Reset to default', 'runway' ); ?></button>
								<button class="button-primary ajax-save"><?php echo __( 'Save menu', 'runway' ); ?></button>
							</div>
						</div>
					</div>

			</div>
		</div>
	</div>

</div>
