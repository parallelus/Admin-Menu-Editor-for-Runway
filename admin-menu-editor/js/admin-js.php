<script type="text/javascript">
	(function ($) {
		$(function () {

			var cm = $.parseJSON('<?php echo mysql_real_escape_string( strip_tags( json_encode( $cm ) ) ); ?>');

			if(!cm['removed']){
				cm['removed'] = [];
			}

			function intiDeletedDrug() {
				$('.deleted-menu-items li').draggable({
					connectToSortable: '#menu-to-edit',
					helper: 'clone'
				});
			}

			function restoreElement(elm){
				var apnd = elm.parent().parent().parent().parent();
				apnd.addClass()
				for(key in data.removed){
					if(data.removed[key][0] == apnd.data('0')){
						data.removed.splice(key, 1);
					}
				}
				apnd.find("a.item-restore").remove();
				apnd.find("span.item-controls").append('<span class="item-type"><?php echo __('Default', 'framework'); ?></span>');
				$('#menu-to-edit li').first().before(apnd).fadeIn('slow');
			}

			function showMenuItemSettings(elm){
				if(elm.closest('.deleted-menu-items').length) return false;
				var $item = elm.closest('li');
				var $editForemoved = $item.find('.menu-item-settings').slideToggle('slow');

				return false;
			}
			//li[data-is_protected="true"]
			var placeholder = null;
			$('.new-items li').draggable({
				connectToSortable: '#menu-to-edit',
				helper: 'clone',
				stop: function () {
					if(!$('#menu-to-edit .menu-item-template-to-clone').length) return true;
					currentTime = new Date().getTime();
				   data_arr = {
					   0: '<?php echo __('New title', 'framework'); ?>',
					   1: 'switch_themes',
					   2: 'index.php?p=' + currentTime,
					   3: "",
					   4: '',
					   5: '',
					   6: "div",
					   source: '<?php echo __('Custom', 'framework'); ?>'
				   };
				   $('#menu-to-edit .menu-item-template-to-clone').replaceWith($('.templates #menu-item-tmpl').clone().tmpl({item: data_arr}).addClass(placeholder.removeClass('sortable-placeholder').prop("class")));
					$("#menu-to-edit .menu-item").find('.menu-item-handle').unbind('click').bind('click', function(){
						showMenuItemSettings($(this));
					});

					$('#restore-delete-item').unbind('click');
					$('body').on('click', '#restore-delete-item', function(e){
						e.preventDefault();
						restoreElement($(this));
					});

					$('.item-delete').unbind('click');
					$(".item-delete").click(function(e) {
						e.preventDefault();
						itemRemoveEvent($(this).closest('li'));
					});
					$('a.default-settings').unbind('click');
					$('a.default-settings').bind('click', function(e){
						settings_from_default(cm, $(this),e);
					});
				}
			});

			$("#menu-to-edit").bind("sortstart", function(event, ui) {
				placeholder = $('.sortable-placeholder');
			});

			for(var key in cm.menu) {

				if(cm.menu[key][4] == 'wp-menu-separator') {
					$('#menu-to-edit').append(
						$('.templates #menu-spacer-tmpl')
							.clone()
							.tmpl({item: cm.menu[key]})
							.addClass('menu-item-depth-0 menu-spacer-template')
					);
				} else {
					$('#menu-to-edit').append(
						$('.templates #menu-item-tmpl')
							.clone()
							.tmpl({item: cm.menu[key]})
							.addClass('menu-item-depth-0')
					);

					if(cm.menu[key].subitems) {
						for(var skey in cm.menu[key].subitems) {

							$('#menu-to-edit').append(
								$('.templates #menu-item-tmpl')
									.clone()
									.tmpl({item: cm.menu[key].subitems[skey]})
									.addClass('menu-item-depth-1')
							);
						}
					}
				}
			}

			jQuery('li[data-is_dynamic="true"]').sortable({
				connectWith: "#menu-to-edit",
				placeholder: 'sortable-placeholder',
				stop: function (event, ui) {
					var el = $(event.target).append($("#menu-to-edit>dl.menu-item-bar").clone());

					$("#menu-to-edit>dl.menu-item-bar").replaceWith(el);

					if(el.prev().hasClass('menu-spacer-template'))
						el.removeClass('menu-item-depth-1').addClass('menu-item-depth-0');

					$(el).attr("data-is_protected", false);
					$(this).sortable('destroy');

					wpNavMenu.initSortables();
				},

			});

			for(var key in cm['removed']){
				$('ul.deleted-menu-items').append(
					$('.templates #delete-item-tmpl')
						.clone()
						.tmpl({item: cm['removed'][key]})
				);
			}

			$(".menu-item-handle").bind('click', function (e) {
				e.preventDefault();
				showMenuItemSettings($(this));
			});

			function item_remove($item) {

				if($item.hasClass("menu-spacer-template")) {
					$item.fadeOut('slow', function () {
						$item.remove();
					});
				} else {
					$item.fadeOut('slow', function () {
						$item.find('.menu-item-settings').hide();
						$item.removeClass('menu-item-depth-1');
						if(!$item.hasClass('menu-item-depth-0')){
							$item.addClass('menu-item-depth-0')
						}
						$('.deleted-menu-items').append($item);
						$item.find("span.item-type").remove();
						$item.find("span.item-controls").append('<a class="item-restore" id="restore-delete-item" title="restore" href=""><?php echo __('Restore', 'framework'); ?></a>');
						$item.fadeIn("slow");
					});
				}
			}

			var data = {
				menu: cm.menu,
				removed: cm['removed'],
				save: true
			}; // data to ajax

			var index = 0;
			if(data.removed.length != 0) index = data.removed.length;

			function itemRemoveEvent($item){
				var toRemove = [];

				toRemove.push($item);

				if($item.data('is_protected')){
					return false;
				}

				if($item.hasClass('menu-item-depth-0') && !$item.hasClass('menu-spacer-template')) {
					data.removed[index]={
						0: $item.data('0'),
						1: $item.data('1'),
						2: $item.data('2'),
						3: "",
						4: $item.data('4'),
						5: $item.data('5'),
						6: "div"
					};
					currentTop = index; index++

					while($item.next().length == 1 && $item.next().hasClass('menu-item-depth-1')) {
						toRemove.push($item.next());
						$item = $item.next();
						data.removed[index] = {
							0: $item.data('0'),
							1: $item.data('1'),
							2: $item.data('2')
						};
						index++;
					}
				}
				for(var key in toRemove) {
					item_remove(toRemove[key]);
				}

				$('#restore-delete-item').unbind('click');
				$('body').on('click', '#restore-delete-item', function(e){
					e.preventDefault();
					restoreElement($(this));
				});
				return false;
			}

			$(".item-delete").click(function(e) {
				e.preventDefault();
				itemRemoveEvent($(this).closest('li'));
			});

			$('.reset-menu').click(function () {
				$.ajax({
					url: "<?php echo admin_url('admin.php?page=admin-menu'); ?>",
					data: {reset: true},
					success: function (data) {
						location.reload();
					}
				});
			});

			$('.ajax-save').click(function () {

				var index = 0;
				var currentTop = index;
				var _menu = {};
				var separator_index = 0;

				$('#menu-to-edit li').each(function () {

					if($(this).hasClass('menu-spacer-template')) {

						separator_index++;

						_menu["separator" + separator_index] = {
							0: "",
							1: "read",
							2: "separator" + separator_index,
							3: "",
							4: "wp-menu-separator",
							source: $(this).find('.item-type').text(),
							is_protected: $(this).data('is_protected'),
							is_dynamic: $(this).data('is_dynamic')
						};

					} else {
						if($(this).hasClass('menu-item-depth-0')) {
							_menu[$(this).find('#edit-menu-item-url-21').val()] = {
								0: $(this).find('#edit-menu-item-title-21').val(),
								1: $(this).find('#edit-menu-item-permissions-21').val(),
								2: $(this).find('#edit-menu-item-url-21').val(),
								3: "",
								4: $(this).data('4'),
								5: $(this).data('5'),
								6: $(this).data('6'),
								subitems: [],
								source: $(this).find('.item-type').text(),
								is_protected: $(this).data('is_protected'),
								is_dynamic: $(this).data('is_dynamic')
							};

							currentTop = $(this).find('#edit-menu-item-url-21').val();

						} else {
							_menu[currentTop].subitems.push({
								0: $(this).find('#edit-menu-item-title-21').val(),
								1: $(this).find('#edit-menu-item-permissions-21').val(),
								2: $(this).find('#edit-menu-item-url-21').val(),
								source: $(this).find('.item-type').text(),
								is_protected: $(this).data('is_protected'),
								is_dynamic: $(this).data('is_dynamic')
							});
						}
					}
				
					index++;
				});

				data = {
					menu: _menu,
					save: true,
					removed: data.removed,
					imported: cm.imported
				};

				$.ajax({
					url: "<?php echo admin_url('admin.php?page=admin-menu'); ?>",
					type: "post",
					data: data,
					success: function (data) {
						location.reload();
					}
				});
			});

			// Event to restore elements
			$('a.item-restore').bind('click',function(e){
				e.preventDefault();
				restoreElement($(this));
			});

			// Event to set default settings
			$('a.default-settings').bind('click', function(e){
				settings_from_default(cm, $(this),e);
			});

			function settings_from_default(cm, elm, e){
				var item = elm.closest('li');
				e.preventDefault();
				$("#dialog").dialog({
	                open: function(event, ui) {
	                    jQuery('#adminmenuwrap').css({'z-index':0});
	                },
	                close: function(event, ui) {
	                    jQuery('#adminmenuwrap').css({'z-index':'auto'});
	                },						
					title: '<?php echo __('Get settings from', 'framework'); ?>...',
					width: 325,
					modal: true,
					resizable: false,
					buttons: {
						'Accept': function(){
							defaultValue = $('select#check-default :selected');
							item.find('#edit-menu-item-title-21').val(defaultValue.data('0').replace('--- ', ''));
							item.find('#edit-menu-item-permissions-21').val(defaultValue.data('1'));
							var url = defaultValue.data('2');
							if(url.search(/[\?]/g) == -1) {
								url += "?custom=yes";
							} else {
								url += "&custom=yes";
							}
							item.find('#edit-menu-item-url-21').val(url);
							item.find(".item-title").text(defaultValue.data('0').replace('--- ', ''));
							$(this).dialog("close");
						},
						'Cancel': function(){
							$(this).dialog('close');
						}
					}
				});

				for(var key in cm['original']){
					if(cm['original'][key][4] != 'wp-menu-separator'){
						// append first-level menu items
						$('#check-default').append(
								$('#dialog-content-tmpl')
										.clone()
										.tmpl({item: cm['original'][key]})
						);

						// append second-level menu items
						for(var k in cm['original'][key]['subitems']){
							if(cm['original'][key]['subitems'][k]) {

								cm['original'][key]['subitems'][k][0] = '--- ' + cm['original'][key]['subitems'][k][0];

								$('#check-default').append(
									$('#dialog-content-tmpl')
											.clone()
											.tmpl({item: cm['original'][key]['subitems'][k]})
								);

							}
						}
					}
				}

				$(".ui-widget-overlay").bind('click',function(){
					$("#dialog").dialog("close");
				});
			}
		});
	})(jQuery);

</script>
