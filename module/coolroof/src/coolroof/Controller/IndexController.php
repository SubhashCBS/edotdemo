<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace coolroof\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;


class IndexController extends AbstractActionController
{
	
	protected $glasstypesTable;
	public $user_id = 1;

    public function indexAction()
    {
		/*if ($user = $this->identity()) {
            $user = new User;
			$user_id = $user->id;
        }
		else
		{*/
			$user_id = $this->user_id;
		/*}*/
		
        return new ViewModel(array(
             'user_glass_types' => $this->getglasstypesTable()->fetchUserGlasses($user_id),
         ));
    }
	
	public function libraryAction()
    {
		$request = $this->getRequest();
   
		if($request->isPost()) 
		{
			$data = $request->getPost();
			$data->user_id = $this->user_id;
		
			$this->getglasstypesTable()->saveUserGlasses($data);
        }
	
        return new ViewModel(array(
             'glass_types' => $this->getglasstypesTable()->fetchAll(),
         ));
    }
	
		public function getglasstypesTable()
     {
         if (!$this->glasstypesTable) {
             $sm = $this->getServiceLocator();
             $this->glasstypesTable = $sm->get('coolroof\Model\GlassTypeTable');
         }
         return $this->glasstypesTable;
     }
}
