<?PHP
require_once('../libs/vendor/autoload.php');

use GuzzleHttp\Client;
use GuzzleHttp\Psr7;
use GuzzleHttp\Exception\ClientException;

class Metadata{

    function file_get_contents_curl($url)
    {
        $ch = curl_init($url);
        curl_setopt( $ch, CURLOPT_POST, false );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
        // curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7");
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 6.0.1; Moto G (4)) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/97.0.4692.71 Mobile Safari/537.36");
        curl_setopt( $ch, CURLOPT_HEADER, false );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
        $data = curl_exec( $ch );
        return $data;
    }

    function getSiteOG( $url, $specificTags=0 )
    {
        $res = [];
        $realUrl = $url;
        // URL to lower case
        $url = strtolower($url);

        // Get the Domain from the URL
        $fulldomain = parse_url($url);
        $domain = '';

        // Check Domain
        $domainParts = explode('.', $fulldomain['host']);
        if(count($domainParts) == 3 and $domainParts[0]!='www') {
            // With Subdomain (if not www)
            $domain = $domainParts[0].'.'.
                $domainParts[count($domainParts)-2].'.'.$domainParts[count($domainParts)-1];
        } else if (count($domainParts) >= 2) {
            // Without Subdomain
            $domain = $domainParts[count($domainParts)-2].'.'.$domainParts[count($domainParts)-1];
        } else {
            // Without http(s)
            $domain = $url;
        }
        
        $doc = new DOMDocument();
                
        @$doc->loadHTML('<?xml encoding="UTF-8">' . $this->file_get_contents_curl($realUrl));

        if ($doc->getElementsByTagName('title')->length == 0){
          @$doc->loadHTMLFile($url, LIBXML_NOWARNING);  
        }

        if ($doc->getElementsByTagName('title')->length != 0){
            $res['title'] = $doc->getElementsByTagName('title')->item(0)->nodeValue;
        }

        foreach ($doc->getElementsByTagName('meta') as $m){
            $tag = $m->getAttribute('name') ?: $m->getAttribute('property');
            if(in_array($tag,['description','keywords']) || strpos($tag,'og:')===0) $res[str_replace('og:','',$tag)] = $m->getAttribute('content');
        }

        foreach($doc->getElementsByTagName('link') as $l){
           $type = $l->getAttribute('rel');
           if ($type == 'icon') {
              $res['favicon'] = $this->rel2abs($l->getAttribute('href'), $url);
           }
        }

        return $specificTags? array_intersect_key( $res, array_flip($specificTags) ) : $res;
    }

    public function getURLInfo($table, $params, $validation_data){
        $result = $this->getSiteOG($params['url']);
        if (isset($result['title'])){
            $result['title'] = htmlentities($result['title'], ENT_QUOTES | ENT_IGNORE);
        }
        if (isset($result['description'])){
            $result['description'] = htmlentities($result['description'], ENT_QUOTES | ENT_IGNORE);
        }
        
        return ['result' => 1, 'data' => $result];
    }

    /* HELPER: Change URL from relative to absolute */
    function rel2abs( $rel, $base ) {
        extract( parse_url( $base ) );
        if ( strpos( $rel,"//" ) === 0 ) return $scheme . ':' . $rel;
        if ( parse_url( $rel, PHP_URL_SCHEME ) != '' ) return $rel;
        if ( $rel[0] == '#' or $rel[0] == '?' ) return $base . $rel;
        $path = preg_replace( '#/[^/]*$#', '', $path);
        if ( $rel[0] ==  '/' ) $path = '';
        $abs = $host . $path . "/" . $rel;
        $abs = preg_replace( "/(\/\.?\/)/", "/", $abs);
        $abs = preg_replace( "/\/(?!\.\.)[^\/]+\/\.\.\//", "/", $abs);
        return $scheme . '://' . $abs;
    }
}