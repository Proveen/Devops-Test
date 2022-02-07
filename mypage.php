<?php 
if(isset($_POST['results_per_page_set'])){
	$Page_Per = $_POST['PerPage'];
	setcookie("Page", $Page_Per, mktime(0,0,0, date("d"),date("m"), (date("Y")+1)),'/');
	header("Location:mypage.php");
	exit();
}else {
	
}
?>
<?php require_once __DIR__ . '/db_con.php';
$sql = "SELECT functions.functionid,functionname FROM `user_access` , functions  where   user_access.functionid =  functions.functionid and  user_access.userid= " . $_SESSION['userid'];
$result = mysqli_query($conn, $sql);
$function_array = array();
$function_array2 = array();
if (isset($result->num_rows) && $result->num_rows > 0) {
	while ($row = mysqli_fetch_array($result)) {
		$function_array[] = "`" . $row['functionname'] . "`";
		$function_array2[] = $row['functionname'];
	}
}

$functionall = implode(',', $function_array);

if (isset($_POST['export']) && $_POST['export'] != "") {
	ob_start();
	header('Content-Type: application/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=search_result.csv');
	$output = fopen('php://output', 'w');

	global $conn;

	$sql = "SELECT functions.functionid,functionname FROM `user_access` , functions  where   user_access.functionid =  functions.functionid and  user_access.userid= " . $_SESSION['userid'];

	$result = mysqli_query($conn, $sql);
	$function_array = array();
	$function_array2 = array();
	if (isset($result->num_rows) && $result->num_rows > 0) {
		while ($row = mysqli_fetch_array($result)) {
			$function_array[] = "`" . $row['functionname'] . "`";
			$function_array2[] = $row['functionname'];
		}
	}

	$functionall = implode(',', $function_array);





	$sql = "SELECT * FROM `tablelist` ORDER BY iid DESC";
	$result = mysqli_query($conn, $sql);
	$tablenameAry = [];
	if (isset($result->num_rows) && $result->num_rows > 0) {
		while ($row = mysqli_fetch_array($result)) {

			$tablenameAry[] = $row;
		}
	}
	$where = '';

	$tableHtm = '';
	$pagenationui = '';
	// for selecting ---
	$tableName = '';

	if (isset($_GET['tblName'])) {
		$tableName = $_GET['tblName'];
	} else if (isset($_POST['tableName'])) {
		$tableName = $_POST['tableName'];
	} else {
		if (count($tablenameAry) > 0) {
			$tableName = $tablenameAry[0]['table_name'];
		}
	}
	$sql_getcolumn = "SELECT *
						FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_NAME='" . $tableName . "'";
	$colResult = $conn->query($sql_getcolumn);

	$columnAry = [];
	$selcolumnAry = [];
	if (isset($colResult->num_rows) && $colResult->num_rows > 0) {

		while ($columnrow = mysqli_fetch_assoc($colResult)) {
			if (in_array($columnrow['COLUMN_NAME'], $function_array2)) {
				$columnAry[] = $columnrow['COLUMN_NAME'];
				$selcolumnAry[] = "`" . $columnrow['COLUMN_NAME'] . "`";
			}
		}
		// $tableHtm .= '<th width="40px"><a href="javascript:;"><span class="glyphicon glyphicon-edit"></span></a></th>';
	}

	$functionall = implode(',', $selcolumnAry);

	if (isset($_GET['pageno'])) {
		$pageno = $_GET['pageno'];
	} else {
		$pageno = 1;
	}
	$no_of_records_per_page = 1;

	$sql = "SELECT a.* FROM `user_export_access` a, `tablelist` b  where a.table_name = b.iid and  a.userid= " . $_SESSION['userid'] . " and b.table_name ='" . $tableName . "'";
	$result = mysqli_query($conn, $sql);


	if (isset($result->num_rows) && $result->num_rows > 0) {

		while ($row = mysqli_fetch_array($result)) {

			$no_of_records_per_page = $row["export_limit"];
		}
	}





	$offset = ($pageno - 1) * $no_of_records_per_page;

	if (isset($_GET['search']) && !empty($_GET['search'])) {
		if (count($columnAry) > 0) {
			foreach ($columnAry as $key => $columnrow) {
				if ($key == 0) {
					$where .= " WHERE `" . $columnrow . "` LIKE '%" . $_GET['search'] . "%' ";
				} else {
					$where .= " OR `" . $columnrow . "` LIKE '%" . $_GET['search'] . "%' ";
				}
			}
		}
	}


	if (isset($_GET['citysearch']) && !empty($_GET['citysearch'])) {

		$where = $where == "" ? " where `City` like  '%" . $_GET['citysearch'] . "%'" : $where .= " or `City` like  '%" . $_GET['citysearch'] . "%'";
	}



	if (isset($_GET['gpo']) && !empty($_GET['gpo'])) {

		$where = $where == "" ? " where `GPO` like  '%" . $_GET['gpo'] . "%'" : $where .= " or `GPO` like  '%" . $_GET['gpo'] . "%'";
	}
	if (isset($_GET['facility']) && !empty($_GET['facility'])) {

		$where = $where == "" ? " where `Medicare Hospital Type` like  '%" . $_GET['facility'] . "%'" : $where .= " or `Medicare Hospital Type` like  '%" . $_GET['facility'] . "%'";
	}

	if (isset($_GET['idn']) && !empty($_GET['idn'])) {

		$where = $where == "" ? " where `IDN` like  '%" . $_GET['idn'] . "%'" : $where .= " or `IDN` like  '%" . $_GET['idn'] . "%'";
	}


	if (isset($_GET['statesearch']) && !empty($_GET['statesearch'])) {

		$where = $where == "" ? " where `State` like  '%" . $_GET['statesearch'] . "%'" : $where .= " or `State` like  '%" . $_GET['statesearch'] . "%'";
	}

	if (isset($_GET['hospitalsearch']) && !empty($_GET['hospitalsearch'])) {

		$where = $where == "" ? " where `Hospital Name` like '%" . $_GET['hospitalsearch'] . "%'" : $where .= " or `Hospital Name` like '%" . $_GET['hospitalsearch'] . "%'";
	}




	$where = $where == "" ? "where `Merge ID` is null " : $where .= " and `Merge ID` is null ";
	$sql =  "SELECT * FROM `" . $tableName . "`" . $where . " LIMIT " . $offset . ", " . $no_of_records_per_page . "";
	$res_data = mysqli_query($conn, $sql);
	
	$search_result = array();

	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		while ($row = mysqli_fetch_assoc($res_data)) {
			$search_result[] = $row;
		}
	}


	fputcsv($output, $columnAry);

	if (count($search_result) > 0) {
		foreach ($search_result as $row) {
			fputcsv($output, $row);
		}
	}


	//move back to beginning of file
	//fseek(	$output, 0);

	//set headers to download file rather than displayed


	//output all remaining data on a file pointer
	//fpassthru(	$output);
	exit;
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>CLIENT PANEL</title>

	<!-- <script type="text/javascript" src="assets/js/jquery-3.3.1.js" defer></script> -->
	<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.24/datatables.min.css" />

	<script type="text/javascript" src="https://cdn.datatables.net/v/dt/jq-3.3.1/dt-1.10.24/datatables.min.js"></script>

	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.9.2/css/bulma.min.css" />
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
	<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
	<link rel="stylesheet" type="text/css" href="assets/css/style-new.css?v=1">

	<style>
		* {
			box-sizing: border-box;
			font-size: 1rem;
		}

		/*the container must be positioned relative:*/
		.autocomplete {
			position: relative;
			display: inline-block;
		}

		.autocomplete-items ,.autocomplete-itemss {
			position: absolute;
			border: 1px solid #d4d4d4;
			border-bottom: none;
			border-top: none;
			z-index: 99;
			/*position the autocomplete items to be the same width as the container:*/
			top: 100%;
			left: 0;
			right: 0;
		}

		.autocomplete-items div ,.autocomplete-itemss div {
			padding: 10px;
			cursor: pointer;
			background-color: #fff;
			border-bottom: 1px solid #d4d4d4;
		}

		/*when hovering an item:*/
		.autocomplete-items div:hover , .autocomplete-itemss div:hover {
			background-color: #e9e9e9;
		}

		/*when navigating through the items using the arrow keys:*/
		.autocomplete-active {
			background-color: DodgerBlue !important;
			color: #ffffff;
		}

		.w3-red {
			background-color: var(--green) !important;
		}

		.search_result_tab {
			margin-top: 30px;
		}

		.w3-bar .w3-bar-item {
			padding: 8px 10px;
			float: left;
			width: auto;
			border: none;
			display: block;
			outline: 0;
			color: white;
		}
		svg.icon.icon-tabler.icon-tabler-building-hospital {
    vertical-align: text-bottom;
    stroke: #4caf50;
    width: 34px;
}
	</style>



