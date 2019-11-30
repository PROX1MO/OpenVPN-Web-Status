<?php
//	modified by PROXIMO https://github.com/PROX1MO/OpenVPN-Web-Status
###############################################################
# Page Password Protect 2.13
###############################################################
# Visit http://www.zubrag.com/scripts/ for updates
############################################################### 
#
# Usage:
# Set usernames / passwords below between SETTINGS START and SETTINGS END.
# Open it in browser with "help" parameter to get the code
# to add to all files being protected. 
#    Example: password_protect.php?help
# Include protection string which it gave you into every file that needs to be protected
#
# Add following HTML code to your page where you want to have logout link
# <a href="./index.php?logout=1">Logout</a>
#
###############################################################

/*
-------------------------------------------------------------------
SAMPLE if you only want to request login and password on login form.
Each row represents different user.

$LOGIN_INFORMATION = array(
  'zubrag' => 'root',
  'test' => 'testpass',
  'admin' => 'passwd'
);

--------------------------------------------------------------------
SAMPLE if you only want to request only password on login form.
Note: only passwords are listed

$LOGIN_INFORMATION = array(
  'root',
  'testpass',
  'passwd'
);

--------------------------------------------------------------------
*/

##################################################################
#  SETTINGS START
##################################################################

// Add login/password pairs below, like described above
// NOTE: all rows except last must have comma "," at the end of line
// to use md5 pass > line 132 $pass = md5($_POST['access_password']);
$LOGIN_INFORMATION = array(
  'admin' => 'admin',
);

// request login? true - show login and password boxes, false - password box only
define('USE_USERNAME', false);

// User will be redirected to this page after logout
define('LOGOUT_URL', './');

// time out after NN minutes of inactivity. Set to 0 to not timeout
define('TIMEOUT_MINUTES', 0);

// This parameter is only useful when TIMEOUT_MINUTES is not zero
// true - timeout time from last activity, false - timeout time from login
define('TIMEOUT_CHECK_ACTIVITY', true);

##################################################################
#  SETTINGS END
##################################################################


///////////////////////////////////////////////////////
// do not change code below
///////////////////////////////////////////////////////

// show usage example
if(isset($_GET['help'])) {
  die('Include following code into every page you would like to protect, at the very beginning (first line):<br>&lt;?php include("' . str_replace('\\','\\\\',__FILE__) . '"); ?&gt;');
}

// timeout in seconds
$timeout = (TIMEOUT_MINUTES == 0 ? 0 : time() + TIMEOUT_MINUTES * 60);

// logout?
if(isset($_GET['logout'])) {
  setcookie("verify", '', $timeout, '/'); // clear password;
  header('Location: ' . LOGOUT_URL);
  exit();
}

