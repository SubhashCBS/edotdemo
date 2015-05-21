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
		//echo "<pre>";
		//print_r($_POST);
		
		$unique_counter = time();
		
		if(is_array($_POST['azimuth']))
		{
			$azi = $_POST['azimuth'];
		}
		else
		{
			$azi = array($_POST['azimuth']);
		}
		
		if(is_array($_POST['aspect-ratio']))
		{
			$lbybratio = $_POST['aspect-ratio'];
		}
		else
		{
			$lbybratio = array($_POST['aspect-ratio']);
		}
		
		$ashgc = $_POST['glass-types'];
		
		$wwr = array();

		foreach($_POST['wwr'] as $wr)
		{
			if(!empty($wr) & $wr != 0)
			{
				$wwr[] = $wr;
			}
		
		}
		
		
		//$_POST['over-hang'] = array(10);
		if(is_array($_POST['over-hang']))
		{
			$depth = $_POST['over-hang'];
		}
		else
		{
			$depth = array($_POST['over-hang']);
		}
			
			
		if($_POST['wwr-direction'] == 'different_dir')
		{
			$au_factor = array(0.5);
			$avlt = array(0.75);
		}
		else
		{
			$au_factor = $_POST['roof-type'];
			$avlt =  $_POST['wall-type'];
		}
		
		$au_factor = array_unique($au_factor);
		$avlt = array_unique($avlt);
		
		$glass_types =  $_POST['glass-types'];
			
		
		$hvactype2=6;
		$ptotal_area=50;
		$location2=2;
		//extract($_POST);
		//extract($_GET);

		//echo $unique_counter;

		$old = umask(0);
		mkdir($_SERVER['DOCUMENT_ROOT']."/edotdemo/working_directory/parametric/$unique_counter", 0777  ) or print "<br>Can not create working directory";//a working directory is made for every user where data related to him would be stored
		umask($old);


		/*sort($azi);
		sort($wwr);
		sort($depth);
		sort($lbybratio);*/

		$aazimuth = $azi;
		$awwr = $wwr;
		$adepth= $depth;
		$aratio = $lbybratio;

		print_r($azi);
		print_r($awwr);
		print_r($adepth);
		print_r($aratio);
		print_r($avlt);
		print_r($au_factor);
		print_r($glass_types);
		//exit;
		/*---------------------- make a copy of ini file for every user--------------------*/
		$fileno=1;
		$working_dir=$_SERVER['DOCUMENT_ROOT']."/edotedotdemo/working_directory/parametric/".$unique_counter;
		$working_directory=$_SERVER['DOCUMENT_ROOT']."/edotdemo/working_directory/parametric/".$unique_counter;

		$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/optLinux_template.ini";
		$file1 = fopen($file, "r") or die("can't open template file for reading");
		$theData = fread($file1, filesize($file));
		fclose($file1);
		$file="optLinux.ini";
		$file1 = fopen("$working_dir/$file", "w") or die("can't open template for reading");
		fwrite($file1,$theData);
		fclose($file1);

		/*---------------------- make a copy of cfg file for every user--------------------*/

		$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/EnergyPlusLinux.cfg";
		$file1 = fopen($file, "r") or die("can't open template file for reading");
		$theData = fread($file1, filesize($file));
		fclose($file1);

		$file="EnergyPlusLinux.cfg";
		$file1 = fopen("$working_dir/$file", "w") or die("can't open template for reading");
		fwrite($file1,$theData);
		fclose($file1);

		echo "count ".count($aazimuth).count($awwr).count($adepth).count($aratio).count($ashgc);

		$filesave=fopen($working_dir."/parametricvalues.txt",'w');
		$filecontent="";
		for($x=0;$x<count($aazimuth);$x++)
		{
			for($t=0;$t<count($avlt);$t++)
			{
				for($z=0;$z<count($adepth);$z++)
				{
					for($w=0;$w<count($aratio);$w++)
					{
						for($v=0;$v<count($ashgc);$v++)
						{
						
						for($u=0;$u<count($au_factor);$u++)
						{
						
						for($y=0;$y<count($awwr);$y++)
						{
						
						echo "hi";
							
							$filecontent=$filecontent.$aazimuth[$x];		
							$filecontent=$filecontent." ".$awwr[$y];		
							$filecontent=$filecontent." ".$adepth[$z];		
							$filecontent=$filecontent." ".$aratio[$w];		
							$filecontent=$filecontent." ".$ashgc[$v];		
							$filecontent=$filecontent." ".$au_factor[$u];		
							$filecontent=$filecontent." ".$avlt[$t];
							echo $filecontent=$filecontent." \n"	;
							/*---------------------- store data of template file for every user in a variable--------------------*/

							$file=$_SERVER['DOCUMENT_ROOT']."/edotdemo/tutorial_template.idf";
							$file1 = fopen($file, "r") or die("can't open template file for reading");
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

							$template_file_data = str_replace(array('%azimuth_angle%'),array($aazimuth[$x]),$template_file_data);
							$height_of_window=3;//fixing the height of the window to 3; according the given model

							$wwr_height=$awwr[$y]/100*$height_of_window;
							$wwr_startz=$height_of_window/2-$wwr_height/2;

							$template_file_data = str_replace(array('%wwr_height%','%wwr_startz%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height1%','%wwr_startz1%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height2%','%wwr_startz2%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height3%','%wwr_startz3%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array('%wwr_height4%','%wwr_startz4%'),array($wwr_height,$wwr_startz),$template_file_data);
							$template_file_data = str_replace(array("%depth%"),array($adepth[$z]),$template_file_data);

							$lbybratio_length_value=sqrt($aratio[$w]*$ptotal_area);
							$lbybratio_breadth_value=$ptotal_area/$lbybratio_length_value;
							$template_file_data = str_replace(array("%lbybratio_length%","%lbybratio_breadth%"),array($lbybratio_length_value,$lbybratio_breadth_value),$template_file_data);
							
							
							$template_file_data=str_replace(array("%u_factor%"), array($au_factor[$v]),$template_file_data);
							$template_file_data=str_replace(array("%shgc%"), array($ashgc[$v]),$template_file_data);
							$template_file_data=str_replace(array("%vlt%"), array($avlt[$v]),$template_file_data);




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

		echo $filecontent;

		fwrite($filesave,$filecontent);
		fclose($filesave);

		$cityname="Hyderabad.epw";
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
		}

		/*$host="localhost";
		$port =800;  //port number
		$fp = fsockopen($host, $port, $errno, $errstr);
		if( !$fp)
		{
				die ("couldnot connect to server");
		}
		socket_set_timeout($fp, 300);
		if (!$fp)
		{
				$result = "Error: could not open socket connection";
				echo $result;
		}
		else
		{*/
			/*$str = $_SERVER['DOCUMENT_ROOT']."/edotdemo/working_directory/parametric/".$unique_counter." ".$cityname;
				fputs ($fp, $str);
				$msg="";
				$msg=fgets($fp,17);
				sleep(5);
				echo "message from server.c is $msg <br>";
				if($msg!="")
				{
						//header("Location: mydisplay.php?unique_counter=".$unique_counter."&var_quantities=".$var_quantities);
				}

				close($fp);*/
		//}
			//return new ViewModel();
			
			         $sumofvarq=2;
         if($var_quantities[0]=='1'){
            $sumofvarq=$sumofvarq+1;
         }
         if($var_quantities[1]=='1'){
            $sumofvarq=$sumofvarq+1;            
         }
         if($var_quantities[1]=='2'){
            $sumofvarq=$sumofvarq+4;            
         }
         if($var_quantities[2]=='1'){
            $sumofvarq=$sumofvarq+1;
         }
         if($var_quantities[3]=='1'){
            $sumofvarq=$sumofvarq+1;
         }
         if($var_quantities[4]=='1'){
            $sumofvarq=$sumofvarq+1;
         }

         $filename = $working_directory."flagfile.txt";
         
         if (file_exists($filename)) 
         {
         }
         else{
         	echo "<script type='text/javascript'>
         		var bar1= createBar(300,15,'white',1,'black','blue',85,7,3,'');
         	</script>
         		";
         }

		 
		  $item1=array();
         $item2=array();
         $item3=array();
         $item4=array();
         $item5=array();
         $item6=array();
	     $itme32=array();
         $itme33=array();
         $itme34=array();         
	     $count=0;
         $file=fopen($working_directory."parametricvalues.txt","r");
         $flag=0;
         if($file == NULL){
         	echo "null file found";
         }
         else{
         	//parsing the file till the end to get the output generated by genopt
         	while(!feof($file))
         	{
         		$no=0;
         		$a= fgets($file);
         		$len=strlen($a);
         		if($len==0 or $len==1)
         		{
         			continue;
         		}
         		else
         		{
         			if($len > 4 and $a[0]=='S' and $a[1]=='i' and $a[2]=='m' and $a[3]=='u' and $a[4]=='l' and $a[5]=='a')
         			{
         				$flag=1;
         				continue;
         			}
         			if($flag==1)
         			{
         				$piece = preg_split('/[\s]+/', $a);
         				$item1[$count]=$piece[4];
         				$item1[$count]=((float)($item1[$count]))/(3600000);
         				$take=5;
         				if($var_quantities[0]=="1")//for azimuth
         				{
         					$item2[$count]=$piece[$take];
         					$take=$take+1;
         				}
         				else
         				{
         					$item2[$count]=1;
         				}
         
         			if($var_quantities[1]=="1")//for wwr
                                {
                                        $item3[$count]=$piece[$take];
                                        $item3[$count]=$item3[$count]/3*100;//to get back the ratio
                                        $item32[$count]=1;
                                        $item33[$count]=1;
                                        $item34[$count]=1;
                                        $take=$take+1;
                                }
                                else if($var_quantities[1]=="0")
                                {
                                        $item3[$count]=1;
                                        $item32[$count]=1;
                                        $item33[$count]=1;
                                        $item34[$count]=1;
                                }
                                else
                                {
                                        $item3[$count]=$piece[$take];
                                        $item3[$count]=$item3[$count]/3*100;//converting height to wwr of the window
                                        $take=$take+1;

                                        $item32[$count]=$piece[$take];
                                        $item32[$count]=$item32[$count]/3*100;
                                        $take=$take+1;

                                        $item33[$count]=$piece[$take];
                                        $item33[$count]=$item33[$count]/3*100;
                                        $take=$take+1;

                                        $item34[$count]=$piece[$take];
                                        $item34[$count]=$item34[$count]/3*100;
                                        $take=$take+1;

                                }
         				if($var_quantities[2]=="1")//overhange depth
         				{
         					$item4[$count]=$piece[$take];
         					$take=$take+1;
         				}
         				else
         				{
         					$item4[$count]=1;
         				}
         				if($var_quantities[3]=="1")//aspect ratio
         				{
         					$item5[$count]=$piece[$take];
         					$item5[$count]=($item5[$count]*$item5[$count])/$total_area;//converting length to aspect ratio
         					$take=$take+1;
         				}
         				else
         				{
         					$item5[$count]=1;
         				}
         				if($var_quantities[4]=="1")
         				{
         					$item6[$count]=$piece[$take];
         					$take=$take+1;
         				}
         				else
         				{
         					$item6[$count]=1;
         				}
         				$count=$count+1;
         			}          
         
         		}
         	}
         	fclose($file);
         }
         $x1=0;
         $y1=0;
         
         //sorting the output
         
         while($x1 < $count)
         {
         	$y1=0;
         	while($y1 < $x1)
         	{
         		if($item1[$x1] < $item1[$y1])
         		{
         			$temp1=$item1[$x1];
         			$item1[$x1]=$item1[$y1];
         			$item1[$y1]=$temp1;
         
         
         			$temp2=$item2[$x1];
         			$item2[$x1]=$item2[$y1];
         			$item2[$y1]=$temp2;
         
         
         			$temp3=$item3[$x1];
         			$item3[$x1]=$item3[$y1];
         			$item3[$y1]=$temp3;

                    $temp32=$item32[$x1];
                    $item32[$x1]=$item32[$y1];
                    $item32[$y1]=$temp32;

                    $temp33=$item33[$x1];
                    $item33[$x1]=$item33[$y1];
                    $item33[$y1]=$temp33;

                    $temp34=$item34[$x1];
                    $item34[$x1]=$item34[$y1];
                    $item34[$y1]=$temp34;


                    $temp4=$item4[$x1];
                    $item4[$x1]=$item4[$y1];
                    $item4[$y1]=$temp4;

                    $temp5=$item5[$x1];
         			$item5[$x1]=$item5[$y1];
         			$item5[$y1]=$temp5;
         
         			$temp6=$item6[$x1];
         			$item6[$x1]=$item6[$y1];
         			$item6[$y1]=$temp6;
         
         		}
         		$y1=$y1+1;
         	}
         	$x1=$x1+1;
         
         }
         
         
         $fp1=fopen($working_directory."/results250.js","w");
         if(!$fp1){
         	echo "unable to open file";
         }
         $str="var foods = [
         ";
         
         $foldsize=($count/10);
         if($foldsize<=0){
         	$foldsize=1;
         }
         for($i=0;$i<$count;$i++){
         	$str=$str."{'group':".(int)($i/$foldsize);
         	if($var_quantities[0]=='1'){
         		$str=$str.",'Orientation (degrees)':$item2[$i]";
         	}
         	 if($var_quantities[1]=='1'){
                                $str=$str.",'WWR (%)':$item3[$i]";
                }

                if($var_quantities[1]=='2'){
                                $str=$str.",'Front WWR (%)':$item3[$i]";
                                $str=$str.",'Back WWR (%)':$item32[$i]";
                                $str=$str.",'Right WWR (%)':$item33[$i]";
                                $str=$str.",'Left WWR (%)':$item34[$i]";
                }

         	if($var_quantities[2]=='1'){
         		$str=$str.",'Overhang Depth (m)':$item4[$i]";
         	}
         	if($var_quantities[3]=='1'){
         		$str=$str.",'Aspect Ratio':$item5[$i]";
         	}
         	if($var_quantities[4]=='1'){
         		$str=$str.",'SHGC':$item6[$i]";
         	}
         	$str=$str.",'Energy (kWh)':$item1[$i]";
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
		 
		  //checking whether all updates have been performed or not or do we need to still update the graph page
         $filename = $working_directory."flagfile.txt";
         
         if (file_exists($filename)) 
         {
         	echo "redirecting";
         	//echo("<meta http-equiv=\"refresh\" content=\"4;URL=mydisplay.php?unique_counter=".$unique_counter."&var_quantities=".$var_quantities."\">");
         }
         
         else{
         	//echo "Will update new results shortly".$var_quantities;
         	//echo("<meta http-equiv=\"refresh\" content=\"10;URL=displaygenopt_ver1.php?unique_counter=".$unique_counter."&var_quantities=".$var_quantities."&total_area=".$total_area."\">");
         }
        
		/* $viewModel = new ViewModel(array(
				'foo' => 'bar'
			));

			$viewModel->setTerminal(true);

			return $viewModel;*/
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
