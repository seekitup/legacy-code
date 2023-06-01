<?PHP
use \Firebase\JWT\JWT;

class Security extends DBMaster{
    
    function validatetoken(){
        $key = $this->systemConf['api']['key'];

        $data = json_decode(file_get_contents("php://input"));

        // get jwt
        $jwt=isset($data->jwt) ? $data->jwt : "";

        $authheader = '';
        $headers = getallheaders();
        if (isset($headers['Authorization'])){
            $authheader = $headers['Authorization'];
        }
        if (!$jwt && $authheader != ''){
            $jwt = substr($authheader,7);
        }

        // if jwt is not empty
        if($jwt){
        
            // if decode succeed, show user details
            try {
                // decode jwt
                $decoded = JWT::decode($jwt, $key, array('HS256'));
                $decoded_data = [
                    'id' => $decoded->data->id,
                    'ext_id' => $decoded->data->ext_id
                ];
                $newtoken = $this->generateToken(["data" => $decoded_data]);
                // set response code
                //http_response_code(200);
        
                // show user details
                $validate_result = [
                    "result" => 1,
                    "message" => "Access granted.",
                    "data" => array_merge(
                                $decoded_data,
                                ['newtoken' => $newtoken['data']['jwt']]
                            )
                ];
        
            }// if decode fails, it means jwt is invalid
            catch (Exception $e){
            
                // set response code
                //http_response_code(400);
            
                // tell the user access denied  & show error message
                $validate_result = [
                    "result" => -1,
                    "message" => "An error has occurred.",
                    "error" => $e->getMessage()
                ];
            }
        }// show error message if jwt is empty
        else{
            
            if (!isset($noprint) || !$noprint){
                // set response code
                //http_response_code(401);
            }
        
            // tell the user access denied
            $validate_result = [
                    "result" => 0,
                    "errorcode" => -1,
                    "message" => "Access denied"
            ];
            
        }

        return $validate_result;
    }

    function getToken(){
        $data = json_decode(file_get_contents("php://input"));

        if (empty($data) || !isset($data->user) || !isset($data->password)){
            $validationStatus = ['result' => 0, 'errorcode' => DBMaster::SYSTEM_LOGIN_BADREQUEST,'message' => 'Bad Request'];
            $this->auditData(1,DBMaster::SYSTEM_LOGIN_BADREQUEST,$data,null);
        }else{
            $validationStatus = $this->validateUser($data->user, $data->password);
        }
       
        if($validationStatus['result'] == 1){
            
            $result = $this->generateToken($validationStatus);
            
        }// login failed
        else{
            
            /*if (!isset($validationStatus['errorcode'])){
                http_response_code(500);
            }else if ($validationStatus['errorcode'] == DBMaster::SYSTEM_LOGIN_BADREQUEST){
                http_response_code(400);
            }else{
                // set response code
                http_response_code(401);    
            }*/
            
            // tell the user login failed
            $result = [
                'result' => 0, 
                "message" => $validationStatus['message'],
                "errorcode" => $validationStatus['errorcode']                
            ];
        }
        return $result;
    }

