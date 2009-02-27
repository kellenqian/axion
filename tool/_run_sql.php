<html>
<head>
<title>MYSQL :: RESULT</title>
<meta name="Author" content="intLover" />
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<?php
	include( 'default.ini.php' );
	$str_dbName = '';
	$str_logName = '';
	$str_logPS = '';
	
	$arr_pageParas = getRequest();
	$str_hostName = ( empty( $arr_pageParas['str_hostName'] ) ? 'localhost' : $arr_pageParas['str_hostName'] ); //主机地址
	$int_hostPort = ( empty( $arr_pageParas['int_hostPort'] ) ? '3306' : $arr_pageParas['int_hostPort'] );	//端口号
	$str_logName = ( empty( $arr_pageParas['str_logName'] ) ? 'root' : $arr_pageParas['str_logName'] );	//登录名
	$str_logPS = ( empty( $arr_pageParas['str_logPS'] ) ? '123456' : $arr_pageParas['str_logPS'] );		//密码
	
?>
</head>
<body>
<form method='post' action='_run_sql.php'>
	HOST:<input type="text" name="str_hostName" value="<?=$str_hostName?>" />
	PORT:<input type="text" name="int_hostPort" value="<?=$int_hostPort?>" /><br
	/>NAME:<input type="text" name="str_logName" value="<?=$str_logName?>" />
	PS:&nbsp;&nbsp;<input type="password" name="str_logPS" value="<?=$str_logPS?>" /><br
	/>DB:&nbsp;&nbsp;<input type="text" name="str_dbName" value="<?=( empty( $arr_pageParas['str_dbName'] ) ? '' : $arr_pageParas['str_dbName'] )?>" /><br
	/>History:&nbsp;<textarea name="str_sqlQuerys"  id="history" cols='60' rows='5'><?=( empty( $arr_pageParas['str_sqlQuerys'] ) ? '' : $arr_pageParas['str_sqlQuerys'] )?>
	
	<?=( empty( $arr_pageParas['str_sql'] ) ? '' : $arr_pageParas['str_sql'] )?>
	
	</textarea><br 
	/><input type="button" onclick="document.getElementById( 'history' ).innerText = '';" value="clear"/><br
	/>Last:&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<textarea name='str_sql' cols='60' rows='5'><?=( empty( $arr_pageParas['str_sql'] ) ? '' : $arr_pageParas['str_sql'] )?></textarea><br	
	/><input type='submit'>
</form>
<?
if( empty( $arr_pageParas['str_dbName'] ) )
{
	echo "<span style='font-size:28px; color:red; '>Please select a database first</span>";
	exit();
}//if

$str_dbName = $arr_pageParas['str_dbName'];	//数据库名

$obj_processStatus = new ProcessStatus();
$obj_MySQL = new MySQL( $str_hostName, $int_hostPort, $str_logName, $str_logPS, $str_dbName, true );

if( !empty( $arr_pageParas['str_sql'] ) )
{
	$void_result = $obj_MySQL->querySQL( $arr_pageParas['str_sql'] );
	if( $void_result == false )
		print_r( $obj_MySQL->getAllData() );
	else 
	{
		if( is_array( $void_result ) )
		{
			foreach ( $void_result as $arr_result )
			{
				foreach (  $arr_result as $str_key => $str_result ) 
					echo "{$str_key} => '{$str_result}'<br />";
				echo "<hr />";
			}
		}
		else 
			echo "完成";
	}//else
}
?>
</body>
</html>
