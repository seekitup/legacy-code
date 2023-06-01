<?PHP

$allowed_http_origins = $utils->system_config['api']['allowedOrigins'];

if (isset($_SERVER['HTTP_ORIGIN'])){
	$http_origin = $_SERVER['HTTP_ORIGIN'];
	if (in_array($http_origin, $allowed_http_origins)){  
		@header("Access-Control-Allow-Origin: " . $http_origin);
	} else if (substr($_SERVER['HTTP_ORIGIN'],0,17) == 'chrome-extension:') {
		@header("Access-Control-Allow-Origin: " . $http_origin);
	}
}
// required headers
//header("Access-Control-Allow-Origin: http://localhost/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Credentials: true");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
