<?php
	/**
	 * 图像处理相关函数定义文件
	 * @author [Alone] 〖alonedistian@gmail.com〗
	 */
	/*********************************************
	☆		  				更新说明							☆
	**********************************************
	☆															☆
	***********************************************/ 
	
	/***************************************************
	☆		  				IncludeFunction						☆
	 ***************************************************
	☆	1.imgRevocer		将图片转换为指定的大小。				☆
	☆	2.createPie			绘制饼型统计图。						☆
	☆	3.createChkPic		根据给定的字符串内容 生成验证码。	☆
	☆																	☆
	***************************************************/ 
	/**
	 * 图像文件类型转换
	 *
	 * @param string $str_imgSourceName 源文件名
	 * @param integer $int_width 转换后宽度
	 * @param integer $int_height 转换后高度
	 * @return image 转换后的图片
	 */
	function imgRecover( $str_imgSourceName, $int_width = null, $int_height = null )
	{
		//判断源文件是否存在
		if( !file_exists($str_imgSourceName ) )
			return false;
		
		//获取图像信息
		$_arr_imgContent = getimagesize( $str_imgSourceName );
		//判断文件是否为标准的且被PHP支持的图像文件
		if( !$_arr_imgContent )
			return false;
		
		//计算转换后的图像大小
		if( is_null( $int_width ) && is_null( $int_height ) )
		{
			$int_width = $_arr_imgContent[0];
			$int_height = $_arr_imgContent[1];
		}//if
		else if( is_null( $int_width ) )
			$int_width =  $int_height * $_arr_imgContent[0] / $_arr_imgContent[1];
		else if( is_null( $int_height ) )
			$int_height = $int_width * $_arr_imgContent[1] / $_arr_imgContent[0];
			
		switch ( $_arr_imgContent[2] )
		{
			case 1 :
					$bitImgSource = imagecreatefromgif( $str_imgSourceName );
					break;
				
			case 2 :
					$bitImgSource = imagecreatefromjpeg( $str_imgSourceName );
					break;
				
			case 3 :
					$bitImgSource = imagecreatefrompng( $str_imgSourceName );
					break;
				
			case 6 :
					$bitImgSource = imagecreatefromwbmp( $str_imgSourceName );
					break;
				
			default:
				return false;
		}//end switch
		$bitNewImage = imagecreatetruecolor( $int_width, $int_height );
		imagecopyresized( $bitNewImage, $bitImgSource, 0, 0, 0, 0, $int_width, $int_height, $_arr_imgContent[0], $_arr_imgContent[1] );
		return $bitNewImage;
	}//function imgRecover
		
	/**
	 * 绘制饼型统计图
	 *
	 * @param array $arr_paras 参数数组
	 * @example array ( array( 'name'=>'name1', 'value'=>'value1' ), array ( 'name'=>'name2', 'value'=>'value2' ) )
	 * @param integer $int_width 生成图像的宽度
	 * @param integer $int_height 生成图像的高度
	 * @return image 生成的图像结果对象
	 */
	function createPie( $arr_paras, $int_width, $int_height = '' )
	{
		//获取图片大小
		if( empty( $int_height ) )
			$int_height = $int_width;
		//生成整体背景图
		$img_background = imagecreatetruecolor( $int_width, $int_height + 50 );
		imagefilledrectangle( $img_background, 0, 0, $int_width, $int_height + 50, imagecolorallocate( $img_background, 0xff, 0xff, 0xff ) );
		//生成饼图部分背景图
		$img_pieBack = imagecreatetruecolor( $int_width, $int_height + 50 );
		//使用白色填充饼图背景
		imagefilledrectangle( $img_pieBack, 0, 0, $int_width, $int_height + 50, imagecolorallocate( $img_pieBack, 0xff, 0xff, 0xff ) );
		//生成文字颜色
		$_textImg = imagecolorallocate( $img_pieBack, 0xff, 0xff, 0xff );
		/**
		 * 计算统计信息
		 */
		//获取统计指标个数
		$int_paraNumber = count( $arr_paras );
		//存储统计指标总数
		$int_maxNumber = 0;
		foreach (  $arr_paras as $_arr_info )
			$int_maxNumber += $_arr_info['value'];
		//保存当前弧形角度
		$int_currentAngle = 0;
		
		//绘制饼图
		if( $int_maxNumber != 0 )
		{
			for( $_int_i = 0; $_int_i < $int_paraNumber; $_int_i ++ )
			{
				$_int_change = -1;
				/**
				 * 生成饼图颜色
				 */
				switch ( $_int_i % 3 )
				{
					case 1 :
				 		$_color = imagecolorallocate( $img_pieBack, ( 0 + $_int_i * 30 ), ( 75 + $_int_i * 18 ), ( 150 + $_int_i * 18 ) );
				 		break;
					case 0 :
				 		$_color = imagecolorallocate( $img_pieBack, ( 150 + $_int_i * 18 ), ( 0 + $_int_i * 30 ), ( 75 + $_int_i * 18 ) );
				 		break;
					case 2 :
				 		$_color = imagecolorallocate( $img_pieBack, ( 75 + $_int_i * 18 ), ( 150 + $_int_i * 18 ), ( 0 + $_int_i * 30 ) );
				 		break;
				}//switch
				
				//绘制图例
				imagefilledrectangle( $img_pieBack, $_int_i * ( $int_width / $int_paraNumber  ), $int_height, ( $_int_i + 1 ) * ( $int_width / $int_paraNumber ) - 5, $int_width+50, $_color );
				//绘制图例文字
				imagefttext( $img_pieBack, 7, 0, $_int_i * ( $int_width / $int_paraNumber  ) + 2, $int_height + 14, $_textImg, STR_DOCUMENT_ROOT . 'function/ARIALUNI.TTF', sprintf( '%s:', $arr_paras[$_int_i]['name'] ) );
				imagefttext( $img_pieBack, 7, 0, $_int_i * ( $int_width / $int_paraNumber  ) + 2, $int_height + 32, $_textImg, STR_DOCUMENT_ROOT . 'function/ARIALUNI.TTF', sprintf( '%.2f', $arr_paras[$_int_i]['value']/$int_maxNumber * 100 )."%" );
				//绘制扇形
				imagefilledarc( $img_pieBack, $int_width/2, $int_height/2, $int_width, $int_height/2, $int_currentAngle, ( $int_currentAngle+$arr_paras[$_int_i]['value']/$int_maxNumber*360 ), $_color, IMG_ARC_PIE );
				//记录当前使用的扇形角度	
				$int_currentAngle += $arr_paras[$_int_i]['value']/$int_maxNumber*360;
			}//for
		}//for
		
		//将扇形图形中的白色设置为透明
		imagecolortransparent( $img_pieBack, imagecolorallocate( $img_pieBack, 0xff, 0xff, 0xff ) );
		//绘制饼图中椭圆的下边界
		imageellipse( $img_background, $int_width/2, $int_height/2+7.5, $int_width+2, $int_height/2+7.5, imagecolorallocate( $img_background, 0x50, 0x50, 0x50 ) );
		//将饼图合并到背景中并产生3D效果
		for ( $_int_i = 10; $_int_i >= 0; $_int_i -- )
			imagecopymerge( $img_background, $img_pieBack, 0, 0+$_int_i, 0, 0,$int_width, $int_height + 50, 100 );
		//绘制饼图中椭圆的下边界
		imageellipse( $img_background, $int_width/2, $int_height/2, $int_width, $int_height/2, imagecolorallocate( $img_background, 0xc0, 0xc0, 0xc0 ) );
		
		return $img_background;
	}//function createPie
	
	/**
	 * Finish
	 * o._.o
	 */
	
/******************************Example************************	
 	//function ImgRevover
	$img = ImgRecover( '4.gif', 400, 300 );
	if( $img )
		imagepng( $img );
	else 	
		echo 'error';
		
	
	//function createPie
	$arr = array( );
	$arr[] = array( 'name' => 'NO.1', 'value' => '54' );
	$arr[] = array( 'name' => 'NO.2', 'value' => '74' );
	$arr[] = array( 'name' => 'NO.3', 'value' => '24' );
	$arr[] = array( 'name' => 'NO.4', 'value' => '58' );
	$arr[] = array( 'name' => 'NO.5', 'value' => '1' );
	$arr[] = array( 'name' => 'NO.6', 'value' => '19' );
	$obj = createPie( $arr, 500 );
	
	header('Content-type: image/png');
	imagepng( $obj );
	
	//function createChkPic
	imagepng( createChkPic( 'thdst', 'Backpic.jpg' ) );
	imagedestroy( $img_backPic );
*************************************************************/

?>