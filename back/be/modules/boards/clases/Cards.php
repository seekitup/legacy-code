<?PHP

class Cards extends DBMaster{

    function save($table, $fieldList, $validation_data){
       if (isset($fieldList['id'])){
        return parent::save($table, $fieldList, $validation_data);
       }
       include '../modules/boards/clases/Metadata.php';
       $meta = new Metadata;
       $metaInfo = $meta->getURLInfo($table, $fieldList, $validation_data)['data'];
       $permalink = uniqid();

       $cardInfo = [
         'type' => 1,
         'link' => $fieldList['url'],
         'permalink' => $permalink,
         'title' => $metaInfo['title'],
         'owner' => $fieldList['user']
       ];

       if (isset($fieldList['board'])) {
         $cardInfo['board'] = $fieldList['board'];
       }
       if (isset($metaInfo['description'])) {
         $cardInfo['description'] = $metaInfo['description'];
       }
       if (isset($metaInfo['msapplication-tileimage'])) {
        $cardInfo['image'] = $metaInfo['msapplication-tileimage'];
       }
       if (isset($metaInfo['image'])) {
         $cardInfo['image'] = $metaInfo['image'];
       }
       if (isset($metaInfo['favicon'])) {
         $cardInfo['favicon'] = $metaInfo['favicon'];
       }
       if (isset($metaInfo['url'])) {
        $cardInfo['link'] = $metaInfo['url'];
       }


       $result = parent::save($table, $cardInfo, $validation_data);

       if ($result['result'] == 1) {
        $url = strtolower($cardInfo['link']);

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

        if (!in_array($domain,['facebook.com', 'instagram.com', 'twitter.com', 'linkedin.com', 'youtube.com', 'youtu.be', 'tiktok.com'])){

          $fileCopy = false; 
          if (isset($cardInfo['image']) && !empty($cardInfo['image'])) {
            $imageString = file_get_contents('https://seekitup-test.herokuapp.com/?url='.$cardInfo['image']);
            $fileCopy = file_put_contents('repository/captures/'.$permalink.'.png', $imageString);
          }
          if ($fileCopy == false) {
            $imageString = file_get_contents('https://seekitup-test.herokuapp.com/?url='.$cardInfo['link']);
            $fileCopy = file_put_contents('repository/captures/'.$permalink.'.png', $imageString);
          }
        }
       }
       return $result;
    }

    public function find($table, $params, $validation_data){
      $params['subqueries'] = [
        [
          'field' => 'card',
          'table' => 'views',
          'alias' => 'views'
        ],
        [
          'field' => 'card',
          'table' => 'comments',
          'alias' => 'comments'
        ]
      ];

      $results = parent::find($table, $params, $validation_data);
      if ($results['result'] == 1 && isset($validation_data['data']['id'])) {
        foreach($results['records'] as $key => $result){
          $results['records'][$key]['isFab'] = false;
          foreach($this->connection->query('SELECT * FROM `cardfabs` WHERE `user` = '.$validation_data['data']['id'].' AND `card` = '.$result['id']) as $row){
            $results['records'][$key]['isFab'] = true;
          }
        }
      }
      return $results;

    }

    public function setFab($table, $params, $validation_data){
      if ($params['state'] == false) {
        $query = 'DELETE FROM `cardfabs` WHERE `card` = '.$params['card'].' AND `user` = '.$params['user'];
      } else {
        $query = 'INSERT INTO `cardfabs`(`card`, `user`) VALUES ('.$params['card'].','.$params['user'].')';
      }
      $this->connection->query($query);
      return ['result' => 1];
    }

    function getPublic($data) {
      return $this->find('cards', $data, []);
    }

    function getPublicComments($data) {
      return $this->find('comments', $data, []);
    }

