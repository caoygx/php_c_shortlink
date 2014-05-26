<?php
class CommonAction extends Action {
	protected $m = null;
	protected $user = array(); //用户信息数组
	protected $uid = 0; //用户uid
	protected $u = null;
	public function __construct($pre = ''){
		parent::__construct();
		if($pre){ //有表前缀
			new CommonModel($this->getActionName(),$pre);
		}else{
			try{
				$this->m = D($this->getActionName()); //实例化model
				if(empty($this->m)){
					$this->m = M($this->getActionName());
				}
			}catch(Exception $e){
				//echo $e->getMessage();
				//
			}
		}
		//简单的权限验证操作
		if (method_exists ( $this, '_permissionAuthentication' )) {
			$this->_permissionAuthentication ();
		}
		
		//if(empty($this->m)) exit($this->getActionName().'对象不存在');
		
	}
	
	public function msg($result,$text = '',$url=''){
		if(false !== $result){
			$this->success($text."成功");
		}else{
			$this->error($text."失败");
		}
	}
	
	//获取用户登录凭证信息
	function getAuth(){
		$u = getUserAuth();
		$this->user = getUserAuth();;
		$this->uid = $this->user['uid'];
		//return $u;
	}							
	
	function _initialize() {
		$this->getAuth();
		
		//用户信息
		$this->u = D('User');
		if(!empty($this->uid)){
			
			$r = $this->u->find($this->uid);
			//echo $this->u->getLastSql();
			//var_dump($r);
			$this->assign ( 'user', $r );
		}
		
		if(C('USER_AUTH_ON')){
			import ( '@.ORG.Util.RBAC_WEB' );
			$app = 'USER';
			
		}else{
			import ( 'ORG.Util.RBAC' );
			$app = APP_NAME;
		}
		//var_dump(RBAC::checkAccess());
		//exit('xx');
		if(RBAC::checkAccess()){
			//认证，临时简化版
			$this->getAuth();
			//var_dump($this->user);exit('s');
			if(empty($this->user->uid)){ //是否登录
				redirect (C ( 'USER_AUTH_GATEWAY' ) );
				
				//是否有权限
			}
			//var_dump(RBAC::AccessDecision ());exit('x');
			
			if (! RBAC::AccessDecision ($app)) {
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					redirect (C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					$this->error ( L ( '_VALID_ACCESS_' ) );
				}
			}
		}else{
			//不认证
		}
			
			
			//exit;
		// 用户权限检查
		/*if (C ( 'USER_AUTH_ON' ) && !in_array(MODULE_NAME,explode(',',C('NOT_AUTH_MODULE')))) {
			import ( 'ORG.Util.RBAC' );
			if (! RBAC::AccessDecision ()) {
				//检查认证识别号
				if (! $_SESSION [C ( 'USER_AUTH_KEY' )]) {
					//跳转到认证网关
					redirect ( PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
				}
				// 没有权限 抛出错误
				if (C ( 'RBAC_ERROR_PAGE' )) {
					// 定义权限错误页面
					redirect ( C ( 'RBAC_ERROR_PAGE' ) );
				} else {
					if (C ( 'GUEST_AUTH_ON' )) {
						$this->assign ( 'jumpUrl', PHP_FILE . C ( 'USER_AUTH_GATEWAY' ) );
					}
					// 提示错误信息
					$this->error ( L ( '_VALID_ACCESS_' ) );
				}
			}
		}*/
		
		$this->keywords = C('keywords');
		$this->description = C('description');
		
	}

		
	public function index() {
               
                        
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		
		
		$name=$this->getActionName();
		//$model = D ($name);
		if (! empty ( $this->m )) {
			$this->_list ( $this->m, $map );
		}
		$this->display ();
		return;
	}
	
	//有连接表显示列表
	public function indexLink($option=array()) {
		//列表过滤器，生成查询Map对象
		$map = $this->_search ();
		if (method_exists ( $this, '_filter' )) {
			$this->_filter ( $map );
		}
		$name=$this->getActionName();
		//$model = D ($name);
		if (! empty ( $this->m )) {
			if($option['join']){
				$this->_listLink ( $this->m, $map,$option );
			}else{
				$this->_list($this->m,$map);
			}
		}
		$this->display ();
		return;
	}
	
