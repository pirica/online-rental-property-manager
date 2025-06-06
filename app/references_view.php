<?php
// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/references.php');
	include_once(__DIR__ . '/references_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('references');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'references';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`references`.`id`" => "id",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "tenant",
		"`references`.`reference_name`" => "reference_name",
		"CONCAT_WS('-', LEFT(`references`.`phone`,3), MID(`references`.`phone`,4,3), RIGHT(`references`.`phone`,4))" => "phone",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`references`.`id`',
		2 => 2,
		3 => 3,
		4 => 4,
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`references`.`id`" => "id",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "tenant",
		"`references`.`reference_name`" => "reference_name",
		"CONCAT_WS('-', LEFT(`references`.`phone`,3), MID(`references`.`phone`,4,3), RIGHT(`references`.`phone`,4))" => "phone",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`references`.`id`" => "ID",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "Tenant",
		"`references`.`reference_name`" => "Reference name",
		"`references`.`phone`" => "Reference phone",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`references`.`id`" => "id",
		"IF(    CHAR_LENGTH(`applicants_and_tenants1`.`first_name`) || CHAR_LENGTH(`applicants_and_tenants1`.`last_name`), CONCAT_WS('',   `applicants_and_tenants1`.`first_name`, ' ', `applicants_and_tenants1`.`last_name`), '') /* Tenant */" => "tenant",
		"`references`.`reference_name`" => "reference_name",
		"CONCAT_WS('-', LEFT(`references`.`phone`,3), MID(`references`.`phone`,4,3), RIGHT(`references`.`phone`,4))" => "phone",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = ['tenant' => 'Tenant', ];

	$x->QueryFrom = "`references` LEFT JOIN `applicants_and_tenants` as applicants_and_tenants1 ON `applicants_and_tenants1`.`id`=`references`.`tenant` ";
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
	$x->ScriptFileName = 'references_view.php';
	$x->RedirectAfterInsert = 'references_view.php?SelectedID=#ID#';
	$x->TableTitle = 'References';
	$x->TableIcon = 'resources/table_icons/application_from_storage.png';
	$x->PrimaryKey = '`references`.`id`';

	$x->ColWidth = [160, 160, ];
	$x->ColCaption = ['Reference name', 'Reference phone', ];
	$x->ColFieldName = ['reference_name', 'phone', ];
	$x->ColNumber  = [3, 4, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/references_templateTV.html';
	$x->SelectedTemplate = 'templates/references_templateTVS.html';
	$x->TemplateDV = 'templates/references_templateDV.html';
	$x->TemplateDVP = 'templates/references_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: references_init
	$render = true;
	if(function_exists('references_init')) {
		$args = [];
		$render = references_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: references_header
	$headerCode = '';
	if(function_exists('references_header')) {
		$args = [];
		$headerCode = references_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php');
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: references_footer
	$footerCode = '';
	if(function_exists('references_footer')) {
		$args = [];
		$footerCode = references_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php');
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
