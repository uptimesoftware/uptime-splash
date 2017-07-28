<?php
require_once $_SERVER['DOCUMENT_ROOT'] . "/includes/classLoader.inc";
$_SESSION["isCrossSiteScriptingFound"] = false;
$_REQUEST                              = CrossSiteScriptingDetector::removeCrossSiteScripting($_REQUEST);
if ($_SESSION["isCrossSiteScriptingFound"]) {
	header("Location: /index.php?loggedout");
}
session_set_cookie_params(null, '/', null, null, true);
session_start();
if (!isset($_SESSION['loginAttempt'])) {
	$_SESSION['loginAttempt'] = 0;
}
// see if we have a EULA to display and if so then load it
$EULAenable = file_exists('./EULA.html');
if ($EULAenable) {
	$EULAtext = file_get_contents('./EULA.html', FILE_USE_INCLUDE_PATH);
}
$userLogin = new UserLogin();
$userLogin->handleLogin();
session_write_close();
$licenseErrorChecker = new LicenseErrorChecker();
HtmlPage::startPage(true);
HtmlPage::startPageHead("Uptime", true);
?>
<!-- for EULA popup -->
<script>
	$( function() {
		$( "#dialog-confirm" ).dialog({
			resizable: false,
			draggable: false,
			closeOnEscape: false,
			height: 450,
			width: 500,
			modal: true,
			show: {
				effect: "fade",
				duration: 500
			},
			hide: {
				effect: "fade",
				duration: 200
			},
			buttons: {
				"Accept and Continue": function() { $( this ).dialog( "close" ); }
			}
		});
	});
</script>
<script type="text/javascript">
$(function() {
	$("#username").focus();
	$("#closeButton").click(function(){
		$("#loginErrorBox").fadeOut("fast");
	});

<?php
if ($userLogin->isInitialSetup()) {
?>
	$("#username").val("admin").attr("disabled", "disabled");
	$("#password").focus();
	$("#loginForm").submit(function(){
		$("#username").removeAttr('disabled');
	});
	$("#loginBox").attr("style", "width:-moz-min-content");
	$("#loginForm input[type='text'], input[type='password']").attr("style", "width:200px; font-size:0.8em; ");
	$("#loginForm label").attr("style", "width:200px; font-size:13px;margin-top: 5px;");
	
	
<?php
}
?>
	$("#username").attr("title", ""); 
<?php
if ($userLogin->isAdLdapAuthEnabled()) {
?>
	$("#username").attr("title", "Provide your domain login name, do not include your domain in the username"); 
<?php
}
?>
});
</script>
<link rel="stylesheet" href="/styles/reset.css" type="text/css">
<link rel="stylesheet" href="/styles/login.css" type="text/css">
<!-- present EULA window if enabled -->
<?php
if ($EULAenable) {
	echo'<link rel="stylesheet" href="/styles/EULA.css" type="text/css">';
	echo'<div id="dialog-confirm" >'.$EULAtext.'</div>';
}
?>
<!-- what the heck is this? commenting it out...
<style>
p {
    text-align: center;
    text-decoration: underline;
    height:0px;
}

</style>
-->

<?php
HtmlPage::endPageHead();
HtmlPage::startBody();
?>

<div id="loginBoxWrapper">
<div id="loginBox">
<div id="loginLogo"></div>
<?php
if ($userLogin->hasError() || $licenseErrorChecker->hasError()) {
?>
<div id="loginErrorBox">
<a id="closeButton"><span class="closeIcon"></span></a>
<div><?php
	echo $licenseErrorChecker->getMessage();
?></div>
<div><?php
	echo $userLogin->getErrorMessage();
?></div>
</div>
<?php
}
?>
<form id="loginForm" method="post">
<?php
if ($userLogin->isInitialSetup()) {
?>
		<label for="username">Username</label><br><br>
	    <input type="text" id="username" name="username" placeholder="Username"><br><br>
	    <label for="password">Password</label><br><br>
	    <input type="password" id="password" name="password" placeholder="Password"><br><br>
	 <?php
} else {
?>
	 	<!-- <label for="username">Username</label> -->
	 	<input type="text" id="username" name="username" placeholder="Username"><br><br><br>
	 	<!--  <label for="password">Password</label> -->
	 	<input type="password" id="password" name="password" placeholder="Password">
	<?php
}
?>	
	<?php
$userLogin->renderRedirectLink();
?>
	<?php
$userLogin->renderInitialSetup($userLogin->getErrorMessage());
?>
	<input id="loginButton" type="submit" value="Login">
</form>
</div>
</div>
<div id="footer">
	<div style="float: left; width: 25%;">
		<ul id="uptimeInfo">
			<li>Uptime <?php
print BinaryVersion::getInstance()->toDisplayString();
?></li>
			<li><a href="http://support.uptimesoftware.com">Contact support</a></li>
			<li>
			<?php
if (UiOnlyInstance::isEnabled()) {
	print ' | UI Instance';
}
?>
			</li>
		</ul>
	</div>
	<div id="copyright">
		<a>&#169 Copyright 2017 IDERA, Inc. All Rights Reserved </a> 
	</div>		
</div>
<?php
HtmlPage::endBody();
HtmlPage::endPage();
?>