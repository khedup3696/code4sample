<?php 
////1) We were faced with challenge to migrate the old html version to our own custom CMS, so if we do manually it will take lots of ////time(more then 6000 articles), So the following code does the migration of html recursively into the directories and  by using a ///regex expression it will identifies the Title from the body and saves in our mysql databases.

//////////////////////////////   ***************************  ////////////////////////////////////


$link=mysql_connect('localhost','root','') or die(mysql_error());    //mysql Connection
mysql_select_db('tibet_net');								
						// mysql selection of database function where i am validating the date format of Y-m-d

function valid_date($date, $format='Y-m-d') { 										 
    $format = strtolower($format); 
    if (substr_count($date,"-")) $datebits = explode('-',$date); 
    if (substr_count($date,"/")) $datebits = explode('/',$date); 
    if (count($datebits) != 3) return false; 
    $year = intval($datebits[strpos($format, 'y')]); 
    $month = intval($datebits[strpos($format, 'm')]); 
    $day = intval($datebits[strpos($format, 'd')]); 
    return checkdate($month, $day, $year); 
}

function addhtml($topic, $location, $html, $date, $time)		// this is simple function to add the processed data i.e. $html with other variables to mysql database
{
	$sql="insert into pressold(topic, location, html, date, time) values('$topic', '$location', '$html', '$date', '$time')";
	$res=mysql_query($sql)or die("Invalid query: " . mysql_error());

	if($res)
	{
		//echo $topic.'Inserted properly'.'<br>';
	}
	else
	{
		echo $topic.'Not Inserted properly'.'<br>';
	}

}

function html_to_mysql($dir,$href,$anchortext)    //// here it is function where after getting the url of html file, 
										///////////   the function does the splitting of documents into title and the main content
{
	
		$location ='Dharamsala';				/// Since all the articles or new are published in Dharamsala.
		$month=substr($href,15,2);			/// /en/flash/2008/020908.html   two digits from 15th position
		$day=substr($href,20,2);				/// /en/flash/2008/020908.html   two digits from 20th position
		if($day>31)						
		{
			$day='0'.substr($day,0,1);
		}
		else
		if(!(is_numeric($day)))
		{
			$day='01';
		}
		
		$date=$dir.'-'.$month.'-'.$day;    ///// $dir will be the vitiable    e.g. 2008   so it becomes 2008-02-09
		
		$title=$anchortext;
$time='01:01:01';		////Since that time time wasn't important and we have limited time to migrate we just made them as 01:01:01
	$handle = fopen ('c:/wamp/www'.$href, "r");     /// we are option the file for instance  c:/wamp/www/en/flash/2008/020908.html 
		$htmlcontent = "";
		do {
			$data = fread ($handle, filesize ('c:/wamp/www'.$href));
			if (strlen($data) == 0) 			// if url isn't correct
			{
				break;					// stop current loop
			}
			$htmlcontent .= $data;			// else append al the data in variable as $htmlcontent
		} while(true);
		fclose ($handle);
		$html1=$htmlcontent;
		$str = preg_split('#<!---- Begin ----->#',$html1, -1, PREG_SPLIT_NO_EMPTY);    // old webiste has rule to have begin tag
		
		$str = preg_split('#<!---- End ----->#',$str[1], -1, PREG_SPLIT_NO_EMPTY);		// old webiste has rule to have end tag
		
		// html tags within <!---- Begin-----> and <!------ End ----- > tag is $str[1] which is stored below in $htmlfinal
			
		
		$htmlfinal=$str[1];
		$title=addslashes($title);						// we have to escape title
		$htmlfinal=addslashes($htmlfinal);				// we have to escape $htmlfinal

		//echo $htmlfinal;	
		addhtml($title, $location, $htmlfinal, $date, $time);   // then above function which i said will save to db will be called
}
$dir='';
if($_POST['Submit']!='')
{
					$dir=$_POST['year'];			// input variable which is year like 2008
					$handle = fopen ($dir.'/index.html', "r"); // within each year dic there is an index file with hasll all url of each individual pages
					$contents = "";
					do {
						$data = fread ($handle, filesize ($dir.'/index.html'));// reads the content of index to $data
						if (strlen($data) == 0) 
						{
								break;
						}
						$contents .= $data;
					} 
					while(true);
					fclose ($handle);
					
					$html=$contents;
				
					$match_result =	preg_match_all('/<a\s[^>]*href=\"([^\"]*)\"[^>]*>(.*)<\/a>/siU', 
					$html, $match_array,	PREG_SET_ORDER);// retrives the url using regex to look for anchor tag from index.html
					foreach ($match_array as $entry) 
					{
						$href = $entry[1]; 
						$anchortext = $entry[2];
						if($href!='#list')   // if url is not #list
						{
							html_to_mysql($dir,$href,$anchortext);   // call above function to read the file of $href
						}					
					}
}



/////////////////////////// ***************************** ///////////////////////////////////////
?>