if(!function_exists('showLoginPasswordProtect')) {

// show login form
function showLoginPasswordProtect($error_msg) {
?>
<html>
<head>
  <title>OpenVPN Secure Login</title>
  <link rel="shortcut icon" href="data:image/x-icon;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHLuAAAAAAABd+0DAG7pCwAAAABZKACZXCoA/l0sAP9cKwD/WCgAYgAAAAAIeeELAXXpAgAAAAAAbOkAAHHrAAAAAAAAcdUHAHP3dwBw8LEAAAAAWCgAZ10rAPheLAD/XCsA9lkpAEMAAAAAAHLyqwBy9GYAULEDAAAAAAAAAAAAbrwDAHH1iwBz/P8AcvvmAAAAAFgmADVdKwDyXiwA/1wrANhXJwAmAAAAAABy+f0Ac/z5AG/xZQBm8gAAAAAAAHDyTwBy+/gAdP78AHP97wBw6i7/AAAAWyoA7F4rAP9cKgC/egAACABv6DAAc/3/AHT+/QBz+/kBcOEiAHHuCgBy970AdP7/AHT+/ABy/PEAcvJNAAAAAFopAMldKwD/WioAoQAAAAAAbu1dAHP8/wB0/vwAc/7/AG/ypAFy7CIAcvv/AHT+/ABz/v4AcPOrAAAAAFUmAB1aKQDFXSsA/1opAK5VJgAPAAAAAABx9b8AdP7/AHT+/ABy+/kAcfFYAHP+/wB0/vsAc/3/AG/fJXgGAAlaKQCyXSsA/14rAP9dKwD/WikAjJ4AAAIBcOk5AHP9/wB0/vsAc/38AHT5cwBz/v8AdP78AHP8/wAAAABYJwAzXCsA5V4sAP9eLAD+XiwA/1sqANdZKAAOAHH1EwBy+f8AdP78AHP9/ABx8GwAdP3/AHT+/ABy+/8Ab44EWiUAIlwrANVeLAD/XiwA/14sAP9bKgC/WyUACgFv6B0Acvv/AHT+/AB0/fwAcOo8AHP8/wB0/vsAdP3/AHDtYwAAAABZKQBvXCsA41wrAPxbKgDdVykBRwAAAAAAcfBzAHP+/wB0/vsAc/z9AHHsEgBz+uoAdP7+AHT+/ABy+t4Ac+MjAAAAAFkpADFZKQBKWCYAJwAAAAAAcukyAHL77wB0/v0Ac/7+AHL52QJu1gMAcvSCAHP9/wB0/vsAc/38AHL42ABt5kYAAAAAAAAAAAAAAAAAbu1fAHL76QBz/v0AdP77AHP9/wBx7lgAAAAAAG3mIQBy+M8Ac/39AHT++wB0/v0Ac/z/AHL36gBy+tEAc/rwAHP9/wB0/vwAdP78AHP9/wBy97gBe+4IAF7/AAAAAAAAdPA9AHL51ABz/f8AdP79AHT++wB0/v4AdP7/AHT+/QB0/vsAdP79AHP9/wBz+MMAcO4hAAAAABeLtwAATv8AAAAAAAFy7SYAcvaSAHL6+QBz/v8AdP7/AHP+/wB0/v8Ac/7/AHL68gBy9YMDdO4aAAAAAAAA/wAAAAAACYrgAABA/wAAAAAAAHLmBQJ48BwAcPBgAHDzlgB0+qYAcvWTAHHvUwF18RUAb+MEAAAAAABb/wAEf+kA/D8AAPY3AADGMwAAxjEAAIYwAACGMAAAjBgAAIwYAACMGAAAjjgAAIfwAACD4QAAwAEAAOADAADwBwAA/j8AAA%3D%3D" />
  <META HTTP-EQUIV="CACHE-CONTROL" CONTENT="NO-CACHE">
  <META HTTP-EQUIV="PRAGMA" CONTENT="NO-CACHE">
  <style type="text/css">
body {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 14px;
    background-color: #E5EAF0;
}
input {
	border: 1px solid black;
	border-radius: 5px;
}
   </style>
</head>
<body>
  <div style="width:auto; margin-left:auto; margin-right:auto; margin-top:200px; text-align:center">
  <form method="post">
	<img src="data:image/png;base64,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" alt="OpenVPN"> 
	<h2>Status Monitor</h3>
    <font color="red"><?php echo $error_msg; ?></font><br>
<?php if (USE_USERNAME) echo 'Login:<br><input autofocus="autofocus" type="input" name="access_login" /><br>Password:<br>'; ?>
    <input autofocus="autofocus" type="password" name="access_password" /><p></p><input type="submit" name="Submit" value="Submit" />
  </form>
  <br>
  <a style="font-size: 10px; color: #B0B0B0; font-family: Verdana, Arial;" href="#" title="Provided by PROXIMO">Provided by PROXIMO</a>
  </div>
</body>
</html>

<?php
  // stop at this point
  die();
}
}

// user provided password
if (isset($_POST['access_password'])) {

  $login = isset($_POST['access_login']) ? $_POST['access_login'] : '';
  $pass = $_POST['access_password'];
  if (!USE_USERNAME && !in_array($pass, $LOGIN_INFORMATION)
  || (USE_USERNAME && ( !array_key_exists($login, $LOGIN_INFORMATION) || $LOGIN_INFORMATION[$login] != $pass ) ) 
  ) {
    showLoginPasswordProtect("Incorrect password.");
  }
  else {
    // set cookie if password was validated
    setcookie("verify", md5($login.'%'.$pass), $timeout, '/');
    
    // Some programs (like Form1 Bilder) check $_POST array to see if parameters passed
    // So need to clear password protector variables
    unset($_POST['access_login']);
    unset($_POST['access_password']);
    unset($_POST['Submit']);
  }

}

