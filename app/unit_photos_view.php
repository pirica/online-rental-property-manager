<?php
// This script and data application was generated by AppGini, https://bigprof.com/appgini
// Download AppGini for free from https://bigprof.com/appgini/download/

	include_once(__DIR__ . '/lib.php');
	@include_once(__DIR__ . '/hooks/unit_photos.php');
	include_once(__DIR__ . '/unit_photos_dml.php');

	// mm: can the current member access this page?
	$perm = getTablePermissions('unit_photos');
	if(!$perm['access']) {
		echo error_message($Translation['tableAccessDenied']);
		exit;
	}

	$x = new DataList;
	$x->TableName = 'unit_photos';

	// Fields that can be displayed in the table view
	$x->QueryFieldsTV = [
		"`unit_photos`.`id`" => "id",
		"IF(    CHAR_LENGTH(`properties1`.`property_name`) || CHAR_LENGTH(`units1`.`unit_number`), CONCAT_WS('',   `properties1`.`property_name`, ' - unit# ', `units1`.`unit_number`), '') /* Unit */" => "unit",
		"`unit_photos`.`photo`" => "photo",
		"`unit_photos`.`description`" => "description",
	];
	// mapping incoming sort by requests to actual query fields
	$x->SortFields = [
		1 => '`unit_photos`.`id`',
		2 => 2,
		3 => 3,
		4 => 4,
	];

	// Fields that can be displayed in the csv file
	$x->QueryFieldsCSV = [
		"`unit_photos`.`id`" => "id",
		"IF(    CHAR_LENGTH(`properties1`.`property_name`) || CHAR_LENGTH(`units1`.`unit_number`), CONCAT_WS('',   `properties1`.`property_name`, ' - unit# ', `units1`.`unit_number`), '') /* Unit */" => "unit",
		"`unit_photos`.`photo`" => "photo",
		"`unit_photos`.`description`" => "description",
	];
	// Fields that can be filtered
	$x->QueryFieldsFilters = [
		"`unit_photos`.`id`" => "ID",
		"IF(    CHAR_LENGTH(`properties1`.`property_name`) || CHAR_LENGTH(`units1`.`unit_number`), CONCAT_WS('',   `properties1`.`property_name`, ' - unit# ', `units1`.`unit_number`), '') /* Unit */" => "Unit",
		"`unit_photos`.`description`" => "Description",
	];

	// Fields that can be quick searched
	$x->QueryFieldsQS = [
		"`unit_photos`.`id`" => "id",
		"IF(    CHAR_LENGTH(`properties1`.`property_name`) || CHAR_LENGTH(`units1`.`unit_number`), CONCAT_WS('',   `properties1`.`property_name`, ' - unit# ', `units1`.`unit_number`), '') /* Unit */" => "unit",
		"`unit_photos`.`description`" => "description",
	];

	// Lookup fields that can be used as filterers
	$x->filterers = ['unit' => 'Unit', ];

	$x->QueryFrom = "`unit_photos` LEFT JOIN `units` as units1 ON `units1`.`id`=`unit_photos`.`unit` LEFT JOIN `properties` as properties1 ON `properties1`.`id`=`units1`.`property` ";
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
	$x->ScriptFileName = 'unit_photos_view.php';
	$x->RedirectAfterInsert = 'unit_photos_view.php?SelectedID=#ID#';
	$x->TableTitle = 'Unit photos';
	$x->TableIcon = 'resources/table_icons/camera_link.png';
	$x->PrimaryKey = '`unit_photos`.`id`';

	$x->ColWidth = [150, 150, ];
	$x->ColCaption = ['Photo', 'Description', ];
	$x->ColFieldName = ['photo', 'description', ];
	$x->ColNumber  = [3, 4, ];

	// template paths below are based on the app main directory
	$x->Template = 'templates/unit_photos_templateTV.html';
	$x->SelectedTemplate = 'templates/unit_photos_templateTVS.html';
	$x->TemplateDV = 'templates/unit_photos_templateDV.html';
	$x->TemplateDVP = 'templates/unit_photos_templateDVP.html';

	$x->ShowTableHeader = 1;
	$x->TVClasses = "";
	$x->DVClasses = "";
	$x->HasCalculatedFields = false;
	$x->AllowConsoleLog = false;
	$x->AllowDVNavigation = true;

	// hook: unit_photos_init
	$render = true;
	if(function_exists('unit_photos_init')) {
		$args = [];
		$render = unit_photos_init($x, getMemberInfo(), $args);
	}

	if($render) $x->Render();

	// hook: unit_photos_header
	$headerCode = '';
	if(function_exists('unit_photos_header')) {
		$args = [];
		$headerCode = unit_photos_header($x->ContentType, getMemberInfo(), $args);
	}

	if(!$headerCode) {
		include_once(__DIR__ . '/header.php');
	} else {
		ob_start();
		include_once(__DIR__ . '/header.php');
		echo str_replace('<%%HEADER%%>', ob_get_clean(), $headerCode);
	}

	echo $x->HTML;

	// hook: unit_photos_footer
	$footerCode = '';
	if(function_exists('unit_photos_footer')) {
		$args = [];
		$footerCode = unit_photos_footer($x->ContentType, getMemberInfo(), $args);
	}

	if(!$footerCode) {
		include_once(__DIR__ . '/footer.php');
	} else {
		ob_start();
		include_once(__DIR__ . '/footer.php');
		echo str_replace('<%%FOOTER%%>', ob_get_clean(), $footerCode);
	}
