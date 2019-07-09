<?php
require_once('connect.inc.php');
$value = $_GET['val'];
$result = '';
if (!empty($value))
{
  $query_tittle = "SELECT title FROM book WHERE `title` LIKE '%%$value%%' ORDER BY title LIMIT 0, 6";
  $query_author = "SELECT authors FROM book WHERE `authors` LIKE '%%$value%%' GROUP BY authors ORDER BY authors LIMIT 0, 6";
  $query_genre = "SELECT category FROM book WHERE `category` LIKE '%%$value%%' GROUP BY category ORDER BY category LIMIT 0, 6";

  $run_tittle = mysqli_query($con, $query_tittle) or die('Query 1 error: ' . mysqli_error($con));
  while ($row_tittle = mysqli_fetch_assoc($run_tittle))
  {
    $result .= 'Title: '.$row_tittle['title'].',';
  }
  $run_author = mysqli_query($con, $query_author) or die('Query 2 error: ' . mysqli_error($con));
  while ($row_author = mysqli_fetch_assoc($run_author))
  {
    $temp_author = $row_author['authors'];
    $author_array = explode(',', $temp_author);
    for($i = 0; $i < count($author_array); $i++)
    {
      $result .= 'Author: '.$author_array[$i].',';
    }

  }
  $run_genre = mysqli_query($con, $query_genre) or die('Query 3 error: ' . mysqli_error($con));
  while ($row_genre = mysqli_fetch_assoc($run_genre))
  {
    $result .= 'Genre: '.$row_genre['category'].',';
  }
}

$result = remove_doubles($result);
echo $result;

function remove_doubles($string)
{
  $new_array = array();
  $string_array = explode(',', $string);
  $result = '';
  for ($i = 0; $i < count($string_array); $i++)
  {
    $new_array = remove_helper($string_array[$i], $new_array);
  }
  for ($i = 0; $i < count($new_array); $i++)
  {
    $result .= $new_array[$i].',';
  }
  return $result;
}

function remove_helper($value, $arr)
{
  $exist = false;
  $counter = 0;
  for ($counter = 0; $counter < count($arr); $counter++)
  {
    if($value == $arr[$counter])
    {
      $exist = true;
    }
  }
  if($exist == false)
  {
    $arr[$counter] = $value;
  }
  return $arr;
}
 ?>
