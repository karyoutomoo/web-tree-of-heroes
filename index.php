<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tokoh Sejarah Indonesia</title>
    <link href='https://fonts.googleapis.com/css?family=Roboto:400,700,400italic' rel='stylesheet' type='text/css'>
    <!-- foundation css -->
    <link rel="stylesheet" href="foundation/css/app.css">
    <!--    tree view-->
    <link rel="stylesheet"
          type="text/css"
          href="style.css"/>
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
                        <h5><strong>Pohon Keluarga</strong></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br>


    <?php
    require 'vendor/autoload.php';
    require_once("sparqllib.php");

    EasyRdf_Namespace::set('rdf', 'http://www.w3.org/1999/02/22-rdf-syntax-ns#');
    EasyRdf_Namespace::set('rdfs', 'http://www.w3.org/2000/01/rdf-schema#');
    EasyRdf_Namespace::set('foaf', 'http://xmlns.com/foaf/0.1/');
    EasyRdf_Namespace::set('bio', 'http://purl.org/vocab/bio/0.1/');
    EasyRdf_Namespace::set('familyTree', 'http://www.co-ode.org/roberts/family-tree.owl#');
    EasyRdf_Namespace::set('ihero', 'http://www.semanticweb.org/asus/ontologies/2016/3/ihero/');


    $graph = new EasyRdf_Graph();
    $graph->parseFile('ihero.rdf', 'rdf');
    $data = sparql_get("localhost:3030/sample/query",
        "PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                        
                        SELECT ?s
                        WHERE {
                        ?s rdf:type <http://www.semanticweb.org/asus/ontologies/2016/3/ihero/Hero>
                        }
                        LIMIT 100");
    if (!isset($data)) {
        print "<p>Error: " . sparql_errno() . ": " . sparql_error() . "</p>";
    }
    //progres
    $i=0;
    foreach ($data as $row) {
        foreach ($data->fields() as $field) {
            $data[$i] = $row[$field];
            $i++;
        }
    }
    //progres
    ?>
    <div class="row content">
        <div class="large-up-8">
            <div class="callout">
                <h6 class="subheader">PILIH TOKOH</h6>
                <form method="GET" action="#">
                    <div class="input-group">
                        <select class="input-group-field" name="entity">
                            <?php
                            $j=0;//progres
                            foreach ($data as $row) {
                                foreach ($data->fields() as $name) { ?>
                                    <option selected value="<?= $row[$name] ?>"><?= $row[$name] ?></option>//progres
                                    <?$j++?>//progres
                                <?php }
                            } ?>
                        </select>
                        <div class="input-group-button">
                            <input type="submit" class="button" name="submit" value="Submit">
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="row content">
        <div class="large">
            <?php
            if (isset($_GET['entity'])) {
                echo "
                <div class=" . "callout " . " style=" . "padding-bottom:700px" . ">
                <h6 class=" . "subheader" . ">DESKRIPSI INSTANCE</h6>";

                $selected_val = $_GET['entity'];

                // Nama Instance
                $data_name = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?name
                                            WHERE {
                                            
                                                <' . $selected_val . '> fam:hasName ?name.
                                                ?URI fam:hasName ?name
                                            }
                                            LIMIT 1');
                if (!isset($data_name)) {
                    print "<p>Error: " . sparql_errno() . ": " . sparql_error() . "</p>";
                }
                foreach ($data_name as $row) {
                    foreach ($data_name->fields() as $field) {
                        print "<h3>$row[$field]</h3>";
                    }
                }
                echo "<div class=\"tree\" style=\"margin-left:150px\">";
                // new -> hasFather
                $data_father = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> fam:hasFather ?fatherIRI.
                                                ?fatherIRI fam:hasName ?name
                                            }
                                            LIMIT 1');
                $data_fatherIRI = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?fatherIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasFather ?fatherIRI
                                            }
                                            LIMIT 1');
                foreach ($data_fatherIRI as $row) {
                    foreach ($data_fatherIRI->fields() as $field) {
                        $fatherIRI = $row[$field];
                    }
                }
                if (!isset($data_father) || $data_father == '') {
                    echo "<ul>";
                    echo "<li>";
                    echo "<a>Father Unknown</a>";
                    echo "--❤--";
                } else {
                    foreach ($data_father as $row) {
                        foreach ($data_father->fields() as $field) {
                            echo "<ul>";
                            echo "<li>";
                            echo '<a href="?entity='.urlencode($fatherIRI).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "",$row[$field]).'</a>';
                            echo "--❤--";
                        }
                    }
                }

                // new -> hasMother
                $data_mother = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> fam:hasMother ?motherIRI.
                                                ?motherIRI fam:hasName ?name
                                            }
                                            LIMIT 1');
                $data_motherIRI = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?motherIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasMother ?motherIRI
                                            }
                                            LIMIT 1');
                foreach ($data_motherIRI as $row) {
                    foreach ($data_motherIRI->fields() as $field) {
                        $motherIRI = $row[$field];
                    }
                }
                if (!isset($data_mother) || $data_mother == '') {
                    echo "<a>Mother Unknown</a>";
                } else {
                    foreach ($data_mother as $row) {
                        foreach ($data_mother->fields() as $field) {
                            echo '<a href="?entity='.urlencode($motherIRI).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "",$row[$field]).'</a>';
                        }
                    }
                }

                // new -> hasSibling
                $data_sibling = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> fam:hasSister|fam:hasBrother ?siblingIRI.
                                                ?siblingIRI fam:hasName ?name
                                            }');
                $data_siblingIRI = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?siblingIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasSister|fam:hasBrother ?siblingIRI
                                            }');

                $i=0;
                foreach ($data_siblingIRI as $rowSiblingIRI) {
                    foreach ($data_siblingIRI->fields() as $field) {
                        $siblingIRI[$i] = $rowSiblingIRI[$field];
                        $i++;
                    }
                }

                if (!isset($data_sibling) || $data_sibling == '') {
                    echo "<ul>";
                } else {
                    echo "<ul>";
                    $i=0;
                    foreach ($data_sibling as $row) {
                        foreach ($data_sibling->fields() as $field) {
                            echo "<li>";
                            echo '<a href="?entity='.urlencode($siblingIRI[$i]).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "",$row[$field]).'</a>';
                            $i++;
                            echo "</li>";
                        }
                    }
                }


                // Name
                foreach ($data_name as $row) {
                    foreach ($data_name->fields() as $field) {
                        echo "<li>";
                        print "<a>$row[$field]</a>";
                    }
                }

                // spouse
                $data_spouse = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> fam:isSpouseOf ?sbj.
                                                ?sbj fam:hasName ?name
                                            }');
                $data_spouseIRI = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?spouseIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:isSpouseOf ?spouseIRI
                                            }');
                $i=0;
                foreach ($data_spouseIRI as $rowSpouseIRI) {
                    foreach ($data_spouseIRI->fields() as $field) {
                        $spouseIRI[$i] = $rowSpouseIRI[$field];
                        $i++;
                    }
                }

                if (!isset($data_spouse) || $data_spouse == '') {
                    echo "-❤-<a>Spouse Unknown</a>";
                } else {
                    $i=0;
                    foreach ($data_spouse as $row) {
                        foreach ($data_spouse->fields() as $field) {
                            echo "-❤-";
                            echo '<a href="?entity='.urlencode($spouseIRI[$i]).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "",$row[$field]).'</a>';
                            echo '</br>';
                            $i++;
                            $spouseName = $row[$field];
                            $data_child = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?childName
                                            WHERE {
                                                    <' . $selected_val . '> fam:hasChild ?childIRI.
                                                  ?spouseIRI fam:hasName "' . $spouseName . '".
                                                  ?spouseIRI fam:hasChild ?childIRI.
                                                  ?spouseIRI fam:isSpouseOf <' . $selected_val . '>.
                                                  ?childIRI fam:hasName ?childName
                                                 }');
                            $data_childIRI = sparql_get("localhost:3030/sample/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        SELECT ?childIRI
                                           WHERE {  
                                              <' . $selected_val . '> fam:hasChild ?childIRI.
                                              ?spouseIRI fam:hasName "' . $spouseName . '".
                                              ?spouseIRI fam:hasChild ?childIRI.
                                              ?spouseIRI fam:isSpouseOf <' . $selected_val . '> 
                                            }');
                            $j=0;
                            foreach ($data_childIRI as $rowChildIRI) {
                                foreach ($data_childIRI->fields() as $field) {
                                    $childIRI[$j] = $rowChildIRI[$field];
                                    $j++;
                                }
                            }
                            $flagChild = 0;
                            if (isset($data_child)) {
                                foreach ($data_child as $rowChild) {
                                    foreach ($data_child->fields() as $field) {
                                        if($rowChild[$field] == ''){
                                            $flagChild = 0;
                                        }else $flagChild = 1;
                                    }
                                }
                                if($flagChild == 1){
                                    $j=0;
                                    echo "<ul>";
                                    foreach ($data_child as $rowChild) {
                                        foreach ($data_child->fields() as $field) {
                                            echo "<li>";
                                            echo '<a href="?entity='.urlencode($childIRI[$j]).'">'.str_replace('http://www.semanticweb.org/asus/ontologies/2016/3/ihero/', "",$rowChild[$field]).'</a>';
                                            $j++;
                                            echo "</li>";
                                        }
                                    }
                                    echo "</ul>";
                                }
                            }
                        }
                    }
                }
                echo "</li>";// name and sibling
                echo "</ul>";// father to son
                echo "</li>";// father
                echo "</ul>";
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