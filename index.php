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
                        <h4><strong>iHerolation</strong></h4>
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
    EasyRdf_Namespace::set('res','http://id.dbpedia.org/resource/');
    EasyRdf_Namespace::set('prop','http://id.dbpedia.org/property/');
    EasyRdf_Namespace::set('ont','http://id.dbpedia.org/ontology/');

    $graph = new EasyRdf_Graph();
    $graph->parseFile('result.rdf', 'rdf');

    $localhost = "localhost/ihero";

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
                                echo "<option value='".$name."'>".str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $name->get('prop:name'))."</option>";
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
                echo "<h3>".str_replace('http://www.co-ode.org/roberts/family-tree.owl#', "", $ins->get('prop:name'))."</h3><br/>";

                echo "<table>";
                echo "<tr>";

                // Name
                if($ins->get('prop:name')){
                    echo "<tr><td>name</td><td>";
                    foreach ($ins->all('prop:name') as $subject) {
                        echo str_replace('http://www.co-ode.org/roberts/family-tree.owl#', "", $subject)."<br/>";
                    }
                    echo "</td></tr>";
                }

                // spouse
                if($ins->get('prop:spouse')){
                    echo "<tr><td>spouse</td><td>";
                    foreach ($ins->all('prop:spouse') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.co-ode.org/roberts/family-tree.owl#', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // hasHusband
                if($ins->get('prop:husband')){
                    echo "<tr><td>husband</td><td>";
                    foreach ($ins->all('prop:husband') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // hasWife
                if($ins->get('prop:wife')){
                    echo "<tr><td>wife</td><td>";
                    foreach ($ins->all('prop:wife') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasParent
                if($ins->get('prop:parent')){
                    echo "<tr><td>parent</td><td>";
                    foreach ($ins->all('prop:parent') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasFather
                if($ins->get('prop:father')){
                    echo "<tr><td>father</td><td>";
                    foreach ($ins->all('prop:father') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasMother
                if($ins->get('prop:mother')){
                    echo "<tr><td>mother</td><td>";
                    foreach ($ins->all('prop:mother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepParent
                if($ins->get('ihero:hasStepParent')){
                    echo "<tr><td>stepParent</td><td>";
                    foreach ($ins->all('ihero:hasStepParent') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepFather
                if($ins->get('ihero:hasStepFather')){
                    echo "<tr><td>stepFather</td><td>";
                    foreach ($ins->all('ihero:hasStepFather') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepMother
                if($ins->get('ihero:hasStepMother')){
                    echo "<tr><td>stepMother</td><td>";
                    foreach ($ins->all('ihero:hasStepMother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // hasChild
                if($ins->get('prop:children')){
                    echo "<tr><td>children</td><td>";
                    foreach ($ins->all('prop:children') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasSon
                if($ins->get('prop:son')){
                    echo "<tr><td>son</td><td>";
                    foreach ($ins->all('prop:son') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasDaughter
                if($ins->get('prop:daughter')){
                    echo "<tr><td>daughter</td><td>";
                    foreach ($ins->all('prop:daughter') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepChild
                if($ins->get('ihero:hasStepChild')){
                    echo "<tr><td>stepChild</td><td>";
                    foreach ($ins->all('ihero:hasStepChild') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepSon
                if($ins->get('ihero:hasStepSon')){
                    echo "<tr><td>stepSon</td><td>";
                    foreach ($ins->all('ihero:hasStepSon') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepDaughter
                if($ins->get('ihero:hasStepDaughter')){
                    echo "<tr><td>stepDaughter</td><td>";
                    foreach ($ins->all('ihero:hasStepDaughter') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasSibling
                if($ins->get('prop:sibling')){
                    echo "<tr><td>sibling</td><td>";
                    foreach ($ins->all('prop:sibling') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasBrother
                if($ins->get('prop:brother')){
                    echo "<tr><td>brother</td><td>";
                    foreach ($ins->all('prop:brother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasSister
                if($ins->get('prop:sister')){
                    echo "<tr><td>sister</td><td>";
                    foreach ($ins->all('prop:sister') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepSibling
                if($ins->get('ihero:hasStepSibling')){
                    echo "<tr><td>stepSibling</td><td>";
                    foreach ($ins->all('ihero:hasStepSibling') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepBrother
                if($ins->get('ihero:hasStepBrother')){
                    echo "<tr><td>stepBrother</td><td>";
                    foreach ($ins->all('ihero:hasStepBrother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // new -> hasStepSister
                if($ins->get('ihero:hasStepSister')){
                    echo "<tr><td>stepSister</td><td>";
                    foreach ($ins->all('ihero:hasStepSister') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isHusbandOf
                if($ins->get('familyTree:isHusbandOf')){
                    echo "<tr><td>isHusbandOf</td><td>";
                    foreach ($ins->all('familyTree:isHusbandOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isWifeOf
                if($ins->get('familyTree:isWifeOf')){
                    echo "<tr><td>isWifeOf</td><td>";
                    foreach ($ins->all('familyTree:isWifeOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isParentOf
                if($ins->get('prop:parent')){
                    echo "<tr><td>isParentOf</td><td>";
                    foreach ($ins->all('prop:parent') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isFatherOf
                if($ins->get('prop:father')){
                    echo "<tr><td>isFatherOf</td><td>";
                    foreach ($ins->all('prop:father') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isMotherOf
                if($ins->get('prop:mother')){
                    echo "<tr><td>isMotherOf</td><td>";
                    foreach ($ins->all('prop:mother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isChildOf
                if($ins->get('familyTree:isChildOf')){
                    echo "<tr><td>isChildOf</td><td>";
                    foreach ($ins->all('familyTree:isChildOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isSonOf
                if($ins->get('familyTree:isSonOf')){
                    echo "<tr><td>isSonOf</td><td>";
                    foreach ($ins->all('familyTree:isSonOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isDaughterOf
                if($ins->get('familyTree:isDaughterOf')){
                    echo "<tr><td>isDaughterOf</td><td>";
                    foreach ($ins->all('familyTree:isDaughterOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isBrotherOf
                if($ins->get('familyTree:isBrotherOf')){
                    echo "<tr><td>isBrotherOf</td><td>";
                    foreach ($ins->all('familyTree:isBrotherOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
                    }
                    echo "</td></tr>";
                }

                // isSisterOf
                if($ins->get('familyTree:isSisterOf')){
                    echo "<tr><td>isSisterOf</td><td>";
                    foreach ($ins->all('familyTree:isSisterOf') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('prop:name'))."</a><br/>";
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