    function addfromExt($data){
      if (!isset($_SERVER['HTTP_ORIGIN']) || substr($_SERVER['HTTP_ORIGIN'],0,17) != 'chrome-extension:') {
        return ['result' => 0, 'error' => 'Acceso no autorizado'];
      }
      $result = $this->execQuery("SELECT * FROM `cards` WHERE `owner` = ".$data['user']." AND `link` = '".$data['url']."'");        
      if ($result['result'] == 0){
        return $result;
      }
      if (count($result['records']) > 0){
        return ['result' => 0, 'error' => 'El link ya se encuentra guardado en Seekitup'];
      }
      include '../modules/boards/clases/Metadata.php';
       $meta = new Metadata;
       $metaInfo = $meta->getURLInfo('cards', $data, null)['data'];
       
       $permalink = uniqid();
       $cardInfo = [
         'type' => 1,
         'link' => $data['url'],
         'permalink' => $permalink,
         'title' => $metaInfo['title'],
         'owner' => $data['user']
       ];

       if (isset($data['board'])) {
         $cardInfo['board'] = $data['board'];
       }
       if (isset($metaInfo['description'])) {
         $cardInfo['description'] = $metaInfo['description'];
       }
       if (isset($metaInfo['msapplication-tileimage'])) {
        $cardInfo['image'] = $metaInfo['msapplication-tileimage'];
       }
       if (isset($metaInfo['image'])) {
         $cardInfo['image'] = $metaInfo['image'];
       }
       if (isset($metaInfo['favicon'])) {
         $cardInfo['favicon'] = $metaInfo['favicon'];
       }
       if (isset($metaInfo['url'])) {
        $cardInfo['link'] = $metaInfo['url'];
       }

       $id = $this->saveData('cards', $cardInfo, null);
       
       if ( is_array($id) ){
        return $id;
       }else{
       
        $url = strtolower($cardInfo['link']);

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

        if (!in_array($domain,['facebook.com', 'instagram.com', 'twitter.com', 'linkedin.com', 'youtube.com', 'youtu.be', 'tiktok.com'])){

          $fileCopy = false; 
          if (isset($cardInfo['image']) && !empty($cardInfo['image'])) {
            $imageString = file_get_contents('https://seekitup-test.herokuapp.com/?url='.$cardInfo['image']);
            $fileCopy = file_put_contents('repository/captures/'.$permalink.'.png', $imageString);
          }
          if ($fileCopy == false) {
            $imageString = file_get_contents('https://seekitup-test.herokuapp.com/?url='.$cardInfo['link']);
            $fileCopy = file_put_contents('repository/captures/'.$permalink.'.png', $imageString);
          }
        }
       }

       return ["result" => "1", "data" => $id];
       
    }

    function delete($table, $fieldList, $validation_data){
      $cardInfo = $this->execQuery('SELECT * FROM `cards` WHERE `id` = '.$fieldList['id']);
      $deleteResult = parent::delete($table, $fieldList, $validation_data);

        if ($deleteResult['result'] == 1){
            if (is_array($fieldList)){
                $id = $fieldList['id']; 
            }else{
                $id = $fieldList;
            }
            if (file_exists('repository/captures/'.$cardInfo['records'][0]['permalink'].'.png')) {
              unlink('repository/captures/'.$cardInfo['records'][0]['permalink'].'.png');
            }
        }

        return $deleteResult;
    }

    function capture($table, $fieldList, $validation_data){
      set_time_limit(0);
      $cardPool = $this->execQuery('SELECT * FROM `cards` WHERE `id` >='.$fieldList['id']);
      foreach($cardPool['records'] as $cardInfo){
        $permalink = $cardInfo['permalink'];
        $url = strtolower($cardInfo['link']);

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

        if (!in_array($domain,['facebook.com', 'instagram.com', 'twitter.com', 'linkedin.com', 'youtube.com', 'youtu.be', 'tiktok.com', 'vm.tiktok.com'])){

          $fileCopy = false; 
          if (isset($cardInfo['image']) && !empty($cardInfo['image'])) {
            $imageString = file_get_contents('https://seekitup-test.herokuapp.com/?url='.$cardInfo['image']);
            $fileCopy = file_put_contents('repository/captures/'.$permalink.'.png', $imageString);
          }
          if ($fileCopy == false) {
            $imageString = file_get_contents('https://seekitup-test.herokuapp.com/?url='.$cardInfo['link']);
            $fileCopy = file_put_contents('repository/captures/'.$permalink.'.png', $imageString);
          }
        }
      }
       return ["result" => "1", "data" => $cardPool['records'] ];
    }

    function fixlinks($table, $fieldList, $validation_data){
      set_time_limit(0);
      $cardPool = $this->execQuery("SELECT * FROM `cards` WHERE link like 'https://vm.tiktok.com%'");
      $fixed = [];
      include '../modules/boards/clases/Metadata.php';
      $meta = new Metadata;
      foreach($cardPool['records'] as $cardInfo){        
        $metaInfo = $meta->getURLInfo('cards', ['url' => $cardInfo['link']], null)['data'];
        $fixed []= $this->saveData('cards', ['link' => $metaInfo['url']], $cardInfo['id']);
      }
      return ["result" => "1", "data" => $fixed ];
    }
}