else {

  // check if password cookie is set
  if (!isset($_COOKIE['verify'])) {
    showLoginPasswordProtect("");
  }

  // check if cookie is good
  $found = false;
  foreach($LOGIN_INFORMATION as $key=>$val) {
    $lp = (USE_USERNAME ? $key : '') .'%'.$val;
    if ($_COOKIE['verify'] == md5($lp)) {
      $found = true;
      // prolong timeout
      if (TIMEOUT_CHECK_ACTIVITY) {
        setcookie("verify", md5($lp), $timeout, '/');
      }
      break;
    }
  }
  if (!$found) {
    showLoginPasswordProtect("");
  }

}

?>
<?php
// Configuration values --------
// write in /etc/openvpn/server.conf > management localhost 5555
$vpn_name = "OpenVPN";
$vpn_host = "127.0.0.1";
$vpn_port = 5555;
// -----------------------------

$fp = fsockopen($vpn_host, $vpn_port, $errno, $errstr, 30);
if (!$fp) {
    echo "$errstr ($errno)<br>\n";
    exit;
}

fwrite($fp, "status\n\n\n");
sleep(1);
fwrite($fp, "quit\n\n\n");
sleep(1);
$clients = array();
$inclients = $inrouting = false;
while (!feof($fp)) {
    $line = fgets($fp, 128);
    if (substr($line, 0, 13) == "ROUTING TABLE") {
        $inclients = false;
    }
    if ($inclients) {
        $cdata = split(',', $line);
        $clines[$cdata[1]] = array($cdata[2], $cdata[3], $cdata[4]);
    }
    if (substr($line, 0, 11) == "Common Name") {
        $inclients = true;
    }

    if (substr($line, 0, 12) == "GLOBAL STATS") {
        $inrouting = false;
    }
    if ($inrouting) {
        $routedata = split(',', $line);
        array_push($clients, array_merge($routedata, 
$clines[$routedata[2]]));
    }
    if (substr($line, 0, 15) == "Virtual Address") {
        $inrouting = true;
    }
}
$headers = array('VPN Address', 'Pforile', 'Real Address', 'Last Active', 'Sent', 'Received', 'Connected Since');
//$tdalign = array('left', 'center', 'center', 'center', 'center', 'center', 'center');
/* DEBUG
print "<pre>";
print_r($headers);
print_r($clients);
print_r($clines);
print_r($routedata);
print "</pre>";
*/
fclose($fp);
?> 
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<title><?php echo $vpn_name ?> Status</title>
<link rel="shortcut icon" href="data:image/png;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHLuAAAAAAABd+0DAG7pCwAAAABZKACZXCoA/l0sAP9cKwD/WCgAYgAAAAAIeeELAXXpAgAAAAAAbOkAAHHrAAAAAAAAcdUHAHP3dwBw8LEAAAAAWCgAZ10rAPheLAD/XCsA9lkpAEMAAAAAAHLyqwBy9GYAULEDAAAAAAAAAAAAbrwDAHH1iwBz/P8AcvvmAAAAAFgmADVdKwDyXiwA/1wrANhXJwAmAAAAAABy+f0Ac/z5AG/xZQBm8gAAAAAAAHDyTwBy+/gAdP78AHP97wBw6i7/AAAAWyoA7F4rAP9cKgC/egAACABv6DAAc/3/AHT+/QBz+/kBcOEiAHHuCgBy970AdP7/AHT+/ABy/PEAcvJNAAAAAFopAMldKwD/WioAoQAAAAAAbu1dAHP8/wB0/vwAc/7/AG/ypAFy7CIAcvv/AHT+/ABz/v4AcPOrAAAAAFUmAB1aKQDFXSsA/1opAK5VJgAPAAAAAABx9b8AdP7/AHT+/ABy+/kAcfFYAHP+/wB0/vsAc/3/AG/fJXgGAAlaKQCyXSsA/14rAP9dKwD/WikAjJ4AAAIBcOk5AHP9/wB0/vsAc/38AHT5cwBz/v8AdP78AHP8/wAAAABYJwAzXCsA5V4sAP9eLAD+XiwA/1sqANdZKAAOAHH1EwBy+f8AdP78AHP9/ABx8GwAdP3/AHT+/ABy+/8Ab44EWiUAIlwrANVeLAD/XiwA/14sAP9bKgC/WyUACgFv6B0Acvv/AHT+/AB0/fwAcOo8AHP8/wB0/vsAdP3/AHDtYwAAAABZKQBvXCsA41wrAPxbKgDdVykBRwAAAAAAcfBzAHP+/wB0/vsAc/z9AHHsEgBz+uoAdP7+AHT+/ABy+t4Ac+MjAAAAAFkpADFZKQBKWCYAJwAAAAAAcukyAHL77wB0/v0Ac/7+AHL52QJu1gMAcvSCAHP9/wB0/vsAc/38AHL42ABt5kYAAAAAAAAAAAAAAAAAbu1fAHL76QBz/v0AdP77AHP9/wBx7lgAAAAAAG3mIQBy+M8Ac/39AHT++wB0/v0Ac/z/AHL36gBy+tEAc/rwAHP9/wB0/vwAdP78AHP9/wBy97gBe+4IAF7/AAAAAAAAdPA9AHL51ABz/f8AdP79AHT++wB0/v4AdP7/AHT+/QB0/vsAdP79AHP9/wBz+MMAcO4hAAAAABeLtwAATv8AAAAAAAFy7SYAcvaSAHL6+QBz/v8AdP7/AHP+/wB0/v8Ac/7/AHL68gBy9YMDdO4aAAAAAAAA/wAAAAAACYrgAABA/wAAAAAAAHLmBQJ48BwAcPBgAHDzlgB0+qYAcvWTAHHvUwF18RUAb+MEAAAAAABb/wAEf+kA/D8AAPY3AADGMwAAxjEAAIYwAACGMAAAjBgAAIwYAACMGAAAjjgAAIfwAACD4QAAwAEAAOADAADwBwAA/j8AAA%3D%3D" />
<meta http-equiv='refresh' content='60' />

