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
use coolroof\Entity\Project;
$user_id = 1;

class IndexController extends AbstractActionController
{
	
	protected $glasstypesTable;
	

    public function indexAction()
    {
		/*if ($user = $this->identity())
		{
            $user = new User;
			$user_id = $user->id;
        }
		else
		{
			$user_id = $this->user_id;
		}*/
		
		$request = $this->getRequest();
		
		if($request->isPost()) 
		{
			$data = $request->getPost();
			//echo "<pre>";
			//print_r($data);
			
			return $this->mycommand_file_generator();
		}
		
        return new ViewModel(array(
             'user_glass_types' => $this->getglasstypesTable()->fetchAll(),
         ));
    }
	
	public function ProjectsAction()
    {
	
		$request = $this->getRequest();
		$em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
		if($request->isPost()) 
		{
			$data = $request->getPost();
			$user = $this->identity();
			$project = new Project();
			$project->setName($data->name);
			$project->setUser($user);
			
			$em->persist($project);
			$em->flush();
			
        }
		
		return new ViewModel();
	
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
	 
	 public function getProjectsTable()
     {
         if (!$this->ProjectsTable) {
             $sm = $this->getServiceLocator();
             $this->projectsTable= $sm->get('coolroof\Model\ProjectsTable');
         }
         return $this->projectsTable;
     }
	 
	 public function mycommandfilegeneratorAction()
	 {
	 error_reporting(0);
		/*echo "<pre>";
		print_r($_POST);
		exit;*/
		$unique_counter = time();
		
		if(is_array($_POST['azimuth']))
		{
			$azimuth = $_POST['azimuth'];
		}
		else
		{
			$azimuth = array($_POST['azimuth']);
		}
		
		if(is_array($_POST['aspect-ratio']))
		{
			$aspect_ratio = $_POST['aspect-ratio'];
		}
		else
		{
			$aspect_ratio = array($_POST['aspect-ratio']);
		}
		
		
		$wall_ratio = array();

		foreach($_POST['wwr'] as $wr)
		{
			if(!empty($wr) & $wr != 0)
			{
				$wall_ratio[] = $wr;
			}
		
		}
		
		
		//$_POST['over-hang'] = array(10);
		if(is_array($_POST['over-hang']))
		{
			$overhang = $_POST['over-hang'];
		}
		else
		{
			$overhang = array($_POST['over-hang']);
		}
			
			
		if($_POST['wwr-direction'] == 'different_dir')
		{
			$wall_type = array(0.5);
			$root_type = array(0.75);
		}
		else
		{
			$wall_type = $_POST['wall-type'];
			$roof_type =  $_POST['roof-type'];
		}
		
		$wall_type = array_unique($wall_type);
		$roof_type = array_unique($roof_type);
		
		$glass_types =  $_POST['glass-types'];
			
		
		$hvactype2=6;
		$ptotal_area=50;
		$location=$_POST['location'];
	
		$old = umask(0);
		mkdir($_SERVER['DOCUMENT_ROOT']."/edotdemo/working_directory/nonparametric/$unique_counter", 0777) or print "<br>Can not create working directory";//a working directory is made for every user where data related to him would be stored
		umask($old);

		//exit;
		/*---------------------- make a copy of ini file for every user--------------------*/
		$fileno=1;
		$working_dir=$_SERVER['DOCUMENT_ROOT']."/edotdemo/working_directory/nonparametric/".$unique_counter;
		$working_directory=$_SERVER['DOCUMENT_ROOT']."/edotdemo/working_directory/nonparametric/".$unique_counter;

		$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/optLinux_template.ini";
		$file1 = fopen($file, "r") or die("can't open optLinux_template.ini template file for reading");
		$theData = fread($file1, filesize($file));
		fclose($file1);
		$file="optLinux.ini";
		$file1 = fopen("$working_dir/$file", "w") or die("can't Create optLinux.ini");
		fwrite($file1,$theData);
		fclose($file1);

		/*---------------------- make a copy of cfg file for every user--------------------*/

		$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/EnergyPlusLinux.cfg";
		$file1 = fopen($file, "r") or die("can't open EnergyPlusLinux.cfg file for reading");
		$theData = fread($file1, filesize($file));
		fclose($file1);
		if($location!=""){
			$cityname=$location;
		}else{
			$cityname="Hyderabad.epw";
		}
	//	$theData = str_replace(array('%weatherfile%'),array($cityname),$theData);
		$file="EnergyPlusLinux.cfg";
		$file1 = fopen("$working_dir/$file", "w") or die("can't create EnergyPlusLinux.cfg");
		fwrite($file1,$theData);
		fclose($file1);

		//echo "count ".count($aazimuth).count($awwr).count($adepth).count($aratio).count($ashgc);

		$filesave=fopen($working_dir."/parametricvalues.txt",'w');
		$filecontent="";
		for($x=0;$x<count($azimuth);$x++)
		{
			for($t=0;$t<count($aspect_ratio);$t++)
			{
				for($z=0;$z<count($overhang);$z++)
				{
					for($w=0;$w<count($glass_types);$w++)
					{
						for($v=0;$v<count($wall_type);$v++)
						{
						
						for($u=0;$u<count($roof_type);$u++)
						{
						
						for($y=0;$y<count($wall_ratio);$y++)
						{
						
					//	echo "hi";
							
							$filecontent=$filecontent.$azimuth[$x];		
							$filecontent=$filecontent." ".$wall_ratio[$y];		
							$filecontent=$filecontent." ".$overhang[$z];		
							$filecontent=$filecontent." ".$aspect_ratio[$t];		
							$filecontent=$filecontent." ".$glass_types[$w];		
							$filecontent=$filecontent." ".$wall_type[$v];		
							$filecontent=$filecontent." ".$roof_type[$u];
							$filecontent=$filecontent." \n"	;
							/*---------------------- store data of template file for every user in a variable--------------------*/

							$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/tutorial_template.idf";
							$file1 = fopen($file, "r") or die("can't open tutorial_template.idf file for reading");
							$template_file_data = fread($file1, filesize($file));//stores the data of template file in a variable
							fclose($file1);

							$template_file_data = $this->addhvac($template_file_data,$hvactype2);
							
							/*---------------------- make a copy of idf file for every user--------------------*/

							/*$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/tutorial.idf";
							$file1 = fopen($file, "r") or die("can't open template file for reading");
							$theData = fread($file1, filesize($file));
							fclose($file1);
							$file="tutorial.idf";
							$file1 = fopen("$working_dir/$file", "w") or die("can't open template for writing");
							fwrite($file1,$theData);
							fclose($file1);
							*/

							$template_file_data = str_replace(array('%azimuth_angle%'),array($azimuth[$x]),$template_file_data);
							$height_of_window=3;//fixing the height of the window to 3; according the given model

							$wwr_height=$wall_ratio[$y]/100*$height_of_window;
							$wwr_startz=$height_of_window/2-$wwr_height/2;

							$template_file_data = str_replace(array('%wwr_height%','%wwr_startz%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height1%','%wwr_startz1%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height2%','%wwr_startz2%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height3%','%wwr_startz3%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height4%','%wwr_startz4%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array("%depth%"),array($overhang[$z]),$template_file_data);

							$lbybratio_length_value=sqrt($aspect_ratio[$w]*$ptotal_area);
							$lbybratio_breadth_value=$ptotal_area/$lbybratio_length_value;
							$template_file_data = str_replace(array("%lbybratio_length%","%lbybratio_breadth%"),array($lbybratio_length_value,$lbybratio_breadth_value),$template_file_data);
							
							if(isset($glass_types[$w]))
							{
							$glass_info = $this->getglasstypesTable()->fetchGlassinfo($glass_types[$w]);
							
							$template_file_data=str_replace(array("%shgc%"), array($glass_info->SolarHeat),$template_file_data);
							
							
							
							
							$template_file_data=str_replace(array("%vlt%"), array($glass_info->VisibleTrans),$template_file_data);
							$template_file_data=str_replace(array("%u_factor%"), array($glass_info->USI),$template_file_data);
							
							}


							$file=$fileno.".idf";
							$file1 = fopen("$working_dir/$file", "w") or die("can't open abcde  model template for writing");
							
							//echo $template_file_data;
							
							fwrite($file1,$template_file_data);
							fclose($file1);
							$fileno=$fileno+1;
							
						}	
						}

						}


					}
				}
			}
		}

		//echo $filecontent;

		fwrite($filesave,$filecontent);
		fclose($filesave);

		/*$cityname="Hyderabad.epw";
		if($location2==1){
			$cityname="New_Delhi.epw";
		}
		else if($location2==2){
			$cityname="Hyderabad.epw";
		}
		else if($location2==3){
			$cityname="Kolkata.epw";
		}
		else if($location2==4){
			$cityname="Banglore.epw";
		}*/

		sleep(20);
		$host="localhost";
		$port =5436;  //port number
		$fp = fsockopen($host, $port, $errno, $errstr);
		if( !$fp)
		{
				die ("couldnot connect to server");
		}
		socket_set_timeout($fp, 500);
		if (!$fp)
		{
				$result = "Error: could not open socket connection";
				echo $result;
		}
		else
		{
			//$str = $_SERVER['DOCUMENT_ROOT']."/edotdemo/".$cityname;
			 /*$str="p"."./working_directory/nonparametric/".$unique_counter." ".$cityname." ".$fileno;
				fputs ($fp, $str);
				stream_set_blocking($fp,TRUE);
				stream_set_timeout(600);
				$msg="";
				sleep(100);
				$msg=fgets($fp,17);
				sleep(5);
				echo "message from server.c is $msg <br>";
				if($msg!="")
				{
				
					$working_directory_location_parametric = "./working_directory/nonparametric/$unique_counter/";

					$azimuth1=array();
					$energy1=array();
					$wwr1=array();
					$ratio1=array();
					$shgc1=array();
					$depth1=array();
					$u_factor1=array();
					$vlt1=array();
					$count=0;
					$file=fopen($working_directory_location_parametric."finalvalues.txt","r");
					$flag=0;
					if($file == NULL){

					}
					else{
						 while(!feof($file))
						 {
							 $no=0;
							 $a= fgets($file);
							 $len=strlen($a);
							 if($len==0 or $len==1)
							 {
								break;
							 }
							 $piece=explode(" ",$a);
							 $energy1[$count]=$piece[0];
							 $azimuth1[$count]=$piece[1];
							 $wwr1[$count]=$piece[2];
							 $depth1[$count]=$piece[3];
							 $ratio1[$count]=$piece[4];
							 $shgc1[$count]=$piece[5];
							 $u_factor1[$count]=$piece[6];
							 $vlt1[$count]=$piece[7];
							 $count=$count+1;
						  }

						  //sorting the values
						  $x1=0;
						  $y1=0;
						  while($x1 < $count)
						  {
							$y1=0;
							while($y1 < $count)
							{
							  if($energy1[$x1] < $energy1[$y1])
							  {
								$temp1=$energy1[$x1];
								$energy1[$x1]=$energy1[$y1];
								$energy1[$y1]=$temp1;


								$temp1=$azimuth1[$x1];
								$azimuth1[$x1]=$azimuth1[$y1];
								$azimuth1[$y1]=$temp1;

								$temp1=$wwr1[$x1];
								$wwr1[$x1]=$wwr1[$y1];
								$wwr1[$y1]=$temp1;

								$temp1=$depth1[$x1];
								$depth1[$x1]=$depth1[$y1];
								$depth1[$y1]=$temp1;

								$temp1=$ratio1[$x1];
								$ratio1[$x1]=$ratio1[$y1];
								$ratio1[$y1]=$temp1;

								$temp1=$shgc1[$x1];
								$shgc1[$x1]=$shgc1[$y1];
								$shgc1[$y1]=$temp1;

								$temp1=$u_factor1[$x1];
								$u_factor1[$x1]=$u_factor1[$y1];
								$u_factor1[$y1]=$temp1;

								$temp1=$vlt1[$x1];
								$vlt1[$x1]=$vlt1[$y1];
								$vlt1[$y1]=$temp1;

							  }
							  $y1=$y1+1;
							}
							$x1=$x1+1;
						  }
					}

					$fp1=fopen($working_directory_location_parametric."results250_3.js","w");
					if(!$fp1){

						 echo "unable to open file";
					}

					$str="var data = [
					";

					$foldsize=($count/10);
					if($foldsize<=0){
					$foldsize=1;
					}
					for($i=0;$i<$count;$i++){
						 $str=$str."{'group':".(int)($i/$foldsize);
						 $str=$str.",'azimuth':$azimuth1[$i]";
								 $str=$str.",'wwr':$wwr1[$i]";
								 $str=$str.",'overhang':$depth1[$i]";
								 $str=$str.",'aspectRatio':$ratio1[$i]";
								 $str=$str.",'shgc':$shgc1[$i]";
								 $str=$str.",'energy':$energy1[$i]";
						 if($i==$count-1){
						 $str=$str."}
					";
						 }
						 else{
						 $str=$str."},
					";

						 }
					}
					$str=$str."];";
					fwrite($fp1,$str);
					fclose($fp1);
					$working_directory_location_parametric = "./working_directory/nonparametric/$unique_counter/";

					$azimuth1=array();
					$energy1=array();
					$wwr1=array();
					$ratio1=array();
					$shgc1=array();
					$depth1=array();
					$u_factor1=array();
					$vlt1=array();
					$count=0;
					$file=fopen($working_directory_location_parametric."finalvalues.txt","r");
					$flag=0;
					if($file == NULL){

					}
					else{
						 while(!feof($file))
						 {
							 $no=0;
							 $a= fgets($file);
							 $len=strlen($a);
							 if($len==0 or $len==1)
							 {
								break;
							 }
							 $piece=explode(" ",$a);
							 $energy1[$count]=$piece[0];
							 $azimuth1[$count]=$piece[1];
							 $wwr1[$count]=$piece[2];
							 $depth1[$count]=$piece[3];
							 $ratio1[$count]=$piece[4];
							 $shgc1[$count]=$piece[5];
							 $u_factor1[$count]=$piece[6];
							 $vlt1[$count]=$piece[7];
							 $count=$count+1;
						  }

						  //sorting the values
						  $x1=0;
						  $y1=0;
						  while($x1 < $count)
						  {
							$y1=0;
							while($y1 < $count)
							{
							  if($energy1[$x1] < $energy1[$y1])
							  {
								$temp1=$energy1[$x1];
								$energy1[$x1]=$energy1[$y1];
								$energy1[$y1]=$temp1;


								$temp1=$azimuth1[$x1];
								$azimuth1[$x1]=$azimuth1[$y1];
								$azimuth1[$y1]=$temp1;

								$temp1=$wwr1[$x1];
								$wwr1[$x1]=$wwr1[$y1];
								$wwr1[$y1]=$temp1;

								$temp1=$depth1[$x1];
								$depth1[$x1]=$depth1[$y1];
								$depth1[$y1]=$temp1;

								$temp1=$ratio1[$x1];
								$ratio1[$x1]=$ratio1[$y1];
								$ratio1[$y1]=$temp1;

								$temp1=$shgc1[$x1];
								$shgc1[$x1]=$shgc1[$y1];
								$shgc1[$y1]=$temp1;

								$temp1=$u_factor1[$x1];
								$u_factor1[$x1]=$u_factor1[$y1];
								$u_factor1[$y1]=$temp1;

								$temp1=$vlt1[$x1];
								$vlt1[$x1]=$vlt1[$y1];
								$vlt1[$y1]=$temp1;

							  }
							  $y1=$y1+1;
							}
							$x1=$x1+1;
						  }
					}

					$fp1=fopen($working_directory_location_parametric."results250_3.js","w");
					if(!$fp1){

						 echo "unable to open file";
					}

					$str="var data = [
					";

					$foldsize=($count/10);
					if($foldsize<=0){
					$foldsize=1;
					}
					for($i=0;$i<$count;$i++){
						 $str=$str."{'group':".(int)($i/$foldsize);
						 $str=$str.",'azimuth':$azimuth1[$i]";
								 $str=$str.",'wwr':$wwr1[$i]";
								 $str=$str.",'overhang':$depth1[$i]";
								 $str=$str.",'aspectRatio':$ratio1[$i]";
								 $str=$str.",'shgc':$shgc1[$i]";
								 $str=$str.",'energy':$energy1[$i]";
						 if($i==$count-1){
						 $str=$str."}
					";
						 }
						 else{
						 $str=$str."},
					";

						 }
					}
					$str=$str."];";
					fwrite($fp1,$str);
					fclose($fp1);
					$working_directory_location_parametric = "./working_directory/nonparametric/$unique_counter/";

					$azimuth1=array();
					$energy1=array();
					$wwr1=array();
					$ratio1=array();
					$shgc1=array();
					$depth1=array();
					$u_factor1=array();
					$vlt1=array();
					$count=0;
					$file=fopen($working_directory_location_parametric."finalvalues.txt","r");
					$flag=0;
					if($file == NULL){

					}
					else{
						 while(!feof($file))
						 {
							 $no=0;
							 $a= fgets($file);
							 $len=strlen($a);
							 if($len==0 or $len==1)
							 {
								break;
							 }
							 $piece=explode(" ",$a);
							 $energy1[$count]=$piece[0];
							 $azimuth1[$count]=$piece[1];
							 $wwr1[$count]=$piece[2];
							 $depth1[$count]=$piece[3];
							 $ratio1[$count]=$piece[4];
							 $shgc1[$count]=$piece[5];
							 $u_factor1[$count]=$piece[6];
							 $vlt1[$count]=$piece[7];
							 $count=$count+1;
						  }

						  //sorting the values
						  $x1=0;
						  $y1=0;
						  while($x1 < $count)
						  {
							$y1=0;
							while($y1 < $count)
							{
							  if($energy1[$x1] < $energy1[$y1])
							  {
								$temp1=$energy1[$x1];
								$energy1[$x1]=$energy1[$y1];
								$energy1[$y1]=$temp1;


								$temp1=$azimuth1[$x1];
								$azimuth1[$x1]=$azimuth1[$y1];
								$azimuth1[$y1]=$temp1;

								$temp1=$wwr1[$x1];
								$wwr1[$x1]=$wwr1[$y1];
								$wwr1[$y1]=$temp1;

								$temp1=$depth1[$x1];
								$depth1[$x1]=$depth1[$y1];
								$depth1[$y1]=$temp1;

								$temp1=$ratio1[$x1];
								$ratio1[$x1]=$ratio1[$y1];
								$ratio1[$y1]=$temp1;

								$temp1=$shgc1[$x1];
								$shgc1[$x1]=$shgc1[$y1];
								$shgc1[$y1]=$temp1;

								$temp1=$u_factor1[$x1];
								$u_factor1[$x1]=$u_factor1[$y1];
								$u_factor1[$y1]=$temp1;

								$temp1=$vlt1[$x1];
								$vlt1[$x1]=$vlt1[$y1];
								$vlt1[$y1]=$temp1;

							  }
							  $y1=$y1+1;
							}
							$x1=$x1+1;
						  }
					}

					$fp1=fopen($working_directory_location_parametric."results250_3.js","w");
					if(!$fp1){

						 echo "unable to open file";
					}

					$str="var data = [
					";

					$foldsize=($count/10);
					if($foldsize<=0){
					$foldsize=1;
					}
					for($i=0;$i<$count;$i++){
						 $str=$str."{'group':".(int)($i/$foldsize);
						 $str=$str.",'azimuth':$azimuth1[$i]";
								 $str=$str.",'wwr':$wwr1[$i]";
								 $str=$str.",'overhang':$depth1[$i]";
								 $str=$str.",'aspectRatio':$ratio1[$i]";
								 $str=$str.",'shgc':$shgc1[$i]";
								 $str=$str.",'energy':$energy1[$i]";
						 if($i==$count-1){
						 $str=$str."}
					";
						 }
						 else{
						 $str=$str."},
					";

						 }
					}
					$str=$str."];";
					fwrite($fp1,$str);
					fclose($fp1);
					
							
				 $viewModel = new ViewModel(array(
             'unique_counter' => $unique_counter,
         ));

					$viewModel->setTerminal(true);

					return $viewModel;

					
						//header("Location: mydisplay.php?unique_counter=".$unique_counter."&var_quantities=".$var_quantities);
				}

				close($fp);*/
		}
		
					
				 $viewModel = new ViewModel();

					$viewModel->setTerminal(true);

		
			return new ViewModel();
			
	 }


	 
	public function addhvac($template_file_data,$hvactype2){

			if($hvactype2==5){
		  $template_file_data=$template_file_data."!-   ===========  ALL OBJECTS IN CLASS: HVACTEMPLATE:ZONE:PTAC ===========
		HVACTemplate:Zone:PTAC,
			Testzone,                !- Zone Name
			Thermostat_test,         !- Template Thermostat Name
			autosize,                !- Cooling Supply Air Flow Rate {m3/s}
			autosize,                !- Heating Supply Air Flow Rate {m3/s}
			,                        !- No Load Supply Air Flow Rate {m3/s}
			1.2,                     !- Zone Heating Sizing Factor
			1.2,                     !- Zone Cooling Sizing Factor
			Sum,                     !- Outdoor Air Method
			0.00944,                 !- Outdoor Air Flow Rate per Person {m3/s}
			0.01,                    !- Outdoor Air Flow Rate per Zone Floor Area {m3/s-m2}
			,                        !- Outdoor Air Flow Rate per Zone {m3/s}
			,                        !- System Availability Schedule Name
			Fan sch,                 !- Supply Fan Operating Mode Schedule Name
			DrawThrough,             !- Supply Fan Placement
			0.7,                     !- Supply Fan Total Efficiency
			75,                      !- Supply Fan Delta Pressure {Pa}
			0.9,                     !- Supply Fan Motor Efficiency
			SingleSpeedDX,           !- Cooling Coil Type
			,                        !- Cooling Coil Availability Schedule Name
			autosize,                !- Cooling Coil Rated Capacity {W}
			autosize,                !- Cooling Coil Rated Sensible Heat Ratio
			3,                       !- Cooling Coil Rated COP {W/W}
			Electric,                !- Heating Coil Type
			,                        !- Heating Coil Availability Schedule Name
			autosize,                !- Heating Coil Capacity {W}
			0.8,                     !- Gas Heating Coil Efficiency
			,                        !- Gas Heating Coil Parasitic Electric Load {W}
			,                        !- Dedicated Outdoor Air System Name
			SupplyAirTemperature,    !- Zone Cooling Design Supply Air Temperature Input Method
			14.0,                    !- Zone Cooling Design Supply Air Temperature {C}
			,                        !- Zone Cooling Design Supply Air Temperature Difference {deltaC}
			SupplyAirTemperature,    !- Zone Heating Design Supply Air Temperature Input Method
			50.0,                    !- Zone Heating Design Supply Air Temperature {C}
			;                        !- Zone Heating Design Supply Air Temperature Difference {deltaC}
			";
			}

			else{
		$template_file_data=$template_file_data."!-   ===========  ALL OBJECTS IN CLASS: HVACTEMPLATE:ZONE:PTHP ===========
		HVACTemplate:Zone:PTHP,
			Testzone,                !- Zone Name
			Thermostat_test,         !- Template Thermostat Name
			autosize,                !- Cooling Supply Air Flow Rate {m3/s}
			autosize,                !- Heating Supply Air Flow Rate {m3/s}
			,                        !- No Load Supply Air Flow Rate {m3/s}
			1.25,                    !- Zone Heating Sizing Factor
			1.15,                    !- Zone Cooling Sizing Factor
			Sum,                     !- Outdoor Air Method
			0.00944,                 !- Outdoor Air Flow Rate per Person {m3/s}
			0.01,                    !- Outdoor Air Flow Rate per Zone Floor Area {m3/s-m2}
			,                        !- Outdoor Air Flow Rate per Zone {m3/s}
			,                        !- System Availability Schedule Name
			Fan sch,                 !- Supply Fan Operating Mode Schedule Name
			DrawThrough,             !- Supply Fan Placement
			0.7,                     !- Supply Fan Total Efficiency
			75,                      !- Supply Fan Delta Pressure {Pa}
			0.9,                     !- Supply Fan Motor Efficiency
			SingleSpeedDX,           !- Cooling Coil Type
			,                        !- Cooling Coil Availability Schedule Name
			autosize,                !- Cooling Coil Rated Capacity {W}
			autosize,                !- Cooling Coil Rated Sensible Heat Ratio
			3,                       !- Cooling Coil Rated COP {W/W}
			SingleSpeedDXHeatPump,   !- Heat Pump Heating Coil Type
			,                        !- Heat Pump Heating Coil Availability Schedule Name
			autosize,                !- Heat Pump Heating Coil Rated Capacity {W}
			2.75,                    !- Heat Pump Heating Coil Rated COP {W/W}
			-8,                      !- Heat Pump Heating Minimum Outdoor Dry-Bulb Temperature {C}
			5,                       !- Heat Pump Defrost Maximum Outdoor Dry-Bulb Temperature {C}
			ReverseCycle,            !- Heat Pump Defrost Strategy
			Timed,                   !- Heat Pump Defrost Control
			0.058333,                !- Heat Pump Defrost Time Period Fraction
			Electric,                !- Supplemental Heating Coil Type
			,                        !- Supplemental Heating Coil Availability Schedule Name
			autosize,                !- Supplemental Heating Coil Capacity {W}
			21,                      !- Supplemental Heating Coil Maximum Outdoor Dry-Bulb Temperature {C}
			0.8,                     !- Supplemental Gas Heating Coil Efficiency
			,                        !- Supplemental Gas Heating Coil Parasitic Electric Load {W}
			,                        !- Dedicated Outdoor Air System Name
			SupplyAirTemperature,    !- Zone Cooling Design Supply Air Temperature Input Method
			14,                      !- Zone Cooling Design Supply Air Temperature {C}
			11.11,                   !- Zone Cooling Design Supply Air Temperature Difference {deltaC}
			SupplyAirTemperature,    !- Zone Heating Design Supply Air Temperature Input Method
			50,                      !- Zone Heating Design Supply Air Temperature {C}
			30;                      !- Zone Heating Design Supply Air Temperature Difference {deltaC}
			";
			}

			return $template_file_data;

	}

}
