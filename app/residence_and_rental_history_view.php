<?php
// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/residence_and_rental_history.php');
	include_once(__DIR__ . '/residence_and_rental_history_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('residence_and_rental_history');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'residence_and_rental_history';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`residence_and_rental_history`.`id`" => "id",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "tenant",
		"`residence_and_rental_history`.`address`" => "address",
		"`residence_and_rental_history`.`landlord_or_manager_name`" => "landlord_or_manager_name",
		"`residence_and_rental_history`.`landlord_or_manager_phone`" => "landlord_or_manager_phone",
		"CONCAT('$', FORMAT(`residence_and_rental_history`.`monthly_rent`, 2))" => "monthly_rent",
		"if(`residence_and_rental_history`.`duration_of_residency_from`,date_format(`residence_and_rental_history`.`duration_of_residency_from`,'%m/%d/%Y'),'')" => "duration_of_residency_from",
		"if(`residence_and_rental_history`.`to`,date_format(`residence_and_rental_history`.`to`,'%m/%d/%Y'),'')" => "to",
		"`residence_and_rental_history`.`reason_for_leaving`" => "reason_for_leaving",
		"`residence_and_rental_history`.`notes`" => "notes",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`residence_and_rental_history`.`id`',
		2 => 2,
		3 => 3,
		4 => 4,
		5 => 5,
		6 => '`residence_and_rental_history`.`monthly_rent`',
		7 => '`residence_and_rental_history`.`duration_of_residency_from`',
		8 => '`residence_and_rental_history`.`to`',
		9 => 9,
		10 => 10,
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`residence_and_rental_history`.`id`" => "id",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "tenant",
		"`residence_and_rental_history`.`address`" => "address",
		"`residence_and_rental_history`.`landlord_or_manager_name`" => "landlord_or_manager_name",
		"`residence_and_rental_history`.`landlord_or_manager_phone`" => "landlord_or_manager_phone",
		"CONCAT('$', FORMAT(`residence_and_rental_history`.`monthly_rent`, 2))" => "monthly_rent",
		"if(`residence_and_rental_history`.`duration_of_residency_from`,date_format(`residence_and_rental_history`.`duration_of_residency_from`,'%m/%d/%Y'),'')" => "duration_of_residency_from",
		"if(`residence_and_rental_history`.`to`,date_format(`residence_and_rental_history`.`to`,'%m/%d/%Y'),'')" => "to",
		"`residence_and_rental_history`.`reason_for_leaving`" => "reason_for_leaving",
		"`residence_and_rental_history`.`notes`" => "notes",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`residence_and_rental_history`.`id`" => "ID",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "Tenant",
		"`residence_and_rental_history`.`address`" => "Address",
		"`residence_and_rental_history`.`landlord_or_manager_name`" => "Landlord/manager name",
		"`residence_and_rental_history`.`landlord_or_manager_phone`" => "Landlord/manager phone",
		"`residence_and_rental_history`.`monthly_rent`" => "Monthly rent",
		"`residence_and_rental_history`.`duration_of_residency_from`" => "Duration of residency from",
		"`residence_and_rental_history`.`to`" => "to",
		"`residence_and_rental_history`.`reason_for_leaving`" => "Reason for leaving",
		"`residence_and_rental_history`.`notes`" => "Notes",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`residence_and_rental_history`.`id`" => "id",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "tenant",
		"`residence_and_rental_history`.`address`" => "address",
		"`residence_and_rental_history`.`landlord_or_manager_name`" => "landlord_or_manager_name",
		"`residence_and_rental_history`.`landlord_or_manager_phone`" => "landlord_or_manager_phone",
		"CONCAT('$', FORMAT(`residence_and_rental_history`.`monthly_rent`, 2))" => "monthly_rent",
		"if(`residence_and_rental_history`.`duration_of_residency_from`,date_format(`residence_and_rental_history`.`duration_of_residency_from`,'%m/%d/%Y'),'')" => "duration_of_residency_from",
		"if(`residence_and_rental_history`.`to`,date_format(`residence_and_rental_history`.`to`,'%m/%d/%Y'),'')" => "to",
		"`residence_and_rental_history`.`reason_for_leaving`" => "reason_for_leaving",
		"`residence_and_rental_history`.`notes`" => "notes",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = ['tenant' => 'Tenant', ];

	$x->QueryFrom = "`residence_and_rental_history` LEFT JOIN `applicants_and_tenants` as applicants_and_tenants1 ON `applicants_and_tenants1`.`id`=`residence_and_rental_history`.`tenant` ";
	$x->QueryWhere = '';
	$x->QueryOrder = '';

	$x->AllowSelection = 1;
	$x->HideTableView = ($perm['view'] == 0 ? 1 : 0);
	$x->AllowDelete = $perm['delete'];
	$x->AllowMassDelete = true;
	$x->AllowInsert = $perm['insert'];
	$x->AllowUpdate = $perm['edit'];
	$x->SeparateDV = 1;
	$x->AllowDeleteOfParents = 0;
	$x->AllowFilters = 1;
	$x->AllowSavingFilters = 1;
	$x->AllowSorting = 1;
	$x->AllowNavigation = 1;
	$x->AllowPrinting = 1;
	$x->AllowPrintingDV = 1;
	$x->AllowCSV = 1;
	$x->AllowAdminShowSQL = showSQL();
	$x->RecordsPerPage = 10;
	$x->QuickSearch = 1;
	$x->QuickSearchText = $Translation['quick search'];
	$x->ScriptFileName = 'residence_and_rental_history_view.php';
	$x->RedirectAfterInsert = 'residence_and_rental_history_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Residence and rental history';
	$x->TableIcon = 'resources/table_icons/document_comment_above.png';
	$x->PrimaryKey = '`residence_and_rental_history`.`id`';

	$x->ColWidth = [180, 120, 100, 80, 100, 80, 120, ];
	$x->ColCaption = ['Address', 'Landlord/manager name', 'Landlord/manager phone', 'Monthly rent', 'Duration of residency from', 'to', 'Reason for leaving', ];
	$x->ColFieldName = ['address', 'landlord_or_manager_name', 'landlord_or_manager_phone', 'monthly_rent', 'duration_of_residency_from', 'to', 'reason_for_leaving', ];
	$x->ColNumber  = [3, 4, 5, 6, 7, 8, 9, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/residence_and_rental_history_templateTV.html';
	$x->SelectedTemplate = 'templates/residence_and_rental_history_templateTVS.html';
	$x->TemplateDV = 'templates/residence_and_rental_history_templateDV.html';
	$x->TemplateDVP = 'templates/residence_and_rental_history_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: residence_and_rental_history_init
	$render = true;
	if(function_exists('residence_and_rental_history_init')) {
		$args = [];
		$render = residence_and_rental_history_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: residence_and_rental_history_header
	$headerCode = '';
	if(function_exists('residence_and_rental_history_header')) {
		$args = [];
		$headerCode = residence_and_rental_history_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php');
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: residence_and_rental_history_footer
	$footerCode = '';
	if(function_exists('residence_and_rental_history_footer')) {
		$args = [];
		$footerCode = residence_and_rental_history_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php');
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
