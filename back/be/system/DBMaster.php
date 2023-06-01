<?PHP

class DBMaster{
	
	protected $connection = null;
	protected $systemConf = null;

	const SYSTEM_LOGIN_BADREQUEST = -1;
	const SYSTEM_LOGIN_BADUSER = 1;
	const SYSTEM_LOGIN_BADPASS = 2;
	const SYSTEM_LOGIN_USRLOCK = 3;
	const SYSTEM_LOGIN_USRLOGIN = 4;
	const SYSTEM_USER_USERCREATED = 5;
	const SYSTEM_USER_USEREXISTS = 6;
	const SYSTEM_USER_NOLOGIN = 7;
	const SYSTEM_USER_ACTIVATION_BADUSER = 8;
	const SYSTEM_USER_ACTIVATION_BADCODE = 9;
	const SYSTEM_USER_ACTIVATION_CODEEXPIRED = 10;
	const SYSTEM_LOGIN_USRUNLOCK = 11;
	const SYSTEM_REQUEST_BADID = 12;
	
	function __construct() {
		global $utils;
		if(!isset($utils)){
			$mainConf = json_decode(file_get_contents('../conf/main.json'),true);
			$env_conf = json_decode(file_get_contents('../conf/config_'.$mainConf['enviroment'].'.json'),true);			
			$this->systemConf = $env_conf;
		}else{
			$this->systemConf = $utils->system_config;
		}
		

		$db = $this->systemConf['db']['name'];
		if (isset($this->systemConf['db']['instance'])){
			$db .= '_'.$this->systemConf['db']['instance'];
		}
		$host = $this->systemConf['db']['host'];
		$connResult = $this->connect("mysql:dbname=$db;host=$host",$this->systemConf['db']['user'],$this->systemConf['db']['password']);
		if ($connResult['result'] == 0){
			echo json_encode(['DBError' => $connResult['result']]);
			exit();
		}
	}
	
	public function connect($dsn, $user, $pass){
		try{  
			$this->connection = new PDO($dsn, $user, $pass);
			return ['result' => 1];		
		} catch (PDOException $e) {
			$error = "Connection error: ".$e->getMessage();
			return ['result' => 0, 'error' => $error];
		}
	}
	
	public function tableExists($table) {
		if ($this->connection == null){
			return ['result' => 0, 'error' => 'Connection not set'];
		}
		// Try a select statement against the table
		// Run it in try/catch in case PDO is in ERRMODE_EXCEPTION.
		try {
			$result = $this->connection->query("SELECT 1 FROM $table LIMIT 1");
		} catch (Exception $e) {
			// We got an exception == table not found
			return FALSE;
		}

		// Result is either boolean FALSE (no table found) or PDOStatement Object (table found)
		return $result !== FALSE;
	}
	
	public function execQuery($query){
		$records = [];
		try{
			foreach($this->connection->query($query) as $row){
				$purgeRow = [];
				foreach($row as $key => $value){
					if (!is_int($key)){
						$purgeRow[$key] = $value;
					}
				}
				$records []=$purgeRow;
			}
		} catch (PDOException $e) {
			$error = "Query error: ".$e->getMessage();
			return ['result' => 0, 'error' => $error];
		}
		
		return ['result' => 1, 'records' => $records];
	}

	/*
		Recibe dos parametros:
		page -> con la pagina actual y
		r_page -> con la cantidad de registros por pagina
	*/
	public function paginate($params){
		$result = '';
		if (isset($params['page']) && isset($params['r_page'])){
			$records_by_page = $params['r_page'];
			$page = $params['page'];
			$result = ' limit '.( ($page - 1) * $records_by_page).', '.$records_by_page;
		}
		return $result;
	}

	/*
	Recibe un array de arrays de uno o dos registros. Ej:
		"order" :  [
					["id", "DESC"]
				   ]
	*/
	public function order($params){
		$result = '';
		if (isset($params['order'])){
			$orderItems = [];
			foreach($params['order'] as $order){
				$direction = 'ASC';
				if (isset($order[1])){
					$direction = $order[1];
				}
				$orderItems []= ' `'.$order[0].'` '.$direction;
			}
			if (count($orderItems) > 0){
				$result = ' ORDER BY '.implode(', ',$orderItems);
			}
		}
		return $result;
	}

