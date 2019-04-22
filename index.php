<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tokoh Sejarah Indonesia</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,400italic' rel='stylesheet' type='text/css'>
    <!-- foundation css -->
<!--    <link rel="stylesheet" href="foundation/css/app.css">-->
<!--    tree view-->
    <link rel = "stylesheet"
          type = "text/css"
          href = "style.css" />
    </head>
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
    EasyRdf_Namespace::set('bio', 'http://purl.org/vocab/bio/0.1/');
    EasyRdf_Namespace::set('familyTree', 'http://www.co-ode.org/roberts/family-tree.owl#');
    EasyRdf_Namespace::set('ihero', 'http://www.semanticweb.org/asus/ontologies/2016/3/ihero/');

    $graph = new EasyRdf_Graph();
    $graph->parseFile('ihero.rdf', 'rdf');

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
                            foreach($graph->allOfType('ihero:Hero') as $name) {
                                echo "<option value='".$name."'>".str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $name->get('familyTree:hasName'))."</option>";
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
                echo "<h3>".str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $ins->get('familyTree:hasName'))."</h3><br/>";

                echo "<div class=\"tree\">";

//                // new -> hasParent
//                if($ins->get('familyTree:hasParent')){
//                    echo "<ul>";
//                    echo "<li>";
//                    foreach ($ins->all('familyTree:hasParent') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a>";
//                    }
//                }

                // new -> hasFather
                if($ins->get('familyTree:hasFather')){
                    echo "<ul>";
                    echo "<li>";
                    foreach ($ins->all('familyTree:hasFather') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a>";
                    }echo "\n----\n";
                }else{
                    echo "<ul>";
                    echo "<li>";
                    echo "<a href=\"#\">Father</a>";
                    echo "\n----\n";
                }

                // new -> hasMother
                if($ins->get('familyTree:hasMother')){
                    foreach ($ins->all('familyTree:hasMother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a>";
                    }
                }else{
                    echo "<a href=\"#\">Mother</a>";
                }

                // new -> hasStepParent
                if($ins->get('ihero:hasStepParent')){
                    echo "<tr><td>stepParent</td><td>";
                    foreach ($ins->all('ihero:hasStepParent') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
                    }
                }

                // new -> hasStepFather
                if($ins->get('ihero:hasStepFather')){
                    echo "<tr><td>stepFather</td><td>";
                    foreach ($ins->all('ihero:hasStepFather') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
                    }
                }

                // new -> hasStepMother
                if($ins->get('ihero:hasStepMother')){
                    echo "<tr><td>stepMother</td><td>";
                    foreach ($ins->all('ihero:hasStepMother') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
                    }
                }

                // new -> hasSibling
                if($ins->get('ihero:hasSibling')){
                    echo "<tr><td>sibling</td><td>";
                    foreach ($ins->all('ihero:hasSibling') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
                    }
                }

                // Name
                if($ins->get('familyTree:hasName')){
                    echo "<ul>";
                    echo "<li>";
                    foreach ($ins->all('familyTree:hasName') as $subject) {
                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject)."</a>";
                    }
                }

                // spouse
                if($ins->get('familyTree:isSpouseOf')){
                    echo "<ul>";
                    foreach ($ins->all('familyTree:isSpouseOf') as $subject) {
                        echo "<li>";
                        echo '<s href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</s>";
                        if($subject->get('familyTree:hasChild')){
                            echo "<ul>";
                            foreach ($ins->all('familyTree:hasChild') as $subject) {
                                echo "<li>";
                                echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
                                echo "</li>";
                            }
                            echo "</ul>";
                        }
                        echo "</li>";
                    }
                    echo "</ul>";
                }else{
                    echo "<a href=\"#\">Spouse</a>";
                }

//                // hasHusband
//                if($ins->get('familyTree:hasHusband')){
//                    echo "<tr><td>husband</td><td>";
//                    foreach ($ins->all('familyTree:hasHusband') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // hasWife
//                if($ins->get('familyTree:hasWife')){
//                    echo "<tr><td>wife</td><td>";
//                    foreach ($ins->all('familyTree:hasWife') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }

                // hasChild


