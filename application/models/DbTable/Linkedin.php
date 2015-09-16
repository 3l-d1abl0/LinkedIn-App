<?php

class Application_Model_DbTable_Linkedin extends Zend_Db_Table_Abstract
{

    protected $_name = 'lapp';


        public function getUserDetails($uid)
		{
			$uid = (int)$uid;
			$row = $this->fetchRow('uid = ' . $uid);
			if (!$row) {
			throw new Exception("Could not find row $id");
			}
			return $row->toArray();
		}

		public function updateUser($data, $uid)
		{	
			$this->update($data, 'uid = '. (int)$uid);
		}

		public function addUser($data)
		{
			$this->insert($data);
		}
        

	public function verify($id) {
    
       $db = $this->getDefaultAdapter(); //throws exception
     
    	$result = $db->fetchRow('SELECT * FROM lapp WHERE id = "'.$id.'"');
    	if ($result == null){
    		return 0;
    	}
    	else{
    		return 1;
    	}
	}

	public function ifExists($id) {
    
       $db = $this->getDefaultAdapter(); //throws exception
     
    	$result = $db->fetchRow('SELECT uid FROM lapp WHERE id = "'.$id.'"');
    	if ($result == null){
    		return 0;
    	}
    	else{
    		return $result;
    	}
	}

}

/*
	https://github.com/3l-d1abl0
*/