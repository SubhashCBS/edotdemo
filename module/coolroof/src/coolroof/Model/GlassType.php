<?php
namespace coolroof\Model;


 class GlassType
 {
      public $id;
     public $name;
	 public $code;
	 public $USI;
	public $solar_heat;
	public $shading;
	public $visible_trans;

     public function exchangeArray($data)
     {
         $this->id     = (!empty($data['id'])) ? $data['id'] : null;
         $this->name  = (!empty($data['name'])) ? $data['name'] : null;
		 $this->code  = (!empty($data['code'])) ? $data['code'] : null;
		 $this->USI  = (!empty($data['U-SI'])) ? $data['U-SI'] : null;
		 $this->solar_heat  = (!empty($data['Solar Heat'])) ? $data['Solar Heat'] : null;
		 $this->shading  = (!empty($data['Shading'])) ? $data['Shading'] : null;
		 $this->visible_trans  = (!empty($data['Visible Trans'])) ? $data['Visible Trans'] : null;
     }
 }
?>