//                // new -> hasSon
//                if($ins->get('familyTree:hasSon')){
//                    echo "<tr><td>son</td><td>";
//                    foreach ($ins->all('familyTree:hasSon') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasDaughter
//                if($ins->get('familyTree:hasDaughter')){
//                    echo "<tr><td>daughter</td><td>";
//                    foreach ($ins->all('familyTree:hasDaughter') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasStepChild
//                if($ins->get('ihero:hasStepChild')){
//                    echo "<tr><td>stepChild</td><td>";
//                    foreach ($ins->all('ihero:hasStepChild') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasStepSon
//                if($ins->get('ihero:hasStepSon')){
//                    echo "<tr><td>stepSon</td><td>";
//                    foreach ($ins->all('ihero:hasStepSon') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasStepDaughter
//                if($ins->get('ihero:hasStepDaughter')){
//                    echo "<tr><td>stepDaughter</td><td>";
//                    foreach ($ins->all('ihero:hasStepDaughter') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasBrother
//                if($ins->get('familyTree:hasBrother')){
//                    echo "<tr><td>brother</td><td>";
//                    foreach ($ins->all('familyTree:hasBrother') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasSister
//                if($ins->get('familyTree:hasSister')){
//                    echo "<tr><td>sister</td><td>";
//                    foreach ($ins->all('familyTree:hasSister') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasStepSibling
//                if($ins->get('ihero:hasStepSibling')){
//                    echo "<tr><td>stepSibling</td><td>";
//                    foreach ($ins->all('ihero:hasStepSibling') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasStepBrother
//                if($ins->get('ihero:hasStepBrother')){
//                    echo "<tr><td>stepBrother</td><td>";
//                    foreach ($ins->all('ihero:hasStepBrother') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // new -> hasStepSister
//                if($ins->get('ihero:hasStepSister')){
//                    echo "<tr><td>stepSister</td><td>";
//                    foreach ($ins->all('ihero:hasStepSister') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isHusbandOf
//                if($ins->get('familyTree:isHusbandOf')){
//                    echo "<tr><td>isHusbandOf</td><td>";
//                    foreach ($ins->all('familyTree:isHusbandOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isWifeOf
//                if($ins->get('familyTree:isWifeOf')){
//                    echo "<tr><td>isWifeOf</td><td>";
//                    foreach ($ins->all('familyTree:isWifeOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isParentOf
//                if($ins->get('familyTree:isParentOf')){
//                    echo "<tr><td>isParentOf</td><td>";
//                    foreach ($ins->all('familyTree:isParentOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isFatherOf
//                if($ins->get('familyTree:isFatherOf')){
//                    echo "<tr><td>isFatherOf</td><td>";
//                    foreach ($ins->all('familyTree:isFatherOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isMotherOf
//                if($ins->get('familyTree:isMotherOf')){
//                    echo "<tr><td>isMotherOf</td><td>";
//                    foreach ($ins->all('familyTree:isMotherOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isChildOf
//                if($ins->get('familyTree:isChildOf')){
//                    echo "<tr><td>isChildOf</td><td>";
//                    foreach ($ins->all('familyTree:isChildOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isSonOf
//                if($ins->get('familyTree:isSonOf')){
//                    echo "<tr><td>isSonOf</td><td>";
//                    foreach ($ins->all('familyTree:isSonOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isDaughterOf
//                if($ins->get('familyTree:isDaughterOf')){
//                    echo "<tr><td>isDaughterOf</td><td>";
//                    foreach ($ins->all('familyTree:isDaughterOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isBrotherOf
//                if($ins->get('familyTree:isBrotherOf')){
//                    echo "<tr><td>isBrotherOf</td><td>";
//                    foreach ($ins->all('familyTree:isBrotherOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
//
//                // isSisterOf
//                if($ins->get('familyTree:isSisterOf')){
//                    echo "<tr><td>isSisterOf</td><td>";
//                    foreach ($ins->all('familyTree:isSisterOf') as $subject) {
//                        echo '<a href="?entity='.urlencode($subject).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "", $subject->get('familyTree:hasName'))."</a><br/>";
//                    }
//                    echo "</td></tr>";
//                }
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