<style type="text/css">
body {
    font-family: Verdana, Arial, Helvetica, sans-serif;
    font-size: 14px;
    background-color: #E5EAF0;
}
h1 {
    color: green;
    font-size: 24px;
    text-align: center;
    padding-bottom: 0;
    margin-bottom: 0;
}
h2 {
    text-align: center;
}
p.info {
    text-align: center;
    font-size: 12px;
}
table {
    #border: medium solid maroon;
    margin: 0 auto;
    border-collapse: collapse;
	width: 75%;
}
table tr:nth-child(odd) {
    background-color: #ccc;
}
th {
    background: #34495e;
    color: white;
}
th.d0 {
	background-color: #527a7a;
}
tr {
    border-bottom: 1px solid silver;
}
td {
    padding: 0px 10px 0px 10px;
	text-align: center;
}
.status1 {
	color: green;
	font-family: Verdana, Arial;
    text-align: center;
    background: url("data:image/png;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHLuAAAAAAABd+0DAG7pCwAAAABZKACZXCoA/l0sAP9cKwD/WCgAYgAAAAAIeeELAXXpAgAAAAAAbOkAAHHrAAAAAAAAcdUHAHP3dwBw8LEAAAAAWCgAZ10rAPheLAD/XCsA9lkpAEMAAAAAAHLyqwBy9GYAULEDAAAAAAAAAAAAbrwDAHH1iwBz/P8AcvvmAAAAAFgmADVdKwDyXiwA/1wrANhXJwAmAAAAAABy+f0Ac/z5AG/xZQBm8gAAAAAAAHDyTwBy+/gAdP78AHP97wBw6i7/AAAAWyoA7F4rAP9cKgC/egAACABv6DAAc/3/AHT+/QBz+/kBcOEiAHHuCgBy970AdP7/AHT+/ABy/PEAcvJNAAAAAFopAMldKwD/WioAoQAAAAAAbu1dAHP8/wB0/vwAc/7/AG/ypAFy7CIAcvv/AHT+/ABz/v4AcPOrAAAAAFUmAB1aKQDFXSsA/1opAK5VJgAPAAAAAABx9b8AdP7/AHT+/ABy+/kAcfFYAHP+/wB0/vsAc/3/AG/fJXgGAAlaKQCyXSsA/14rAP9dKwD/WikAjJ4AAAIBcOk5AHP9/wB0/vsAc/38AHT5cwBz/v8AdP78AHP8/wAAAABYJwAzXCsA5V4sAP9eLAD+XiwA/1sqANdZKAAOAHH1EwBy+f8AdP78AHP9/ABx8GwAdP3/AHT+/ABy+/8Ab44EWiUAIlwrANVeLAD/XiwA/14sAP9bKgC/WyUACgFv6B0Acvv/AHT+/AB0/fwAcOo8AHP8/wB0/vsAdP3/AHDtYwAAAABZKQBvXCsA41wrAPxbKgDdVykBRwAAAAAAcfBzAHP+/wB0/vsAc/z9AHHsEgBz+uoAdP7+AHT+/ABy+t4Ac+MjAAAAAFkpADFZKQBKWCYAJwAAAAAAcukyAHL77wB0/v0Ac/7+AHL52QJu1gMAcvSCAHP9/wB0/vsAc/38AHL42ABt5kYAAAAAAAAAAAAAAAAAbu1fAHL76QBz/v0AdP77AHP9/wBx7lgAAAAAAG3mIQBy+M8Ac/39AHT++wB0/v0Ac/z/AHL36gBy+tEAc/rwAHP9/wB0/vwAdP78AHP9/wBy97gBe+4IAF7/AAAAAAAAdPA9AHL51ABz/f8AdP79AHT++wB0/v4AdP7/AHT+/QB0/vsAdP79AHP9/wBz+MMAcO4hAAAAABeLtwAATv8AAAAAAAFy7SYAcvaSAHL6+QBz/v8AdP7/AHP+/wB0/v8Ac/7/AHL68gBy9YMDdO4aAAAAAAAA/wAAAAAACYrgAABA/wAAAAAAAHLmBQJ48BwAcPBgAHDzlgB0+qYAcvWTAHHvUwF18RUAb+MEAAAAAABb/wAEf+kA/D8AAPY3AADGMwAAxjEAAIYwAACGMAAAjBgAAIwYAACMGAAAjjgAAIfwAACD4QAAwAEAAOADAADwBwAA/j8AAA%3D%3D") no-repeat;
}
.status2 {
	color: red;
	font-family: Verdana, Arial;
    text-align: center;
    background: url("data:image/png;base64,AAABAAEAEBAAAAEAIABoBAAAFgAAACgAAAAQAAAAIAAAAAEAIAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAHLuAAAAAAABd+0DAG7pCwAAAABZKACZXCoA/l0sAP9cKwD/WCgAYgAAAAAIeeELAXXpAgAAAAAAbOkAAHHrAAAAAAAAcdUHAHP3dwBw8LEAAAAAWCgAZ10rAPheLAD/XCsA9lkpAEMAAAAAAHLyqwBy9GYAULEDAAAAAAAAAAAAbrwDAHH1iwBz/P8AcvvmAAAAAFgmADVdKwDyXiwA/1wrANhXJwAmAAAAAABy+f0Ac/z5AG/xZQBm8gAAAAAAAHDyTwBy+/gAdP78AHP97wBw6i7/AAAAWyoA7F4rAP9cKgC/egAACABv6DAAc/3/AHT+/QBz+/kBcOEiAHHuCgBy970AdP7/AHT+/ABy/PEAcvJNAAAAAFopAMldKwD/WioAoQAAAAAAbu1dAHP8/wB0/vwAc/7/AG/ypAFy7CIAcvv/AHT+/ABz/v4AcPOrAAAAAFUmAB1aKQDFXSsA/1opAK5VJgAPAAAAAABx9b8AdP7/AHT+/ABy+/kAcfFYAHP+/wB0/vsAc/3/AG/fJXgGAAlaKQCyXSsA/14rAP9dKwD/WikAjJ4AAAIBcOk5AHP9/wB0/vsAc/38AHT5cwBz/v8AdP78AHP8/wAAAABYJwAzXCsA5V4sAP9eLAD+XiwA/1sqANdZKAAOAHH1EwBy+f8AdP78AHP9/ABx8GwAdP3/AHT+/ABy+/8Ab44EWiUAIlwrANVeLAD/XiwA/14sAP9bKgC/WyUACgFv6B0Acvv/AHT+/AB0/fwAcOo8AHP8/wB0/vsAdP3/AHDtYwAAAABZKQBvXCsA41wrAPxbKgDdVykBRwAAAAAAcfBzAHP+/wB0/vsAc/z9AHHsEgBz+uoAdP7+AHT+/ABy+t4Ac+MjAAAAAFkpADFZKQBKWCYAJwAAAAAAcukyAHL77wB0/v0Ac/7+AHL52QJu1gMAcvSCAHP9/wB0/vsAc/38AHL42ABt5kYAAAAAAAAAAAAAAAAAbu1fAHL76QBz/v0AdP77AHP9/wBx7lgAAAAAAG3mIQBy+M8Ac/39AHT++wB0/v0Ac/z/AHL36gBy+tEAc/rwAHP9/wB0/vwAdP78AHP9/wBy97gBe+4IAF7/AAAAAAAAdPA9AHL51ABz/f8AdP79AHT++wB0/v4AdP7/AHT+/QB0/vsAdP79AHP9/wBz+MMAcO4hAAAAABeLtwAATv8AAAAAAAFy7SYAcvaSAHL6+QBz/v8AdP7/AHP+/wB0/v8Ac/7/AHL68gBy9YMDdO4aAAAAAAAA/wAAAAAACYrgAABA/wAAAAAAAHLmBQJ48BwAcPBgAHDzlgB0+qYAcvWTAHHvUwF18RUAb+MEAAAAAABb/wAEf+kA/D8AAPY3AADGMwAAxjEAAIYwAACGMAAAjBgAAIwYAACMGAAAjjgAAIfwAACD4QAAwAEAAOADAADwBwAA/j8AAA%3D%3D") no-repeat;
}
.footer {
	font-size: 10px;
	color: #B0B0B0;
	font-family: Verdana, Arial;
    text-align: center;
}
</style>

