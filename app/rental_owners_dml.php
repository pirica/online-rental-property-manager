<?php

// Data functions (insert, update, delete, form) for table rental_owners

// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

function rental_owners_insert(&$error_message = '') {
	global $Translation;

	// mm: can member insert record?
	$arrPerm = getTablePermissions('rental_owners');
	if(!$arrPerm['insert']) return false;

	$data = [
		'first_name' => Request::val('first_name', ''),
		'last_name' => Request::val('last_name', ''),
		'company_name' => Request::val('company_name', ''),
		'date_of_birth' => Request::dateComponents('date_of_birth', ''),
		'primary_email' => Request::val('primary_email', ''),
		'alternate_email' => Request::val('alternate_email', ''),
		'phone' => Request::val('phone', ''),
		'country' => Request::val('country', ''),
		'street' => Request::val('street', ''),
		'city' => Request::val('city', ''),
		'state' => Request::val('state', ''),
		'zip' => Request::val('zip', ''),
		'comments' => Request::val('comments', ''),
	];


	// hook: rental_owners_before_insert
	if(function_exists('rental_owners_before_insert')) {
		$args = [];
		if(!rental_owners_before_insert($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$error = '';
	// set empty fields to NULL
	$data = array_map(function($v) { return ($v === '' ? NULL : $v); }, $data);
	insert('rental_owners', backtick_keys_once($data), $error);
	if($error) {
		$error_message = $error;
		return false;
	}

	$recID = db_insert_id(db_link());

	update_calc_fields('rental_owners', $recID, calculated_fields()['rental_owners']);

	// hook: rental_owners_after_insert
	if(function_exists('rental_owners_after_insert')) {
		$res = sql("SELECT * FROM `rental_owners` WHERE `id`='" . makeSafe($recID, false) . "' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) {
			$data = array_map('makeSafe', $row);
		}
		$data['selectedID'] = makeSafe($recID, false);
		$args = [];
		if(!rental_owners_after_insert($data, getMemberInfo(), $args)) { return $recID; }
	}

	// mm: save ownership data
	// record owner is current user
	$recordOwner = getLoggedMemberID();
	set_record_owner('rental_owners', $recID, $recordOwner);

	// if this record is a copy of another record, copy children if applicable
	if(strlen(Request::val('SelectedID'))) rental_owners_copy_children($recID, Request::val('SelectedID'));

	return $recID;
}

function rental_owners_copy_children($destination_id, $source_id) {
	global $Translation;
	$requests = []; // array of curl handlers for launching insert requests
	$eo = ['silentErrors' => true];
	$safe_sid = makeSafe($source_id);

	// launch requests, asynchronously
	curl_batch($requests);
}

function rental_owners_delete($selected_id, $AllowDeleteOfParents = false, $skipChecks = false) {
	// insure referential integrity ...
	global $Translation;
	$selected_id = makeSafe($selected_id);

	// mm: can member delete record?
	if(!check_record_permission('rental_owners', $selected_id, 'delete')) {
		return $Translation['You don\'t have enough permissions to delete this record'];
	}

	// hook: rental_owners_before_delete
	if(function_exists('rental_owners_before_delete')) {
		$args = [];
		if(!rental_owners_before_delete($selected_id, $skipChecks, getMemberInfo(), $args))
			return $Translation['Couldn\'t delete this record'] . (
				!empty($args['error_message']) ?
					'<div class="text-bold">' . strip_tags($args['error_message']) . '</div>'
					: '' 
			);
	}

	// child table: properties
	$res = sql("SELECT `id` FROM `rental_owners` WHERE `id`='{$selected_id}'", $eo);
	$id = db_fetch_row($res);
	$rires = sql("SELECT COUNT(1) FROM `properties` WHERE `owner`='" . makeSafe($id[0]) . "'", $eo);
	$rirow = db_fetch_row($rires);
	if($rirow[0] && !$AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation["couldn't delete"];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'properties', $RetMsg);
		return $RetMsg;
	} elseif($rirow[0] && $AllowDeleteOfParents && !$skipChecks) {
		$RetMsg = $Translation['confirm delete'];
		$RetMsg = str_replace('<RelatedRecords>', $rirow[0], $RetMsg);
		$RetMsg = str_replace('<TableName>', 'properties', $RetMsg);
		$RetMsg = str_replace('<Delete>', '<input type="button" class="btn btn-danger" value="' . html_attr($Translation['yes']) . '" onClick="window.location = \'rental_owners_view.php?SelectedID=' . urlencode($selected_id) . '&delete_x=1&confirmed=1&csrf_token=' . urlencode(csrf_token(false, true)) . '\';">', $RetMsg);
		$RetMsg = str_replace('<Cancel>', '<input type="button" class="btn btn-success" value="' . html_attr($Translation[ 'no']) . '" onClick="window.location = \'rental_owners_view.php?SelectedID=' . urlencode($selected_id) . '\';">', $RetMsg);
		return $RetMsg;
	}

	sql("DELETE FROM `rental_owners` WHERE `id`='{$selected_id}'", $eo);

	// hook: rental_owners_after_delete
	if(function_exists('rental_owners_after_delete')) {
		$args = [];
		rental_owners_after_delete($selected_id, getMemberInfo(), $args);
	}

	// mm: delete ownership data
	sql("DELETE FROM `membership_userrecords` WHERE `tableName`='rental_owners' AND `pkValue`='{$selected_id}'", $eo);
}

function rental_owners_update(&$selected_id, &$error_message = '') {
	global $Translation;

	// mm: can member edit record?
	if(!check_record_permission('rental_owners', $selected_id, 'edit')) return false;

	$data = [
		'first_name' => Request::val('first_name', ''),
		'last_name' => Request::val('last_name', ''),
		'company_name' => Request::val('company_name', ''),
		'date_of_birth' => Request::dateComponents('date_of_birth', ''),
		'primary_email' => Request::val('primary_email', ''),
		'alternate_email' => Request::val('alternate_email', ''),
		'phone' => Request::val('phone', ''),
		'country' => Request::val('country', ''),
		'street' => Request::val('street', ''),
		'city' => Request::val('city', ''),
		'state' => Request::val('state', ''),
		'zip' => Request::val('zip', ''),
		'comments' => Request::val('comments', ''),
	];

	// get existing values
	$old_data = getRecord('rental_owners', $selected_id);
	if(is_array($old_data)) {
		$old_data = array_map('makeSafe', $old_data);
		$old_data['selectedID'] = makeSafe($selected_id);
	}

	$data['selectedID'] = makeSafe($selected_id);

	// hook: rental_owners_before_update
	if(function_exists('rental_owners_before_update')) {
		$args = ['old_data' => $old_data];
		if(!rental_owners_before_update($data, getMemberInfo(), $args)) {
			if(isset($args['error_message'])) $error_message = $args['error_message'];
			return false;
		}
	}

	$set = $data; unset($set['selectedID']);
	foreach ($set as $field => $value) {
		$set[$field] = ($value !== '' && $value !== NULL) ? $value : NULL;
	}

	if(!update(
		'rental_owners', 
		backtick_keys_once($set), 
		['`id`' => $selected_id], 
		$error_message
	)) {
		echo $error_message;
		echo '<a href="rental_owners_view.php?SelectedID=' . urlencode($selected_id) . "\">{$Translation['< back']}</a>";
		exit;
	}


	$eo = ['silentErrors' => true];

	update_calc_fields('rental_owners', $data['selectedID'], calculated_fields()['rental_owners']);

	// hook: rental_owners_after_update
	if(function_exists('rental_owners_after_update')) {
		$res = sql("SELECT * FROM `rental_owners` WHERE `id`='{$data['selectedID']}' LIMIT 1", $eo);
		if($row = db_fetch_assoc($res)) $data = array_map('makeSafe', $row);

		$data['selectedID'] = $data['id'];
		$args = ['old_data' => $old_data];
		if(!rental_owners_after_update($data, getMemberInfo(), $args)) return;
	}

	// mm: update record update timestamp
	set_record_owner('rental_owners', $selected_id);
}

function rental_owners_form($selectedId = '', $allowUpdate = true, $allowInsert = true, $allowDelete = true, $separateDV = true, $templateDV = '', $templateDVP = '') {
	// function to return an editable form for a table records
	// and fill it with data of record whose ID is $selectedId. If $selectedId
	// is empty, an empty form is shown, with only an 'Add New'
	// button displayed.

	global $Translation;
	$eo = ['silentErrors' => true];
	$noUploads = $row = $urow = $jsReadOnly = $jsEditable = $lookups = null;
	$noSaveAsCopy = false;
	$hasSelectedId = strlen($selectedId) > 0;

	// mm: get table permissions
	$arrPerm = getTablePermissions('rental_owners');
	$allowInsert = ($arrPerm['insert'] ? true : false);
	$allowUpdate = $hasSelectedId && check_record_permission('rental_owners', $selectedId, 'edit');
	$allowDelete = $hasSelectedId && check_record_permission('rental_owners', $selectedId, 'delete');

	if(!$allowInsert && !$hasSelectedId)
		// no insert permission and no record selected
		// so show access denied error -- except if TVDV: just hide DV
		return $separateDV ? $Translation['tableAccessDenied'] : '';

	if($hasSelectedId && !check_record_permission('rental_owners', $selectedId, 'view'))
		return $Translation['tableAccessDenied'];

	// print preview?
	$dvprint = $hasSelectedId && Request::val('dvprint_x') != '';

	$showSaveNew = !$dvprint && ($allowInsert && !$hasSelectedId);
	$showSaveChanges = !$dvprint && $allowUpdate && $hasSelectedId;
	$showDelete = !$dvprint && $allowDelete && $hasSelectedId;
	$showSaveAsCopy = !$dvprint && ($allowInsert && $hasSelectedId && !$noSaveAsCopy);
	$fieldsAreEditable = !$dvprint && (($allowInsert && !$hasSelectedId) || ($allowUpdate && $hasSelectedId) || $showSaveAsCopy);


	// populate filterers, starting from children to grand-parents

	// unique random identifier
	$rnd1 = ($dvprint ? rand(1000000, 9999999) : '');
	// combobox: date_of_birth
	$combo_date_of_birth = new DateCombo;
	$combo_date_of_birth->DateFormat = "mdy";
	$combo_date_of_birth->MinYear = defined('rental_owners.date_of_birth.MinYear') ? constant('rental_owners.date_of_birth.MinYear') : 1900;
	$combo_date_of_birth->MaxYear = defined('rental_owners.date_of_birth.MaxYear') ? constant('rental_owners.date_of_birth.MaxYear') : 2100;
	$combo_date_of_birth->DefaultDate = parseMySQLDate('', '');
	$combo_date_of_birth->MonthNames = $Translation['month names'];
	$combo_date_of_birth->NamePrefix = 'date_of_birth';
	// combobox: country
	$combo_country = new Combo;
	$combo_country->ListType = 0;
	$combo_country->MultipleSeparator = ', ';
	$combo_country->ListBoxHeight = 10;
	$combo_country->RadiosPerLine = 1;
	if(is_file(__DIR__ . '/hooks/rental_owners.country.csv')) {
		$country_data = addslashes(implode('', @file(__DIR__ . '/hooks/rental_owners.country.csv')));
		$combo_country->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions($country_data))));
		$combo_country->ListData = $combo_country->ListItem;
	} else {
		$combo_country->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions("Afghanistan;;Albania;;Algeria;;American Samoa;;Andorra;;Angola;;Anguilla;;Antarctica;;Antigua, Barbuda;;Argentina;;Armenia;;Aruba;;Australia;;Austria;;Azerbaijan;;Bahamas;;Bahrain;;Bangladesh;;Barbados;;Belarus;;Belgium;;Belize;;Benin;;Bermuda;;Bhutan;;Bolivia;;Bosnia, Herzegovina;;Botswana;;Bouvet Is.;;Brazil;;Brunei Darussalam;;Bulgaria;;Burkina Faso;;Burundi;;Cambodia;;Cameroon;;Canada;;Canary Is.;;Cape Verde;;Cayman Is.;;Central African Rep.;;Chad;;Channel Islands;;Chile;;China;;Christmas Is.;;Cocos Is.;;Colombia;;Comoros;;Congo, D.R. Of;;Congo;;Cook Is.;;Costa Rica;;Croatia;;Cuba;;Cyprus;;Czech Republic;;Denmark;;Djibouti;;Dominica;;Dominican Republic;;Ecuador;;Egypt;;El Salvador;;Equatorial Guinea;;Eritrea;;Estonia;;Ethiopia;;Falkland Is.;;Faroe Is.;;Fiji;;Finland;;France;;French Guiana;;French Polynesia;;French Territories;;Gabon;;Gambia;;Georgia;;Germany;;Ghana;;Gibraltar;;Greece;;Greenland;;Grenada;;Guadeloupe;;Guam;;Guatemala;;Guernsey;;Guinea-bissau;;Guinea;;Guyana;;Haiti;;Heard, Mcdonald Is.;;Honduras;;Hong Kong;;Hungary;;Iceland;;India;;Indonesia;;Iran;;Iraq;;Ireland;;Israel;;Italy;;Ivory Coast;;Jamaica;;Japan;;Jersey;;Jordan;;Kazakhstan;;Kenya;;Kiribati;;Korea, D.P.R Of;;Korea, Rep. Of;;Kuwait;;Kyrgyzstan;;Lao Peoples D.R.;;Latvia;;Lebanon;;Lesotho;;Liberia;;Libyan Arab Jamahiriya;;Liechtenstein;;Lithuania;;Luxembourg;;Macao;;Macedonia, F.Y.R Of;;Madagascar;;Malawi;;Malaysia;;Maldives;;Mali;;Malta;;Mariana Islands;;Marshall Islands;;Martinique;;Mauritania;;Mauritius;;Mayotte;;Mexico;;Micronesia;;Moldova;;Monaco;;Mongolia;;Montserrat;;Morocco;;Mozambique;;Myanmar;;Namibia;;Nauru;;Nepal;;Netherlands Antilles;;Netherlands;;New Caledonia;;New Zealand;;Nicaragua;;Niger;;Nigeria;;Niue;;Norfolk Island;;Norway;;Oman;;Pakistan;;Palau;;Palestinian Terr.;;Panama;;Papua New Guinea;;Paraguay;;Peru;;Philippines;;Pitcairn;;Poland;;Portugal;;Puerto Rico;;Qatar;;Reunion;;Romania;;Russian Federation;;Rwanda;;Samoa;;San Marino;;Sao Tome, Principe;;Saudi Arabia;;Senegal;;Seychelles;;Sierra Leone;;Singapore;;Slovakia;;Slovenia;;Solomon Is.;;Somalia;;South Africa;;South Georgia;;South Sandwich Is.;;Spain;;Sri Lanka;;St. Helena;;St. Kitts, Nevis;;St. Lucia;;St. Pierre, Miquelon;;St. Vincent, Grenadines;;Sudan;;Suriname;;Svalbard, Jan Mayen;;Swaziland;;Sweden;;Switzerland;;Syrian Arab Republic;;Taiwan;;Tajikistan;;Tanzania;;Thailand;;Timor-leste;;Togo;;Tokelau;;Tonga;;Trinidad, Tobago;;Tunisia;;Turkey;;Turkmenistan;;Turks, Caicoss;;Tuvalu;;Uganda;;Ukraine;;United Arab Emirates;;United Kingdom;;United States;;Uruguay;;Uzbekistan;;Vanuatu;;Vatican City;;Venezuela;;Viet Nam;;Virgin Is. British;;Virgin Is. U.S.;;Wallis, Futuna;;Western Sahara;;Yemen;;Yugoslavia;;Zambia;;Zimbabwe"))));
		$combo_country->ListData = $combo_country->ListItem;
	}
	$combo_country->SelectName = 'country';
	// combobox: state
	$combo_state = new Combo;
	$combo_state->ListType = 0;
	$combo_state->MultipleSeparator = ', ';
	$combo_state->ListBoxHeight = 10;
	$combo_state->RadiosPerLine = 1;
	if(is_file(__DIR__ . '/hooks/rental_owners.state.csv')) {
		$state_data = addslashes(implode('', @file(__DIR__ . '/hooks/rental_owners.state.csv')));
		$combo_state->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions($state_data))));
		$combo_state->ListData = $combo_state->ListItem;
	} else {
		$combo_state->ListItem = array_trim(explode('||', entitiesToUTF8(convertLegacyOptions("AL;;AK;;AS;;AZ;;AR;;CA;;CO;;CT;;DE;;DC;;FM;;FL;;GA;;GU;;HI;;ID;;IL;;IN;;IA;;KS;;KY;;LA;;ME;;MH;;MD;;MA;;MI;;MN;;MS;;MO;;MT;;NE;;NV;;NH;;NJ;;NM;;NY;;NC;;ND;;MP;;OH;;OK;;OR;;PW;;PA;;PR;;RI;;SC;;SD;;TN;;TX;;UT;;VT;;VI;;VA;;WA;;WV;;WI;;WY"))));
		$combo_state->ListData = $combo_state->ListItem;
	}
	$combo_state->SelectName = 'state';

	if($hasSelectedId) {
		$res = sql("SELECT * FROM `rental_owners` WHERE `id`='" . makeSafe($selectedId) . "'", $eo);
		if(!($row = db_fetch_array($res))) {
			return error_message($Translation['No records found'], 'rental_owners_view.php', false);
		}
		$combo_date_of_birth->DefaultDate = $row['date_of_birth'];
		$combo_country->SelectedData = $row['country'];
		$combo_state->SelectedData = $row['state'];
		$urow = $row; /* unsanitized data */
		$row = array_map('safe_html', $row);
	} else {
		$filterField = Request::val('FilterField');
		$filterOperator = Request::val('FilterOperator');
		$filterValue = Request::val('FilterValue');
		$combo_country->SelectedText = (isset($filterField[1]) && $filterField[1] == '9' && $filterOperator[1] == '<=>' ? $filterValue[1] : entitiesToUTF8(''));
		$combo_state->SelectedText = (isset($filterField[1]) && $filterField[1] == '12' && $filterOperator[1] == '<=>' ? $filterValue[1] : entitiesToUTF8(''));
	}
	$combo_country->Render();
	$combo_state->Render();

	ob_start();
	?>

	<script>
		// initial lookup values

		jQuery(function() {
			setTimeout(function() {
			}, 50); /* we need to slightly delay client-side execution of the above code to allow AppGini.ajaxCache to work */
		});
	</script>
	<?php

	$lookups = str_replace('__RAND__', $rnd1, ob_get_clean());


	// code for template based detail view forms

	// open the detail view template
	if($dvprint) {
		$template_file = is_file("./{$templateDVP}") ? "./{$templateDVP}" : './templates/rental_owners_templateDVP.html';
		$templateCode = @file_get_contents($template_file);
	} else {
		$template_file = is_file("./{$templateDV}") ? "./{$templateDV}" : './templates/rental_owners_templateDV.html';
		$templateCode = @file_get_contents($template_file);
	}

	// process form title
	$templateCode = str_replace('<%%DETAIL_VIEW_TITLE%%>', 'Rental owner details', $templateCode);
	$templateCode = str_replace('<%%RND1%%>', $rnd1, $templateCode);
	$templateCode = str_replace('<%%EMBEDDED%%>', (Request::val('Embedded') ? 'Embedded=1' : ''), $templateCode);
	// process buttons
	if($showSaveNew) {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-success" id="insert" name="insert_x" value="1" onclick="return rental_owners_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save New'] . '</button>', $templateCode);
	} elseif($showSaveAsCopy) {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="insert" name="insert_x" value="1" onclick="return rental_owners_validateData();"><i class="glyphicon glyphicon-plus-sign"></i> ' . $Translation['Save As Copy'] . '</button>', $templateCode);
	} else {
		$templateCode = str_replace('<%%INSERT_BUTTON%%>', '', $templateCode);
	}

	// 'Back' button action
	if(Request::val('Embedded')) {
		$backAction = 'AppGini.closeParentModal(); return false;';
	} else {
		$backAction = '$j(\'form\').eq(0).attr(\'novalidate\', \'novalidate\'); document.myform.reset(); return true;';
	}

	if($hasSelectedId) {
		if(!Request::val('Embedded')) $templateCode = str_replace('<%%DVPRINT_BUTTON%%>', '<button type="submit" class="btn btn-default" id="dvprint" name="dvprint_x" value="1" onclick="$j(\'form\').eq(0).prop(\'novalidate\', true); document.myform.reset(); return true;" title="' . html_attr($Translation['Print Preview']) . '"><i class="glyphicon glyphicon-print"></i> ' . $Translation['Print Preview'] . '</button>', $templateCode);
		if($allowUpdate)
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '<button type="submit" class="btn btn-success btn-lg" id="update" name="update_x" value="1" onclick="return rental_owners_validateData();" title="' . html_attr($Translation['Save Changes']) . '"><i class="glyphicon glyphicon-ok"></i> ' . $Translation['Save Changes'] . '</button>', $templateCode);
		else
			$templateCode = str_replace('<%%UPDATE_BUTTON%%>', '', $templateCode);

		if($allowDelete)
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
			$allowInsert
			&& !$allowUpdate && !$allowDelete && !$arrPerm['view']
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
	if(!$fieldsAreEditable) {
		$jsReadOnly = '';
		$jsReadOnly .= "\tjQuery('#first_name').replaceWith('<div class=\"form-control-static\" id=\"first_name\">' + (jQuery('#first_name').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#last_name').replaceWith('<div class=\"form-control-static\" id=\"last_name\">' + (jQuery('#last_name').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#company_name').replaceWith('<div class=\"form-control-static\" id=\"company_name\">' + (jQuery('#company_name').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#date_of_birth').prop('readonly', true);\n";
		$jsReadOnly .= "\tjQuery('#date_of_birthDay, #date_of_birthMonth, #date_of_birthYear').prop('disabled', true).css({ color: '#555', backgroundColor: '#fff' });\n";
		$jsReadOnly .= "\tjQuery('#primary_email').replaceWith('<div class=\"form-control-static\" id=\"primary_email\">' + (jQuery('#primary_email').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#primary_email, #primary_email-edit-link').hide();\n";
		$jsReadOnly .= "\tjQuery('#alternate_email').replaceWith('<div class=\"form-control-static\" id=\"alternate_email\">' + (jQuery('#alternate_email').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#alternate_email, #alternate_email-edit-link').hide();\n";
		$jsReadOnly .= "\tjQuery('#phone').replaceWith('<div class=\"form-control-static\" id=\"phone\">' + (jQuery('#phone').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#country').replaceWith('<div class=\"form-control-static\" id=\"country\">' + (jQuery('#country').val() || '') + '</div>'); jQuery('#country-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#street').replaceWith('<div class=\"form-control-static\" id=\"street\">' + (jQuery('#street').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#city').replaceWith('<div class=\"form-control-static\" id=\"city\">' + (jQuery('#city').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('#state').replaceWith('<div class=\"form-control-static\" id=\"state\">' + (jQuery('#state').val() || '') + '</div>'); jQuery('#state-multi-selection-help').hide();\n";
		$jsReadOnly .= "\tjQuery('#zip').replaceWith('<div class=\"form-control-static\" id=\"zip\">' + (jQuery('#zip').val() || '') + '</div>');\n";
		$jsReadOnly .= "\tjQuery('.select2-container').hide();\n";

		$noUploads = true;
	} else {
		// temporarily disable form change handler till time and datetime pickers are enabled
		$jsEditable = "\tjQuery('form').eq(0).data('already_changed', true);";
		$jsEditable .= "\tjQuery('form').eq(0).data('already_changed', false);"; // re-enable form change handler
	}

	// process combos
	$templateCode = str_replace(
		'<%%COMBO(date_of_birth)%%>', 
		(!$fieldsAreEditable ? 
			'<div class="form-control-static">' . $combo_date_of_birth->GetHTML(true) . '</div>' : 
			$combo_date_of_birth->GetHTML()
		), $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(date_of_birth)%%>', $combo_date_of_birth->GetHTML(true), $templateCode);
	$templateCode = str_replace('<%%COMBO(country)%%>', $combo_country->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(country)%%>', $combo_country->SelectedData, $templateCode);
	$templateCode = str_replace('<%%COMBO(state)%%>', $combo_state->HTML, $templateCode);
	$templateCode = str_replace('<%%COMBOTEXT(state)%%>', $combo_state->SelectedData, $templateCode);

	/* lookup fields array: 'lookup field name' => ['parent table name', 'lookup field caption'] */
	$lookup_fields = [];
	foreach($lookup_fields as $luf => $ptfc) {
		$pt_perm = getTablePermissions($ptfc[0]);

		// process foreign key links
		if(($pt_perm['view'] && isDetailViewEnabled($ptfc[0])) || $pt_perm['edit']) {
			$templateCode = str_replace("<%%PLINK({$luf})%%>", '<button type="button" class="btn btn-default view_parent" id="' . $ptfc[0] . '_view_parent" title="' . html_attr($Translation['View'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-eye-open"></i></button>', $templateCode);
		}

		// if user has insert permission to parent table of a lookup field, put an add new button
		if($pt_perm['insert'] /* && !Request::val('Embedded')*/) {
			$templateCode = str_replace("<%%ADDNEW({$ptfc[0]})%%>", '<button type="button" class="btn btn-default add_new_parent" id="' . $ptfc[0] . '_add_new" title="' . html_attr($Translation['Add New'] . ' ' . $ptfc[1]) . '"><i class="glyphicon glyphicon-plus text-success"></i></button>', $templateCode);
		}
	}

	// process images
	$templateCode = str_replace('<%%UPLOADFILE(id)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(first_name)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(last_name)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(company_name)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(date_of_birth)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(primary_email)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(alternate_email)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(phone)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(country)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(street)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(city)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(state)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(zip)%%>', '', $templateCode);
	$templateCode = str_replace('<%%UPLOADFILE(comments)%%>', '', $templateCode);

	// process values
	if($hasSelectedId) {
		if( $dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', safe_html($urow['id']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(id)%%>', html_attr($row['id']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode($urow['id']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(first_name)%%>', safe_html($urow['first_name']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(first_name)%%>', html_attr($row['first_name']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(first_name)%%>', urlencode($urow['first_name']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(last_name)%%>', safe_html($urow['last_name']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(last_name)%%>', html_attr($row['last_name']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(last_name)%%>', urlencode($urow['last_name']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(company_name)%%>', safe_html($urow['company_name']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(company_name)%%>', html_attr($row['company_name']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(company_name)%%>', urlencode($urow['company_name']), $templateCode);
		$templateCode = str_replace('<%%VALUE(date_of_birth)%%>', app_datetime($row['date_of_birth']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(date_of_birth)%%>', urlencode(app_datetime($urow['date_of_birth'])), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(primary_email)%%>', safe_html($urow['primary_email']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(primary_email)%%>', html_attr($row['primary_email']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(primary_email)%%>', urlencode($urow['primary_email']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(alternate_email)%%>', safe_html($urow['alternate_email']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(alternate_email)%%>', html_attr($row['alternate_email']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(alternate_email)%%>', urlencode($urow['alternate_email']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(phone)%%>', safe_html($urow['phone']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(phone)%%>', html_attr($row['phone']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(phone)%%>', urlencode($urow['phone']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(country)%%>', safe_html($urow['country']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(country)%%>', html_attr($row['country']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(country)%%>', urlencode($urow['country']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(street)%%>', safe_html($urow['street']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(street)%%>', html_attr($row['street']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(street)%%>', urlencode($urow['street']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(city)%%>', safe_html($urow['city']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(city)%%>', html_attr($row['city']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(city)%%>', urlencode($urow['city']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(state)%%>', safe_html($urow['state']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(state)%%>', html_attr($row['state']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(state)%%>', urlencode($urow['state']), $templateCode);
		if( $dvprint) $templateCode = str_replace('<%%VALUE(zip)%%>', safe_html($urow['zip']), $templateCode);
		if(!$dvprint) $templateCode = str_replace('<%%VALUE(zip)%%>', html_attr($row['zip']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(zip)%%>', urlencode($urow['zip']), $templateCode);
		if($fieldsAreEditable) {
			$templateCode = str_replace('<%%HTMLAREA(comments)%%>', '<textarea name="comments" id="comments" rows="5">' . safe_html(htmlspecialchars_decode($row['comments'])) . '</textarea>', $templateCode);
		} else {
			$templateCode = str_replace('<%%HTMLAREA(comments)%%>', '<div id="comments" class="form-control-static">' . $row['comments'] . '</div>', $templateCode);
		}
		$templateCode = str_replace('<%%VALUE(comments)%%>', nl2br($row['comments']), $templateCode);
		$templateCode = str_replace('<%%URLVALUE(comments)%%>', urlencode($urow['comments']), $templateCode);
	} else {
		$templateCode = str_replace('<%%VALUE(id)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(id)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(first_name)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(first_name)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(last_name)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(last_name)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(company_name)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(company_name)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(date_of_birth)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(date_of_birth)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(primary_email)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(primary_email)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(alternate_email)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(alternate_email)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(phone)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(phone)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(country)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(country)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(street)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(street)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(city)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(city)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(state)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(state)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%VALUE(zip)%%>', '', $templateCode);
		$templateCode = str_replace('<%%URLVALUE(zip)%%>', urlencode(''), $templateCode);
		$templateCode = str_replace('<%%HTMLAREA(comments)%%>', '<textarea name="comments" id="comments" rows="5"></textarea>', $templateCode);
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

		if(!$hasSelectedId) {
			$templateCode.="\n\tif(document.getElementById('primary_emailEdit')) { document.getElementById('primary_emailEdit').style.display='inline'; }";
			$templateCode.="\n\tif(document.getElementById('primary_emailEditLink')) { document.getElementById('primary_emailEditLink').style.display='none'; }";
			$templateCode.="\n\tif(document.getElementById('alternate_emailEdit')) { document.getElementById('alternate_emailEdit').style.display='inline'; }";
			$templateCode.="\n\tif(document.getElementById('alternate_emailEditLink')) { document.getElementById('alternate_emailEditLink').style.display='none'; }";
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
	$rdata = $jdata = get_defaults('rental_owners');
	if($hasSelectedId) {
		$jdata = get_joined_record('rental_owners', $selectedId);
		if($jdata === false) $jdata = get_defaults('rental_owners');
		$rdata = $row;
	}
	$templateCode .= loadView('rental_owners-ajax-cache', ['rdata' => $rdata, 'jdata' => $jdata]);

	// hook: rental_owners_dv
	if(function_exists('rental_owners_dv')) {
		$args = [];
		rental_owners_dv(($hasSelectedId ? $selectedId : FALSE), getMemberInfo(), $templateCode, $args);
	}

	return $templateCode;
}