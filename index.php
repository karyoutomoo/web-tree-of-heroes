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

    $data = sparql_get("localhost:3030/brits/query",
        "PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                        PREFIX dbo: <http://dbpedia.org/ontology/>
                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                        
                        SELECT DISTINCT ?s
                        WHERE {
                        ?s rdf:type dbo:Person.
  						?s foaf:name|rdfs:label ?name
                        }");

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
                                foreach ($data->fields() as $name) {
                                    ?>
                                    <option selected value="<?= $row[$name] ?>"><?= $row[$name] ?></option>
                                    <?php
                                }
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
                <div class=" . "callout " . " style=" . "padding-bottom:1500px" . ">
                <h6 class=" . "subheader" . ">DESKRIPSI INSTANCE</h6>";

                $selected_val = $_GET['entity'];

                // Nama Instance
                $data_name = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT ?name
                                            WHERE {
                                                <' . $selected_val . '> foaf:name|rdfs:label ?name
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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                echo "<div class=\"tree\" style=\"position: center\">";
                function printUlLi(){
                    echo "<ul>";
                    echo "<li>";
                }
                printUlLi();
                // new -> hasFather
                $data_fatherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?fatherIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasParent|dbpedia-owl:parent ?fatherIRI
                                            }
                                            LIMIT 1');
                $fatherIRI = "";
                foreach ($data_fatherIRI as $row) {
                    foreach ($data_fatherIRI->fields() as $field) {
                        $fatherIRI = $row[$field];
                    }
                }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //hasMother
                $data_motherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        SELECT ?motherIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:hasParent|dbpedia-owl:parent ?motherIRI
                                                FILTER(?motherIRI != <' . $fatherIRI . '>)
                                            }');
                $motherIRI = "";
                foreach ($data_motherIRI as $row) {
                    foreach ($data_motherIRI->fields() as $field) {
                        $motherIRI = $row[$field];
                    }
                }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //hasGrandFather & grandMother
                //kakek dari ayah
                $data_FgrandfatherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?gfatherIRI
                                            WHERE {
                                                <' . $fatherIRI . '> fam:hasParent|dbpedia-owl:parent ?gfatherIRI
                                            }
                                            LIMIT 1');
                $fgrandfatherIRI = "";
                foreach ($data_FgrandfatherIRI as $row) {
                    foreach ($data_FgrandfatherIRI->fields() as $field) {
                        $fgrandfatherIRI = $row[$field];
                    }
                }
                $hasfgrandfather = -1;
                if (!isset($fgrandfatherIRI) || $fgrandfatherIRI == '') {
                    $hasfgrandfather = 0;
                } else {
                    foreach ($data_FgrandfatherIRI as $row) {
                        foreach ($data_FgrandfatherIRI->fields() as $field) {
                            $hasName = 0;
                            $data_Fgrandfather = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?name
                                            WHERE {
                                                <' . $fgrandfatherIRI . '> foaf:name|rdfs:label ?name
                                            }
                                            LIMIT 1');
                            foreach ($data_Fgrandfather as $row) {
                                foreach ($data_Fgrandfather->fields() as $field) {
                                    echo '<a href="?entity=' . urlencode($fgrandfatherIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($fgrandfatherIRI) > 20)
                                    $fgrandfatherIRIShort = substr($fgrandfatherIRI, 31, 15) . '...';
                                echo '<a ' . urlencode($fgrandfatherIRI) . '>' . $fgrandfatherIRIShort . '</a>';
                            }
                        }
                    }
                }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //nenek dari ayah
                $data_FgrandmotherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?gmotherIRI
                                            WHERE {
                                                <' . $fatherIRI . '> fam:hasParent|dbpedia-owl:parent ?gmotherIRI.
                                                FILTER(?gmotherIRI != <' . $fgrandfatherIRI . '>)
                                            }
                                            LIMIT 1');
                $fgrandmotherIRI = "";
                foreach ($data_FgrandmotherIRI as $row) {
                    foreach ($data_FgrandmotherIRI->fields() as $field) {
                        $fgrandmotherIRI = $row[$field];
                    }
                }
                $hasmgrandmother = -1;
                if (!isset($fgrandmotherIRI) || $fgrandmotherIRI == '') {
                    $hasfgrandmother = 0;
                } else {
                    foreach ($data_FgrandmotherIRI as $row) {
                        foreach ($data_FgrandmotherIRI->fields() as $field) {
                            $hasName = 0;
                            if($hasfgrandfather == -1){
                                echo "-❤-";
                            }

                            $data_Fgrandmother = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?name
                                            WHERE {
                                                <' . $fgrandmotherIRI . '> foaf:name|rdfs:label ?name
                                            }
                                            LIMIT 1');
                            foreach ($data_Fgrandmother as $row) {
                                foreach ($data_Fgrandmother->fields() as $field) {
                                    echo '<a href="?entity=' . urlencode($fgrandmotherIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>&nbsp&nbsp';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($fgrandmotherIRI) > 20)
                                    $fgrandmotherIRIShort = substr($fgrandmotherIRI, 31, 15) . '...';
                                echo '<a ' . urlencode($fgrandmotherIRI) . '>' . $fgrandmotherIRIShort . '</a>&nbsp&nbsp';
                            }
                        }
                    }
                }
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //kakek dari ibu
                $data_MgrandfatherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?gfatherIRI
                                            WHERE {
                                                <' . $motherIRI . '> fam:hasParent|dbpedia-owl:parent ?gfatherIRI
                                            }
                                            LIMIT 1');
                $mgrandfatherIRI = "";
                foreach ($data_MgrandfatherIRI as $row) {
                    foreach ($data_MgrandfatherIRI->fields() as $field) {
                        $mgrandfatherIRI = $row[$field];
                    }
                }
                $hasmgrandfather = -1;
                if (!isset($mgrandfatherIRI) || $mgrandfatherIRI == '') {
                    $hasmgrandfather = 0;
                } else {
                    foreach ($data_MgrandfatherIRI as $row) {
                        foreach ($data_MgrandfatherIRI->fields() as $field) {
                            $hasName = 0;
                            $data_Mgrandfather = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?name
                                            WHERE {
                                                <' . $mgrandfatherIRI . '> foaf:name|rdfs:label ?name
                                            }
                                            LIMIT 1');
                            foreach ($data_Mgrandfather as $row) {
                                foreach ($data_Mgrandfather->fields() as $field) {
                                    echo '<a href="?entity=' . urlencode($mgrandfatherIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($mgrandfatherIRI) > 20)
                                    $mgrandfatherIRIShort = substr($mgrandfatherIRI, 31, 15) . '...';
                                echo '<a ' . urlencode($mgrandfatherIRI) . '>' . $mgrandfatherIRIShort . '</a>';
                            }
                        }
                    }
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //nenek dari ibu
                $data_MgrandmotherIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?gmotherIRI
                                            WHERE {
                                                <' . $mgrandfatherIRI . '> fam:isSpouseOf ?gmotherIRI.
                                                ?gmotherIRI fam:hasChild <' . $motherIRI . '>
                                                FILTER(?gmotherIRI != <' . $mgrandfatherIRI . '>)
                                            }
                                            LIMIT 1');
                $mgrandmotherIRI = "";
                foreach ($data_MgrandmotherIRI as $row) {
                    foreach ($data_MgrandmotherIRI->fields() as $field) {
                        $mgrandmotherIRI = $row[$field];
                    }
                }
                $hasmgrandmother = -1;
                if (!isset($mgrandmotherIRI) || $mgrandmotherIRI == '') {
                    $hasmgrandmother = 0;
                } else {
                    foreach ($data_MgrandmotherIRI as $row) {
                        foreach ($data_MgrandmotherIRI->fields() as $field) {
                            $hasName = 0;
                            if($hasmgrandfather == -1){
                                echo "-❤-";
                            }
                            $data_Mgrandmother = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?name
                                            WHERE {
                                                <' . $mgrandmotherIRI . '> foaf:name|rdfs:label ?name
                                            }
                                            LIMIT 1');
                            foreach ($data_Mgrandmother as $row) {
                                foreach ($data_Mgrandmother->fields() as $field) {
                                    echo '<a href="?entity=' . urlencode($mgrandmotherIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($mgrandmotherIRI) > 20)
                                    $mgrandmotherIRIShort = substr($mgrandmotherIRI, 31, 15) . '...';
                                echo '<a ' . urlencode($mgrandmotherIRI) . '>' . $mgrandmotherIRIShort . '</a>&nbsp&nbsp';
                            }
                        }
                    }
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //print father & mother
                $father = -1;
                if (!isset($fatherIRI) || $fatherIRI == '') {
                    $father = 0;
                } else {
                    foreach ($data_fatherIRI as $row) {
                        foreach ($data_fatherIRI->fields() as $field) {
                            echo "<ul>";
                            echo "<li>";
                            $hasName = 0;
                            $data_father = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        SELECT DISTINCT ?name
                                            WHERE {
                                                <' . $fatherIRI . '> foaf:name|rdfs:label ?name
                                            }
                                            LIMIT 1');
                            foreach ($data_father as $row) {
                                foreach ($data_father->fields() as $field) {
                                    echo '<a href="?entity=' . urlencode($fatherIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $father = 1;
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($fatherIRI) > 20)
                                    $fatherIRIShort = substr($fatherIRI, 31, 15) . '...';
                                echo '<a ' . urlencode($fatherIRIShort) . '>' . $fatherIRIShort . '</a>';
                            }
                        }
                    }
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // new -> hasMother
                if (!isset($data_motherIRI) || $data_motherIRI == '') {
                    echo "<a>Mother Unknown</a>";
                } else {
                    foreach ($data_motherIRI as $row) {
                        foreach ($data_motherIRI->fields() as $field) {
                            if ($father == 0) {
                                echo "<ul>";
                                echo "<li>";
                            }
                            $hasName = 0;
                            $data_mother = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        PREFIX dbpedia-owl: <http://id.dbpedia.org/ontology/>
                                        SELECT ?name
                                            WHERE {
                                                <' . $motherIRI . '> foaf:name|rdfs:label ?name
                                            }
                                            LIMIT 1');

                            foreach ($data_mother as $row) {
                                foreach ($data_mother->fields() as $field) {
                                    echo "-❤-";
                                    echo '<a href="?entity=' . urlencode($motherIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($motherIRI) > 20)
                                    echo "-❤-";
                                $motherIRI = substr($motherIRI, 31, 15) . '...';
                                echo '<a ' . urlencode($motherIRI) . '>' . $motherIRI . '</a>';
                            }
                        }
                    }
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                //hasSibling
                $data_siblingIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        SELECT DISTINCT ?siblingIRI
                                           WHERE {  
                                              <' . $selected_val . '> fam:hasParent ?parent1IRI.
                                              <' . $selected_val . '> fam:hasParent ?parent2IRI.
                                               ?parent1IRI fam:hasChild ?siblingIRI.
                                               ?parent2IRI fam:hasChild ?siblingIRI.
                                                FILTER(?siblingIRI != <' . $selected_val . '>)
                                               FILTER(?parent1IRI != ?parent2IRI)
                                           }');

                $i = 0;
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
                    $i = 0;
                    foreach ($data_siblingIRI as $rowSiblingIRI) {
                        foreach ($data_siblingIRI->fields() as $field) {
                            echo "<li>";
                            $hasName = 0;
                            $data_sibling = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        SELECT ?siblingname
                                           WHERE {  
                                              <' . $siblingIRI[$i] . '> foaf:name|rdfs:label ?siblingname
                                           }LIMIT 1');

                            foreach ($data_sibling as $row) {
                                foreach ($data_sibling->fields() as $field) {
                                    if (strlen($row[$field]) > 20)
                                        $row[$field] = substr($row[$field], 0, 15) . '...';
                                    echo '<a href="?entity=' . urlencode($siblingIRI[$i]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($siblingIRI[$i]) > 20)
                                    $siblingIRIShort[$i] = substr($siblingIRI[$i], 31, 15) . '...';
                                echo '<a ' . urlencode($siblingIRI[$i]) . '>' . $siblingIRIShort[$i] . '</a>';
                            }
                            $i++;
                            echo "</li>";
                        }
                    }
                }

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // Name
                if (isset($data_name)) {
                    foreach ($data_name as $row) {
                        foreach ($data_name->fields() as $field) {
                            echo "<li>";
                            print "<a style='font-weight: bold'>$row[$field]</a>";
                        }
                    }
                } else {
                    echo "<li>";
                    print "<a>'.$selected_val'.</a>";
                }
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
                // spouse
                $data_spouseIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        SELECT DISTINCT ?spouseIRI
                                            WHERE {
                                                <' . $selected_val . '> fam:isSpouseOf ?spouseIRI
                                            } ');
                $i = 0;
                foreach ($data_spouseIRI as $rowSpouseIRI) {
                    foreach ($data_spouseIRI->fields() as $field) {
                        $spouseIRI[$i] = $rowSpouseIRI[$field];
                        $i++;
                    }
                }

                if (!isset($data_spouseIRI) || $data_spouseIRI == '') {
                    echo "-❤-<a>Spouse Unknown</a>";
                } else {
                    $i = 0;
                    foreach ($data_spouseIRI as $rowSpouseIRI) {
                        foreach ($data_spouseIRI->fields() as $field) {
                            if ($i > 0) {   //jika istri lebih dari satu
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                                echo "<br>";
                                foreach ($data_name as $row) {
                                    foreach ($data_name->fields() as $field) {
                                        print "<a style='font-weight: bold'>$row[$field]</a>";
                                    }
                                }
                            }
                            echo "-❤-";
                            $hasName = 0;
                            $data_spouse = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        SELECT ?name
                                            WHERE {
                                                <' . $spouseIRI[$i] . '> foaf:name|rdfs:label ?name
                                            } LIMIT 1
                                            ');
                            foreach ($data_spouse as $row) {
                                foreach ($data_spouse->fields() as $field) {
                                    if (strlen($row[$field]) > 20)
                                        $row[$field] = substr($row[$field], 0, 15) . '...';
                                    echo '<a href="?entity=' . urlencode($spouseIRI[$i]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $row[$field]) . '</a>';
                                    $hasName = 1;
                                }
                            }
                            if ($hasName == 0) {
                                if (strlen($spouseIRI[$i]) > 20)
                                    $spouseIRI[$i] = substr($spouseIRI[$i], 31, 15) . '...';
                                echo '<a ' . urlencode($spouseIRI[$i]) . '>' . $spouseIRI[$i] . '</a>';
                            }
                            //get child data

                            $data_childIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                        SELECT DISTINCT ?childIRI
                                           WHERE {  
                                              <' . $selected_val . '> fam:hasChild ?childIRI.
                                              <' . $spouseIRI[$i] . '> fam:hasChild ?childIRI
                                            }LIMIT 10');
                            $i++;
                            $j = 0;
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
                                        if ($rowChild[$field] == '') {
                                            $flagChild = 0; //tidak punya anak
                                        } else $flagChild = 1;
                                    }
                                }
                                if ($flagChild == 1) {
                                    $cc = 0;
                                    echo "<ul>";
                                    foreach ($data_childIRI as $rowChildIRI) {
                                        foreach ($data_childIRI->fields() as $field) {
                                            if (isset($childIRI[$cc])) {
                                                echo "<li>";
                                                $hasName = 0;
                                                $data_child = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                    PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                    PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                    SELECT ?childName
                                                        WHERE {
                                                    <' . $childIRI[$cc] . '> foaf:name|rdfs:label ?childName
                                                 }LIMIT 1');
                                                foreach ($data_child as $rowChild) {
                                                    foreach ($data_child->fields() as $field) {
                                                        if (strlen($rowChild[$field]) > 20)
                                                            $rowChild[$field] = substr($rowChild[$field], 0, 15) . '...';
                                                        echo '<a href="?entity=' . urlencode($childIRI[$cc]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowChild[$field]) . '</a>';
                                                        $hasName = 1;
                                                    }
                                                }
                                                if ($hasName == 0) {
                                                    if (strlen($childIRI[$cc]) > 20)
                                                        $childIRI[$cc] = substr($childIRI[$cc], 31, 15) . '...';
                                                    echo '<a ' . urlencode($childIRI[$cc]) . '>' . $childIRI[$cc] . '</a>';
                                                }

                                                //getChildInLaw

                                                $data_ChildInLawIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                            PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
                                            PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                            PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                            SELECT  ?sbj
                                            WHERE {
                                                <' . $childIRI[$cc] . '> fam:isSpouseOf ?sbj
                                            }LIMIT 1');
                                                $cc++;
                                            }
                                            foreach ($data_ChildInLawIRI as $rowChildInLawIRI) {
                                                foreach ($data_ChildInLawIRI->fields() as $field) {
                                                    $childInLawIRI = $rowChildInLawIRI[$field];
                                                }
                                            }
                                            if (!isset($data_ChildInLawIRI) || $data_ChildInLawIRI == '') {
                                                echo "-❤-<a>?</a>";
                                            } else if (isset($data_ChildInLawIRI)) {
                                                foreach ($data_ChildInLawIRI as $rowChildInLawIRI) {
                                                    foreach ($data_ChildInLawIRI->fields() as $field) {
                                                        echo "-❤-";
                                                        $hasName = 0;
                                                        $data_ChildInLaw = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
                                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                        SELECT  ?name
                                                        WHERE {
                                                            <' . $childInLawIRI . '> foaf:name|rdfs:label ?name
                                                        }LIMIT 1');
                                                        foreach ($data_ChildInLaw as $rowChildInLaw) {
                                                            foreach ($data_ChildInLaw->fields() as $field) {
                                                                echo '<s href="?entity=' . urlencode($childInLawIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowChildInLaw[$field]) . '</s>';
                                                                $hasName = 1;
                                                            }
                                                        }
                                                        if ($hasName == 0) {
                                                            if (strlen($childInLawIRI) > 20)
                                                                $childInLawIRI = substr($childInLawIRI, 31, 15) . '...';
                                                            echo '<s ' . urlencode($childInLawIRI) . '>' . $childInLawIRI . '</s>';
                                                        }
                                                        //getGrandChild
                                                        $data_grandchildIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
                                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                        PREFIX dbp: <http://dbpedia.org/property/>
                                                        SELECT DISTINCT ?grandchildIRI
                                                        WHERE {  
                                                          <' . $childInLawIRI . '> dbp:issue ?grandchildIRI
                                                        }');
                                                        $m = 0;
                                                        foreach ($data_grandchildIRI as $rowgrandChildIRI) {
                                                            foreach ($data_grandchildIRI->fields() as $field) {
                                                                $grandchildIRI[$m] = $rowgrandChildIRI[$field];
                                                                $m++;
                                                            }
                                                        }
                                                        $flagGrandChild = 0;
                                                        if (isset($data_grandchildIRI)) {
                                                            foreach ($data_grandchildIRI as $rowGC) {
                                                                foreach ($data_grandchildIRI->fields() as $field) {
                                                                    if ($rowGC[$field] == '') {
                                                                        $flagGrandChild = 0; //tidak punya cucu
                                                                    } else $flagGrandChild = 1;
                                                                }
                                                            }
                                                            if ($flagGrandChild == 1) {
                                                                $n = 0;
                                                                echo "<ul>";    //garis vertikal cucu
                                                                foreach ($data_grandchildIRI as $rowgrandChildIRI) {
                                                                    foreach ($data_grandchildIRI->fields() as $field) {
                                                                        echo "<li>";
                                                                        $hasName = 0;
                                                                        $data_grandchild = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                                        SELECT ?grandChildName
                                                                        WHERE {
                                                                                <' . $grandchildIRI[$n] . '> foaf:name|rdfs:label ?grandChildName
                                                                             }LIMIT 1');
                                                                        foreach ($data_grandchild as $rowGC) {
                                                                            foreach ($data_grandchild->fields() as $field) {
                                                                                if (strlen($rowGC[$field]) > 20)
                                                                                    $rowGC[$field] = substr($rowGC[$field], 0, 15) . '...';
                                                                                echo '<a href="?entity=' . urlencode($grandchildIRI[$n]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowGC[$field]) . '</a>';
                                                                                $hasName = 1;
                                                                            }
                                                                        }
                                                                        if ($hasName == 0) {
                                                                            if (strlen($grandchildIRI[$n]) > 20)
                                                                                $grandchildIRI[$n] = substr($grandchildIRI[$n], 31, 15) . '...';
                                                                            echo '<a ' . urlencode($grandchildIRI[$n]) . '>' . $grandchildIRI[$n] . '</a>';
                                                                        }
                                                                        //getGrandChildInLaw
                                                                        $data_GrandChildInLawIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                                                PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                                                SELECT  ?sbj
                                                                                WHERE {
                                                                                    <' . $grandchildIRI[$n] . '> fam:isSpouseOf ?sbj.
                                                                                    ?sbj foaf:name|rdfs:label ?name
                                                                                }LIMIT 1');
                                                                        foreach ($data_GrandChildInLawIRI as $rowGrandChildInLawIRI) {
                                                                            foreach ($data_GrandChildInLawIRI->fields() as $field) {
                                                                                $grandChildInLawIRI = $rowGrandChildInLawIRI[$field];
                                                                            }
                                                                        }
                                                                        if (!isset($data_GrandChildInLawIRI) || $data_GrandChildInLawIRI == '') {
                                                                            echo "-❤-<a>?</a>";
                                                                        } else if (isset($data_GrandChildInLawIRI)) {
                                                                            foreach ($data_GrandChildInLawIRI as $rowGrandChildInLawIRI) {
                                                                                foreach ($data_GrandChildInLawIRI->fields() as $field) {
                                                                                    echo "-❤-";
                                                                                    $hasName = 0;
                                                                                    $data_GrandChildInLaw = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                    PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                                                    PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                                                    SELECT  ?name
                                                                                    WHERE {
                                                                                        <' . $grandchildIRI[$n] . '> fam:isSpouseOf ?sbj.
                                                                                        ?sbj foaf:name|rdfs:label ?name
                                                                                    }LIMIT 1');

                                                                                    foreach ($data_GrandChildInLaw as $rowGrandChildInLaw) {
                                                                                        foreach ($data_GrandChildInLaw->fields() as $field) {
                                                                                            echo '<s href="?entity=' . urlencode($grandChildInLawIRI) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowGrandChildInLaw[$field]) . '</s>';
                                                                                            $hasName = 1;
                                                                                        }
                                                                                    }
                                                                                    if ($hasName == 0) {
                                                                                        if (strlen($rowGrandChildInLaw[$field]) > 20)
                                                                                            $rowGrandChildInLaw[$field] = substr($rowGrandChildInLaw[$field], 31, 15) . '...';
                                                                                        echo '<s ' . urlencode($rowGrandChildInLaw[$field]) . '>' . $rowGrandChildInLaw[$field] . '</s>';
                                                                                    }

                                                                                    //getGreatGrandChild
                                                                                    $data_greatGrandChildIRI = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>
                                                                                        SELECT DISTINCT ?greatgrandchildIRI
                                                                                        WHERE {  
                                                                                          <' . $grandChildInLawIRI . '> fam:hasChild ?greatgrandchildIRI.
                                                                                          <' . $grandchildIRI[$n] . '> fam:hasChild ?greatgrandchildIRI
                                                                                        }');
                                                                                    $m = 0;
                                                                                    foreach ($data_greatGrandChildIRI as $rowgreatGrandChildIRI) {
                                                                                        foreach ($data_greatGrandChildIRI->fields() as $field) {
                                                                                            $greatGrandChildIRI[$m] = $rowgreatGrandChildIRI[$field];
                                                                                            $m++;
                                                                                        }
                                                                                    }
                                                                                    $flagGreatGrandChild = 0;
                                                                                    if (isset($data_greatGrandChildIRI)) {
                                                                                        foreach ($data_greatGrandChildIRI as $rowGGC) {
                                                                                            foreach ($data_greatGrandChildIRI->fields() as $field) {
                                                                                                if ($rowGGC[$field] == '') {
                                                                                                    $flagGreatGrandChild = 0; //tidak punya cicit
                                                                                                } else $flagGreatGrandChild = 1;
                                                                                            }
                                                                                        }
                                                                                        if ($flagGreatGrandChild == 1) {
                                                                                            $p = 0;
                                                                                            echo "<ul>";
                                                                                            foreach ($data_greatGrandChildIRI as $rowGreatGrandChildIRI) {
                                                                                                foreach ($data_greatGrandChildIRI->fields() as $field) {
                                                                                                    echo "<li>";
                                                                                                    $hasName = 0;
                                                                                                    $data_greatGrandChild = sparql_get("localhost:3030/brits/query", 'PREFIX fam: <http://www.co-ode.org/roberts/family-tree.owl#>
                                                                                                        PREFIX foaf: <http://xmlns.com/foaf/0.1/> 
                                                                                                        PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#>
                                                                                                        PREFIX dbpprop-id: <http://id.dbpedia.org/property/>SELECT ?name
                                                                                                        WHERE {  
                                                                                                          <' . $greatGrandChildIRI[$p] . '> foaf:name|rdfs:label ?name
                                                                                                        }LIMIT 1');
                                                                                                    foreach ($data_greatGrandChild as $rowGGC) {
                                                                                                        foreach ($data_greatGrandChild->fields() as $field) {
                                                                                                            if (strlen($rowGGC[$field]) > 20)
                                                                                                                $rowGGC[$field] = substr($rowGGC[$field], 0, 15) . '...';
                                                                                                            echo '<a href="?entity=' . urlencode($greatGrandChildIRI[$p]) . '">' . str_replace('http://www.dbpedia.org/resource/', "", $rowGGC[$field]) . '</a>';
                                                                                                            $hasName = 1;
                                                                                                        }
                                                                                                    }
                                                                                                    if ($hasName == 0) {
                                                                                                        if (strlen($greatGrandChildIRI[$p]) > 20)
                                                                                                            $greatGrandChildIRI[$p] = substr($greatGrandChildIRI[$p], 31, 15) . '...';
                                                                                                        echo '<a ' . urlencode($greatGrandChildIRI[$p]) . '>' . $greatGrandChildIRI[$p] . '</a>';
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
                                                                echo "</ul>"; //tutup cucu
                                                            }
                                                        }
                                                    }
                                                }
                                                echo "</li>";
                                            }
                                        }
                                    }
                                    echo "</ul>";//tutup anak
                                }
                            }
                        }
                    }
                }
                echo "</li>";// name and sibling
                echo "</ul>";// father to son
                echo "</li>";// father
                echo "</ul>";
                echo "</li>";// grandfathers
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