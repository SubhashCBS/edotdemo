<?php
/**
 * CsnUser - Coolcsn Zend Framework 2 User Module
 * 
 * @link https://github.com/coolcsn/CsnUser for the canonical source repository
 * @copyright Copyright (c) 2005-2013 LightSoft 2005 Ltd. Bulgaria
 * @license https://github.com/coolcsn/CsnUser/blob/master/LICENSE BSDLicense
 * @author Stoyan Cheresharov <stoyan@coolcsn.com>
 * @author Svetoslav Chonkov <svetoslav.chonkov@gmail.com>
 * @author Nikola Vasilev <niko7vasilev@gmail.com>
 * @author Stoyan Revov <st.revov@gmail.com>
 * @author Martin Briglia <martin@mgscreativa.com>
 */

return array(
 'mail' => array(
  'transport' => array(
   'options' => array(
    //'host' => 'mail.yahoo.com',
    'host' => 'smtp.gmail.com',
    'connection_class'  => 'login',
    //'port' => '2525',
    'port'=>'465',
    'connection_config' => array(
     'username' => 'pvdprakash.developer@gmail.com',
     'password' => 'hoticecream',
     'ssl' => 'ssl'
    ),
   ),  
  ),
	),
);
