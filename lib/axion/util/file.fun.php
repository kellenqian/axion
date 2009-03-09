<?php
	/**
	 * 文件操作函数定义文件
	 * @author [Alone] [Nick Wang] 〖alonedistian@gmail.com〗
	 */
	/*********************************************
	☆		  				更新说明							☆
	**********************************************
	☆															☆
	***********************************************/ 
	
	/***************************************************
	☆		  				IncludeFunction						☆
	 ***************************************************
	☆	1.FileReader			读取指定文件的内容；				☆
	☆	2.FileCopy				将文件按照指定的方式进行复制;	☆
	☆	3.CreatePath			按照指定的路径建立文件夹;		☆
	☆	4.CutPage				通用分页模块;						☆
	☆																	☆
	***************************************************/ 
	
	/**
	 * 读取指定文件的内容
	 *
	 * @param string $_str_fileURL 文件/链接地址的名称，建议为完全路径
	 * @return string 文件的内容
	 */
	function fileReader( $_str_fileURL ){
		
		$_str_thisPlace = "Function:FileReader >> ";
		$_void_result = false;
		
		//输入参数检测
		if( empty( $_str_fileURL ) )
			return $_void_result;
		
		//打开文件
		$_hd_fp = @fopen( $_str_fileURL , 'r' );
		
		//获取文件失败处理
		if( !$_hd_fp )
			//Return
			return $_void_result;
		
		//获取文件
		$_void_result = @stream_get_contents( $_hd_fp );
		
		//获取文件失败处理
		if( !$_void_result ){
			return false;
		
		return $_void_result;
		
	}//function fileReader
	
	/**
	 * 将文件按照指定的方式进行复制
	 *
	 * @param unknown_type $strFileName ：源文件路径名
	 * @param unknown_type $strFileAimName ：目的文件名
	 * @param unknown_type $strFileAimPath ：目的路径
	 * @param unknown_type $isOverWrite ：是否覆盖已存在的文件
	 * @return array $arrFileContent : 存储文件的详细内容的数组
	 */
	function fileCopy( $strFileName, $strFileAimName = "", $strFileAimPath="", $isOverWrite = false )
	{
		//目的文件的详细信息
		$arrFileContent = array();
		
		if( !file_exists( $strFileName ) )
			return false;
		//判断是否将文件存储到新目录中
		if( !empty( $strFileAimPath ) )
		{
			if( !is_dir( $strFileAimPath ) )
			{
				$strFileAimPath = $this->CreatePath( $strFileAimPath );
				if( !$strFileAimPath )
					return false;
			}//end if
		}//if
		else 
			$strFileAimPath = dirname( $strFileName );
		//判断是否制定了新的文件名
		if( !empty( $strFileAimName ) )
			$strFileAimPath .= $strFileAimName;
		else 
			$strFileAimPath .= basename( $strFileName );
		
		if( !$isOverWrite )
		{
			if( file_exists( $strFileAimPath ) )
				return false;
		}//end if
		
		if( !copy( $strFileName, $strFileAimPath ) )
			return false;
		$arrFileContent['name'] = basename( $strFileAimPath );
		$arrFileContent['path'] = dirname( $strFileAimPath );
		$arrFileContent['type'] = filetype( $strFileAimPath );
		$arrFileContent['size'] = filesize( $strFileAimPath );
		return $arrFileContent;
	}//end function fileCopy
	
	/**
	 * 按照指定的路径建立文件夹
	 *
	 * @param string $strPath ： 文件路径地址
	 * @return boolean : 标示操作成功或者失败
	 */
	function createPath( $strPath )
	{
		$arrPath = array();
		$strTempPath = "";
		if( is_dir( $strPath ) ) 
			return true;
		else 
		{
			$arrPath = explode( "/".$strPath );
			foreach ( $arrPath as $strSinglePath )
			{
				$strTempPath = $strTempPath.$strSinglePath."/";
				if( is_dir( STR_DOCUMENT_ROOT.$strTempPath ) )
					continue;
				else 
					mkdir( STR_DOCUMENT_ROOT.$strTempPath );
			}//end foreach
		}//end if
		return true;
	}// end function createPath
					
	/**
	 * 分页函数
	 * Desc:通用分页模块
	 * @param integer $int_No:总共行数
	 * @param string $str_address:目标页地址
	 * @param integer $int_cPage:当前页面编号
	 * @param array $arr_pageParas:页面参数
	 * @param integer $int_iNo:在每个页面中显示的行数
	 * @param string $str_pTag:用于指定页码编号的标识串
	 * @return array : 页码信息数组
	 */
	function cutPage( $int_No, $str_address, $int_cPage = 0, $arr_paras = null, $int_iNo = INT_CUT_PAGE_LINE, $str_pTag = 'page' )
	{
		$_arr_pageInfo = array( 'first' => '', 'fBack' => '', 'back' => '', 'nosArray' => array(), 'adv' => '', 'fAdv' => '', 'end' => '' );
		$str_address .= "?";
		$_int_page = null;
		$_int_cFirstPage = null;
		$_int_cLastPage = null;
		
		//整理地址信息
		foreach ( $arr_paras as $_str_key => $_void_value ) 
		{
			if( $_str_key != 'page' )
				$str_address .= "{$_str_key}=" . $_void_value ."&amp;";
		}//end foreach
		
		//获取总页数
		$_int_page = ceil( $int_No/ $int_iNo );
		if( $_int_page ==0 )
			return $_arr_pageInfo;
		if( $int_cPage >= $_int_page || $int_cPage < 0 )
			$int_cPage = 0;
	
		//生成“首页”链接地址
		$_arr_pageInfo['first'] = $str_address . "{$str_pTag}=0";
		//生成“快退”链接地址
		$_arr_pageInfo['fBack'] = $str_address . "{$str_pTag}=" . ( ( $int_cPage - 5 ) > 0 ? ( $int_cPage - 5 ) : 0 );
		//生成“上一页”链接地址
		$_arr_pageInfo['back'] = $str_address . "{$str_pTag}=" . ( ( $int_cPage - 1 ) > 0 ? ( $int_cPage - 1 ) : 0 );
		//生成“下一页”链接地址
		$_arr_pageInfo['adv'] = $str_address . "{$str_pTag}=" . ( ( $int_cPage + 1 ) >= $_int_page ? $int_cPage : ( $int_cPage + 1 ) );
		//生成“快进”链接地址
		$_arr_pageInfo['fAdv'] = $str_address . "{$str_pTag}=" . ( ( $int_cPage + 5 ) >= $_int_page ? ( $_int_page - 1 ) : ( $int_cPage + 5 ) );
		//生成“尾页”链接地址
		$_arr_pageInfo['end'] = $str_address . "{$str_pTag}=" . ( $_int_page - 1 );
		
		/**
		 * 生成中间页码信息
		 */
		//获取列表中的第一页的页码
		$_int_cFirstPage = ( ( $int_cPage - 4 ) > 0 ? ( $int_cPage - 4 ) : 0 );
		if( ( $_int_cFirstPage + 9 ) >= $_int_page )
		{
			$_int_cLastPage = $_int_page - 1;
			$_int_cFirstPage = ( ( $_int_cLastPage - 9 ) > 0 ? $_int_cLastPage - 9 : 0 );
		}//if
		else 
			$_int_cLastPage = $_int_cFirstPage + 9;
			
		for( $_int_i = $_int_cFirstPage; $_int_i <= $_int_cLastPage; $_int_i ++ )
			$_arr_pageInfo['nosArray'][] = array( 'no' => $_int_i + 1, 'link' => $str_address . "{$str_pTag}={$_int_i}" );
			
		$_arr_pageInfo['nosArray'][$int_cPage]['link'] = '#';
		return $_arr_pageInfo;
	}//end function cutPage
	
	/**
	 * Finish
	 * o._.o
	 */
?>