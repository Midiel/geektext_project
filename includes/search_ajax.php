<?php
require_once('connect.inc.php');
$value = $_GET['val'];
$result = '';
if (!empty($value))
{
  $query = "SELECT title FROM book WHERE `title` LIKE '%%$value%%' ORDER BY title LIMIT 0, 6";
  $query2 = "SELECT author FROM book WHERE `authors` LIKE '%%$value%%' ORDER BY author LIMIT 0, 6";

  $run = mysqli_query($con, $query) or die('Query 1 error: ' . mysqli_error($con));
  while ($row = mysqli_fetch_assoc($run))
  {
    $result .= 'Title: '.$row['title'].',';
  }
  $run2 = mysqli_query($con, $query2) or die('Query 2 error: ' . mysqli_error($con));
  while ($row2 = mysqli_fetch_assoc($run2))
  {
    $result .= 'Author: '.$row2['authors'].',';
  }
}


echo $result;

 ?>
