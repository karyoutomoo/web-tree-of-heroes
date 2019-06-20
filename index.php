<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Family Tree App</title>
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
                        <h5><strong>Family Tree App</strong></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br><br>


    <?php
    require 'vendor/autoload.php';
    require_once("sparqllib.php");

    $graph = new EasyRdf_Graph();
    $graph->parseFile('ihero.rdf', 'rdf');
    $data = sparql_get("localhost:3030/brits/query",
                        "PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                        
                        SELECT DISTINCT ?s
                        WHERE {
                        ?s rdf:type foaf:Person.
  						?s foaf:name ?name
                        }");
    $name = sparql_get("localhost:3030/brits/query",
        "PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                        
                        SELECT DISTINCT ?name
                        WHERE {
                        ?s rdf:type foaf:Person.
                        ?s foaf:name ?name
                        }
                        LIMIT 100");
    if (!isset($data)) {
        print "<p>Error: " . sparql_errno() . ": " . sparql_error() . "</p>";
    }

    ?>
    <div class="row content">
        <div class="large-up-8">
            <div class="callout">
                <h6 class="subheader">PILIH TOKOH</h6>
                <form method="GET" action="#">
                    <div class="input-group">
                        <select class="input-group-field" name="entity">
                            <?php
                            foreach ($data as $row) {
                                foreach ($data->fields() as $name) { ?>
                                    <option selected value="<?= $row[$name] ?>"><?= $row[$name] ?></option>
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
                $data_name = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> foaf:name ?name
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
                echo "<div class=\"tree\" style=\"margin-left:0px\">";
                // new -> hasFather
                $data_father = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '>fam:hasParent ?fatherIRI.
                                                ?fatherIRI foaf:name ?name.
  												?fatherIRI foaf:gender "male"@en
                                            }
                                            LIMIT 1');
                $data_fatherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?fatherIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasParent ?fatherIRI.
                                                ?fatherIRI foaf:name ?name.
  												?fatherIRI foaf:gender "male"@en
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
                    echo "-❤-";
                } else {
                    foreach ($data_father as $row) {
                        foreach ($data_father->fields() as $field) {
                            echo "<ul>";
                            echo "<li>";
                            echo '<a href="?entity='.urlencode($fatherIRI).'">'.str_replace('http://www.dbpedia.org/resource/', "",$row[$field]).'</a>';
                            echo "-❤-";
                        }
                    }
                }

                // new -> hasMother
                $data_mother = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> fam:hasParent ?motherIRI.
                                                ?motherIRI foaf:name ?name.
  												?motherIRI foaf:gender "female"@en
                                            }
                                            LIMIT 1');
                $data_motherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?motherIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasParent ?motherIRI.
                                                ?motherIRI foaf:gender "female"@en
                                            }');
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
                            echo '<a href="?entity='.urlencode($motherIRI).'">'.str_replace('http://www.dbpedia.org/resource/', "",$row[$field]).'</a>';
                        }
                    }
                }

                // new -> hasSibling
                $data_siblingIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT DISTINCT ?siblingIRI
                                           WHERE {  
                                              <' . $selected_val . '> fam:hasParent ?parentIRI.
                                               ?parentIRI foaf:name ?name.
                                               ?parentIRI fam:hasChild ?siblingIRI.
                                               ?siblingIRI foaf:name ?siblingname
                                                FILTER(?siblingIRI != <' . $selected_val . '>)
                                           }LIMIT 3');

                $i=0;
                foreach ($data_siblingIRI as $rowSiblingIRI) {
                    foreach ($data_siblingIRI->fields() as $field) {
                        $siblingIRI[$i] = $rowSiblingIRI[$field];
                        $i++;
                    }
                }

                if (!isset($data_siblingIRI) || $data_siblingIRI == '') {
                    echo "<ul>";
                } else {
                    echo "<ul>";
                    $i=0;
                    foreach ($data_siblingIRI as $rowSiblingIRI) {
                        foreach ($data_siblingIRI->fields() as $field) {
                            echo "<li>";
                            $data_sibling = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?siblingname
                                           WHERE {  
                                              <' .$siblingIRI[$i] . '> foaf:name ?siblingname
                                           }LIMIT 1');

                            foreach ($data_sibling as $row) {
                                foreach ($data_sibling->fields() as $field) {
                                    if (strlen($row[$field]) > 20)
                                        $row[$field] = substr($row[$field], 0, 15) . '...';
                                    echo '<a href="?entity=' . urlencode($siblingIRI[$i]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                }
                            }
                            $i++;
                            echo "</li>";
                        }
                    }
                }


                // Name
                if(isset($data_name)){
                    foreach ($data_name as $row) {
                        foreach ($data_name->fields() as $field) {
                            echo "<li>";
                            print "<a style='font-weight: bold'>$row[$field]</a>";
                        }
                    }
                }else{
                    echo "<li>";
                    print "<a>'.$selected_val'.</a>";
                }

                // spouse
                $data_spouseIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT DISTINCT ?spouseIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:isSpouseOf ?spouseIRI.
                                                ?spouseIRI foaf:name ?name
                                            } LIMIT 1');
                $i=0;
                foreach ($data_spouseIRI as $rowSpouseIRI) {
                    foreach ($data_spouseIRI->fields() as $field) {
                        $spouseIRI[$i] = $rowSpouseIRI[$field];
                        $i++;
                    }
                }

                if (!isset($data_spouseIRI) || $data_spouseIRI == '') {
                    echo "-❤-<a>Spouse Unknown</a>";
                } else {
                    $i=0;
                    foreach ($data_spouseIRI as $rowSpouseIRI) {
                        foreach ($data_spouseIRI->fields() as $field) {
                            echo "-❤-";
                            $data_spouse = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT ?name
                                            WHERE {
                                                <' . $spouseIRI[$i] . '> foaf:name ?name
                                            } LIMIT 1
                                            ');
                            foreach ($data_spouse as $row) {
                                foreach ($data_spouse->fields() as $field) {
                                    echo '<a href="?entity=' . urlencode($spouseIRI[$i]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                }
                            }
                            //get child data

                            $data_childIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                        SELECT DISTINCT ?childIRI
                                           WHERE {  
                                              <' . $selected_val . '> fam:hasChild ?childIRI.
                                              <' .$spouseIRI[$i] . '> fam:hasChild ?childIRI.
                                              ?childIRI foaf:name ?childName
                                            }LIMIT 10');
                            $i++;
                            $j=0;
                            foreach ($data_childIRI as $rowChildIRI) {
                                foreach ($data_childIRI->fields() as $field) {
                                    $childIRI[$j] = $rowChildIRI[$field];
                                    $j++;
                                }
                            }
                            $flagChild = 0;
                            if (isset($data_childIRI)) {
                                foreach ($data_childIRI as $rowChild) {
                                    foreach ($data_childIRI->fields() as $field) {
                                        if($rowChild[$field] == ''){
                                            $flagChild = 0; //tidak punya anak
                                        }else $flagChild = 1;
                                    }
                                }
                                if($flagChild == 1){
                                    $cc=0;
                                    echo "<ul>";
                                    foreach ($data_childIRI as $rowChildIRI) {
                                        foreach ($data_childIRI->fields() as $field) {
                                            if(isset($childIRI[$cc])) {
                                                echo "<li>";
                                                $data_child = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                    PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                    SELECT ?childName
                                                        WHERE {
                                                    <' . $childIRI[$cc] . '> foaf:name ?childName
                                                 }LIMIT 1');
                                                foreach ($data_child as $rowChild) {
                                                    foreach ($data_child->fields() as $field) {
                                                        if (strlen($rowChild[$field]) > 20)
                                                            $rowChild[$field] = substr($rowChild[$field], 0, 15) . '...';
                                                        echo '<a href="?entity='.urlencode($childIRI[$cc]).'">'.str_replace('http://www.dbpedia.org/resource/', "",$rowChild[$field]).'</a>';
                                                    }
                                                }

                                            //getChildInLaw
                                            $data_ChildInLaw = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                            PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                            SELECT  ?name
                                            WHERE {
                                                <' . $childIRI[$cc] . '> fam:isSpouseOf ?sbj.
                                                ?sbj foaf:name ?name
                                            }LIMIT 1');
                                            $data_ChildInLawIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                            PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                            SELECT  ?sbj
                                            WHERE {
                                                <' . $childIRI[$cc] . '> fam:isSpouseOf ?sbj.
                                                ?sbj foaf:name ?name
                                            }LIMIT 1');
                                            $cc++;
                                            }
                                            foreach ($data_ChildInLawIRI as $rowChildInLawIRI) {
                                                foreach ($data_ChildInLawIRI->fields() as $field) {
                                                    $childInLawIRI = $rowChildInLawIRI[$field];
                                                }
                                            }
                                            if (!isset($data_ChildInLaw) || $data_ChildInLaw == '') {
                                                echo "-❤-<a>?</a>";
                                            }else if(isset($data_ChildInLaw)){
                                                foreach ($data_ChildInLaw as $rowChildInLaw) {
                                                    foreach ($data_ChildInLaw->fields() as $field) {
                                                        echo "-❤-";
                                                        echo '<a href="?entity=' . urlencode($childInLawIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowChildInLaw[$field]) . '</a>';
                                                        $childInLawName = $rowChildInLaw[$field];

                                                        //getGrandChild
                                                        $data_grandchildIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                        PREFIX dbp: <http://dbpedia.org/property/>
                                                        SELECT DISTINCT ?grandchildIRI
                                                        WHERE {  
                                                          <' . $childInLawIRI . '> dbp:issue ?grandchildIRI.
                                                          ?grandchildIRI foaf:name ?name
                                                        }');
                                                        $m=0;
                                                        foreach ($data_grandchildIRI as $rowgrandChildIRI) {
                                                            foreach ($data_grandchildIRI->fields() as $field) {
                                                                $grandchildIRI[$m] = $rowgrandChildIRI[$field];
                                                                $m++;
                                                            }
                                                        }
                                                        $flagGrandChild=0;
                                                        if (isset($data_grandchildIRI)) {
                                                            foreach ($data_grandchildIRI as $rowGC) {
                                                                foreach ($data_grandchildIRI->fields() as $field) {
                                                                    if ($rowGC[$field] == '') {
                                                                        $flagGrandChild = 0; //tidak punya cucu
                                                                    } else $flagGrandChild = 1;
                                                                }
                                                            }
                                                            if($flagGrandChild==1){
                                                                $n=0;
                                                                echo "<ul>";    //garis vertikal cucu
                                                                foreach ($data_grandchildIRI as $rowgrandChildIRI) {
                                                                    foreach ($data_grandchildIRI->fields() as $field) {
                                                                        echo "<li>";
                                                                        $data_grandchild = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                                        SELECT ?grandChildName
                                                                        WHERE {
                                                                                <' . $grandchildIRI[$n] . '> foaf:name ?grandChildName
                                                                             }LIMIT 1');
                                                                        foreach ($data_grandchild as $rowGC) {
                                                                            foreach ($data_grandchild->fields() as $field) {
                                                                                if (strlen($rowGC[$field]) > 20)
                                                                                    $rowGC[$field] = substr($rowGC[$field], 0, 15) . '...';
                                                                                echo '<a href="?entity=' . urlencode($grandchildIRI[$n]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowGC[$field]) . '</a>';
                                                                            }
                                                                        }
                                                                        //getGrandChildInLaw
                                                                        $data_GrandChildInLaw = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                                                SELECT  ?name
                                                                                WHERE {
                                                                                    <' . $grandchildIRI[$n] . '> fam:isSpouseOf ?sbj.
                                                                                    ?sbj foaf:name ?name
                                                                                }LIMIT 1');
                                                                        $data_GrandChildInLawIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                                                SELECT  ?sbj
                                                                                WHERE {
                                                                                    <' . $grandchildIRI[$n] . '> fam:isSpouseOf ?sbj.
                                                                                    ?sbj foaf:name ?name
                                                                                }LIMIT 1');
                                                                        foreach ($data_GrandChildInLawIRI as $rowGrandChildInLawIRI) {
                                                                            foreach ($data_GrandChildInLawIRI->fields() as $field) {
                                                                                $grandChildInLawIRI = $rowGrandChildInLawIRI[$field];
                                                                            }
                                                                        }
                                                                        if (!isset($data_GrandChildInLawIRI) || $data_GrandChildInLawIRI == '') {
                                                                            echo "-❤-<a>?</a>";
                                                                        }else if(isset($data_GrandChildInLawIRI)){
                                                                            foreach ($data_GrandChildInLaw as $rowGrandChildInLaw) {
                                                                                foreach ($data_GrandChildInLaw->fields() as $field) {
                                                                                    echo "-❤-";
                                                                                    echo '<a href="?entity=' . urlencode($grandChildInLawIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowGrandChildInLaw[$field]) . '</a>';

                                                                                    //getGreatGrandChild
                                                                                    $data_greatGrandChildIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                                                        PREFIX dbp: <http://dbpedia.org/property/>
                                                                                        SELECT DISTINCT ?greatgrandchildIRI
                                                                                        WHERE {  
                                                                                          <' . $grandChildInLawIRI . '> fam:hasChild ?greatgrandchildIRI.
                                                                                          <' . $grandchildIRI[$n] . '> fam:hasChild ?greatgrandchildIRI.
                                                                                          ?greatgrandchildIRI foaf:name ?name
                                                                                        }');
                                                                                    $m=0;
                                                                                    foreach ($data_greatGrandChildIRI as $rowgreatGrandChildIRI) {
                                                                                        foreach ($data_greatGrandChildIRI->fields() as $field) {
                                                                                            $greatGrandChildIRI[$m] = $rowgreatGrandChildIRI[$field];
                                                                                            $m++;
                                                                                        }
                                                                                    }
                                                                                    $flagGreatGrandChild = 0;
                                                                                    if(isset($data_greatGrandChildIRI)){
                                                                                        foreach ($data_greatGrandChildIRI as $rowGGC) {
                                                                                            foreach ($data_greatGrandChildIRI->fields() as $field) {
                                                                                                if ($rowGGC[$field] == '') {
                                                                                                    $flagGreatGrandChild = 0; //tidak punya cicit
                                                                                                } else $flagGreatGrandChild = 1;
                                                                                            }
                                                                                        }
                                                                                        if($flagGreatGrandChild == 1){
                                                                                            $p=0;
                                                                                            echo "<ul>";
                                                                                            foreach ($data_greatGrandChildIRI as $rowGreatGrandChildIRI) {
                                                                                                foreach ($data_greatGrandChildIRI->fields() as $field) {
                                                                                                    echo "<li>";
                                                                                                    $data_greatGrandChild = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/>
                                                                                                        SELECT ?name
                                                                                                        WHERE {  
                                                                                                          <' . $greatGrandChildIRI[$p] . '> foaf:name ?name
                                                                                                        }LIMIT 1');
                                                                                                    foreach ($data_greatGrandChild as $rowGGC) {
                                                                                                        foreach ($data_greatGrandChild->fields() as $field) {
                                                                                                            if (strlen($rowGGC[$field]) > 20)
                                                                                                                $rowGGC[$field] = substr($rowGGC[$field], 0, 15) . '...';
                                                                                                            echo '<a href="?entity=' . urlencode($greatGrandChildIRI[$p]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowGGC[$field]) . '</a>';
                                                                                                        }
                                                                                                    }
                                                                                                    $p++;
                                                                                                    echo "</li>";
                                                                                                }
                                                                                            }
                                                                                            echo "</ul>"; //tutup cicit
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                        $n++;
                                                                    }
                                                                }
                                                                echo "</ul>";
                                                            }
                                                        }
                                                    }
                                                }

                                            }
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