    function sso() {
        $data = json_decode(file_get_contents("php://input"));
        if (
            empty($data) || 
            !isset($data->entity) || 
            !isset($data->extid) ||
            !isset($data->email)
            ){
            $validationStatus = ['result' => 0, 'errorcode' => DBMaster::SYSTEM_LOGIN_BADREQUEST,'message' => 'Bad Request'];
            $this->auditData(1,DBMaster::SYSTEM_LOGIN_BADREQUEST,$data,null);
        } else {
            /* TODO: Validate token with SDK */
            $usrInfo = [];
            foreach($this->connection->query("SELECT * FROM `system_login` WHERE `usr` = '".$data->email."'") as $record){
                $usrInfo = $record;
            }
            if (empty($usrInfo)){
                $validationStatus = [
                            'result' => 0,
                            'errorcode' => self::SYSTEM_LOGIN_BADUSER,
                            'message' => 'The user does not exist'
                        ];
                $this->auditData(0,self::SYSTEM_LOGIN_BADUSER,$data,null);
            }else{
                if ( empty($usrInfo['ext_id']) || $usrInfo['ext_id'] == $data->extid ){
                    if ($usrInfo['active'] == 0){
                        $validationStatus = [
                                    'result' => 0,
                                    'errorcode' => self::SYSTEM_LOGIN_USRLOCK,
                                    'message' => 'The user is blocked'
                        ];
                        $this->auditData(1,self::SYSTEM_LOGIN_USRLOCK,['user' => $data->email],null);                    
                    }else{
                        $validationStatus = [
                                    'result' => 1,
                                    'data' => $usrInfo
                        ];
                        $this->saveData('system_login',['ext_id' => $data->extid, 'lastlogin' => date('Y-m-d H:i:s', strtotime('now'))],$usrInfo['id']);
                        $this->saveData('users',['image' => $data->image],$usrInfo['id']);
                        $this->auditData(2,self::SYSTEM_LOGIN_USRLOGIN,['user' => $data->email],null);
                    }
                }else{
                    $validationStatus = [
                                'result' => 0,                        
                                'errorcode' => self::SYSTEM_LOGIN_BADPASS,
                                'message' => 'ExtID relation missmatch'
                        ];
                    $this->auditData(0,self::SYSTEM_LOGIN_BADPASS,$data,null);
                }
            }

        }

        if($validationStatus['result'] == 1){
            
            $result = $this->generateToken($validationStatus);
            
        }// login failed
        else{
            
            $result = [
                'result' => 0, 
                "message" => $validationStatus['message'],
                "errorcode" => $validationStatus['errorcode']                
            ];
        }
        return $result;
    }

    function generateToken($data){
        
         // variables used for jwt
        $key = $this->systemConf['api']['key'];

        $token = array(
            "iat" => strtotime('now'),
            "nbf" => strtotime('-1 min'),
            "exp" => strtotime('+'.$this->systemConf['session']['timeout'].' min'),
            "data" => array(
                "id" => $data['data']['id'],
                "ext_id" => $data['data']['ext_id']
            )
            );	
            
            // set response code
            //http_response_code(200);
        
            // generate jwt
            $jwt = JWT::encode($token, $key);
            $result = [
                'result' => 1,
                'message' => "Successful login.",
                'data' => [
                    "jwt" => $jwt,
                    "id" => $data['data']['id']
                ]
            ]; 
        
        return $result;
    }

    function validateUser($user, $password){
        if (!$this->tableExists('system_login')){
			return ['result' => 0, 'message' => 'Table not exists'];
        }
        $usrInfo = [];
        $user = trim($user);
        foreach($this->connection->query("SELECT * FROM `system_login` WHERE `usr` = '".$user."'") as $record){
            $usrInfo = $record;
        }
        if (empty($usrInfo)){
            $result = [
                        'result' => 0,
                        'errorcode' => self::SYSTEM_LOGIN_BADUSER,
                        'message' => 'The user does not exist'
                    ];
            $this->auditData(0,self::SYSTEM_LOGIN_BADUSER,['user' => $user],null);
        }else{
            if ( password_verify($password, $usrInfo['pass']) ){
                if ($usrInfo['active'] == 0){
                    $result = [
                                'result' => 0,
                                'errorcode' => self::SYSTEM_LOGIN_USRLOCK,
                                'message' => 'The user is blocked'
                    ];
                    $this->auditData(1,self::SYSTEM_LOGIN_USRLOCK,['user' => $user],null);                    
                }else{
                    $result = [
                                'result' => 1,
                                'data' => $usrInfo
                    ];
                    $this->saveData('system_login',['lastlogin' => date('Y-m-d H:i:s', strtotime('now'))],$usrInfo['id']);
                    $this->auditData(2,self::SYSTEM_LOGIN_USRLOGIN,['user' => $user],null);
                }
            }else{
                $result = [
                            'result' => 0,                        
                            'errorcode' => self::SYSTEM_LOGIN_BADPASS,
                            'message' => 'The password entered is invalid'
                    ];
                $this->auditData(0,self::SYSTEM_LOGIN_BADPASS,['user' => $user],null);
            }
        }

        return $result;
        
    }