</head>

<body>
<br>
<table>
  <tr>
    <th class="d0">OpenVPN Server</th>
    <th class="d0">Clients</th>
    <th class="d0">VPN IP</th>
	<th class="d0">Local IP</th>
	<th class="d0">Total Sent</th>
	<th class="d0">Total Received</th>
    <th class="d0">Uptime</th>
  </tr>
  <tr>
    <td><?php
exec("pgrep openvpn", $output, $return);
if ($return == 0) {
    echo '<div class="status1">'."Running".'</div>';
} else {
	echo '<div class="status2">'."Stopped".'</div>';
}
?></td>
    <td><?php echo count($clients);?></td>
    <td><?php $ip1 = exec("ifconfig tun0 | grep 'inet addr'| cut -d: -f2 | cut -d' ' -f1");echo $ip1;?></td>
    <td><?php $ip2 = exec("ifconfig eth0 | grep 'inet addr'| cut -d: -f2 | cut -d' ' -f1");echo $ip2;?></td>
	<td><?php $rx = exec("ifconfig tun0 | grep 'RX bytes'| cut -d: -f2 | cut -d' ' -f2,3");echo $rx;?></td>
	<td><?php $tx = exec("ifconfig tun0 | grep 'TX bytes'| cut -d: -f3 | cut -d' ' -f2,3");echo $tx;?></td>
	<td><?php $up = exec("ps -o etime -p $(pidof openvpn)");echo $up;?></td>
  </tr>
