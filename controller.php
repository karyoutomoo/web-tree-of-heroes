<?php
function getInstance()
{
require 'vendor/autoload.php';

EasyRdf_Namespace::set('time', 'http://www.w3.org/2006/time#');
EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
EasyRdf_Namespace::set('ta', 'htttp://www.tugasakhir2016alief.id/ontology#');

$graph = new EasyRdf_Graph();
$graph->parseFile('tugasakhir2016alief.rdf', 'rdf');


  foreach($graph->allOfType('time:Interval') as $name) {
                //if (preg_match('/Yankee/i', $book->get('ont:bookHasTitle'))) {
    echo "<option value='".$name."'>".$name."</option>";
          // echo "Start : ".$name->get('time:hasBeginning')->get('time:inDateTime')."<br>";
          // echo "End : ".$name->get('time:hasEnd')->get('time:inDateTime')."<br>";
                //}
  }
}

if (isset($_GET['temporalentity'])) {
  echo "TE : ".htmlspecialchars($_GET['temporalentity']);
  
}

?>