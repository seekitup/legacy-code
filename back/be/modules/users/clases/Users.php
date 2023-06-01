<?PHP

class Users extends DBMaster{
	
	public function createUser($void, $params, $validation_data){
        
        if ( !isset($params['usr']) || !isset($params['password']) ){
            $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
        }
        
        $active = 0;
        if (isset($params['active'])){
            $active = $params['active'];
        }

        // include '../system/Security.php';
        $sec = new Security;
        $createInfo = $sec->createUser($params['usr'],$params['password'], $active, $validation_data);
        if ($createInfo['result'] == 0){
            return $createInfo;
        }
        $createResult = $this->saveData('users', ['id' => $createInfo['data']['id'], 'name' => $params['name'], 'lastname' => $params['lastname']], null);
        if (is_array($createResult)){
            return $createResult;
        }else{
           return ['result' => 1, 'data' => $createInfo['data']['id']]; 
        }
    }

    public function activateUser($void, $params){
        if ( !isset($params['user']) || !isset($params['code']) ){
            $con->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
        }

        include '../system/Security.php';
        $sec = new Security;
        return $sec->activateUser($params['user'],$params['code']);
        
    }

    public function getUserInfo($void, $params, $validation_data){
      
        if ( !isset($params['id']) ){
            $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
        }
        if (!$this->checkValidation($validation_data)){
            $this->auditData(2,self::SYSTEM_USER_NOLOGIN,$validation_data,null);
            return [
                    'result' => 0,                        
                    'errorcode' => self::SYSTEM_USER_NOLOGIN,
                    'message' => 'Invalid token',
                    'token' => $validation_data
                    ];
        }
        $usrInfo = [];
        foreach($this->connection->query("SELECT * FROM `users` WHERE `id` = ".$params['id']) as $record){
            $purgeRow = [];
				foreach($record as $key => $value){
					if (!is_int($key)){
						$purgeRow[$key] = $value;
					}
				}
            $usrInfo = $purgeRow;
        }
        if (empty($usrInfo)){
          $this->auditData(2,self::SYSTEM_REQUEST_BADID,['params' => $params, 'table' => 'users'],null);
            return [
                        'result' => 0,
                        'errorcode' => self::SYSTEM_REQUEST_BADID,
                        'message' => 'The user does not exist'
                    ]; 
        }else{
            foreach($this->connection->query("SELECT * FROM `system_login` WHERE `id` = ".$params['id']) as $record){
                $usrInfo['usr']= $record['usr'];
                $usrInfo['active']= $record['active'];
            }
            
            return [
                    'result' => 1,
                    'data' => $usrInfo
            ];
        }

    }

    function getOne($table, $params, $validation_data){
        if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}
		if ( !isset($params['id']) ){
            $this->auditData(1,self::SYSTEM_LOGIN_BADREQUEST,$params,null);
            return ['result' => 0, 'error' => 'Bad request'];
        }
		
		$query = 'SELECT `u`.*, `sl`.`usr`, `sl`.`active`, `sl`.`ext_id` FROM `users` as `u` LEFT JOIN `system_login` as `sl` ON `sl`.`id` = `u`.`id` WHERE `u`.`id`='.$params['id'];

		$result = $this->execQuery($query);
		
        return $result;
    }

    function find($table, $fieldList, $validation_data){
       $fieldList['joins'] = [
            [
                'field' => ['active', 'usr'],
                'table' => 'system_login',
                'on' => ['id', 'id']
            ]
           ];
        return parent::find($table, $fieldList, $validation_data);
    }

    function save($table, $fieldList, $validation_data){
                
        if (!isset($fieldList['id']) || empty($fieldList['id'])){
            $result = $this->createUser($table, $fieldList, $validation_data);
        }else{
        
            if (isset($fieldList['password']) && $fieldList['password'] != ''){
                include '../system/Security.php';
                $sec = new Security;
                $sec->setPasword($fieldList['id'], $fieldList['password']);
                unset($fieldList['password']);
            }
            $active = $fieldList['active'];
            $this->saveData('system_login',['active' => $active], $fieldList['id']);
            unset($fieldList['active']);
            unset($fieldList['usr']);
            unset($fieldList['ext_id']);

            $result = parent::save($table, $fieldList, $validation_data);
        }

        if ($result['result'] == 0 ){
			return $result;
		}
        
		return ["result" => "1", "data" => $result['data']]; 

    }

    function delete($table, $fieldList, $validation_data){
        $result = parent::delete($table, $fieldList, $validation_data);
        if ($result['result'] == '1'){
            $this->deleteData('system_login', $fieldList);
        }
        return $result;
    }
}