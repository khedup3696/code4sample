<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>Untitled Document</title>
</head>
<?php

Class checkCardClass
{	
		public function checkCard($card)
		{
			
			$vowels = array("c", "s", "d", "h", "C", "S", "D", "H");
$cardValue = strtolower(str_replace($vowels, "", $card));
			
			if($cardValue=='a')
				{
					 return 11;
				}
				else
				if($cardValue=='k' or $cardValue=='q' or $cardValue=='j')
				{
					return 10;
				}
				else if ($cardValue > 0 or $cardValue < 11)
				{
					 return $cardValue;
				}		
		}
}



if($_POST['checkBlackJack']!='') // if checkBlackJack button clicked
{
		// lets first split the card number
		$card1=$_POST['firstCard'];
		$card2=$_POST['secondCard'];
		
	if($card1 == $card2)
	{
		$message="please enter two unique cards";
	}
	else
	{
		$cardobj1 = new checkCardClass;
		$cardobj2 = new checkCardClass;
		$c1=$cardobj1->checkCard($card1);
		$c2=$cardobj2->checkCard($card2);
		
		$message=$c1+$c2;
		
	}
}




?>



<style>

#formdiv{ 	
			margin: auto;
			margin-top:200px;
			width:200px; 
			height:300px;
			text-align:center;

}
#formdiv input{
	margin:10px;
}
</style>

<body>	
<div id="formdiv">
<?php echo "<h3>the blackjack is <strong>".$message."</strong></h3><br>";?>

Enter the card value as AS for Ace Spade and 10C for 10 clove
<form name="blackjack" method="post">
<input type="text" name="firstCard" id="firstCard"/>
<input type="text" name="secondCard" id="secondCard"/>
<input type="submit" name="checkBlackJack" value="CheckBlackJack"/>
</form>
</div>
</body>
</html>