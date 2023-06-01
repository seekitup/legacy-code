<?PHP

include_once '../libs/php-jwt-master/src/BeforeValidException.php';
include_once '../libs/php-jwt-master/src/ExpiredException.php';
include_once '../libs/php-jwt-master/src/SignatureInvalidException.php';
include_once '../libs/php-jwt-master/src/JWT.php';

include '../system/Utils.php';
include '../system/DBMaster.php';
include '../system/Security.php';

$utils = new Utils();

include '../system/processheaders.php';

$security = new Security();

$validate_result = $security->validatetoken();

$routerPath = explode('/',$_SERVER['PHP_SELF']);
$URIParams = explode('?',$_SERVER['REQUEST_URI']);
$currentURI = explode('/',$URIParams[0]);
$requestparams = array_slice($currentURI, count($routerPath) - 1);


if (count($requestparams) == 0 || $requestparams[0] == '' || $requestparams[0] == 'getversion'){
	$result = ['result' => 1, 'version' => $utils->system_config['version']];
}else if ($requestparams[0] == 'gettoken'){
	$result = $security->getToken();
}else if ($requestparams[0] == 'sso'){
	$result = $security->sso();
}else if ($requestparams[0] == 'validatetoken'){
	$result = $validate_result;
}else if ($requestparams[0] == 'public'){
	$requestError = true;
	if (isset($requestparams[1])){
	   $headers = getallheaders();
	   if (isset($headers['Content-Type']) &&  strrpos($headers['Content-Type'], 'application/json') !== FALSE){
		 $data = json_decode(file_get_contents("php://input"),true);
	   }else{
		$data = array_merge($_POST, $_GET);
	   }
		
	   $data = $utils->norm_entities($data);
	   if ($requestparams[1] == 'boards') {
		 include "../modules/boards/clases/Boards.php";
		 $con = new Boards();
		 $result = $utils->reverse_norm_entities($con->getPublic($data));
		 $requestError = false;
	   } else if ($requestparams[1] == 'cards') {
		include "../modules/boards/clases/Cards.php";
		$con = new Cards();
		$result = $utils->reverse_norm_entities($con->getPublic($data));
		$requestError = false;
	  } else if ($requestparams[1] == 'comments') {
		include "../modules/boards/clases/Cards.php";
		$con = new Cards();
		$result = $utils->reverse_norm_entities($con->getPublicComments($data));
		$requestError = false;
	  }
	}
	if ($requestError){
		$result = ['result' => 0, 'errorcode' => DBMaster::SYSTEM_LOGIN_BADREQUEST];
	}
}else if ($requestparams[0] == 'share'){
	if (isset($requestparams[1])){
		if (substr($requestparams[1], 0, 1) == 'b') {
			include "../modules/boards/clases/Boards.php";
		 	$con = new Boards();
			$con->printShare(substr($requestparams[1], 1));
			exit(); 
		}
	} else {
		$result = ['result' => 0, 'errorcode' => DBMaster::SYSTEM_LOGIN_BADREQUEST];
	}
}else if ($requestparams[0] == 'addfromExt'){
	
	$headers = getallheaders();
	if (isset($headers['Content-Type']) &&  strrpos($headers['Content-Type'], 'application/json') !== FALSE){
	  $data = json_decode(file_get_contents("php://input"),true);
	}else{
	  $data = array_merge($_POST, $_GET);
	}
		
	$data = $utils->norm_entities($data);
	include "../modules/boards/clases/Cards.php";
	$con = new Cards();
	$result = $utils->reverse_norm_entities($con->addfromExt($data));
	
}else if ($validate_result['result'] != 1){
	$result = $validate_result;
}else{
	
	$module = $requestparams[0];
	if (!isset($requestparams[1]) || $requestparams[1] == ''){
		$class = 'Common';
		$table = strtolower($module);
	}else{
		$class = ucfirst($requestparams[1]);
		$table = strtolower($class);
	}
	if (!isset($requestparams[2]) || $requestparams[2] == ''){
		$method = 'getAll';
	}else{
		$method = $requestparams[2];
	}
	
	if (file_exists("../modules/$module/clases/$class.php")){
		include "../modules/$module/clases/$class.php";
		$con = new $class();
	}else{
		//$table = $module;
		$con = new DBMaster();
	}

	$headers = getallheaders();
	if (isset($headers['Content-Type']) &&  strrpos($headers['Content-Type'], 'application/json') !== FALSE){
		$data = json_decode(file_get_contents("php://input"),true);
	}else{
		$data = array_merge($_POST, $_GET);
	}
		
	$data = $utils->norm_entities($data);
	$result = $utils->reverse_norm_entities($con->$method($table, $data, $validate_result));
}
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
		http_response_code(200);
}else if ($result['result'] == 1){
	http_response_code(200);
}else if ($result['result'] == -1){
	http_response_code(400);
}
else{
	
		if (!isset($result['errorcode'])){
			http_response_code(500);
		}else if ($result['errorcode'] == DBMaster::SYSTEM_LOGIN_BADREQUEST){
			http_response_code(400);
		}else{
			http_response_code(401);    
		}
	
}
echo json_encode($result);