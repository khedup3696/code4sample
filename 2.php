<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>

<body>
<?php

include("dsn.php"); /// mysql connection
 mb_regex_encoding('UTF-8');   // encoding to utf-8 since we are dealing with tibetan unicode
      mb_internal_encoding("UTF-8");
if($_POST['testsubmit']!='') //if the button to translae is clicked
{
	
	//print_r($_POST);
	$toLanguage='english'; /// target language is english
	$text=$_POST['content']; // tibetan script
 
if($toLanguage=='english')
{
		$text=mb_ereg_replace("།","",$text); // ignore tibetan fullstop which indicates end of sentence
		$text=mb_split(" ",$text);// lets split sentence
		$converted='';
	
		$threearr=array();
		
		$twoarr= array();
		$onearr=array();
		
		
		foreach($text as $key=>$val)
		{
				
				$wordarr3=mb_split("་",$val); /// all single segment of words are separated
				$wordarr1=array();
				$i=0;
				
				//three word check
				
				$wordarr3Size=sizeof($wordarr3); //the size of array of wordarr3
				while($wordarr3Size>$i)  // untill i becomes less than 0
				{
					$sql='select wordDef from dictionary where dictionaryType=2 and word=:wordCheck';  /// selecting wordDef with type 2 = english 					                                                      										and word = variable which is preprocessor variable
					$wth= $dbh->prepare($sql);
					$wth->bindParam(':wordCheck',$threeWordCheck, PDO::PARAM_STR, 30);   // mysql data is being check with variable $threeWordCheck
					$threeWordCheck=$wordarr3[$i].'་'.$wordarr3[$i+1].'་'.$wordarr3[$i+2].'་'; //$threeWordCheck is created here
					$wth->execute();
					if($wth->rowCount()>0)			// if row found eg གཞི་ཀ་་རྩེ།
					{		
						$threerow=$wth->fetch();
						$threearr[$i]=$threerow[0]; // create new array $threearr with exact index value as i so that we can arrange all translated
						$i=$i+3;			// increment the timer
					}
					else
					{
						$wordarr2[$i]=$wordarr3[$i]; // if three word seg not found traverse to next index 
													//and add to $wordarr2 to check word with two word segment
						$i++;
					}
				}
				
				$i=0;
				
				//two word check
				$wordarr2Size=sizeof($wordarr2);   // the words seg which are not found as three seg of word, check the size of $wordarr2
				while($wordarr2Size>$i)// untill i becomes less than 0
				{
					$sql='select wordDef from dictionary where dictionaryType=2 and word=:wordCheck';  /// selecting wordDef with type 2 = english 					                                                      										and word = variable which is preprocessor variable
					
					$wth= $dbh->prepare($sql);
					if($dbh->errorCode()<>'0000')
					{
						die("Error: ".implode(': ',$dbh->errorInfo())."\n");
					}
					$wth->bindParam(':wordCheck',$twoWordCheck, PDO::PARAM_STR, 30); // mysql data is being check with variable $threeWordCheck
				
					$twoWordCheck=$wordarr2[$i].'་'.$wordarr2[$i+1].'་';//$threeWordCheck is created here
					
					$wth->execute();
					if($dbh->errorCode()<>'0000')
					{
						die("Error: ".implode(': ',$dbh->errorInfo())."\n");
					}
					
					if($wth->rowCount()>0)			
					{		
						while($tworow=$wth->fetch())
						{
							$twoarr[i]=$tworow[0];  // if found life 3 seg word if 2 seg word are found 
						
						}
						$i=$i+2;			
					}
					else
					{
						$wordarr1[$i]=$wordarr2[$i];
						$i++;
					}
				}
				
				$i=0;
				
				//one word check 
				$wordarr1Size=sizeof($wordarr1); // all remaining words are considered to be word with only 1 segement as word so it check in db and brought corresponding word defination as $wrodarr1
				while($wordarr1Size>$i)
				{
					$sql='select wordDef from dictionary where dictionaryType=2 and word=:wordCheck';
					$wth= $dbh->prepare($sql);
					
					$wth->bindParam(':wordCheck',$oneWordCheck, PDO::PARAM_STR, 30);
					$oneWordCheck=$wordarr1[$i].'་';
					
					$wth->execute();
					if($wth->rowCount()>0)			
					{
						$onerow=$wth->fetch();
						$onearr[$i]=$onerow[0]; // assign the translated word to $onearr;
							
					}
					$i++;
				}
				
				$finalTranslated='';
				
				foreach($wordarr3 as $m=>$vm)/// all $threearr and  $twoarr and $onearr are brought together to arrange corresponding base on index 														value i
				{
					if($threearr[$m]!='')
					{
						$finalTranslated .= $threearr[$m].' '; // all are appened on echo $finalTranslated 
					}
					else
					if($twoarr[$m]!='')
					{
						$finalTranslated .= $twoarr[$m].' ';
					}
					else if($onearr[$m]!='')
					{
						$finalTranslated .= $onearr[$m].' ';
					}
					
					$m++;
				}
			
			
				
		}
		
		
}

}
echo $finalTranslated; // shows the result

?>

<form name="testfrom" method="post">
<textarea name="content" id="content" cols="70" rows="20">
</textarea>
<input type="submit" name="testsubmit"/>
</form>
</body>