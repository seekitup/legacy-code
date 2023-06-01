<?PHP

class Utils{
    public $system_config;

    function __construct() {
        
        $mainConf = json_decode(file_get_contents('../conf/main.json'),true);
		$env_conf = json_decode(file_get_contents('../conf/config_'.$mainConf['enviroment'].'.json'),true);
        $this->system_config = array_merge($mainConf, $env_conf);
    
    }

    function reverse_norm_entities($d) {
        if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->reverse_norm_entities($v);
            }
        } else if (is_string ($d)) {
			return html_entity_decode($d);
			//return utf8_encode($d);
        }
        return $d;
    }
	
	function norm_entities($d){
		if (is_array($d)) {
            foreach ($d as $k => $v) {
                $d[$k] = $this->norm_entities($v);
            }
        } else if (is_string ($d)) {
			//return utf8_decode($d);
			return htmlentities($d);
        }
        return $d;
    }
    
}