<tr>
<?php foreach ($headers as $th) { ?>
<th><?php echo $th?></th>
<?php } ?>
</tr>



<?php foreach ($clients as $client) { 
    $client[3] = date ('d/m/Y H:i', strtotime($client[3]));
    $client[6] = date ('d/m/Y H:i', strtotime($client[6]));
    $client[4] = number_format($client[4], 0, '', '.')/1 ." MB";
    $client[5] = number_format($client[5], 0, '', '.')/1 ." MB";
    $client[2] = preg_replace('/(.*):.*/', '$1', $client[2]);
    $i = 0;
?>
<tr>
<?php foreach ($client as $td) { ?>
<td align='<?php echo $tdalign[$i++] ?>'><?php echo $td?></td>
<?php } ?>
</tr>
<?php } ?>

</table>
<p class='info'>This page gets reloaded every minute.<br>Last update: 
<b><?php echo date ("d/m/Y H:i:s") ?></b></p>
<div class='footer'>________________________________________
<br>
<?php
//Gets the IP address
$ip = getenv("REMOTE_ADDR");
	echo "Your IP: " . $ip;
?>
<br>
<a style="font-size: 10px; color: #B0B0B0; font-family: Verdana, Arial;" href="./index.php?logout=1">Logout</a>
</div>
</body>
</html>
