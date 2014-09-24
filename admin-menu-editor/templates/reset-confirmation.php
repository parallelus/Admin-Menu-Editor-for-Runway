<?php 
	$action_url = admin_url('admin.php?page=admin-menu');
?>

<form method="post" action="<?php echo $action_url; ?>" style="display:inline;">
	<p>
		<?php echo __( 'This will clear any custom menu settings done in the Admin Menu Editor. Are you sure?', 'framework'); ?>
	</p>
	<input type="hidden" name="reset" value=true />	
	<?php submit_button( __( 'Yes, Reset this', 'framework'), 'button', 'submit', false ); ?>
</form>
<form method="post" action="<?php echo $action_url; ?>" style="display:inline;">
	<?php submit_button( __( 'No, Return me to the custom admin menu', 'framework' ), 'button', 'submit', false ); ?>
</form>