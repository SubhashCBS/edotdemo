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
		 $this->USI  = (!empty($data['USI'])) ? $data['USI'] : null;
		 $this->SolarHeat  = (!empty($data['SolarHeat'])) ? $data['SolarHeat'] : null;
		 $this->Shading  = (!empty($data['Shading'])) ? $data['Shading'] : null;
		 $this->VisibleTrans  = (!empty($data['VisibleTrans'])) ? $data['VisibleTrans'] : null;
     }
 }
?>