	/*
		Recibe un array de objetos:
		"subqueries": [
			{
				"field" -> nombre del campo condicional
				"table" -> tabla a consultar
				"type" -> puede ser COUNT, MAX, MIN, si se omite es COUNT
				"alias" -> alias del campo, si se omite es field_table
				"mainfield" -> campo en la tabla principal, si se omite usa id
			}
		]
	*/
	public function getSubQuerys($params){
		$result = [];
		if (isset($params['subqueries'])){
			$orderTable = 1;
			foreach($params['subqueries'] as $subquery){
				if (isset($subquery['field']) && isset($subquery['table'])){
					$group = 'COUNT(*)';
					if (isset($subquery['type'])){
						$group = $type;
					}
					$mainfield = 'id';
					if (isset($subquery['mainfield'])){
						$mainfield = $subquery['mainfield'];
					}
					$resultText = '(SELECT '.$group.' FROM `'.$subquery['table'].'` as `t'.$orderTable.'` WHERE `t'.$orderTable.'`.`'.$subquery['field'].'` = `main`.`'.$mainfield.'`) as ';
					if (isset($subquery['alias'])){
						$resultText .= '\''.$subquery['alias'].'\'';
					}else{
						$resultText .= '\''.$subquery['field'].'_'.$subquery['table'].'\'';
					}
					$orderTable ++;
					$result []= $resultText;
				}
			}
		}
		return $result;
	}

	/*
		Recibe un array de objetos:
		"joins": [
			{
				"field" -> Campo o campos que agregar,
				"table" -> Tabla que une,
				"r_table" -> Tabla con la que se une, si se omite toma la principal
				"alias" -> Alias de los campos (opcional)
				"on" -> Array [Parte 1 -> table, Parte 2 -> table2 o main],
				"direction" -> Dirección del JOIN, si se omite usa LEFT
			}
		]
	*/
	public function getJoins($params){
		$result = [
			'selects' => [], 
			'tables' => ''
		];
		if (isset($params['joins'])){
			$tables = [];
			foreach($params['joins'] as $join){
				if (
					isset($join['field']) &&
					isset($join['table']) &&
					isset($join['on'])
				){
					if (is_array($join['field'])){
						foreach($join['field'] as $field_key => $field_value){
							$fieldText = '`'.$join['table'].'`.`'.$field_value.'`';
							if (isset($join['alias'][$field_key])){
								$fieldText .= ' as \''.$join['alias'][$field_key].'\'';
							}
							$result['selects'][] = $fieldText;
						}
					}else{
						$fieldText = '`'.$join['table'].'`.`'.$join['field'].'`';
						if (isset($join['alias'])){
							$fieldText .= ' as \''.$join['alias'].'\'';
						}
						$result['selects'][] = $fieldText;
					}

					if (!in_array($join['table'], $tables)){
						$tables [] = $join['table'];
						$direction = 'LEFT';
						if (isset($join['direction'])){
							$direction = $join['direction'];
						}
						$remotetable = 'main';
						if (isset($jon['r_table'])){
							$remotetable = $jon['r_table'];
						}
						$result['tables'] .= ' '.$direction.' JOIN `'.$join['table'].'` ON `'.$join['table'].'`.`'.$join['on'][0].'` = `'.$remotetable.'`.`'.$join['on'][1].'`';
					}

				}
			}
		};
		return $result;
	}

	public function getAll($table, $params, $validation_data){
		if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}
		
		$select = ['`main`.*'];
		$paginate = '';
		$order = '';
		$tables = '';

		if (isset($params) && !empty($params)){			
			$paginate = $this->paginate($params);
			$order = $this->order($params);
			$joins = $this->getJoins($params);
			$select = array_merge($select, $joins['selects'], $this->getSubQuerys($params));
			$tables = $joins['tables'];
		}

		$query = 'SELECT '.implode(', ',$select).' FROM `'.$table.'` as `main`'.$tables.$order.$paginate;
		$result = $this->execQuery($query);
		
		if ($paginate != ''){
			$total = $this->execQuery('SELECT COUNT(*) as `total` FROM `'.$table.'` as `main`'.$tables);
			$result['total'] = $total['records'][0]['total'];
		}
		