    function createUser($user, $password, $active, $validation_data){
        if (!$this->tableExists('system_login')){
			return ['result' => 0, 'message' => 'Table not exists'];
        }
        if (!$this->checkValidation($validation_data)){
            return [
                    'result' => 0,                        
                    'errorcode' => self::SYSTEM_USER_NOLOGIN,
                    'message' => 'Invalid token'
                    ];
        }

        $usrInfo = [];
        foreach($this->connection->query("SELECT * FROM `system_login` WHERE `usr` = '".$user."'") as $record){
            $usrInfo = $record;
        }
        if (empty($usrInfo)){
            $codeValues = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            $recoveryCode = '';
            for($c=0;$c<5;$c++){
                $recoveryCode .= $codeValues[rand(0, strlen($codeValues)-1)];
            }
            $usrId = $this->saveData('system_login',['usr' => $user, 'pass' => password_hash($password, PASSWORD_DEFAULT), 'active' => $active, 'activationcode' => $recoveryCode, 'activationcodeexp' => date('Y-m-d H:i:s', strtotime('+1 day'))],null); 
            $result = [
                        'result' => 1,
                        'data' => ['id' => $usrId, 'code' => $recoveryCode] 
                    ];
            $this->auditData(2,self::SYSTEM_USER_USERCREATED,['id' => $usrId, 'user' => $user],null);
        }else{
            $result = [
                            'result' => 0,                        
                            'errorcode' => self::SYSTEM_USER_USEREXISTS,
                            'message' => 'The entered user already exists'
                    ];
            $this->auditData(2,self::SYSTEM_USER_USEREXISTS,['user' => $user],null);
        }
        return $result;
    }

    function activateUser($user, $code){
        if (!$this->tableExists('system_login')){
			return ['result' => 0, 'message' => 'Table not exists'];
        }

        $usrInfo = [];
        foreach($this->connection->query("SELECT * FROM `system_login` WHERE `usr` = '".$user."'") as $record){
            $usrInfo = $record;
        }
        if (!empty($usrInfo)){
            if ($code != $usrInfo['activationcode']){
                $result = [
                            'result' => 0,                        
                            'errorcode' => self::SYSTEM_USER_ACTIVATION_BADCODE,
                            'message' => 'The entered code is invalid'
                    ];
                $this->auditData(2,self::SYSTEM_USER_ACTIVATION_BADCODE,['user' => $user, 'id' => $usrInfo['id']],$usrInfo['id']);
            }else if(strtotime($usrInfo['activationcodeexp']) < strtotime('now')){
                $result = [
                            'result' => 0,                        
                            'errorcode' => self::SYSTEM_USER_ACTIVATION_CODEEXPIRED,
                            'message' => 'The entered code has expired'
                    ];
                $this->auditData(2,self::SYSTEM_USER_ACTIVATION_CODEEXPIRED,['user' => $user, 'id' => $usrInfo['id']],$usrInfo['id']);
            }else{
               $this->saveData('system_login',['active' => 1],$usrInfo['id']); 
               $result = [
                        'result' => 1,
                        'data' => $usrInfo['id']
                    ];
                $this->auditData(2,self::SYSTEM_LOGIN_USRUNLOCK,['user' => $user, 'id' => $usrInfo['id']],$usrInfo['id']);
            }
        }else{
            $result = [
                            'result' => 0,                        
                            'errorcode' => self::SYSTEM_USER_ACTIVATION_BADUSER,
                            'message' => 'The user does not exist'
                    ];
            $this->auditData(2,self::SYSTEM_USER_ACTIVATION_BADUSER,['user' => $user],null);
        }
        return $result;
    }

    function setPasword($usrId, $password){
        return $this->saveData('system_login',['pass' => password_hash($password, PASSWORD_DEFAULT)],$usrId); 
    }
}