	/**
     +----------------------------------------------------------
	 * 取得操作成功后要返回的URL地址
	 * 默认返回当前模块的默认操作
	 * 可以在action控制器中重载
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	function getReturnUrl() {
		return __URL__ . '?' . C ( 'VAR_MODULE' ) . '=' . MODULE_NAME . '&' . C ( 'VAR_ACTION' ) . '=' . C ( 'DEFAULT_ACTION' );
	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param string $name 数据对象名称
     +----------------------------------------------------------
	 * @return HashMap
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _search($name = '') {
		//生成查询条件
		if (empty ( $name )) {
			$name = $this->getActionName();
		}
		//$name=$this->getActionName();
		//$model = D ( $name );
		//var_dump($this->m);exit;
		$map = array ();
		foreach ( $this->m->getDbFields () as $key => $val ) {
			if (isset ( $_REQUEST [$val] ) && $_REQUEST [$val] != '') {
				$map [$val] = trim($_REQUEST [$val]);
			}
		}
		return $map;

	}

	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _list($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		//$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$pk = $model->getPk();
		$count = $model->where ( $map )->count ( $pk );//echo $model->getlastsql();exit('count');
		if ($count > 0) {
			import ( "ORG.Util.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//echo C('PAGE_STYLE');exit;
			$p->style = C('PAGE_STYLE');//设置风格
			$p->setConfig('theme',' %upPage%    %linkPage%   %downPage%  ');
			//分页查询数据
			//var_dump($p->listRows);exit;
			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			if (method_exists ( $this, '_join' )) {
				$this->_join ( $voList );
			}
			
			//echo $model->getlastsql();exit('x');
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}
	
	
	/**
     +----------------------------------------------------------
	 * 根据表单生成查询条件
	 * 进行列表过滤
	 * 返回结果，不输出
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _getlist($model, $map, $sortBy = '', $asc = false) {
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		//$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		//取得满足条件的记录数
		$pk = $model->getPk();
		$count = $model->where ( $map )->count ( $pk );
		if ($count > 0) {
			import ( "ORG.Util.Page" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new Page ( $count, $listRows );
			//echo C('PAGE_STYLE');exit;
			$p->style = C('PAGE_STYLE');//设置风格
			//分页查询数据
			//var_dump($p->listRows);exit;
			$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			//echo $model->getlastsql();
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			return array('list' => $voList ,
						 'sort' => $sort, 
						 'order' => $order);
			//$this->assign ( 'sortImg', $sortImg );
			//$this->assign ( 'sortType', $sortAlt );
			//$this->assign ( "page", $page );*
			/*$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );*/
		}
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}

	/**
     +----------------------------------------------------------
	 * 连接查询列表显示
	 * 进行列表过滤
     +----------------------------------------------------------
	 * @access protected
     +----------------------------------------------------------
	 * @param Model $model 数据对象
	 * @param HashMap $map 过滤条件
	 * @param string $sortBy 排序
	 * @param boolean $asc 是否正序
     +----------------------------------------------------------
	 * @return void
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	protected function _listLink($model,$map,$option=array(),  $sortBy = '', $asc = false) {
		
		extract($option);
		
		$field || $field = "*";
		$table || $table = $model->getTableName();
		//$table = "{$this->trueTableName} j";
		//$r = $this->table($table)->field($field)->join($join)->where($map)->count();
		
		//dump($r);
		//return $r;
		
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		//$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		//取得满足条件的记录数
		if($sql){
			$sqlCount = getCountSql($sql);
			//处理map查询条件
			$count = $db->query($sqlCount);
		}else{
			$pk = $model->getPk();
			$count = $model->table($table)->field($field)->join($join)->where($map)->count( $pk );
		}
		if ($count > 0) {
			import ( "ORG.Util.XPage" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new XPage ( $count, $listRows );
			//echo C('PAGE_STYLE');exit;
			//$s =  rand(1,25);echo $s;
			$p->style = C('PAGE_STYLE');//设置风格
			//分页查询数据
			if ($sql) {
				//处理map查询条件
				$voList = $model->query(sql. "`" . $order . "` " . $sort.$p->firstRow . ',' . $p->listRows);
			}else{
				$voList = $model->table($table)->field($field)->join($join)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			}
			//echo $model->getlastsql();
			
			
			//高亮关键字
			if(C('highLightKeyword') && $_REQUEST['keyword']){
				$keyword = $_REQUEST['keyword'];
				foreach($voList as $k => $v){
					$voList[$k]['jtitle'] = hightLightKeyword($v['jtitle'],$keyword);
					$voList[$k]['request'] = hightLightKeyword($v['request'],$keyword);
					$voList[$k]['ctitle'] = hightLightKeyword($v['ctitle'],$keyword);
				}
			}
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}
	
	function advancedList($model,$map,$join,$field="*",$table="",  $sortBy = '', $asc = false) {
		
		$option['join'] = $join; //有查询条件，开启连接查询
		
		$field || $field = "*";
		$table || $table = $model->getTableName();
		//$table = "{$this->trueTableName} j";
		//$r = $this->table($table)->field($field)->join($join)->where($map)->count();
		
		//dump($r);
		//return $r;
		
		//排序字段 默认为主键名
		if (isset ( $_REQUEST ['_order'] )) {
			$order = $_REQUEST ['_order'];
		} else {
			$order = ! empty ( $sortBy ) ? $sortBy : $model->getPk ();
		}
		//排序方式默认按照倒序排列
		//接受 sost参数 0 表示倒序 非0都 表示正序
		//$setOrder = setOrder(array(array('viewCount', 'a.view_count'), 'a.id'), $orderBy, $orderType, 'a');
		if (isset ( $_REQUEST ['_sort'] )) {
			$sort = $_REQUEST ['_sort'] ? 'asc' : 'desc';
		} else {
			$sort = $asc ? 'asc' : 'desc';
		}
		
		//取得满足条件的记录数
		$pk = $model->getPk();
		
		if ($option['num'] ){ //限制取几条记录，直接返回指定条记录
			if($sql){
				$voList = $model->query($sql);	
			}elseif($option['join']){
				$voList = $model->table($table)->field($field)->join($join)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			}else{
				$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			}
			return $voList;
		}else{ //分页
		
			if($sql){
				$count = $count = $model->query(getCountSql($sql));
				$count = $count[0];
			}elseif($option['join']){
				$count = $model->table($table)->field($field)->join($join)->where($map)->count( $pk );
				
			}else{
				$count = $model->where ( $map )->count ( $pk );
			}
			if($count<0) return;
			import ( "ORG.Util.XPage" );
			//创建分页对象
			if (! empty ( $_REQUEST ['listRows'] )) {
				$listRows = $_REQUEST ['listRows'];
			} else {
				$listRows = '';
			}
			$p = new XPage ( $count, $listRows );
			//echo C('PAGE_STYLE');exit;
			//$s =  rand(1,25);echo $s;
			$p->style = C('PAGE_STYLE');//设置风格
			//分页查询数据

			if($sql){
				$voList = $model->query($sql);	
			}elseif($option['join']){
				$voList = $model->table($table)->field($field)->join($join)->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			}else{
				$voList = $model->where($map)->order( "`" . $order . "` " . $sort)->limit($p->firstRow . ',' . $p->listRows)->select ( );
			}
			
			//高亮关键字
			if(C('highLightKeyword') && $_REQUEST['keyword']){
				$keyword = $_REQUEST['keyword'];
				foreach($voList as $k => $v){
					$voList[$k]['jtitle'] = hightLightKeyword($v['jtitle'],$keyword);
					$voList[$k]['request'] = hightLightKeyword($v['request'],$keyword);
					$voList[$k]['ctitle'] = hightLightKeyword($v['ctitle'],$keyword);
				}
			}
			//分页跳转的时候保证查询条件
			foreach ( $map as $key => $val ) {
				if (! is_array ( $val )) {
					$p->parameter .= "$key=" . urlencode ( $val ) . "&";
				}
			}
			//分页显示
			$page = $p->show ();
			//列表排序显示
			$sortImg = $sort; //排序图标
			$sortAlt = $sort == 'desc' ? '升序排列' : '倒序排列'; //排序提示
			$sort = $sort == 'desc' ? 1 : 0; //排序方式
			//模板赋值显示
			$this->assign ( 'list', $voList );
			$this->assign ( 'sort', $sort );
			$this->assign ( 'order', $order );
			$this->assign ( 'sortImg', $sortImg );
			$this->assign ( 'sortType', $sortAlt );
			$this->assign ( "page", $page );
		}
		Cookie::set ( '_currentUrl_', __SELF__ );
		return;
	}
	
	function insert() {
		//B('FilterString');
		$name=$this->getActionName();
		//$model = D ($name);
		if (false === $this->m->create ()) {
			$this->error ( $this->m->getError () );
		}
		//保存当前数据对象
		$list=$this->m->add ();
		//echo $this->m->getLastSql();exit;
		if ($list!==false) { //保存成功
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('新增成功!');
		} else {
			//失败提示
			$this->error ('新增失败!');
		}
	}

	public function add() {
		if (method_exists ( $this, '_replacePublic' )) {
			$this->_replacePublic ( $vo );
		}
		$this->display ();
	}

	function read() {
		$this->edit ();
	}

	function edit() {
		//exit('s');
		//$name=$this->getActionName();
		//$model = M ( $name );
		//var_dump($this->m);exit;
		$id = $_REQUEST [$this->m->getPk ()];
		$vo = $this->m->getById ( $id );
		if (method_exists ( $this, '_replacePublic' )) {
			$this->_replacePublic ( $vo );
		}
		$this->assign ( 'vo', $vo );
		$this->display ('add');
	}

	function update() {
		//B('FilterString');
		$name=$this->getActionName();
		//$model = D ( $name );
		if (false === $this->m->create ()) {
			$this->error ( $this->m->getError () );
		}
		// 更新数据
		$list=$this->m->save ();
		if (false !== $list) {
			//成功提示
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('编辑成功!');
		} else {
			//错误提示
			$this->error ('编辑失败!');
		}
	}
	/**
     +----------------------------------------------------------
	 * 默认删除操作
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws ThinkExecption
     +----------------------------------------------------------
	 */
	public function delete() {
		//删除指定记录
		$name=$this->getActionName();
		//$model = M ($name);
		if (! empty ( $this->m )) {
			$pk = $this->m->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				$list=$this->m->where ( $condition )->setField ( 'status', - 1 );
				if ($list!==false) {
					$this->success ('删除成功！' );
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
	}
	public function foreverdelete() {
		//删除指定记录
		$name=$this->getActionName();
		//$model = D ($name);
		if (! empty ( $this->m )) {
			$pk = $this->m->getPk ();
			$id = $_REQUEST [$pk];
			if (isset ( $id )) {
				$condition = array ($pk => array ('in', explode ( ',', $id ) ) );
				if (false !== $this->m->where ( $condition )->delete ()) {
					//echo $this->m->getlastsql();
					$this->success ('删除成功！');
				} else {
					$this->error ('删除失败！');
				}
			} else {
				$this->error ( '非法操作' );
			}
		}
		$this->forward ();
	}

	public function clear() {
		//删除指定记录
		$name=$this->getActionName();
		//$this->m = D ($name);
		if (! empty ( $this->m )) {
			if (false !== $this->m->where ( 'status=1' )->delete ()) {
				$this->assign ( "jumpUrl", $this->getReturnUrl () );
				$this->success ( L ( '_DELETE_SUCCESS_' ) );
			} else {
				$this->error ( L ( '_DELETE_FAIL_' ) );
			}
		}
		$this->forward ();
	}
	/**
     +----------------------------------------------------------
	 * 默认禁用操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	public function forbid() {
		$name=$this->getActionName();
		//$model = D ($name);
		$pk = $this->m->getPk ();
		$id = $_REQUEST [$pk];
		$condition = array ($pk => array ('in', $id ) );
		$list=$this->m->forbid ( $condition );
		if ($list!==false) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态禁用成功' );
		} else {
			$this->error  (  '状态禁用失败！' );
		}
	}
	public function checkPass() {
		$name=$this->getActionName();
		//$model = D ($name);
		$pk = $this->m->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $this->m->checkPass( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态批准成功！' );
		} else {
			$this->error  (  '状态批准失败！' );
		}
	}

	public function recycle() {
		$name=$this->getActionName();
		//$model = D ($name);
		$pk = $this->m->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $this->m->recycle ( $condition )) {

			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态还原成功！' );

		} else {
			$this->error   (  '状态还原失败！' );
		}
	}

	public function recycleBin() {
		$map = $this->_search ();
		$map ['status'] = - 1;
		$name=$this->getActionName();
		//$model = D ($name);
		if (! empty ( $this->m )) {
			$this->_list ( $this->m, $map );
		}
		$this->display ();
	}

	/**
     +----------------------------------------------------------
	 * 默认恢复操作
	 *
     +----------------------------------------------------------
	 * @access public
     +----------------------------------------------------------
	 * @return string
     +----------------------------------------------------------
	 * @throws FcsException
     +----------------------------------------------------------
	 */
	function resume() {
		//恢复指定记录
		$name=$this->getActionName();
		//$model = D ($name);
		$pk = $this->m->getPk ();
		$id = $_GET [$pk];
		$condition = array ($pk => array ('in', $id ) );
		if (false !== $this->m->resume ( $condition )) {
			$this->assign ( "jumpUrl", $this->getReturnUrl () );
			$this->success ( '状态恢复成功！' );
		} else {
			$this->error ( '状态恢复失败！' );
		}
	}


function saveSort() {
		$seqNoList = $_POST ['seqNoList'];
		if (! empty ( $seqNoList )) {
			//更新数据对象
		$name=$this->getActionName();
		//$model = D ($name);
			$col = explode ( ',', $seqNoList );
			//启动事务
			$this->m->startTrans ();
			foreach ( $col as $val ) {
				$val = explode ( ':', $val );
				$this->m->id = $val [0];
				$this->m->sort = $val [1];
				$result = $this->m->save ();
				if (! $result) {
					break;
				}
			}
			//提交事务
			$this->m->commit ();
			if ($result!==false) {
				//采用普通方式跳转刷新页面
				$this->success ( '更新成功' );
			} else {
				$this->error ( $this->m->getError () );
			}
		}
	}
	
	protected function msgText($nextModel,$nextModelText,$id){
		$app = __APP__;
		$url = __URL__;
		
		return "发布成功!  <a href='$app/$nextModel/add'>发布{$nextModelText}信息</a> <a href='$url/edit/id/$id'>返回修改信息</a> <a href='$url/'>返回列表</a>";
	}
	
	public function show(){
		$id = $this->_get('id');
		$vo = $this->m->getById ( $id );
		if (method_exists ( $this, '_show' )) {
			$this->_show ( $vo );
		}
		
		$this->assign ( 'vo', $vo );
    	$this->display();
    }
	
	
	
	//==================自己加的==================//
	
	//保存添加和编辑
	function save() {
		//var_dump($this->isAjax());exit;
	
		$id = $_REQUEST [$this->m->getPk ()];
		//$vo = $this->m->getById ( $id );

		if(empty($id)){
			$_POST['uid'] = $this->uid; //添加时默认加上用户id
			if (false === $this->m->create ()) {
				$this->error ( $this->m->getError () );
			}
			$r=$this->m->add ();
		}else{
			if (false === $this->m->create ()) {
				$this->error ( $this->m->getError () );
			}
			$r=$this->m->save ();
		}
		//保存当前数据对象
		
		//echo $this->m->getLastSql();//exit;
		if ($r!==false) { //保存成功
			$this->assign ( 'jumpUrl', Cookie::get ( '_currentUrl_' ) );
			$this->success ('保存成功!','',array("id" => $r,"keyxx" => "valuexx"));
		} else {
			//失败提示
			$this->error ('保存失败!');
		}
		
		
	}
	
	
	/**
	* @name 根据请求方式，显示对应的格式到页面
    * @param  数据  array $data
	* @param  格式类型  int $type    
    * @return   member
    */
	public function toview($data, $type='JSONP'){
		if(isset($_GET[C('VAR_JSONP_HANDLER')])){ //ajax返回,默认json格式
			$this->ajaxReturn($data,$type);
		}elseif($this->isAjax() ){ //jsonp格式
			//var_dump($data);exit;
			$this->ajaxReturn($data,"成功！",1);
		}else{ //html
			$this->assign ( 'vo', $data);
    		$this->display();
		}
		//elseif($xml) //xml返回
	}
	
 	
	//用户信息
	function userinfo(){
		if(empty($this->uid)) return;
		
		$u = M('User');
		$userinfo = $u->find($this->uid);
		if(!is_array($userinfo)) $userinfo = array();
		unset($userinfo['id']);
		unset($userinfo['pwd']);
		unset($userinfo['open_id']);
		unset($userinfo['bind']);
		
		$userinfo = json_encode($userinfo);
		$this->userinfo = $userinfo;
		
	}
	
	//右边栏
	function right(){
		$f = M('Family');
		$r = $f->where("status = 1")->order("num desc")->limit(5)->select();
		$this->listByNum = $r;
		
	}
	
	//设置标题
	function setTitle($title){
		$this->pageTitle = empty($title) ? C('SITE_TITLE') :  $title.'_'.C('SITE_TITLE');
		//$title && $title = $title."_"; 
		//$this->pageTitle = $title.C('SITE_TITLE');
	}
 
	
}
?>