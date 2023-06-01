<?PHP

class Boards extends DBMaster{
   
    public function getforhome($table, $fieldList, $validation_data){
       if (!isset($fieldList['user'])){
        return ['result' => 0, 'error' => 'Bad request'];
       }

       $mainBoard = [];
       $allBoards = [];
       $openBoards = [];

       try{
			//foreach($this->connection->query('SELECT * FROM `cards` WHERE `owner` = '.$fieldList['user'].' AND `board` IS NULL ORDER BY RAND() LIMIT 6') as $row){
                foreach($this->connection->query('SELECT * FROM `cards` WHERE `owner` = '.$fieldList['user'].' AND `board` IS NULL ORDER BY `created` DESC') as $row){
				$purgeRow = [];
				foreach($row as $key => $value){
					if (!is_int($key)){
						$purgeRow[$key] = $value;
					}
				}
				$mainBoard []=$purgeRow;
			}
		} catch (PDOException $e) {
			$error = "Query error: ".$e->getMessage();
			return ['result' => 0, 'error' => $error];
		}

        try{
			//foreach($this->connection->query('SELECT * FROM `boards` WHERE `owner` = '.$fieldList['user'].' AND `parentboard` IS NULL ORDER BY RAND() LIMIT 3') as $row){
			foreach($this->connection->query('SELECT * FROM `boards` WHERE `owner` = '.$fieldList['user'].' AND `parentboard` IS NULL ORDER BY `created` DESC') as $row){
				$purgeRow = [];
				foreach($row as $key => $value){
					if (!is_int($key)){
						$purgeRow[$key] = $value;
					}
				}
                $allBoards []= $purgeRow;
			}
		} catch (PDOException $e) {
			$error = "Query error: ".$e->getMessage();
			return ['result' => 0, 'error' => $error];
		}

        try{
			foreach($this->connection->query('SELECT `b`.* FROM `boardmembers` as `bm` LEFT JOIN `boards` as `b` ON `bm`.`board` = `b`.`id` WHERE `bm`.`user` = '.$fieldList['user'].' AND `b`.`parentboard` IS NULL ORDER BY `b`.`created` DESC') as $row){
				$purgeRow = [];
				foreach($row as $key => $value){
					if (!is_int($key)){
						$purgeRow[$key] = $value;
					}
				}
                $allBoards []= $purgeRow;
			}
		} catch (PDOException $e) {
			$error = "Query error: ".$e->getMessage();
			return ['result' => 0, 'error' => $error];
		}

        foreach($allBoards as $allBoardKey => $allBoardData){
            $allBoards[$allBoardKey]['cards'] = [];
            foreach($this->connection->query('SELECT * FROM `cards` WHERE `board` = '.$allBoardData['id'].' ORDER BY RAND() LIMIT 6') as $row){
                $purgeCard = [];
                foreach($row as $key => $value){
                    if (!is_int($key)){
                        $purgeCard[$key] = $value;
                    }
                }
                $allBoards[$allBoardKey]['cards'] []=$purgeCard;
            }
        }


        if (isset($fieldList['openboards'])) {
           foreach($fieldList['openboards'] as $board){
            if ($board != null){
                $openBoards[$board] = [];
                try{
                    foreach($this->connection->query('SELECT * FROM `boards` WHERE `id` = '.$board) as $row){
                        $purgeRow = [];
                        foreach($row as $key => $value){
                            if (!is_int($key)){
                                $purgeRow[$key] = $value;
                            }
                        }
                        $openBoards[$board] = $purgeRow;
                    }
                } catch (PDOException $e) {
                    $error = "Query error: ".$e->getMessage();
                    return ['result' => 0, 'error' => $error];
                }
                $openBoards[$board]['cards'] = [];
                try{
                        //foreach($this->connection->query('SELECT * FROM `cards` WHERE `owner` = '.$fieldList['user'].' AND `board` = '.$board.' ORDER BY RAND() LIMIT 6') as $row){
                        foreach($this->connection->query('SELECT * FROM `cards` WHERE `board` = '.$board.' ORDER BY `created` DESC') as $row){
                            $purgeRow = [];
                            foreach($row as $key => $value){
                                if (!is_int($key)){
                                    $purgeRow[$key] = $value;
                                }
                            }
                            $openBoards[$board]['cards'] []=$purgeRow;
                        }
                } catch (PDOException $e) {
                    $error = "Query error: ".$e->getMessage();
                    return ['result' => 0, 'error' => $error];
                }
            }        
           }
        }

        return [
            'result' => 1, 
            'data' => [
                       'mainBoard' => $mainBoard,
                       'allBoards' => $allBoards,
                       'openBoards' => $openBoards
                      ]
            ];
    }

    function save($table, $fieldList, $validation_data){
        if (!isset($fieldList['id'])){
          $fieldList['permalink'] = uniqid();
        }
        return parent::save($table, $fieldList, $validation_data);
    }

    function getPublic($data) {
        return $this->find('boards', $data, []);
    }

    function printShare($link) {
        $data = [];
        try{
			foreach($this->connection->query("SELECT `main`.*, `users`.`name` as 'user_name', `users`.`lastname` as 'user_lastname', `users`.`image` as 'user_image' FROM `boards` as `main` LEFT JOIN `users` ON `users`.`id` = `main`.`owner` WHERE `main`.`permalink`='".$link."'") as $row){
				$purgeRow = [];
				foreach($row as $key => $value){
					if (!is_int($key)){
						$purgeRow[$key] = $value;
					}
				}
				$data = $purgeRow;
			}
		} catch (PDOException $e) {
			$error = "Query error: ".$e->getMessage();
			return ['result' => 0, 'error' => $error];
		}
        header("Content-Type: text/html; charset=utf-8");
        echo '<html xmlns="http://www.w3.org/1999/xhtml"><head>';
        echo '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><meta charset="utf-8">';
        echo '<meta name="description" content="'. $data['name'] . ' - Una pizarra de ' . $data['user_name'] . ' ' . $data['user_lastname'] .'" />';
        echo '<title>'.$data['name'].' - Seekitup</title>';
        echo '</head><body>Cargando...';
        echo '<script>setTimeout(function(){';
        echo 'location.href="'.$this->systemConf['front']['location'].'#/board/public/'.$link.'";';
        echo '}, 500)</script>';
        echo '</body></html>';
        exit();
    }

    function stopfollow($table, $fieldList, $validation_data){
        $this->connection->query('DELETE FROM `boardmembers` WHERE `board` = '.$fieldList['board'].' AND `user` = '.$fieldList['user']);
        return ['result' => 1];
    }

    function setMemberLevel($table, $fieldList, $validation_data){
        $this->connection->query('UPDATE `boardmembers` SET `rol`='.$fieldList['rol'].' WHERE `board` = '.$fieldList['board'].' AND `user` = '.$fieldList['user']);
        return ['result' => 1];
    }

    function addMember($table, $fieldList, $validation_data){
        $this->connection->query('DELETE FROM `boardmembers` WHERE `board` = '.$fieldList['board'].' AND `user` = '.$fieldList['user']);
        $this->connection->query('INSERT INTO `boardmembers`(`board`, `user`, `rol`) VALUES ('.$fieldList['board'].', '.$fieldList['user'].', 1)');
        return ['result' => 1]; 
    }

}