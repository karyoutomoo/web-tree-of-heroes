/**
 * Created by PhpStorm.
 * User: ASUS
 * Date: 4/11/2019
 * Time: 7:44 PM
 */

<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Tokoh Sejarah Indonesia</title>
  <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,400italic' rel='stylesheet' type='text/css'>
  <!-- foundation css -->
  <link rel="stylesheet" href="foundation/css/app.css">
  <!-- icon -->
  <link rel="stylesheet" href="foundation/bower_components/foundation-icons/foundation-icons.css">

  <meta class="foundation-mq">
</head>
<body>
  <div id="wrapper">
    <div data-sticky-container>
      <div data-sticky data-margin-top='0' data-top-anchor="top" data-btm-anchor="content:bottom">
        <div class="top-bar">
          <div class="row">
            <div class="large-2 columns large-centered">
             <h4><strong>Pohon Keluarga</strong></h4>
            </div>
          </div>
        </div>
      </div>
    </div>

  <br><br>


  <?php
  require 'vendor/autoload.php';

  EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
  EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
  EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
  EasyRdf_Namespace::set('owl', 'http://www.w3.org/2002/07/owl#');
  EasyRdf_Namespace::set('bio', 'http://purl.org/vocab/bio/0.1/');
  EasyRdf_Namespace::set('familyTree', 'http://www.co-ode.org/roberts/family-tree.owl#');
  EasyRdf_Namespace::set('ihero', 'http://www.semanticweb.org/asus/ontologies/2016/3/ihero/');
  EasyRdf_Namespace::set('dbpedia-resource','http://id.dbpedia.org/resource/');
  EasyRdf_Namespace::set('dbpedia-property','http://id.dbpedia.org/property/');
  EasyRdf_Namespace::set('dbpedia-ontology','http://id.dbpedia.org/ontology/');

  $graph = EasyRdf_Graph::newAndLoad('http://localhost:3030/famtree/get','rdfxml');
  print $graph->dump();
  $localhost = "localhost/famtree";

  //foreach($graph->allOfType('time:Interval') as $name) {
                //if (preg_match('/Yankee/i', $book->get('ont:bookHasTitle'))) {
   // echo "<option value='".$name."'>".$name."</option>";
          // echo "Start : ".$name->get('time:hasBeginning')->get('time:inDateTime')."<br>";
          // echo "End : ".$name->get('time:hasEnd')->get('time:inDateTime')."<br>";
                //}
  //}
//}


  ?>
  <div class="row content">
    <div class="large-8 columns large-centered">
      <div class="callout">
        <h6 class="subheader">PILIH TOKOH</h6>
          <form method="GET" action="#">
            <div class="input-group">
              <select class="input-group-field" name="entity">
                <?php
                  foreach($graph->allOfType('owl:Thing') as $name) {
                  echo "<option value='".$name."'>".str_replace('', "", $name->get('dbpedia-property:name'))."</option>";
                }
                ?>
              </select>
              <div class="input-group-button">
                <input type="submit" class="button" value="Submit">
              </div>
            </div>
          </form>
        </div>
      </div>
  </div>
  <div class="row">
    <div class="large-8 columns large-centered">
      <?php
        if(isset($_GET['entity'])) {
          echo "
          <div class="."callout".">
            <h6 class="."subheader".">DESKRIPSI INSTANCE</h6>";

            // Nama Instance
            $ins = $graph->resource($_GET['entity']);
            echo "<h3>".str_replace('http://www.co-ode.org/roberts/family-tree.owl#', "", $ins->get('dbpedia-property:name'))."</h3><br/>";

            echo "<table>";
            echo "<tr>";

            // Name
            if($ins->get('foaf:name')){
              echo "<tr><td>name</td><td>";
              foreach ($ins->all('dbpedia-property:name') as $subject) {
                echo str_replace('http://www.co-ode.org/roberts/family-tree.owl#', "", $subject)."<br/>";
              }
              echo "</td></tr>";
            }

            // spouse
            if($ins->get('dbpedia-ontology:spouse')){
              echo "<tr><td>spouse</td><td>";
              foreach ($ins->all('dbpedia-ontology:spouse') as $subject) {
                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.co-ode.org/roberts/family-tree.owl/', "", $subject->get('foaf:name'))."</a><br/>";
              }
              echo "</td></tr>";
            }

            // new -> hasParent
            if($ins->get('dbpedia-ontology:parent')){
              echo "<tr><td>parent</td><td>";
              foreach ($ins->all('dbpedia-property:hasParent') as $subject) {
                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('foaf:name'))."</a><br/>";
              }
              echo "</td></tr>";
            }

            // new -> hasFather
            if($ins->get('dbpedia-ontology:father')){
              echo "<tr><td>father</td><td>";
              foreach ($ins->all('dbpedia-ontology:father') as $subject) {
                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('foaf:name'))."</a><br/>";
              }
              echo "</td></tr>";
            }

            // new -> hasMother
            if($ins->get('dbpedia-ontology:mother')){
              echo "<tr><td>mother</td><td>";
              foreach ($ins->all('dbpedia-ontology:mother') as $subject) {
                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('foaf:name'))."</a><br/>";
              }
              echo "</td></tr>";
            }

            // hasChild
            if($ins->get('dbpedia-ontology:children')){
              echo "<tr><td>child</td><td>";
              foreach ($ins->all('dbpedia-ontology:children') as $subject) {
                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('dbpedia-property:name'))."</a><br/>";
              }
              echo "</td></tr>";
            }


            // new -> hasSibling
            if($ins->get('ihero:hasSibling')){
              echo "<tr><td>sibling</td><td>";
              foreach ($ins->all('ihero:hasSibling') as $subject) {
                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('foaf:name'))."</a><br/>";
              }
              echo "</td></tr>";
            }

            echo "</table>";
            echo "</div>";
        }
        ?>
      </div>
    </div>

    <footer>
      <div class="row">
        <!--
		<div class="medium-6 columns">
          <ul class="menu">
            <li><a href="#">Legal</a></li>
            <li><a href="#">Partner</a></li>
            <li><a href="#">Explore</a></li>
          </ul>
        </div>
        <div class="medium-6 columns">
          <ul class="menu float-right">
            <li class="menu-text">Tugas Akhir 2016</li>
          </ul>
        </div>
		-->
      </div>
    </footer>
  </div>
</body>
</html>