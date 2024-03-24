<?php

// Data functions (insert, update, delete, form) for table unit_photos

// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

function unit_photos_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('unit_photos');
	if(!$arrPerm['insert']) return false;

	$data = [
		'unit' => Request::lookup('unit', ''),
		'photo' => Request::fileUpload('photo', [
			'maxSize' => 2048000,
			'types' => 'jpg|jpeg|gif|png|webp',
			'noRename' => false,
			'dir' => '',
			'success' => function($name, $selected_id) {
				createThumbnail($name, getThumbnailSpecs('unit_photos', 'photo', 'tv'));
				createThumbnail($name, getThumbnailSpecs('unit_photos', 'photo', 'dv'));
			},
			'failure' => function($selected_id, $fileRemoved) {
				if(!strlen(Request::val('SelectedID'))) return '';

				/* for empty upload fields, when saving a copy of an existing record, copy the original upload field */
				return existing_value('unit_photos', 'photo', Request::val('SelectedID'));
			},
		]),
		'description' => Request::val('description', ''),
	];


	// hook: unit_photos_before_insert
	if(function_exists('unit_photos_before_insert')) {
		$args = [];
		if(!unit_photos_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('unit_photos', backtick_keys_once($data), $error);
	if($error) {
		$error_message = $error;
		return false;
	}

	$recID = db_insert_id(db_link());

	update_calc_fields('unit_photos', $recID, calculated_fields()['unit_photos']);

	// hook: unit_photos_after_insert
	if(function_exists('unit_photos_after_insert')) {
		$res = sql("SELECT * FROM `unit_photos` WHERE `id`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args = [];
		if(!unit_photos_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	// record owner is current user
	$recordOwner = getLoggedMemberID();
	set_record_owner('unit_photos', $recID, $recordOwner);

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) unit_photos_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function unit_photos_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function unit_photos_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('unit_photos', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: unit_photos_before_delete
	if(function_exists('unit_photos_before_delete')) {
		$args = [];
		if(!unit_photos_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	sql("DELETE FROM `unit_photos` WHERE `id`='{$selected_id}'", $eo);

	// hook: unit_photos_after_delete
	if(function_exists('unit_photos_after_delete')) {
		$args = [];
		unit_photos_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='unit_photos' AND `pkValue`='{$selected_id}'", $eo);
}

function unit_photos_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('unit_photos', $selected_id, 'edit')) return false;

	$data = [
		'unit' => Request::lookup('unit', ''),
		'photo' => Request::fileUpload('photo', [
			'maxSize' => 2048000,
			'types' => 'jpg|jpeg|gif|png|webp',
			'noRename' => false,
			'dir' => '',
			'id' => $selected_id,
			'success' => function($name, $selected_id) {
				createThumbnail($name, getThumbnailSpecs('unit_photos', 'photo', 'tv'));
				createThumbnail($name, getThumbnailSpecs('unit_photos', 'photo', 'dv'));
			},
			'removeOnRequest' => true,
			'remove' => function($selected_id) {
				// do nothing: preserve removed files on server.
			},
			'failure' => function($selected_id, $fileRemoved) {
				if($fileRemoved) return '';
				return existing_value('unit_photos', 'photo', $selected_id);
			},
		]),
		'description' => Request::val('description', ''),
	];

	// get existing values
	$old_data = getRecord('unit_photos', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: unit_photos_before_update
	if(function_exists('unit_photos_before_update')) {
		$args = ['old_data' => $old_data];
		if(!unit_photos_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'unit_photos', 
		backtick_keys_once($set), 
		['`id`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="unit_photos_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('unit_photos', $data['selectedID'], calculated_fields()['unit_photos']);

	// hook: unit_photos_after_update
	if(function_exists('unit_photos_after_update')) {
		$res = sql("SELECT * FROM `unit_photos` WHERE `id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['id'];
		$args = ['old_data' => $old_data];
		if(!unit_photos_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update record update timestamp
	set_record_owner('unit_photos', $selected_id);
}

function unit_photos_form($selected_id = '', $AllowUpdate = 1, $AllowInsert = 1, $AllowDelete = 1, $separateDV = 0, $TemplateDV = '', $TemplateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selected_id. If $selected_id
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = null;
	$row = $urow = $jsReadOnly = $jsEditable = $lookups = null;

	$noSaveAsCopy = false;

	// mm: get table permissions
	$arrPerm = getTablePermissions('unit_photos');
	if(!$arrPerm['insert'] && $selected_id == '')
		// no insert permission and no record selected
		// so show access denied error unless TVDV
		return $separateDV ? $Translation['tableAccessDenied'] : '';
	$AllowInsert = ($arrPerm['insert'] ? true : false);
	// print preview?
	$dvprint = false;
	if(strlen($selected_id) && Request::val('dvprint_x') != '') {
		$dvprint = true;
	}

	$filterer_unit = Request::val('filterer_unit');

	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: unit
	$combo_unit = new DataCombo;

	if($selected_id) {
		if(!check_record_permission('unit_photos', $selected_id, 'view'))
			return $Translation['tableAccessDenied'];

		// can edit?
		$AllowUpdate = check_record_permission('unit_photos', $selected_id, 'edit');

		// can delete?
		$AllowDelete = check_record_permission('unit_photos', $selected_id, 'delete');

		$res = sql("SELECT * FROM `unit_photos` WHERE `id`='" . makeSafe($selected_id) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'unit_photos_view.php', false);
		}
		$combo_unit->SelectedData = $row['unit'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
		$combo_unit->SelectedData = $filterer_unit;
	}
	$combo_unit->HTML = '<span id="unit-container' . $rnd1 . '"></span><input type="hidden" name="unit" id="unit' . $rnd1 . '" value="' . html_attr($combo_unit->SelectedData) . '">';
	$combo_unit->MatchText = '<span id="unit-container-readonly' . $rnd1 . '"></span><input type="hidden" name="unit" id="unit' . $rnd1 . '" value="' . html_attr($combo_unit->SelectedData) . '">';

	ob_start();
	?>

	<script>
		// initial lookup values
		AppGini.current_unit__RAND__ = { text: "", value: "<?php echo addslashes($selected_id ? $urow['unit'] : htmlspecialchars($filterer_unit, ENT_QUOTES)); ?>"};

		jQuery(function() {
			setTimeout(function() {
				if(typeof(unit_reload__RAND__) == 'function') unit_reload__RAND__();
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
		function unit_reload__RAND__() {
		<?php if(($AllowUpdate || $AllowInsert) && !$dvprint) { ?>

			$j("#unit-container__RAND__").select2({
				/* initial default value */
				initSelection: function(e, c) {
					$j.ajax({
						url: 'ajax_combo.php',
						dataType: 'json',
						data: { id: AppGini.current_unit__RAND__.value, t: 'unit_photos', f: 'unit' },
						success: function(resp) {
							c({
								id: resp.results[0].id,
								text: resp.results[0].text
							});
							$j('[name="unit"]').val(resp.results[0].id);
							$j('[id=unit-container-readonly__RAND__]').html('<span class="match-text" id="unit-match-text">' + resp.results[0].text + '</span>');
							if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=units_view_parent]').hide(); } else { $j('.btn[id=units_view_parent]').show(); }


							if(typeof(unit_update_autofills__RAND__) == 'function') unit_update_autofills__RAND__();
						}
					});
				},
				width: '100%',
				formatNoMatches: function(term) { return '<?php echo addslashes($Translation['No matches found!']); ?>'; },
				minimumResultsForSearch: 5,
				loadMorePadding: 200,
				ajax: {
					url: 'ajax_combo.php',
					dataType: 'json',
					cache: true,
					data: function(term, page) { return { s: term, p: page, t: 'unit_photos', f: 'unit' }; },
					results: function(resp, page) { return resp; }
				},
				escapeMarkup: function(str) { return str; }
			}).on('change', function(e) {
				AppGini.current_unit__RAND__.value = e.added.id;
				AppGini.current_unit__RAND__.text = e.added.text;
				$j('[name="unit"]').val(e.added.id);
				if(e.added.id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=units_view_parent]').hide(); } else { $j('.btn[id=units_view_parent]').show(); }


				if(typeof(unit_update_autofills__RAND__) == 'function') unit_update_autofills__RAND__();
			});

			if(!$j("#unit-container__RAND__").length) {
				$j.ajax({
					url: 'ajax_combo.php',
					dataType: 'json',
					data: { id: AppGini.current_unit__RAND__.value, t: 'unit_photos', f: 'unit' },
					success: function(resp) {
						$j('[name="unit"]').val(resp.results[0].id);
						$j('[id=unit-container-readonly__RAND__]').html('<span class="match-text" id="unit-match-text">' + resp.results[0].text + '</span>');
						if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=units_view_parent]').hide(); } else { $j('.btn[id=units_view_parent]').show(); }

						if(typeof(unit_update_autofills__RAND__) == 'function') unit_update_autofills__RAND__();
					}
				});
			}

		<?php } else { ?>

			$j.ajax({
				url: 'ajax_combo.php',
				dataType: 'json',
				data: { id: AppGini.current_unit__RAND__.value, t: 'unit_photos', f: 'unit' },
				success: function(resp) {
					$j('[id=unit-container__RAND__], [id=unit-container-readonly__RAND__]').html('<span class="match-text" id="unit-match-text">' + resp.results[0].text + '</span>');
					if(resp.results[0].id == '<?php echo empty_lookup_value; ?>') { $j('.btn[id=units_view_parent]').hide(); } else { $j('.btn[id=units_view_parent]').show(); }

					if(typeof(unit_update_autofills__RAND__) == 'function') unit_update_autofills__RAND__();
				}
			});
		<?php } ?>

		}
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$TemplateDVP}") ? "./{$TemplateDVP}" : './templates/unit_photos_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$TemplateDV}") ? "./{$TemplateDV}" : './templates/unit_photos_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Unit photo details', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($AllowInsert) {
		if(!$selected_id) $templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return unit_photos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return unit_photos_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($selected_id) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($AllowUpdate)
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return unit_photos_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

		if($AllowDelete)
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '<button type="submit" class="btn btn-danger" id="delete" name="delete_x" value="1" title="' . html_attr($Translation['Delete']) . '"><i class="glyphicon glyphicon-trash"></i> ' . $Translation['Delete'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="deselect" name="deselect_x" value="1" onclick="' . $backAction . '" title="' . html_attr($Translation['Back']) . '"><i class="glyphicon glyphicon-chevron-left"></i> ' . $Translation['Back'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);
		$templateCode = str_replace('<%%DELETE_BUTTON%%>', '', $templateCode);

		// if not in embedded mode and user has insert only but no view/update/delete,
		// remove 'back' button
		if(
			$arrPerm['insert']
			&& !$arrPerm['update'] && !$arrPerm['delete'] && !$arrPerm['view']
			&& !Request::val('Embedded')
		)
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
		elseif($separateDV)
			$templateCode = str_replace(
				'<%%DESELECT_BUTTON%%>', 
				'<button
					type="submit" 
					class="btn btn-default" 
					id="deselect" 
					name="deselect_x" 
					value="1" 
					onclick="' . $backAction . '" 
					title="' . html_attr($Translation['Back']) . '">
						<i class="glyphicon glyphicon-chevron-left"></i> ' .
						$Translation['Back'] .
				'</button>',
				$templateCode
			);
		else
			$templateCode = str_replace('<%%DESELECT_BUTTON%%>', '', $templateCode);
	}

	// set records to read only if user can't insert new records and can't edit current record
	if(($selected_id && !$AllowUpdate && !$AllowInsert) || (!$selected_id && !$AllowInsert)) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#unit').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#unit_caption').prop('disabled', true).css({ color: '#555', backgroundColor: 'white' });\n";
		$jsReadOnly .= "\tjQuery('#photo').replaceWith('<div class=\"form-control-static\" id=\"photo\">' + (jQuery('#photo').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} elseif($AllowInsert) {
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);"; // temporarily disable form change handler
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace('<%%COMBO(unit)%%>', $combo_unit->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(unit)%%>', $combo_unit->MatchText, $templateCode);
	$templateCode = str_replace('<%%URLCOMBOTEXT(unit)%%>', urlencode($combo_unit->MatchText), $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = ['unit' => ['units', 'Unit'], ];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if($pt_perm['view'] || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(id)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(unit)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(photo)%%>', ($noUploads ? '' : "<div>{$Translation['upload image']}</div>" . '<input type="file" name="photo" id="photo" data-filetypes="jpg|jpeg|gif|png|webp" data-maxsize="2048000" style="max-width: calc(100% - 1.5rem);" accept="capture=camera,image/*">' . '<i class="text-danger clear-upload hidden pull-right" style="margin-top: -.1em; font-size: large;">&times;</i>'), $templateCode);
	if($AllowUpdate && $row['photo'] != '') {
		$templateCode = str_replace('<%%REMOVEFILE(photo)%%>', '<input type="checkbox" name="photo_remove" id="photo_remove" value="1"> <label for="photo_remove" style="color: red; font-weight: bold;">'.$Translation['remove image'].'</label>', $templateCode);
	} else {
		$templateCode = str_replace('<%%REMOVEFILE(photo)%%>', '', $templateCode);
	}
	$templateCode = str_replace('<%%UPLOADFILE(description)%%>', '', $templateCode);

	// process values
	if($selected_id) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', safe_html($urow['id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', html_attr($row['id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode($urow['id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(unit)%%>', safe_html($urow['unit']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(unit)%%>', html_attr($row['unit']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(unit)%%>', urlencode($urow['unit']), $templateCode);
		$row['photo'] = ($row['photo'] != '' ? $row['photo'] : 'blank.gif');
		if( $dvprint) $templateCode = str_replace('<%%VALUE(photo)%%>', safe_html($urow['photo']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(photo)%%>', html_attr($row['photo']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(photo)%%>', urlencode($urow['photo']), $templateCode);
		if($AllowUpdate || $AllowInsert) {
			$templateCode = str_replace('<%%HTMLAREA(description)%%>', '<textarea name="description" id="description" rows="5">' . safe_html(htmlspecialchars_decode($row['description'])) . '</textarea>', $templateCode);
		} else {
			$templateCode = str_replace('<%%HTMLAREA(description)%%>', '<div id="description" class="form-control-static">' . $row['description'] . '</div>', $templateCode);
		}
		$templateCode = str_replace('<%%VALUE(description)%%>', nl2br($row['description']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(description)%%>', urlencode($urow['description']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(unit)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(unit)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(photo)%%>', 'blank.gif', $templateCode);
		$templateCode = str_replace('<%%HTMLAREA(description)%%>', '<textarea name="description" id="description" rows="5"></textarea>', $templateCode);
	}

	// process translations
	$templateCode = parseTemplate($templateCode);

	// clear scrap
	$templateCode = str_replace('<%%', '<!-- ', $templateCode);
	$templateCode = str_replace('%%>', ' -->', $templateCode);

	// hide links to inaccessible tables
	if(Request::val('dvprint_x') == '') {
		$templateCode .= "\n\n<script>\$j(function() {\n";
		$arrTables = getTableList();
		foreach($arrTables as $name => $caption) {
			$templateCode .= "\t\$j('#{$name}_link').removeClass('hidden');\n";
			$templateCode .= "\t\$j('#xs_{$name}_link').removeClass('hidden');\n";
		}

		$templateCode .= $jsReadOnly;
		$templateCode .= $jsEditable;

		if(!$selected_id) {
		}

		$templateCode.="\n});</script>\n";
	}

	// ajaxed auto-fill fields
	$templateCode .= '<script>';
	$templateCode .= '$j(function() {';


	$templateCode.="});";
	$templateCode.="</script>";
	$templateCode .= $lookups;

	// handle enforced parent values for read-only lookup fields
	$filterField = Request::val('FilterField');
	$filterOperator = Request::val('FilterOperator');
	$filterValue = Request::val('FilterValue');

	// don't include blank images in lightbox gallery
	$templateCode = preg_replace('/blank.gif" data-lightbox=".*?"/', 'blank.gif"', $templateCode);

	// don't display empty email links
	$templateCode=preg_replace('/<a .*?href="mailto:".*?<\/a>/', '', $templateCode);

	/* default field values */
	$rdata = $jdata = get_defaults('unit_photos');
	if($selected_id) {
		$jdata = get_joined_record('unit_photos', $selected_id);
		if($jdata === false) $jdata = get_defaults('unit_photos');
		$rdata = $row;
	}
	$templateCode .= loadView('unit_photos-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: unit_photos_dv
	if(function_exists('unit_photos_dv')) {
		$args = [];
		unit_photos_dv(($selected_id ? $selected_id : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}