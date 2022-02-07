<style>
#TopNav {
	display:flex;
	justify-content:space-between;
	align-items:center;
}
.navbar-end1 {
    display: flex;
    justify-content: center;
    align-items: center;
    color: #fff;
}

.MenuSearch {
    align-items: center;
    display: flex;
    margin-top: 14px;
}
ul.Submenu {
    display: flex;
    flex-wrap: wrap;
    align-content: space-between;
    justify-content: center;
    flex-direction: column;
    margin-top: 4px;
}
.navbar-link, a.navbar-item {
	border-radius: 5px;
    margin-bottom: 2px;
}
span.is-size-4.has-text-weight-bold ,.has-text-weight-normal {
    font-family: 'Dosis';
}

.detail.open {
  -moz-transform: translateX(0);
  -ms-transform: translateX(0);
  -webkit-transform: translateX(0);
  transform: translateX(0);
}

.detail-container {
  margin: 0 auto;
  padding: 40px;
  max-width: 500px;
  color:#fff;
}

dl {
  margin: 0;
  padding: 0;
}

dt {
  font-size: 2.2rem;
  font-weight: 300;
}

dd {
    margin: 0 0 40px 0;
    font-size: 1.2rem;
    padding-bottom: 5px;
    border-bottom: 1px solid var(--green);
    box-shadow: 0 1px 0 var(--green);
}

.close {
  background: none;
  padding: 18px;
  color: #fff;
  font-weight: 300;
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 4px;
  line-height: 1;
  font-size: 1.8rem;
  position: fixed;
  right: 40px;
  top: 20px;
  -moz-transition: border 0.3s linear;
  -o-transition: border 0.3s linear;
  -webkit-transition: border 0.3s linear;
  transition: border 0.3s linear;
}
.close:hover, .close:focus {
  background-color: var(--green);
  border: 1px solid var(--green);
}
.button {
	border:0;
}
.button.is-link {
    background-color: var(--green)!important;
}
button.w3-bar-item.w3-button.tablink svg, svg.icon.icon-tabler.icon-tabler-track {
	vertical-align: top;
    margin-right: 6px;
    stroke: #fff;
}
.detail {
  background-color: #797979;
  width: 100%;
  height: 100%;
  padding: 40px 0;
  position: fixed;
  top: 0;
  left: 0;
  overflow: auto;
  -moz-transform: translateX(-100%);
  -ms-transform: translateX(-100%);
  -webkit-transform: translateX(-100%);
  transform: translateX(-100%);
  -moz-transition: -moz-transform 0.3s ease-out;
  -o-transition: -o-transform 0.3s ease-out;
  -webkit-transition: -webkit-transform 0.3s ease-out;
  transition: transform 0.3s ease-out;
  z-index: 1000;
}
.detail.open {
  -moz-transform: translateX(0);
  -ms-transform: translateX(0);
  -webkit-transform: translateX(0);
  transform: translateX(0);
}

.detail-container {
  margin: 0 auto;
  padding: 40px;
  max-width: 500px;
}

dl {
  margin: 0;
  padding: 0;
}

dt {
  font-size: 2.2rem;
  font-weight: 300;
}


.close {
  background: none;
  padding: 18px;
  color: #fff;
  font-weight: 300;
  border: 1px solid rgba(255, 255, 255, 0.4);
  border-radius: 4px;
  line-height: 1;
  font-size: 1.8rem;
  position: fixed;
  right: 40px;
  top: 20px;
  -moz-transition: border 0.3s linear;
  -o-transition: border 0.3s linear;
  -webkit-transition: border 0.3s linear;
  transition: border 0.3s linear;
  
}
.close:hover, .close:focus {
  background-color: var(--green);
  border: 1px solid var(--green);
}
@media (max-width:552px){
	.navbar-end1 {
		display:block;
	}
}
body {
    background: url(assets/img/bg.png);
    background-color: #f4f6f5;
}
.autocomplete {
    margin-right: 7px;
}
.column {
	padding:0;
}
.navbar {
	background:transparent;
	box-shadow:unset;
	position:relative;
}
/* devanagari */
@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLGT9Z11lFc-K.woff2) format('woff2');
  unicode-range: U+0900-097F, U+1CD0-1CF6, U+1CF8-1CF9, U+200C-200D, U+20A8, U+20B9, U+25CC, U+A830-A839, U+A8E0-A8FB;
}
/* latin-ext */
@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLGT9Z1JlFc-K.woff2) format('woff2');
  unicode-range: U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;
}
/* latin */
@font-face {
  font-family: 'Poppins';
  font-style: normal;
  font-weight: 500;
  font-display: swap;
  src: url(https://fonts.gstatic.com/s/poppins/v15/pxiByp8kv8JHgFVrLGT9Z1xlFQ.woff2) format('woff2');
  unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
}
button.w3-bar-item.button.is-pulled-right {
    background: #5aa897!important;
	background-color: #5aa897!important;
	border-radius:0px;
}
button.w3-bar-item.w3-button.tablink.pwz {
    background: #126e82;
    background-color: #126e82;
}
button.w3-bar-item.w3-button.tablink.PwButton {
    background: #77c720;
}
button.w3-bar-item.w3-button.tablink.azx {
    background: indianred;
}
button.bordered {
    border-radius: 50%;
    width: 40px;
    height: 40px;
}
iframe.has-ratio {
    border: 0;
    border-radius: 5px;
}
.col-sm-6 {
    width: 50%;
    float: left;
}
.leftside {
    background: #fff;
    color: #333;
    padding: 1rem;
    border-radius: 7px;
    margin-bottom: 16px;
	    height: max-content;

    box-shadow: 1px 1px 4px rgb(0 0 0 / 23%);
}
.table thead {
    background-color: #5B5B5B!important;
}
table.dataTable thead .sorting{
	color:#fff;
}
button.btnSelect {
    display: inline-block;
    padding: 6px 12px;
    margin-bottom: 0;
    color: #fff;
    background-color: #337ab7;
    border-color: #2e6da4;
    font-size: 14px;
    font-weight: 400;
    line-height: 1.42857143;
    text-align: center;
    white-space: nowrap;
    vertical-align: middle;
    -ms-touch-action: manipulation;
    touch-action: manipulation;
    cursor: pointer;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    background-image: none;
    border: 1px solid transparent;
    border-radius: 4px;
}
button.btnSelect.active, button.btnSelect:active {
    background-image: none;
    outline: 0;
    -webkit-box-shadow: inset 0 3px 5px rgb(0 0 0 / 13%);
    box-shadow: inset 0 3px 5px rgb(0 0 0 / 13%);
}
div#example_filter {
    margin-bottom: 15px;
}
.table-bordered>tbody>tr>td, .table-bordered>tbody>tr>th, .table-bordered>tfoot>tr>td, .table-bordered>tfoot>tr>th, .table-bordered>thead>tr>td, .table-bordered>thead>tr>th {
    border: 1px solid #ddd;
}
</style>
 <?php
  $url = explode("/", $_SERVER["REQUEST_URI"]);
  ?>