		$result['query'] = $query;
		return $result;
	}
	
	public function getOne($table, $params, $validation_data){
		if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}
		if ( !isset($params['id']) ){
            $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
        }
		
		$query = 'SELECT * FROM `'.$table.'` WHERE `id`='.$params['id'];

		return $this->execQuery($query);
		
	}

	public function find($table, $params, $validation_data){
		if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}
		
		if (!isset($params)){
			return $this->getAll($table, $params, $validation_data);
		}

		$paginate = $this->paginate($params);
		$order = $this->order($params);
		$joins = $this->getJoins($params);
		$tables = $joins['tables'];
		$select = array_merge(['`main`.*'], $joins['selects'], $this->getSubQuerys($params));
			

		if (isset($params['page']) && isset($params['r_page'])){
			unset($params['page']);
			unset($params['r_page']);
		}
		if (isset($params['order'])){
			unset($params['order']);
		}
		if (isset($params['subqueries'])){
			unset($params['subqueries']);
		}
		if (isset($params['joins'])){
			unset($params['joins']);
		}
		$filter = '';
		if (isset($params['filter'])){
			$filter = $params['filter'];
			unset($params['filter']);
		}

		$condition = $this->buildcondition($params);
		if (!empty($condition)){
			$condition = ' WHERE '.$condition;
			if ($filter != ''){
				$condition .= ' AND '.$filter;
			}
		}else if($filter != ''){
			$condition = ' WHERE '.$filter;
		}

		$query = 'SELECT '.implode(', ',$select).' FROM `'.$table.'` as `main`'.$tables.$condition.$order.$paginate;
		$result = $this->execQuery($query);
		

		if ($paginate != ''){
			$total = $this->execQuery('SELECT COUNT(*) as `total` FROM `'.$table.'` as `main`'.$tables.$condition);
			$result['total'] = $total['records'][0]['total'];
		}
		
		$result['query'] = $query;

		return $result;
		
	}
	/*
	function saveForm
	params:
			$fieldList [Array][Tabla => [Campo => Valor]] Lista de tablas con sus respectivos campos			
	*/
	public function saveForm($void, $fieldList){
		$resultSet = [];
		
		foreach($fieldList as $table => $record){
			if (is_array($record)){
				if (isset($record['id'])){
					$id = $record['id'];
					unset($record['id']);
				}else{
					$id = '';
				}
				$resultSet[$table] = $this->saveData($table, $record, $id);
			}
		}
		
		return $resultSet;
		
	}
	
	/*
	function save
	params:
		   $table [string] Nombre de la tabla
		   $fieldList [Array][Campo => Valor] Lista de campos
		   $validation_data [Array] Token de validación
	*/
	function save($table, $fieldList, $validation_data){
		if (!$this->checkValidation($validation_data)){
            $this->auditData(2,self::SYSTEM_USER_NOLOGIN,$validation_data,null);
            return [
                    'result' => 0,                        
                    'errorcode' => self::SYSTEM_USER_NOLOGIN,
                    'message' => 'Invalid token',
                    'token' => $validation_data
                    ];
		}
		
		
		if (isset($fieldList['id'])){
			$id = $fieldList['id'];
			unset($fieldList['id']);
		}else{
			$id = '';
		}

		$result = $this->saveData($table, $fieldList, $id);
		if ( is_array($result) ){
			return $result;
		}else{
			return ["result" => "1", "data" => $result];
		}
		
	}
	
	/*
	function saveData
	params:
			$table [string] Nombre de la tabla
			$fieldList [Array][Campo => Valor] Lista de campos
			$id [Integer][Opcional] Valor del id a actualizar
	*/
	public function saveData($table, $fieldList, $id = null){
		if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}

				
		if ($id != null){
			//EDITAR
			$valores = implode(', ', array_map(
												function ($v, $k) { 
													if (($v === 'NULL' || is_null($v)) && !is_bool($v) ){
														return sprintf("`%s`= NULL", $k);
													}else{
														return sprintf("`%s`='%s'", $k, $v); 
													}
												},
												$fieldList,
												array_keys($fieldList)
											)
							  );
			$query = 'UPDATE `'.$table.'` SET '.$valores.' WHERE `id`='.$id;
		}else{
			//CREAR
			$campos = implode(', ', array_map(
												function ($v, $k) { return sprintf("`%s`", $k); },
												$fieldList,
												array_keys($fieldList)
											)
							  );
			$valores = implode(', ', array_map(
												function ($v, $k) { 
													if (($v === 'NULL' || is_null($v)) && !is_bool($v)){
														return 'NULL';
													}else{
														return sprintf("'%s'", $v); 
													}
												},
												$fieldList,
												array_keys($fieldList)
											)
							  );
							  
			$query = 'INSERT INTO `'.$table.'`('.$campos.') VALUES ('.$valores.')';
			
		}
			
		$data = $this->connection->prepare($query);
		$data->execute();
		if ($id != null){
			$resultId = $id;
		}else{
			$resultId = $this->connection->lastInsertId();
		}
		
		/*
		if ($resultId == 0){
			echo 'Error: '.$query;
		}
		
		return ['result' => 0, 'data' => $query];*/
		
		$validateColum = $this->connection->query('SHOW COLUMNS FROM `'.$table.'` WHERE FIELD = "updated"');
		if ($validateColum->fetchColumn()){
			$this->connection->query('UPDATE `'.$table.'` SET `updated`="'.date('Y-m-d H:i:s').'" WHERE `id`='.$resultId);
		}
		
		return $resultId;
		
		
	}

	/*
	function deleteData
	params:
			$table [string] Nombre de la tabla
			$id [Integer] Valor del id a borrar
	*/
	public function deleteData($table, $id){
		if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}	
		
		$query = 'DELETE FROM `'.$table.'` WHERE `id`='.$id;
		return $this->connection->query($query);
	}

	/*
	function delete
	params:
		   $table [string] Nombre de la tabla
		   $fieldList [Array][Campo => Valor] Lista de campos || [Number] Id a borrar
		   $validation_data [Array] Token de validación
	*/
	function delete($table, $fieldList, $validation_data){
		if (!$this->checkValidation($validation_data)){
            $this->auditData(2,self::SYSTEM_USER_NOLOGIN,$validation_data,null);
            return [
                    'result' => 0,                        
                    'errorcode' => self::SYSTEM_USER_NOLOGIN,
                    'message' => 'Invalid token',
                    'token' => $validation_data
                    ];
		}
		
		if (is_array($fieldList)){
			if (isset($fieldList['id'])){
				$id = $fieldList['id'];
				unset($fieldList['id']);
			}else{
				$id = '';
			}
		}else{
			$id = $fieldList;
		}

		$result = $this->deleteData($table, $id);
		if ( is_array($result) ){
			return $result;
		}else{
			return ["result" => "1", "data" => $result];
		}
		
	}

	/*
	function auditData
	params:
		$auditLevel [Integer] Nivel mínimo de registro
		$eventType [Integer] Tipo de evento
		$data [Array] Información a registrar
		$usr [Integer][Opcional] Id del usuario que generó el evento
	*/
	public function auditData($auditLevel, $eventType, $data, $usr = null){
		if ($this->systemConf['api']['loglevel'] >= $auditLevel){
			
			$origin = '';

			foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key){
				if (array_key_exists($key, $_SERVER) === true){
					foreach (explode(',', $_SERVER[$key]) as $ip){
						$ip = trim($ip); // just to be safe

						if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false){
							$origin = $ip;
						}
					}
				}
			}			

			$payload = [
						'type' => $eventType,
						'data' => json_encode($data),
						'origin' => $origin
			];
			if ($usr != null){
				$payload['usr'] = $usr;
			}
			$this->saveData('system_audit', $payload, null);
		}
	}
	
	/*
	function checkValidation
	params:
		validation_data [Array] información de validación
	*/
	function checkValidation($validation_data){
		if ($validation_data == '' || !isset($validation_data['result']) || $validation_data['result'] != 1){
			return false;
		}else{
			return true;
		}
	}
	/*
	function buildcondition
	params:
		$queryParams [Array] array condicional
	*/
	function buildcondition($queryParams){
		return implode(' AND ', array_map(
												function ($v, $k) { 
													$lastChar = substr($k, -1);
													if ($lastChar != '=' && $lastChar != '>' && $lastChar != '<'){
														if ($lastChar == ')' || $lastChar == '`'){
															$mask = "%s ='%s'";	
														}else{
															$mask = "`%s`='%s'";
														}
														if (strtolower($v) == 'null'){
														  if ($lastChar == ')' || $lastChar == '`'){
															$mask = "%s IS NULL";	
														  }else{
															$mask = "`%s` IS NULL";
														  }	
														}
														return sprintf($mask, $k, $v); 
													}else{
														$mask = "`%s`%s'%s'";
														if (substr($k, 0, 1) == '(' || substr($k, 0, 1) == '`'){
															$mask = "%s %s'%s'";	
														}
														$fullCond = substr($k, -2);
														if ($fullCond == '>=' || $fullCond == '<=' || $fullCond == '!='){
															$k = substr($k, 0, -2);
															return sprintf($mask, $k, $fullCond,$v);
														}else if ($fullCond == '%='){
															$k = substr($k, 0, -2);
															return sprintf($mask, $k, 'LIKE',$v);
														}
														else{
															$k = substr($k, 0, -1);
															return sprintf($mask, $k, $lastChar,$v);
														}
													}
												},
												$queryParams,
												array_keys($queryParams)
											)
						);
	}
	
	function upload($table, $params, $validation_data){
		if ( !isset($_FILES['file']) ){
            $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
		}
		if (!getimagesize($_FILES["file"]["tmp_name"])){
            $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Wrong file'];
		}

		if (move_uploaded_file($_FILES["file"]["tmp_name"], 'repository/'.$params['repository'].'/'.$params['name'])) {
			return ['result' => 1, 'data' => 'File uploaded'];
		} else {
			return ['result' => 0, 'error' => 'Error uploading file'];
		}

	}

	function fileRemove($table, $params, $validation_data){
		if (!isset($params['repository']) || !isset($params['name'])){
			 $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
		}
		if (unlink('repository/'.$params['repository'].'/'.$params['name'])){
			return ['result' => 1, 'data' => 'File removed'];
		} else {
			return ['result' => 0, 'error' => 'Error removing file'];
		}
	}

}