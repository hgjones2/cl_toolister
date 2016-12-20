<?php
/*
  cl tool finder

  1. put in your own db creds.
  2. run scanner from cli
  3. query cl table (group by link!)
     example:
     SELECT link, body FROM cl WHERE body LIKE '%lathe%' GROUP BY link
  4. do NOT do it often.

*/
$DB_SERVER   = 'localhost';
$DB_USER     = 'root';
$DB_PASSWORD = 'root';
$DB_DATABASE = 'scotchbox';

// Old school db, for the kids
$connect = mysql_connect( $DB_SERVER, $DB_USER, $DB_PASSWORD );
@mysql_select_db( $DB_DATABASE, $connect ) or die( "Unable to select database");

// Why am I truncating? Because.
mysql_query("TRUNCATE cl");

// gimme that rss feed list
$content = file('tla.list');
$numLines = count($content);

for ($i = 0; $i < $numLines; $i++) {
  $feedURL = trim($content[$i]);

  // walk that dom
  $doc = new DOMDocument();
  $doc->load($feedURL);
  $arrFeeds = array();

  // fetch that data
  foreach ($doc->getElementsByTagName('item') as $node) {
    $itemRSS = array (
     'title'       => $node->getElementsByTagName('title')->item(0)->nodeValue,
     'body'        => $node->getElementsByTagName('description')->item(0)->nodeValue,
     'link'        => $node->getElementsByTagName('link')->item(0)->nodeValue,
     'submitted_date'        => $node->getElementsByTagName('date')->item(0)->nodeValue
   );

  array_push($arrFeeds, $itemRSS);

    // Put that data away, like a squirrel in the fall
    foreach($arrFeeds as $arrItem)
      {
        print_r($arrItem);
        print "\n";
        $sql='INSERT INTO cl (title,body,link,submitted_date) VALUES ("' . mysql_escape_string($arrItem[title]) . '","' . mysql_escape_string($arrItem[body]) . '","' . mysql_escape_string($arrItem[link]) . '", NOW())';
        mysql_query($sql)or die(mysql_error());
      }
    }
  }
  print "done.";
?>