<nav class="navbar" style="margin-bottom:0;" role="navigation" aria-label="main navigation">
   <div class="containere" style="display:flex;width: 100%;justify-content: space-between;align-items:center;">
     <div class="navbar-brand mr-4">
       <a class="navbar-item py-3" href="#">
         <img src="assets/img/logo.png" style="max-height: fit-content" width="127" height="80" />
       </a>

       <a role="button" class="navbar-burger" aria-label="menu" aria-expanded="false" data-target="navbarBasicExample">
         <span aria-hidden="true"></span>
         <span aria-hidden="true"></span>
         <span aria-hidden="true"></span>
       </a>
     </div>

     <div class="navbar-menuw">
     
		<div class="MenuSearch">
		<div>
			<div>
				<script>
					document.onkeydown = function(evt) {
						var keyCode = evt ? (evt.which ? evt.which : evt.keyCode) : event.keyCode;
						if (keyCode == 13) {
							//your function call here
							document.search_form.submit();
						}
					}
				</script>

				<form method="get" id="search_form" style="display:flex; justify-content:center; align-items:center; gap:10px; margin-bottom:1rem" name="search_form" autocomplete="off">

					<input type="hidden" name="tblName" value="<?php echo $tableName; ?>" />
					<input type="hidden" name="pageno" value="1" />

					<div class="columns">
						<div class="column autocomplete">
							<input type="text" class="input" id="myInputHospital" style="width:160px;" name="hospitalsearch" placeholder="Search by Hospital Name" value="<?php if (isset($_GET['hospitalsearch'])) echo $_GET['hospitalsearch']; ?>" />
						</div>
						<div class="column autocomplete">
							<input type="text" class="input" id="myInputIdn" style="width:70px;" name="idn" placeholder="IDN" value="<?php if (isset($_GET['idn'])) echo $_GET['idn']; ?>" />
						</div>
						<div class="column autocomplete">
							<input type="text" class="input" id="myInputFacility" style="width:120px;" name="facility" placeholder="Facility Type" value="<?php if (isset($_GET['facility'])) echo $_GET['facility']; ?>" />
						</div>
						<div class="column autocomplete">
							<input type="text" class="input" id="myInputState" style="width:140px;" name="statesearch" placeholder="Search by State" value="<?php if (isset($_GET['statesearch'])) echo $_GET['statesearch']; ?>" />
						</div>
						<div class="column autocomplete">
							<input type="text" class="input" id="myInputCity" style="width:140px;" name="citysearch" placeholder="Search by City" value="<?php if (isset($_GET['citysearch'])) echo $_GET['citysearch']; ?>" />
						</div>


						<div class="column autocomplete">
							<input type="text" class="input" id="myInputGpo" style="width:70px;" name="gpo" placeholder="GPO" value="<?php if (isset($_GET['gpo'])) echo $_GET['gpo']; ?>" />
						</div>

						<div class="column autocomplete">
							<input type="text" class="input" id="myInputGeneral" style="width:140px;" name="search" placeholder="General Search" value="<?php if (isset($_GET['search'])) echo $_GET['search']; ?>" />
						</div>
						<div class="column">
						<button type="submit" class="button bordered">
							<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="24" height="24" viewBox="0 0 24 24" stroke-width="1.5" stroke="#fff" fill="none" stroke-linecap="round" stroke-linejoin="round">
							  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
							  <circle cx="10" cy="10" r="7" />
							  <line x1="21" y1="21" x2="15" y2="15" />
							</svg>
						</button>
						</div>

					</div>
				</form>
			</div>
		</div>
		
	</div>
     </div>
	  <div class="row" style="display: flex;"> 
		         <div class="pull-right" style="margin-right: 15px;">
			      <a href="logout.php?url=mypage">Logout </a>
				  | Welcome  admin
			   </div>
		      
		   
		</div>
   </div>
 </nav>
 <div class="row">
		     <div class="w3-bar w3-blue" style="    background-color: #585858 !important;border-radius: 10px;display: flex;justify-content: start;">
				  <a href="mypage.php"><button class="w3-bar-item w3-button tablink1  w3-red " style="width:150px;">HOME</button></a>
				  <a href="notification.php"><button class="w3-bar-item w3-button tablink1   " style="width:150px;"> NOTIFICATIONS</button></a>
				  
			</div>

		</div>