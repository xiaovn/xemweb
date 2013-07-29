<? ob_start(); ?>
<?
session_start();
//mysql_query("set names 'utf8' ");
header('Content-Type: text/html; charset=UTF-8');
require_once("./func/mysql.php");
include_once "./func/func.php";
$info = "Tài khoản hoặc mật khẩu không đúng";
$first = 1;
if ( $_GET['act'] == "login" )
{
  $usernames = addslashes( $_POST['username'] );
	$nnpage = "index";
	$nnpage = addslashes( $_POST['nextpage'] );
	if($usernames == null)
	{
		header("Location: login.php");
	}
	$sql_query = @mysql_query("SELECT * FROM xdata_account WHERE username='{$usernames}' or xid='{$usernames}'");
	$member = @mysql_fetch_array( $sql_query );
	$pwd = addslashes( $_POST['password'] ); 
	$newpass2 = md5($pwd);
	
    if ( @mysql_num_rows( $sql_query ) <= 0 )
    {
		#Start Sync UCenter
			$info = "Xin lỗi, tài khoản của bạn không tồn tại<br>Vui lòng liên hệ quản trị viên để biết thêm chi tiết.<br>";
			echo formlogin(0,$info,1);
		
    }
    if ( $newpass2 != $member['password'] )
    {
        session_destroy(); 
		formlogin(0,$info,1);
        exit;
    }
    // Khởi động phiên làm việc (session)
	$_SESSION['user_id'] = $member['xid'];
	$time = date("Y-m-d H:i:s");
	$action = "Login";
	$result = 1;
	$ip=$_SERVER['REMOTE_ADDR'];
	$xid = $member['xid'];
	@$a=mysql_query("INSERT INTO xdata_log(xid,date,ip,action,result) VALUES ('{$xid}', '{$time}','{$ip}','{$action}','{$result}')");
	
	$sql_query = @mysql_query("SELECT * FROM xdata_account WHERE username='{$usernames}' or xid='{$usernames}'");
	$array = @mysql_fetch_array( $sql_query );
	//Kiểu trả về lưu vào session
	$_SESSION['xid'] = $array['xid'];
	$_SESSION['username'] = $array['username'];
	$_SESSION['lastlogin'] = $array['lastlogin'];
	$_SESSION['lastlogin_ip'] = $array['lastlogin_ip'];
	$_SESSION['group'] = $array['group'];
	$_SESSION['login_status'] = $array['login_status'];
	
	//addlogin($member['xid'],$time,$ip,$action,$result);
	if(get("firstlog",$xid) == 1)
	{
		header("Location: changeusername.php");
	}
	else
	{
    // Thông báo đăng nhập thành công
	if($nnpage != null)
	{
    header("Location: ".$nnpage.".php");
	}
	else
		header("Location: index.php");
	//echo "Đăng nhập thành công";
	}
}
else
{
	echo formlogin(1,$info,0);
}
function formlogin($first,$info,$error)
{
$npage = $_GET['ids'];
?>
<?include_once "./module/header.php"?>
      <ul id="topbar">
        <li><a class="button white fl" title="preview" href="index.html"><span class="icon_single preview"></span></a></li>
		<li class="s_1"></li>
        <li class="logo"><strong>XIAO JSC</strong> SERVICE CONTROL PANEL</li>
      </ul>
	
	<div id="content-login">
	<div class="logo"></div>
	<h2 class="header-login">Đăng nhập </h2>
	<form id="box-login" action="login.php?act=login" method="post">
		<input type="hidden" name="nextpage" value="<?=$npage?>">
		<p>
			<label class="req"> Tài khoản </label>
			<br/>
			<input type="text" name="username" value="" id="username"/>
		</p>
		<p>
			<label class="req"> Mật khẩu </label>
			<br/>
			<input type="password" name="password" value="" id="password"/>
		</p>
		<p class="fl">
			<input type="checkbox" name="remember" value="1" id="remember"/>
			<label class="rem"> Luôn giữ đăng nhập </label>
		</p>
		<p class="fr">
			<input type="submit" value="Đăng nhập" class="button themed" id="login"/>
			
		</p>
		
		<div class="clear"></div>
	</form>
	<a class="forgot" href="#"> Quên mật khẩu? </a> 
	<a class="forgot" href="#"> Đăng ký? </a> 
	<?
	if($first == 1 && $error == 0)
	{
	?>
	<span class="message information">Bạn có thể sử dụng Xiao ID để đăng nhập.</span>
	<?
	}
	else
	{
	?>
	<span class="message <?if($error == 0){?>information<?}else{?>warning<?}?>"><?=$info?></span>
	<?
	}
	?>
	</div>
	
<?include_once "./module/footer.php"?>
<?
}
?><? ob_flush(); 
