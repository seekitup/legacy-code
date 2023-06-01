<?PHP

class Documents extends DBMaster{

    function save($table, $fieldList, $validation_data){
        
        if (isset($fieldList['uploadername'])){
            unset($fieldList['uploadername']);
        }
        
        $saveItem = parent::save($table, $fieldList, $validation_data);

        if ($saveItem['result'] == 1 && isset($_FILES["file"])){
            move_uploaded_file($_FILES["file"]["tmp_name"], 'repository/documents/'.$saveItem['data']);
        }

        return $saveItem;
    }

    function delete($table, $fieldList, $validation_data){

        $deleteResult = parent::delete($table, $fieldList, $validation_data);

        if ($deleteResult['result'] == 1){
            if (is_array($fieldList)){
                $id = $fieldList['id']; 
            }else{
                $id = $fieldList;
            }
            unlink('repository/documents/'.$id);
        }

        return $deleteResult;

    }

    public function find($table, $params, $validation_data){
		if (!$this->tableExists($table)){
			return ['result' => 0, 'error' => 'Table not exists'];
		}
		
		if (!isset($params)){
			return $this->getAll($table, $params, $validation_data);
		}
		
		$records = [];
		try{
			foreach($this->connection->query('SELECT *, (SELECT CONCAT(`u`.`name`, " ", `u`.`lastname`) FROM `users` as `u` WHERE `u`.`id` = `uploader`) as `uploadername` FROM `'.$table.'` WHERE '.$this->buildcondition($params)) as $row){
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
}