<?php
namespace coolroof\Model;

 use Zend\Db\TableGateway\TableGateway;
 use Zend\Db\Sql\Sql;
 use Zend\Db\Sql\Insert;


 class ProjectsTable
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
	
     public function saveUserProjects($data)
     { 
		//print_r($data);
		
		echo $project_name = $data->name;
		echo $user_id = $data->user_id;	
		
		$insert = new Insert();
		$insert->into('projects');
		$insert->columns(array('name', 'user_id'));

		$insert->values($gdata);
		
		$this->tableGateway = 'projects';
		$this->tableGateway->insertWith($insert);

		
     }

	
}
?>