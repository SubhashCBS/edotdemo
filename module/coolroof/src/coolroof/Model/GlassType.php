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
		 $this->solar_heat  = (!empty($data['SolarHeat'])) ? $data['Solar Heat'] : null;
		 $this->shading  = (!empty($data['Shading'])) ? $data['Shading'] : null;
		 $this->visible_trans  = (!empty($data['VisibleTrans'])) ? $data['Visible Trans'] : null;
     }
 }
?>