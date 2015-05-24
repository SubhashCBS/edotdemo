<?php
namespace coolroof\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Insert;


 class GlassTypeTable
 {
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }

     public function fetchAll()
     {
         $resultSet = $this->tableGateway->select();
         return $resultSet;
     }
	 
	 public function fetchUserGlasses($user_id) 
    { 
        $select = new \Zend\Db\Sql\Select ; 
        $select->from('glasstypes'); 
        $select->columns(array('id','name')); 
        $select->join('user_glasstypes', "user_glasstypes.glass_id = glasstypes.id", array('user_id','glass_id'), 'left'); 
		$select->where("user_glasstypes.user_id = ".$user_id);     
       //$select->getSqlString(); 
        $resultSet = $this->tableGateway->selectWith($select); 
       return $resultSet; 
    }

	public function fetchGlassinfo($glass_id) 
    { 
        $select = new \Zend\Db\Sql\Select ; 
        $select->from('glasstypes'); 
        $select->columns(array('Visible Trans as vlt','Solar Heat as u_factor')); 
        $select->where("glasstypes.id = ".$glass_id);     
       //$select->getSqlString(); 
        $resultSet = $this->tableGateway->selectWith($select); 
       return $resultSet; 
    }
	
	
     public function saveUserGlasses($data)
     { 
		//print_r($data);
		
		echo $glass_ids = $data->to;
		echo $user_id = $data->user_id;	
		foreach($glass_ids as $gid)
		{
			$gdata['glass_id'] = $gid;
			$gdata['user_id'] = $user_id;
			
			$insert = new Insert();
			$insert->into('user_glasstypes');
			$insert->columns(array('user_id', 'glass_id'));

			$insert->values($gdata);
			
			$this->tableGateway = 'user_glasstypes';
			$this->tableGateway->insertWith($insert);
		}
     }

	
}
?>