</head>

<?php
ini_set('display_errors', 1);

require_once __DIR__ . '/library/simplexlsx.class.php';
require_once __DIR__ . '/library/SimpleCSV.php';
require_once __DIR__ . '/library/SimpleXLS.php';
if (check_login() == false) {

	print '<script type="text/javascript">window.top.location.href = "login.php";</script>';
}

global $conn;

init();

if (isset($_POST['action']) && $_POST['action'] == "inserted") {
	insert_action();
}









function init()
{
	// tablelist();
}

$sql = "SELECT tl.* FROM `tablelist`  tl 
	     left join user_export_access ue  on ue.table_name = tl.iid  and   ue.userid=" . $_SESSION['userid'] . "  ORDER BY iid DESC     ";
$result = mysqli_query($conn, $sql);
$tablenameAry = [];
if (isset($result->num_rows) && $result->num_rows > 0) {
	while ($row = mysqli_fetch_array($result)) {
		$tablenameAry[] = $row;
	}
}

// for selecting ---
$tableName = '';

if (isset($_GET['tblName'])) {
	$tableName = $_GET['tblName'];
} else if (isset($_POST['tablename'])) {
	$tableName = $_POST['tablename'];
} else {
	if (count($tablenameAry) > 0) {
		$tableName = $tablenameAry[0]['table_name'];
	}
}





?>

