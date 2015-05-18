<?php
namespace coolroof\Model;


 class Projects
 {
     public $id;
     public $name;
	 public $user_id;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->name  = (!empty($data['name'])) ? $data['name'] : null;
		 $this->user_id  = (!empty($data['user_id'])) ? $data['user_id'] : null;
     }
 }
?>