<body>
	<div class="loading_cover row" style="display: none;">
		<div class="loading"></div>
	</div>
	<?php
	require_once __DIR__ . '/header-v1.php';
	?>
	




	<div class="row table_section search">


		<?php

		$tableHtm = '';
		$pagenationui = '';

		$sql_getcolumn = "SELECT *
						FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_NAME='" . $tableName . "'";
		$colResult = $conn->query($sql_getcolumn);

		if (isset($_GET['pageno'])) {
			$pageno = $_GET['pageno'];
		} else {
			$pageno = 1;
		}
		
			$no_of_records_per_page = 15;
		
		$offset = ($pageno - 1) * $no_of_records_per_page;

		$total_pages_sql = "SELECT COUNT(*) FROM `" . $tableName . "`";
		$result = mysqli_query($conn, $total_pages_sql);
		if (isset($result->num_rows) && $result->num_rows > 0) {
			$total_rows = mysqli_fetch_array($result)[0];
			$total_pages = ceil($total_rows / $no_of_records_per_page);
		}

		// thead start-----------------------------



		$columnAry = [];
		$selcolumnAry = [];
		if (isset($colResult->num_rows) && $colResult->num_rows > 0) {

			while ($columnrow = mysqli_fetch_assoc($colResult)) {
				if (in_array($columnrow['COLUMN_NAME'], $function_array2)) {
					$columnAry[] = $columnrow['COLUMN_NAME'];
					$selcolumnAry[] = "`" . $columnrow['COLUMN_NAME'] . "`";
				}
			}
			// $tableHtm .= '<th width="40px"><a href="javascript:;"><span class="glyphicon glyphicon-edit"></span></a></th>';
		}


		// thead end-----------------------------


		$functionall = implode(',', $selcolumnAry);


		if (isset($_GET['pageno'])) {
			$pageno = $_GET['pageno'];
		} else {
			$pageno = 1;
		}
		$no_of_records_per_page = 1;
		$offset = ($pageno - 1) * $no_of_records_per_page;






		// thead end-----------------------------

		// print_r($columnAry);
		$where = '';
		if (isset($_GET['search']) && !empty($_GET['search'])) {
			if (count($columnAry) > 0) {
				foreach ($columnAry as $key => $columnrow) {
					if ($key == 0) {
						$where .= " WHERE `" . $columnrow . "` LIKE '%" . $_GET['search'] . "%' ";
					} else {
						$where .= " or  `" . $columnrow . "` LIKE '%" . $_GET['search'] . "%' ";
					}
				}
			}
		}


		if (isset($_GET['citysearch']) && !empty($_GET['citysearch'])) {

			$where = $where == "" ? " where `City` like  '%" . $_GET['citysearch'] . "%'" : $where .= " and `City` like  '%" . $_GET['citysearch'] . "%'";
		}
		if (isset($_GET['gpo']) && !empty($_GET['gpo'])) {

			$where = $where == "" ? " where `GPO Affiliations` like  '%" . $_GET['gpo'] . "%'" : $where .= " and `GPO Affiliations` like  '%" . $_GET['gpo'] . "%'";
		}
		if (isset($_GET['idn']) && !empty($_GET['idn'])) {

			$where = $where == "" ? " where `IDN` like  '%" . $_GET['idn'] . "%'" : $where .= " and `IDN` like  '%" . $_GET['idn'] . "%'";
		}

		if (isset($_GET['facility']) && !empty($_GET['facility'])) {

			$where = $where == "" ? " where `Medicare Hospital Type` like  '%" . $_GET['facility'] . "%'" : $where .= " and `Medicare Hospital Type` like  '%" . $_GET['facility'] . "%'";
		}


		if (isset($_GET['statesearch']) && !empty($_GET['statesearch'])) {

			$where = $where == "" ? " where `State` like  '%" . $_GET['statesearch'] . "%'" : $where .= " and `State` like  '%" . $_GET['statesearch'] . "%'";
		}

		if (isset($_GET['hospitalsearch']) && !empty($_GET['hospitalsearch'])) {

			$where = $where == "" ? " where `Hospital Name` like '%" . $_GET['hospitalsearch'] . "%'" : $where .= " and `Hospital Name` like '%" . $_GET['hospitalsearch'] . "%'";
		}



		$where = $where == "" ? "where `Merge ID` is null " : $where .= " and `Merge ID` is null ";


		$sql = "SELECT * FROM  `" . $tableName . "`" . $where . " LIMIT " . $offset . ", " . $no_of_records_per_page . "";
		$res_data = mysqli_query($conn, $sql);








		$total_pages_sql = "SELECT count(*) total FROM `" . $tableName . "`" . $where;
		$result = mysqli_query($conn, $total_pages_sql);
		if (isset($result->num_rows) && $result->num_rows > 0) {
			$row = mysqli_fetch_assoc($result);
			$total_rows =  $row['total'];
			$total_pages = ceil($total_rows / $no_of_records_per_page);
		}




		$st_nxt_cla = '';
		$st_pre_cla = '';
		$pagenum = '';
		if ($pageno <= 1) {
			$st_pre_cla = 'disabled';
			$st_pre_link = '#';
		} else {
			$st_pre_link = "?pageno=" . ($pageno - 1);
		}
		if ($pageno >= $total_pages) {
			$st_nxt_cla = 'disabled';
			$st_nxt_link = '#';
		} else {
			$st_nxt_link = "?pageno=" . ($pageno + 1);
		}

		$pagenum .= '<li>
				  				<a href="javascript:;" id="pageid_li"><input type="number" class="input" name="pageno" value="' . (isset($_GET['pageno']) ? $_GET['pageno'] : 1) . '" /> </a><li> of ' . $total_pages . '</li>
				  			</li>';

		$table_con = isset($_GET['tblName']) ? '&tblName=' . $_GET['tblName'] : "";

		if (isset($_GET['facility']) || isset($_GET['idn']) ||  isset($_GET['gpo'])  || isset($_GET['search']) ||  isset($_GET['citysearch'])  || isset($_GET['statesearch']) || isset($_GET['hospitalsearch']))
			$table_con = $table_con . '&facility=' . $_GET['facility'] . '&idn=' . $_GET['idn'] . '&gpo=' . $_GET['gpo'] . '&search=' . $_GET['search'] . '&citysearch=' . $_GET['citysearch'] . '&statesearch=' . $_GET['statesearch'] . '&hospitalsearch=' . $_GET['hospitalsearch'];



		$pagenationui = '<ul class="pagination">
					  			        <li><a class="button  bordered" href="?pageno=1' . $table_con . '">
											<svg xmlns="http://www.w3.org/2000/svg" style="stroke:#fff;" class="icon icon-tabler icon-tabler-arrow-loop-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
											  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
											  <path d="M13 21v-13a4 4 0 1 1 4 4h-13" />
											  <path d="M7 15l-3 -3l3 -3" />
											</svg>										
										</a></li>
					  			        <li class="' . $st_pre_cla . $table_con . '">
					  			            <a class="button  bordered" href="' . $st_pre_link . $table_con . '">
												<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round" style="stroke: #fff;">
												  <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
												  <line x1="5" y1="12" x2="19" y2="12"></line>
												  <line x1="5" y1="12" x2="11" y2="18"></line>
												  <line x1="5" y1="12" x2="11" y2="6"></line>
												</svg>
											</a>
					  			        </li>' . $pagenum . '
					  			        <li class="' . $st_nxt_cla . '">
					  			            <a class="button bordered" href="' . $st_nxt_link . $table_con . '">
												<svg xmlns="http://www.w3.org/2000/svg" style="stroke:#fff;" class="icon icon-tabler icon-tabler-arrow-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
												  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
												  <line x1="5" y1="12" x2="19" y2="12" />
												  <line x1="13" y1="18" x2="19" y2="12" />
												  <line x1="13" y1="6" x2="19" y2="12" />
												</svg>
											</a>
					  			        </li>
					  			        <li><a class="button  " href="?pageno=' . $total_pages . $table_con . '">
											<svg xmlns="http://www.w3.org/2000/svg" style="stroke:#fff;" class="icon icon-tabler icon-tabler-arrow-loop-right" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
											  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
											  <path d="M12 21v-13a4 4 0 1 0 -4 4h13" />
											  <path d="M18 15l3 -3l-3 -3" />
											</svg>
										</a></li>
					  			    </ul>';
		?>



		<?php
		$record_id = "";

		if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
			$row = mysqli_fetch_assoc($res_data);


			foreach ($row as $key => $value) {
				if ($value == "") {

					unset($row[$key]);
				}
			}

			$record_id    = $row['iid'];
			$table_name  = $tableName;
			$userid      = $_SESSION['userid'];

			$msg = "";
			$sql = 'SELECT * FROM search_table WHERE record_id=' . $record_id . " and userid=" . $userid . " and table_name= '" . $table_name . "'";
			$result = $conn->query($sql);
			$dataAry = [];
			if (isset($result->num_rows) && $result->num_rows > 0) {
				$msg = "already";
			}


			$kkk = 0;
			$sql  = "select * from categories_table_link where table_name='" . $tableName . "' ";

			$result = mysqli_query($conn, $sql);


		?>



		
			<div class="is-pulled-left mb-4 ml-3" style="position: relative; z-index:999;width: 100%;display: flex;justify-content: center;">

				<form method="get" style="display:inline" id="gettabledata_form">
					<div class="select mt-3">
						<select id="tblNameSel" name="tblName">
							<?php
							foreach ($tablenameAry as $key => $value) {
								echo '<option ' . (($tableName == $value['table_name']) ? "selected='selected'" : "") . ' value="' . $value['table_name'] . '">' . $value['file_name'] . '</option>';
							}
							?>
						</select>
					</div>
				</form>
				<form method="post" id="export_form" style="display: inline;" enctype="multipart/form-data">
					<button type="button" class="button is-link" style="margin-top: 11px;margin-left: 11px;" id="export"> &nbsp; Export <span class="glyphicon glyphicon-open"></span>&nbsp; </button>

					<input type="file" class="input" id="fileinput" name="file" style="display: none" />
					<input type="hidden" name="action" value="inserted">
					<input type="hidden" name="export" value="export">
					<input type="hidden" name="tableName" value="<?php echo $tableName; ?>">
				</form>



			</div>
			<div class="pagenation_section my-4" align="center">

				<form method="get">
					<?php echo $pagenationui; ?>
				</form>
			</div>


			<div class="row">
				<div class="w3-bar" style="display:flex;padding-left:30px;justify-content:center;">
					<?php if (isset($result->num_rows) && $result->num_rows > 0) {
						while ($row2 = mysqli_fetch_assoc($result)) { ?>
							<button class="w3-bar-item w3-button tablink azx" onclick="openSearchData(event,'<?php echo $row2["categories_name"]; ?>')"><?php echo $row2["categories_name"]; ?></button>

					<?php }
					} ?>

				    <button class="w3-bar-item w3-button tablink pwz" onclick="openSearchData(event,'ALLTAB')">ALL</button>
					<button class="w3-bar-item w3-button tablink PwButton" onclick="openSearchData(event,'GridView')">
					<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-grid" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
					  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
					  <rect x="4" y="4" width="6" height="6" rx="1" />
					  <rect x="14" y="4" width="6" height="6" rx="1" />
					  <rect x="4" y="14" width="6" height="6" rx="1" />
					  <rect x="14" y="14" width="6" height="6" rx="1" />
					</svg>GridView</button>


					<?php if ($msg == "already") { ?>
						<button class="button" onMouseOver="this.style.background-color=none" style="border:1px solid green;margin-right:20px;" onclick="Track_Result('<?php echo $row['iid']; ?>','<?php echo $tableName ?>')"><b>Stop Tracking Result </b></button>
					<?php } else { ?>
						<button class="w3-bar-item button is-pulled-right" style="background:transparent;margin-right:20px;" onclick="Track_Result('<?php echo $row['iid']; ?>','<?php echo $tableName ?>')">
						<svg xmlns="http://www.w3.org/2000/svg" style="margin-right:0;" class="icon icon-tabler icon-tabler-track" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
						  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
						  <path d="M4 15l11 -11m5 5l-11 11m-4 -8l7 7m-3.5 -10.5l7 7m-3.5 -10.5l7 7" />
						</svg>-Track Result-</button>
					<?php } ?>


				</div>


			</div>

			<div class="row" style="padding-left:10px;color:#333;width:99%;padding-bottom:100px;" ID="hospital_search">





				<?php
				$sql  = "select * from categories_table_link where table_name='" . $tableName . "' ";

				$result = mysqli_query($conn, $sql);

				if (isset($result->num_rows) && $result->num_rows > 0) {
					while ($row2 = mysqli_fetch_assoc($result)) { ?>
						<div class="container search_result_tab" id="<?php echo $row2["categories_name"]; ?>" style="display:none;">
							<table class="table is-striped is-hoverable">
								<tbody>
									<?php
									$sql  = "select f.functionname from categories_function_table_link a , functions f where  a.function_id = f.functionid and a.cat_table_id	='" . $row2['cat_tab_id'] . "' ";
									$result1 = mysqli_query($conn, $sql);
									if (isset($result1->num_rows) && $result1->num_rows > 0) {
										while ($row3 = mysqli_fetch_assoc($result1)) {

											if (isset($row[$row3['functionname']]) && in_array($row3['functionname'], $function_array2)) { ?>

												<tr>
													<td><b><?php echo $row3['functionname']; ?></b></td>
													<td>

														<?php
														if ($row3['functionname'] != "Google Maps Link")
															echo $row[$row3['functionname']];
														else
															echo "<a href='" . $row[$row3['functionname']] . "' target='_blank'>View Map</a>";

														?></td>
												</tr>
									<?php }
										}
									} ?>


								</tbody>
							</table>

						</div>


				<?php }
				} ?>


				<div class="container search_result_tab" id="ALLTAB" style="display:block;width:100%;">
					<?php
					$sql  = "select * from categories_table_link where table_name='" . $tableName . "' ";
					$google_map_address = "";
					$result = mysqli_query($conn, $sql);
					$old_functions = array();
					
					if (isset($result->num_rows) && $result->num_rows > 0) {
						$i = 0;
					?>
						<div class="columns">
							
							<div class="column is-half leftside">
								<table class="table is-striped is-hoverable">
									<tbody>
										<?php

							while ($row2 = mysqli_fetch_assoc($result)){
							
							   $sql  = "select f.functionname from categories_function_table_link a , functions f where  a.function_id = f.functionid and a.cat_table_id	='".$row2['cat_tab_id']."' ";
	                           $result1 = mysqli_query($conn,$sql);
										   $draw = 0;
											
						                  	if (isset( $result1->num_rows) && $result1->num_rows > 0) {
							                       while ($row3 = mysqli_fetch_assoc($result1)){
												   
												   
								       	 
											if($row3['functionname']!="Google Maps Address" && $row3['functionname']!="Medicare Hospital overall rating" &&  
																								  $row3['functionname']!= "Medicare Safety of care national comparison" &&  
																								  $row3['functionname']!="Medicare Readmission national comparison" && 
																								  $row3['functionname']!= "Medicare Patient experience national comparison" ) 
											 {
												 
											if(!empty($row[$row3['functionname']])){				
											     ?>
													
											 <div class="row">
												   <div class="col-sm-6">
												   
												- <?php echo $row3['functionname'];?></div>
												   <div class="col-sm-6"><?php   
																   if($row3['functionname']!="Google Maps Link")
																		echo $row[$row3['functionname']];
																   else 
																	   echo "<a href='".$row[$row3['functionname']]."' target='_blank'>View Map</a>";
																   ?>
																   
													</div>
											</div>
											 <?php } ?>				
																
														   													  <?php  $old_functions[] = $row3['functionname'];
													   // end while 
												 
	
								             }  
                                              else {
								       									   if($row3['functionname']=="Medicare Hospital overall rating"|| 
																								  $row3['functionname']== "Medicare Safety of care national comparison"|| 
																								  $row3['functionname']=="Medicare Readmission national comparison" || 
																								  $row3['functionname']== "Medicare Patient experience national comparison" )
																								  {
																								     		 if($row3['functionname']=="Medicare Hospital overall rating")
																											       $func_rat[$draw] = "MHOR"; 
                                                                                                             else if($row3['functionname']=="Medicare Safety of care national comparison")
																											       $func_rat[$draw] = "MSCNC"; 
																												  else if($row3['functionname']=="Medicare Readmission national comparison")
																											       $func_rat[$draw] = "MRNC"; 
																												  else  if($row3['functionname']=="Medicare Patient experience national comparison")
																											       $func_rat[$draw] = "MPENC"; 
																												

																											
																											 $rating[$draw] =0;
																										    if(isset( $row[$row3['functionname']]) && $row[$row3['functionname']]=="Same as the national average")
																											       $rating[$draw] = 50 ;
																											else if    ( isset( $row[$row3['functionname']]) && $row[$row3['functionname']]=="Above the national average")
																										         $rating[$draw] =80 ;
																										    else if    (  isset( $row[$row3['functionname']]) &&$row[$row3['functionname']]=="Below the national average")
																										         $rating[$draw] =30 ;
																												 $draw++;
																												 
																												 
								                                                                           }
																							else  if($row3['functionname']=="Google Maps Address")
                                                                                                          {
																										  
																										      $google_map_address = $row['Google Maps Address'];
																										  
																										  }																							
								
								                } // end else 
                                           	 
								 						     }// while 

							 } // end if 

							 $old_functions[] = $row3['functionname'];
							   $i++;  }  // end while
												
							            ?>
										  
								 
									</tbody>
								</table>
							</div>


							<div class="column is-half" style="margin:0 auto;">
							<center style="font-size:20px;color:#585858;"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-hospital" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#000000" fill="none" stroke-linecap="round" stroke-linejoin="round">
							  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							  <line x1="3" y1="21" x2="21" y2="21" />
							  <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16" />
							  <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
							  <line x1="10" y1="9" x2="14" y2="9" />
							  <line x1="12" y1="7" x2="12" y2="11" />
							</svg>- <?php if (isset($row['Hospital Name']))  echo $row['Hospital Name']; ?> -</center>
								<?php if ($google_map_address != "") { ?>
									<figure class="image is-4by3" >
										<iframe width="100%;" class="has-ratio" width="600" height="450" style="border: 0" frameborder="0" src="https://www.google.com/maps/embed/v1/place?key=AIzaSyC_ir82YnmN8ID8Z2HAa_EoWnIedsaKWhc
											&q=<?php echo $google_map_address; ?>" allowfullscreen> </iframe>
									</figure>
								<?php }

								if ($rating[0] == 0 && $rating[1] == 0 && $rating[2] == 0 && $rating[3] == 0) {
								} else {

								?>


									<div class="row">
										<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
										<script type="text/javascript">
											google.charts.load('current', {
												'packages': ['gauge']
											});
											google.charts.setOnLoadCallback(drawChart);

											function drawChart() {

												var data = google.visualization.arrayToDataTable([
													['Label', 'Value'],
													['<?php echo $func_rat[0]; ?>', <?php echo $rating[0]; ?>],
													['<?php echo $func_rat[1]; ?>', <?php echo $rating[1]; ?>],
													['<?php echo $func_rat[2]; ?>', <?php echo $rating[2]; ?>],
													['<?php echo $func_rat[3]; ?>', <?php echo $rating[3]; ?>]

												]);

												var options = {
													width: 400,
													height: 120,
													redFrom: 90,
													redTo: 100,
													yellowFrom: 75,
													yellowTo: 90,
													minorTicks: 5
												};

												var chart = new google.visualization.Gauge(document.getElementById('chart_div'));

												chart.draw(data, options);

												setInterval(function() {
													data.setValue(0, 1, 40 + Math.round(60 * Math.random()));
													chart.draw(data, options);
												}, 100000);
												setInterval(function() {
													data.setValue(1, 1, 40 + Math.round(60 * Math.random()));
													chart.draw(data, options);
												}, 100000);
												setInterval(function() {
													data.setValue(2, 1, 60 + Math.round(20 * Math.random()));
													chart.draw(data, options);
												}, 100000);
											}
										</script>


										<div id="chart_div" style="width: 100%; height: 120px;text-align:right;"></div>
										<div class="row">
											<table width="100%">
												<tr>
													<td style="width:25%">Medicare Hospital overall rating</td>
													<td style="width:25%">Medicare Safety of care national comparison</td>
													<td style="width:25%"> Medicare Readmission national comparison</td>
													<td style="width:25%">Medicare Patient experience national comparison</td>
												</tr>
											</table>
										</div>
									</div>


								<?php } ?>


							</div>
							<!--col6 -->						</div>
						<!--end row -->

					<?php } // end if
					if(isset($_GET['hospitalsearch'])){
					?>
						<div style="margin:0 auto;display:table;" class="select">
						<select  onchange="this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);">
						<?php 
						
						$Search_Query = mysqli_real_escape_string($conn,$_GET['hospitalsearch']);
						$Get_HOS = mysqli_query($conn,"SELECT * FROM `" . $tableName . "` WHERE `Hospital Name` like '%$Search_Query%' and `Merge ID` is null");
						
							while($Hospitals = mysqli_fetch_assoc($Get_HOS)){
								$HOS = str_replace(' ','+',$Hospitals['Hospital Name']);
								echo '<option value="mypage.php?tblName='.$tableName.'&pageno=1&hospitalsearch='.$Hospitals['Hospital Name'].'&idn=&facility=&statesearch=&citysearch=&gpo=&search=">'.$Hospitals['Hospital Name'].'</option>';
							}
						
						?>
						</select>
						</div>
						<?php
				}?>
				</div>
				<!--all tab -->
				

				<?php
				
				// for grid view

				// thead start-----------------------------
				if(isset($_COOKIE["Page"])){
					$no_of_records_per_page = $_COOKIE["Page"];
				}else {
					$no_of_records_per_page = 15;
				}
				
				$sql = "SELECT * FROM  `" . $tableName . "`" . $where . " LIMIT " . $offset . ", " . $no_of_records_per_page . "";
				$res_data_grid = mysqli_query($conn, $sql);


				$sql_getcolumn = "SELECT *
						FROM INFORMATION_SCHEMA.COLUMNS
						WHERE TABLE_NAME='" . $tableName . "'";
				$colResult = $conn->query($sql_getcolumn);

				$tableHtm .= '<thead>';

				$columnAry = [];
				if (isset($colResult->num_rows) && $colResult->num_rows > 0) {
					$tableHtm .= '<th></th>';
					while ($columnrow = mysqli_fetch_assoc($colResult)) {
						if ($columnrow['COLUMN_NAME'] != 'iid') {
							$tableHtm .= '<th>' . $columnrow['COLUMN_NAME'] . '</th>';
							$columnAry[] = $columnrow['COLUMN_NAME'];
						}
					}
					// $tableHtm .= '<th width="40px"><a href="javascript:;"><span class="glyphicon glyphicon-edit"></span></a></th>';
				}
				$tableHtm .= '</thead>';





				if (isset($res_data_grid->num_rows) && $res_data_grid->num_rows > 0) {
					$kkk = 0;
					while ($row = mysqli_fetch_assoc($res_data_grid)) {
						$hcolumnAry = [];

						if ($row['Update Date'] != "") {

							$sql_getcolumn = "SELECT *
								FROM INFORMATION_SCHEMA.COLUMNS
								WHERE TABLE_NAME='" . $tableName . "'";
								
							$colResult = $conn->query($sql_getcolumn);

							if (isset($colResult->num_rows) && $colResult->num_rows > 0) {

								$hcolumnAry = [];
								while ($columnrow = mysqli_fetch_assoc($colResult)) {
									if ($columnrow['COLUMN_NAME'] != 'iid') {

										$hcolumnAry[] = "case when A.`" . $columnrow['COLUMN_NAME'] . "` = B.`" . $columnrow['COLUMN_NAME'] . "` then 'Match' else 'Mismatch' end as `" . $columnrow['COLUMN_NAME'] . "`";
									}
								}
								// $tableHtm .= '<th width="40px"><a href="javascript:;"><span class="glyphicon glyphicon-edit"></span></a></th>';
							}






							$hsql = " Select " . implode(" , ", $hcolumnAry) . " from `" . $tableName . "` A join  `" . $tableName . "_history` B on A.iid = B.iid WHERE A.iid=" . $row['iid'];
							$hresult = $conn->query($hsql);
							$hcolumnAry = [];
							if (isset($hresult->num_rows) && $hresult->num_rows > 0) {
								while ($hrow = mysqli_fetch_assoc($hresult)) {
									$i = 0;
									foreach ($hrow as $key => $value) {

										$hcolumnAry[$i] = isset($hcolumnAry[$i]) && $hcolumnAry[$i] == 'Mismatch' ? $hcolumnAry[$i] : $value;
										$i++;
									}
								}
							}
						}  //end if 

						// history function  

						$bg = "";

						if ($row['Update Date'] != "") {
							$bg = " style='background-color:#ADD8E6;'";
						}


						$tableHtm .= '<tr rowid="' . $row['iid'] . '"' . $bg . ' >';
						if (count($columnAry) > 0) {
							$tableHtm .= '<td><button class="btnSelect">More info</button></td>';
							foreach ($columnAry as $key => $columnrow) {
								$color = "";
								if (isset($hcolumnAry[$key]) &&  $hcolumnAry[$key] == "Mismatch") {
									$id = $row['iid'];
									$color = " style='color:red;' onmouseover=\"history_data_show('" . $id . "','" . $key . "')\"";
								}


								$tableHtm .= '<td ' . $color . ' >' . $row[$columnrow] . '</td>';
							}
							// $tableHtm .= '<th><a href="javascript:;" title="edit" class="edited" rowid="'. $row['iid'] .'"><span class="glyphicon glyphicon-edit"></span></a></th>';
						}
						$kkk++;
						$tableHtm .= '</tr>';
					}

					$total_pages_sql = "SELECT count(*) total FROM `" . $tableName . "`" . $where;

					$result = mysqli_query($conn, $total_pages_sql);
					if (isset($result->num_rows) && $result->num_rows > 0) {
						$row = mysqli_fetch_assoc($result);
						$total_rows =  $row['total'];
						$total_pages = ceil($total_rows / $no_of_records_per_page);
					}




					$st_nxt_cla = '';
					$st_pre_cla = '';
					$pagenum = '';
					if ($pageno <= 1) {
						$st_pre_cla = 'disabled';
						$st_pre_link = '#';
					} else {
						$st_pre_link = "?pageno=" . ($pageno - 1);
					}
					if ($pageno >= $total_pages) {
						$st_nxt_cla = 'disabled';
						$st_nxt_link = '#';
					} else {
						$st_nxt_link = "?pageno=" . ($pageno + 1);
					}

					$pagenum .= '<li>
				  				<a href="javascript:;" id="pageid_li"><input type="number" class="input" name="pageno" value="' . (isset($_GET['pageno']) ? $_GET['pageno'] : 1) . '" /> </a><li>of ' . $total_pages . '</li>
				  			</li>';
					$table_con = isset($_GET['tblName']) ? '&tblName=' . $_GET['tblName'] : "";

					if (isset($_GET['facility']) || isset($_GET['idn']) || isset($_GET['gpo']) || isset($_GET['search']) ||  isset($_GET['citysearch'])  || isset($_GET['statesearch']) || isset($_GET['hospitalsearch']))
						$table_con = $table_con . '&facility=' . $_GET['facility'] . '&idn=' . $_GET['idn'] . '&gpo=' . $_GET['gpo'] . '&search=' . $_GET['search'] . '&citysearch=' . $_GET['citysearch'] . '&statesearch=' . $_GET['statesearch'] . '&hospitalsearch=' . $_GET['hospitalsearch'];



					$pagenationui = '<ul class="pagination">
					  			        <li><a class="button  " href="?pageno=1' . $table_con . '">First</a></li>
					  			        <li class="' . $st_pre_cla . $table_con . '">
					  			            <a class="button  " href="' . $st_pre_link . $table_con . '">Prev</a>
					  			        </li>' . $pagenum . '
					  			        <li class="' . $st_nxt_cla . '">
					  			            <a class="button  " href="' . $st_nxt_link . $table_con . '">Next</a>
					  			        </li>
					  			        <li><a class="button  " href="?pageno=' . $total_pages . '">Last</a></li>
					  			    </ul>';
				} else {
					$tableHtm .= '<div style="width:100%; text-align:center;">No results</div>';
				}

				// end grid view

				?>
				<?php
				$success_message = "";
				$comment_id = "";
				$comment = "";
				$user_id = $_SESSION['userid'];
				$comment_sql = "SELECT *   FROM `user_comment` where user_id ='" . $user_id . "' and  table_name='" . $tableName . "' and hospital_id ='" . $record_id . "' ";
				$result = mysqli_query($conn, $comment_sql);
				if (isset($result->num_rows) && $result->num_rows > 0) {
					$row = mysqli_fetch_assoc($result);
					$comment_id = $row["comment_id"];
					$comment = $row["comment"];
				}



				if (isset($_POST["add_comment"]) && $_POST["add_comment"] != "") {
					$comment = $_POST['comment'];



					if ($comment_id != "") {

						$sql = "update  user_comment set comment='" . $comment . "' where comment_id ='" . $comment_id . "'";

						$result =  mysqli_query($conn, $sql);
					} else {
						$sql = "insert into user_comment (user_id,comment,table_name,hospital_id) values('" . $user_id . "','" . $comment . "','" . $tableName . "','" . $record_id . "')";

						$result = mysqli_query($conn, $sql);
					}
					$success_message = "<span style='color:green;'>Successfully saved comment!!</span></br>";
				}


				?>
				<div class="my-5 container">
					<form action="" method="post">
						<b> PERSONAL NOTES: </b></br>
						<?php echo $success_message; ?>
						<textarea class="textarea is-medium" name="comment"><?php echo $comment; ?></textarea></br>
						<input type="submit" name="add_comment" class="button has-text-white" value="Save Comment">
					</form>
				</div>


				<div class="container-fluid search_result_tab" id="GridView" style="display:none;">
					<table id="example" class="table table-striped table-bordered table-hover">
						<?php echo  $tableHtm; ?>

					</table>
					<div class="pagenation_section my-4" align="center">
						<form method="get">
							<?php echo $pagenationui; ?>
						</form>
						<br>
					
						<form method="POST" style="margin:0 auto;display:table;">
							<label>Results Per Page : </label>
							<input class="input" type="number" name="PerPage" value="<?php echo $_COOKIE['Page'];?>">
							<br>
							<input type="submit" style="margin-top:10px;" class="button has-text-white" name="results_per_page_set" value="Set">
						</div>
					</div>
					<script>
					$(document).ready(function(){
						 $("#example").on('click', '.btnSelect', function() {
						  
						  var currentRow = $(this).closest("tr");

						  //var col1 = currentRow.find("td:eq(0)").html(); // get current row 1st table cell TD value
						  var Hospital_Name = currentRow.find("td:eq(1)").html(); // get current row 2nd table cell TD value
						  var Address = currentRow.find("td:eq(2)").html(); // get current row 3rd table cell  TD value
						  var city = currentRow.find("td:eq(3)").html(); 
						  var state = currentRow.find("td:eq(4)").html(); 
						  var ZIP = currentRow.find("td:eq(5)").html(); // get current row 3rd table cell  TD value
						  var Provider_Number = currentRow.find("td:eq(6)").html(); // get current row 3rd table cell  TD value
						  var Provider_Category_Subtype_Code = currentRow.find("td:eq(7)").html(); // get current row 3rd table cell  TD value
						  var Provider_Category_Code = currentRow.find("td:eq(8)").html(); // get current row 3rd table cell  TD value
						  var Telephone_Number = currentRow.find("td:eq(9)").html(); // get current row 3rd table cell  TD value
						  var FIPS_State_Code = currentRow.find("td:eq(10)").html(); // get current row 3rd table cell  TD value
						  var FIPS_County_Code = currentRow.find("td:eq(11)").html(); // get current row 3rd table cell  TD value
						  var col13 = currentRow.find("td:eq(12)").html(); // get current row 3rd table cell  TD value
						  var col13 = currentRow.find("td:eq(12)").html(); // get current row 3rd table cell  TD value
						  var col13 = currentRow.find("td:eq(12)").html(); // get current row 3rd table cell  TD value
						  var col13 = currentRow.find("td:eq(12)").html(); // get current row 3rd table cell  TD value
						  var col13 = currentRow.find("td:eq(12)").html(); // get current row 3rd table cell  TD value
						  var col13 = currentRow.find("td:eq(12)").html(); // get current row 3rd table cell  TD value
						  
						  var Operating_Room_Count = currentRow.find("td:eq(18)").html(); // get current row 3rd table cell  TD value
						  var Google_Maps_Address = currentRow.find("td:eq(21)").html(); // get current row 3rd table cell  TD value
						  //var data = col1 + "\n" + col2 + "\n" + col3;
						  var Hospital_Name = Hospital_Name;
						  var Address = Address;
						  var city = city;
						  
						  document.getElementById("hospital_name").innerHTML = Hospital_Name;  
						  document.getElementById("Address").innerHTML = Address;  
						  document.getElementById("city").innerHTML = city;  
						  document.getElementById("state").innerHTML = state;  
						  document.getElementById("ZIP").innerHTML = ZIP;  
						  document.getElementById("Provider_Number").innerHTML = Provider_Number;  
						  document.getElementById("Provider_Category_Subtype_Code").innerHTML = Provider_Category_Subtype_Code;  
						  document.getElementById("Provider_Category_Code").innerHTML = Provider_Category_Code;  
						  document.getElementById("Telephone_Number").innerHTML = Telephone_Number;  
						  document.getElementById("FIPS_State_Code").innerHTML = FIPS_State_Code;  
						  document.getElementById("FIPS_County_Code").innerHTML = FIPS_County_Code;  
						  document.getElementById("Operating_Room_Count").innerHTML = Operating_Room_Count;  
						  document.getElementById("Google_Maps_Address").innerHTML = Google_Maps_Address;  

							$('.detail, html, body').toggleClass('open');
							$("html").css("overflow-y", "hidden");

							
						  
						});
					
					$('.close').on('click', function(e) {
							  
							  $('.detail, html, body').toggleClass('open');
							  $("html").css("overflow-y", "scroll");

							});
					
					 });
					</script>
					 <div class='detail'>
						<div class='detail-container'>
						  <dl>
							<dt>
							  Hospital Name
							</dt>
							<dd id="hospital_name">
							  
							</dd>
							<dt>
							  Address
							</dt>
							<dd id="Address">
							  
							</dd>
							<dt>
							  City
							</dt>
							<dd id="city">
							</dd>
							<dt>
							  State
							</dt>
							<dd id="state">
							  
							</dd>
							<dt>
							  ZIP
							</dt>
							<dd id="ZIP">
							  
							</dd>
							<dt>
							  Provider Number
							</dt>
							<dd id="Provider_Number">
							  
							</dd>
							<dt>
							  Provider Category Subtype Code
							</dt>
							<dd id="Provider_Category_Subtype_Code">
							  
							</dd>
							<dt>
							  Provider Category Code
							</dt>
							<dd id="Provider_Category_Code">
							  
							</dd>
							<dt>
							  Telephone Number
							</dt>
							<dd id="Telephone_Number">
							  
							</dd>
							<dt>
							  FIPS State Code
							</dt>
							<dd id="FIPS_State_Code">
							  
							</dd>
							<dt>
							  FIPS County Code
							</dt>
							<dd id="FIPS_County_Code">
							  
							</dd>
							<dt>
							  Operating Room Count
							</dt>
							<dd id="Operating_Room_Count">
							  
							</dd>
							<dt>
							  Google Maps Address
							</dt>
							<dd id="Google_Maps_Address">
							  
							</dd>							
						  </dl>
						</div>
						<div class='detail-nav'>
						  <span class='close'>
							Close
						  </span>
						</div>
					  </div>
				</div>

			</div>
		<?php


		} else {
			$tableHtm = '<div style="width:100%; text-align:center;">No results</div>';
		}

		?>


	</div>
	<footer class="footer has-background-black pb-6">
		<div class="container has-text-white">
			<div class="columns">
				<div class="column is-two-fifths">
					<img src="assets/img/logo.png" alt="" style="width: 22rem" class="p-5 image has-background-white" />
					<p class="pr-6 pt-5">
						Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis
						mollis et sem sed sollicitudin. Donec non odio neque. Aliquam
						hendrerit sollicitudin purus, quis rutrum mi accumsan nec.
					</p>
				</div>
				<div class="column is-one-third">
					<h3 class="is-size-5 pb-2">Hospital Types</h3>
					<ul>
						<li>Acute Care – Department of Defense</li>
						<li>Acute Care Hospitals</li>
						<li>Children’s</li>
						<li>Critical Access Hospitals</li>
						<li>Psychiatric</li>
					</ul>
				</div>
				<div class="column">
					<h3 class="is-size-5 pb-2">Facility Types</h3>
					<ul>
						<li>ASC</li>
						<li>Hospital</li>
					</ul>
				</div>
			</div>
		</div>
	</footer>
	<footer class="has-background-black has-text-white py-6">
		<div class="container">
			<p class="">&copy; sales data generator - All rights reserved</p>
		</div>
	</footer>
	<button type="button" id="openmodal" class="button button-info button-lg" data-toggle="modal" data-target="#editmodal" style="display: none">Open Modal</button>

	<!-- Modal for edit record -->
	<div id="editmodal" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h4 class="modal-title">All item</h4>
				</div>
				<div class="modal-body" id="edit_item_body">


				</div>
				<div class="modal-footer">
					<button type="button" class="button button-primary" id="edited_item_save">Save</button>
					<button type="button" class="button " data-dismiss="modal">Close</button>
				</div>
			</div>
		</div>
	</div>










	<script type="text/javascript">
		$("table td").dblclick(function() {
			var rid = $(this).parent('tr').attr("rowid");

			$.post("controller.php", {
				'state': 'getItemData',
				'rid': rid,
				'tb_name': tb_name
			}, function(rst) {

				var data = JSON.parse(rst);
				var htmlDom = '';
				$.each(data, function(key, value) {
					// console.log(key, value);
					if (key == "iid") {
						htmlDom += '<input type="hidden" class="input edited_input" name="' + key + '" value=' + value + '>';
					} else if (key != "iid") {

						if (value != "" && value != null) {

							if (key != "Google Maps Link")

							{
								htmlDom += '<div class="row" style="margin-bottom:15px;">\
											<div class="col-sm-4 text-right">\
												<label>' + key + ':</label>\
											</div>\
											<div class="col-sm-6">\
												' + value + '\
											</div>\
										</div>';
							} else {
								htmlDom += '<div class="row" style="margin-bottom:15px;">\
											<div class="col-sm-4 text-right">\
												<label>' + key + ':</label>\
											</div>\
											<div class="col-sm-6">\
											<a href="' + value + '" target="_blank">	View Map</a>\
											</div>\
										</div>';
							}

						}
					}
				});

				$("#edit_item_body").html(htmlDom);

				$("#openmodal").trigger("click");
			});
		});


		function openSearchData(evt, searchType) {
			var i, tablinks;
			var x = document.getElementsByClassName("search_result_tab");

			for (i = 0; i < x.length; i++) {
				x[i].style.display = "none";

			}

			tablinks = document.getElementsByClassName("tablink");
			for (i = 0; i < tablinks.length; i++) {

				tablinks[i].className = tablinks[i].className.replace(" w3-red", "");
			}
			evt.currentTarget.className += " w3-red";

			document.getElementById(searchType).style.display = "block";

		}
	</script>



	<script type="text/javascript">
		var rids = [];
		var tb_name = '<?php echo $tableName; ?>';

		$("#export").click(function() {

			document.getElementById("export_form").submit()

		});

		$("#import").click(function() {
			$("#fileinput").trigger("click");
		});

		$('#fileinput').on('change', function(e) {
			$('.loading_cover').show();

			var filename = (e.target.files)[0].name;
			var d = new Date();
			var randomTableName = d.getFullYear() + d.getMonth() + d.getDate() + "_" + d.getHours() + d.getMinutes() + "_" + randomStr(10, filename.trim());

			$('input[name="tablename"]').val(randomTableName);
			$('input[name="file_name"]').val(filename);

			$("#save_form").submit();
		});

		$("#tblNameSel").change(function() {
			$('#gettabledata_form').submit();
		});

		$(".rowid").click(function() {
			var chkedAry = $(".rowid:checked");
			if (chkedAry.length > 0) {
				$("#deleted").show();
			} else {
				$("#deleted").hide();
			}
			for (var i = 0; i < chkedAry.length; i++) {
				var rid = $(chkedAry[i]).val();
				rids[i] = rid;
			}
		});

		function Track_Result(rids, table_name) {

			$.post("controller.php", {
				'state': 'Track_Result',
				'rids': rids,
				'tb_name': table_name
			}, function(rst) {

				if (rst == "successful") {
					alert(" it's being tracked ");
					location.reload(true);
				} else {
					alert("Stop Tracking");
					location.reload(true);


				}


			});

		}



		$("#deleted").click(function() {
			var currentUrl = $(location).attr("href");
			$.post("controller.php", {
				'state': 'deleted',
				'rids': rids,
				'tb_name': tb_name
			}, function(rst) {
				if (rst == "successful") {
					alert('Success! Item(s) deleted successfully..');
					location.reload(true);
				}
			});
		});



		$("#edited_item_save").click(function() {
			var editedItemAry = {};
			$('.edited_input').each(function(index, item) {
				var key = $(item).attr('name');
				var value = $("input[name='" + key + "']").val();
				editedItemAry[key] = value;
			}).promise().done(function() {
				// alert('Success! Item(s) edited successfully..');
				// location.reload(true);
			});

			$.post("controller.php", {
				'state': 'editedSave',
				'editedData': JSON.stringify(editedItemAry),
				'tb_name': tb_name
			}, function(rst) {
				if (rst == "successful") {
					alert('Success! Item(s) edited successfully..');
					location.reload(true);
				}
			});
		});

		$("#table_del").click(function() {
			var excelname = $('#tblNameSel').children("option:selected").text();
			var tb_name = $('#tblNameSel').children("option:selected").val();
			if (!confirm("Do You Want To Remove \"" + excelname.trim() + "\"")) return;
			$.post("controller.php", {
				'state': 'table_del',
				'tb_name': tb_name
			}, function(rst) {
				if (rst == "successful") {
					alert('Success! Table deleted successfully..');
					documentUrl = document.URL.split('?')[0];
					window.location.href = documentUrl;
				}
			});
		});

		function randomStr(len, arr) {
			var ans = '';
			for (var i = len; i > 0; i--) {
				ans +=
					arr[Math.floor(Math.random() * arr.length)];
			}
			return ans;
		}

		setTimeout(function() {
			$('.alert').hide("500");
		}, 3500);
	</script>


	<script>
		function autocomplete(inp, arr) {
			/*the autocomplete function takes two arguments,
			the text field element and an array of possible autocompleted values:*/
			var currentFocus;
			/*execute a function when someone writes in the text field:*/
			inp.addEventListener("input", function(e) {
				var a, b, i, val = this.value;
				/*close any already open lists of autocompleted values*/
				closeAllLists();
				if (!val) {
					return false;
				}
				currentFocus = -1;
				/*create a DIV element that will contain the items (values):*/
				a = document.createElement("DIV");
				a.setAttribute("id", this.id + "autocomplete-list");
				a.setAttribute("class", "autocomplete-items");
				/*append the DIV element as a child of the autocomplete container:*/
				this.parentNode.appendChild(a);
				/*for each item in the array...*/
				for (i = 0; i < arr.length; i++) {
					/*check if the item starts with the same letters as the text field value:*/
					// if (arr[i].substr(0, val.length).toUpperCase() == val.toUpperCase()) {
					var str = arr[i].toUpperCase();
					var n = str.search(val.toUpperCase());
					if (n != "-1") {

						/*create a DIV element for each matching element:*/
						b = document.createElement("DIV");
						/*make the matching letters bold:*/
						b.innerHTML = "<strong>" + arr[i].substr(0, val.length) + "</strong>";
						b.innerHTML += arr[i].substr(val.length);
						/*insert a input field that will hold the current array item's value:*/
						b.innerHTML += "<input type='hidden' value='" + arr[i] + "'>";
						/*execute a function when someone clicks on the item value (DIV element):*/
						b.addEventListener("click", function(e) {
							/*insert the value for the autocomplete text field:*/
							inp.value = this.getElementsByTagName("input")[0].value;
							/*close the list of autocompleted values,
							(or any other open lists of autocompleted values:*/
							closeAllLists();
						});
						a.appendChild(b);
					}
				}
			});
			/*execute a function presses a key on the keyboard:*/
			inp.addEventListener("keydown", function(e) {
				var x = document.getElementById(this.id + "autocomplete-list");
				if (x) x = x.getElementsByTagName("div");
				if (e.keyCode == 40) {
					/*If the arrow DOWN key is pressed,
					increase the currentFocus variable:*/
					currentFocus++;
					/*and and make the current item more visible:*/
					addActive(x);
				} else if (e.keyCode == 38) { //up
					/*If the arrow UP key is pressed,
					decrease the currentFocus variable:*/
					currentFocus--;
					/*and and make the current item more visible:*/
					addActive(x);
				} else if (e.keyCode == 13) {
					/*If the ENTER key is pressed, prevent the form from being submitted,*/
					e.preventDefault();
					if (currentFocus > -1) {
						/*and simulate a click on the "active" item:*/
						if (x) x[currentFocus].click();
					}
				}
			});

			function addActive(x) {
				/*a function to classify an item as "active":*/
				if (!x) return false;
				/*start by removing the "active" class on all items:*/
				removeActive(x);
				if (currentFocus >= x.length) currentFocus = 0;
				if (currentFocus < 0) currentFocus = (x.length - 1);
				/*add class "autocomplete-active":*/
				x[currentFocus].classList.add("autocomplete-active");
			}

			function removeActive(x) {
				/*a function to remove the "active" class from all autocomplete items:*/
				for (var i = 0; i < x.length; i++) {
					x[i].classList.remove("autocomplete-active");
				}
			}

			function closeAllLists(elmnt) {
				/*close all autocomplete lists in the document,
				except the one passed as an argument:*/
				var x = document.getElementsByClassName("autocomplete-items");
				for (var i = 0; i < x.length; i++) {
					if (elmnt != x[i] && elmnt != inp) {
						x[i].parentNode.removeChild(x[i]);
					}
				}
			}
			/*execute a function when someone clicks in the document:*/
			document.addEventListener("click", function(e) {
				closeAllLists(e.target);
			});
		}

		/*An array containing all the country names in the world:*/
		var countries = ["Afghanistan", "Albania", "Algeria", "Andorra", "Angola", "Anguilla", "Antigua & Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia & Herzegovina", "Botswana", "Brazil", "British Virgin Islands", "Brunei", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central Arfrican Republic", "Chad", "Chile", "China", "Colombia", "Congo", "Cook Islands", "Costa Rica", "Cote D Ivoire", "Croatia", "Cuba", "Curacao", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", "French Polynesia", "French West Indies", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea Bissau", "Guyana", "Haiti", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Kosovo", "Kuwait", "Kyrgyzstan", "Laos", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libya", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Mauritania", "Mauritius", "Mexico", "Micronesia", "Moldova", "Monaco", "Mongolia", "Montenegro", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauro", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "North Korea", "Norway", "Oman", "Pakistan", "Palau", "Palestine", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", "Saint Pierre & Miquelon", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Korea", "South Sudan", "Spain", "Sri Lanka", "St Kitts & Nevis", "St Lucia", "St Vincent", "Sudan", "Suriname", "Swaziland", "Sweden", "Switzerland", "Syria", "Taiwan", "Tajikistan", "Tanzania", "Thailand", "Timor L'Este", "Togo", "Tonga", "Trinidad & Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks & Caicos", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States of America", "Uruguay", "Uzbekistan", "Vanuatu", "Vatican City", "Venezuela", "Vietnam", "Virgin Islands (US)", "Yemen", "Zambia", "Zimbabwe"];
	</script>
	<?php

	$sql = "SELECT distinct(State) FROM `" . $tableName . "`";
	$res_data = mysqli_query($conn, $sql);
	$state_array = array();
	$general_data_array = array();
	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		$kkk = 0;
		while ($row = mysqli_fetch_assoc($res_data)) {
			$state_array[] = '"' . trim($row['State']) . '"';
			$general_data_array[] = '"' . trim($row['State']) . '"';
		}
	}
	$all_state = implode(",", $state_array);


	echo "<script> var states = [" . $all_state . "];</script>";

	$sql = "SELECT distinct(IDN) FROM `" . $tableName . "` limit 0,1000";
	$res_data = mysqli_query($conn, $sql);
	$IDN_array = array();
	$general_data_array = array();
	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		$kkk = 0;
		while ($row = mysqli_fetch_assoc($res_data)) {
			$IDN_array[] = '"' . trim($row['IDN']) . '"';
			$general_data_array[] = '"' . trim($row['IDN']) . '"';
		}
	}
	$all_idn = implode(",", $IDN_array);





	$sql = "SELECT distinct(`GPO Affiliations`) FROM `" . $tableName . "` limit 0,1000";
	$res_data = mysqli_query($conn, $sql);
	$gpo_array = array();
	$general_data_array = array();
	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		$kkk = 0;
		while ($row = mysqli_fetch_assoc($res_data)) {
			$gpo_array[] = '"' . trim($row['GPO Affiliations']) . '"';
			$general_data_array[] = '"' . trim($row['GPO Affiliations']) . '"';
		}
	}
	$all_gpo = implode(",", $gpo_array);


	echo "<script> var gpos = [" . $all_gpo . "];</script>";




	$sql = "SELECT distinct(city) FROM `" . $tableName . "` where city !='' limit 0,1000";
	$res_data = mysqli_query($conn, $sql);
	$city_array = array();

	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		$kkk = 0;
		while ($row = mysqli_fetch_assoc($res_data)) {
			$city_array[] = '"' . trim($row['city']) . '"';
			$general_data_array[] = '"' . trim($row['city']) . '"';
		}
	}
	$all_city = implode(",", $city_array);

	echo "<script> var cities = [" . $all_city . "];</script>";

	$sql = "SELECT `Hospital Name` FROM `" . $tableName . "`";
	$res_data = mysqli_query($conn, $sql);
	$hospital_array = array();

	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		$kkk = 0;
		while ($row = mysqli_fetch_assoc($res_data)) {
			$hospital_array[] = '"' . trim($row['Hospital Name']) . '"';
			$general_data_array[] = '"' . trim($row['Hospital Name']) . '"';
		}
	}
	$all_hospital = implode(",", $hospital_array);
	$general_data = implode(",", $general_data_array);

	echo "<script> var hospitals = [" . $all_hospital . "];</script>";
	echo "<script> var general_data = [" . $general_data . "];</script>";
	echo "<script> var idns = [" . $all_idn . "];</script>";



	$sql = "SELECT `Medicare Hospital Type` FROM `" . $tableName . "` limit 0,1000";
	$res_data = mysqli_query($conn, $sql);
	$mediacre_array = array();

	if (isset($res_data->num_rows) && $res_data->num_rows > 0) {
		$kkk = 0;
		while ($row = mysqli_fetch_assoc($res_data)) {
			$mediacre_array[] = '"' . trim($row['Medicare Hospital Type']) . '"';
		}
	}
	$all_facility = implode(",", $mediacre_array);

	echo "<script> var facilitys = [" . $all_facility . "];</script>";



	?>
	<script>
		/*initiate the autocomplete function on the "myInput" element, and pass along the countries array as possible autocomplete values:*/
		//autocomplete(document.getElementById("myInput"), countries);

		autocomplete(document.getElementById("myInputState"), states);
		autocomplete(document.getElementById("myInputIdn"), idns);
		autocomplete(document.getElementById("myInputGpo"), gpos);

		autocomplete(document.getElementById("myInputCity"), cities);
		//autocomplete(document.getElementById("myInputHospital"), hospitals);
		autocomplete(document.getElementById("myInputGeneral"), general_data);
		autocomplete(document.getElementById("myInputFacility"), facilitys);
		$(document).ready(function() {
			$('#example').DataTable({
				"scrollX": true,
				"paging": false
			});
		});

 $(document).ready(function(){
    $('#myInputHospital').on("keyup input", function(){
		
		
        /* Get input value on change */
        var inputVal = $(this).val();
        var resultDropdown = $(this).siblings("#myInputHospitalautocomplete-listsss");
        if(inputVal.length >= 3){
            $.get("backend-search.php", {term: inputVal}).done(function(data){
                // Display the returned data in browser
                resultDropdown.html(data);
            });
        } else{
            resultDropdown.empty();
        }
		
    });
    
    // Set search input value on click of result item
    $(document).on("click", ".autocomplete-itemss div", function(){
		
        $('#myInputHospital').val($(this).text());
        $(this).parent("#myInputHospitalautocomplete-listsss").empty();
    });
});


	</script>







</body>

</html>