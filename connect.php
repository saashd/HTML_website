<?php
//connection to db
function connect()
{
    $server = "tcp:techniondbcourse01.database.windows.net,1433";
    $user = "id0o";
    $pass = "Qwerty12!";
    $database = "id0o";
    $c = array("Database" => $database, "UID" => $user, "PWD" => $pass);
    sqlsrv_configure('WarningsReturnAsErrors', 0);
    $conn = sqlsrv_connect($server, $c);
    if ($conn === false) {
        echo "error occurred while connecting to database";
        die(print_r(sqlsrv_errors(), true));
    }
    return $conn;
}

// returns array that contains rows which returned from sql query
function execute_sql_query($sql_query)
{
    $conn = connect();
    $result = sqlsrv_query($conn, $sql_query);
    if ($result == false) {
        echo "an error occurred  mysqli_error($result);while running sql query";
    }
    $array = array();
    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
        array_push($array, $row);
    }
    sqlsrv_close($conn);
    return $array;
}



function insertCorpus($token)
{
    $sql = "INSERT INTO corpus (token)
        VALUES ('$token')";
    $conn = connect();
    $result = sqlsrv_query($conn, $sql);
    if ($result == false) {
        echo "an error occurred  mysqli_error($result);while running sql query";
    }
    sqlsrv_close($conn);
    return $result;
}

function load_file_corpus()
{
    $count = 0;
    $message = "";
    $file = $_FILES["csv_corpus"]["tmp_name"];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $message = "Data from  Corpus csv  was added to the database successfully ";
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $result = insertCorpus(addslashes($data[0]));
            if ($result != false) {
                $count += 1;
            } else {
                $message = "An error occurred when uploading cvs.<br> Some of the rows were not uploaded because they are already in db or don't match the constraints. ";
            }
        }
    }
    fclose($handle);
    $message_inserted = "Number of rows from Corpus csv that entered the db is:$count";
    return array($message, $message_inserted);
}

function insertReview($reviewerID, $Asin, $reviewText, $overall, $unixTimeReview, $helped, $overallRead)
{
    $reviewText = str_replace("'", '"', $reviewText);
    $overall = intval($overall);
    $sql = "INSERT INTO reviews(reviewrID, asin, reviewText, overall, unixTimeReview, helped, overallRead) 
        VALUES
             ('$reviewerID','$Asin',
              '$reviewText','$overall','$unixTimeReview','$helped','$overallRead')";
    $conn = connect();
    $result = sqlsrv_query($conn, $sql);
    if ($result == false) {
        echo "an error occurred  mysqli_error($result);while running sql query";
    }
    sqlsrv_close($conn);
    return $result;
}

function load_file_reviews()
{
    $count = 0;
    $message = "";
    $file = $_FILES["csv_review"]["tmp_name"];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $message = "Data from  Reviews csv  was added to the database successfully ";
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $result = insertReview(addslashes($data[0]), addslashes($data[1]), addslashes($data[2]), addslashes($data[3]), addslashes($data[4]), addslashes($data[5]), addslashes($data[6]));
            if ($result != false) {
                $count += 1;
            } else {
                $message = "An error occurred when uploading cvs.<br> Some of the rows were not uploaded because they are already in db or don't match the constraints. ";
            }
        }
    }
    fclose($handle);
    $message_inserted = "Number of rows from Reviews csv that entered the db is:$count";
    return array($message, $message_inserted);

}


function load_file_relations()
{
    $count = 0;
    $message = "";
    $file = $_FILES["csv_relations"]["tmp_name"];
    if (($handle = fopen($file, "r")) !== FALSE) {
        $message = "Data from  Relations csv  was added to the database successfully ";
        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
            $result = insertRelation(addslashes($data[0]), addslashes($data[1]), addslashes($data[2]));
            if ($result != false) {
                $count += 1;
            } else {
                $message = "An error occurred when uploading cvs.<br> Some of the rows were not uploaded because they are already in db or don't match the constraints. ";
            }
        }
    }
    fclose($handle);
    $message_inserted = "Number of rows from Relations csv that entered the db is:$count";
    return array($message, $message_inserted);
}

function insertRelation($reference_asin, $relatedAsin, $type)
{

    $sql = "INSERT INTO relations(referenceAsin, relatedAsin, type)
        VALUES
             ('$reference_asin', 
              '$relatedAsin', 
              '$type')";
    $conn = connect();
    $result = sqlsrv_query($conn, $sql);
    if ($result == false) {
        echo "an error occurred  mysqli_error($result);while running sql query";
    }
//    remove first row-header
    array_shift($result);
    sqlsrv_close($conn);
    return $result;
}

function create_review($probability, $token)
{
    $review = '';
    $num_of_words = 0;
    $sql = "select * from corpus";
    $array = execute_sql_query($sql);
    $arrayLength = count($array);
    $i = 0;
    while (true) {
        $random_prob = rand(0, 10000) / 10000;
        if ($random_prob <= $probability) {
            if ($review == '') {
                $review = $array[$i]['token'];
            } else {
                $review = $review . " " . $array[$i]['token'];
            }
            $num_of_words += 1;
        }
        if ($num_of_words == $token) {
            break;
        }
        $i = ($i + 1) % $arrayLength;
    }
    return $review;
}

function create_chart($array,$str,$name){
    $output = array_slice($array, 0, 10);
    $var='';
    foreach ($output as $row) {
        if($var!=''){
            $var=$var.',';
        }
        $var=$var.$row['result'];
    }
    // Dataset definition
    $DataSet = new pData();
    $DataSet->AddPoint(explode(',',$var));  //Here you define the values of datapoints presented in figure
    $DataSet->AddSerie();
    $DataSet->SetSerieName($str, "Serie1");//Sample data - the name presented in the legend
// Initialise the graph
    $Test = new pChart(700, 230);
    $Test->setFontProperties("../pChart/Fonts/tahoma.ttf", 10);
    $Test->setGraphArea(20,40,680,210);
    $Test->drawGraphArea(252, 252, 252);
    $Test->drawScale($DataSet->GetData(), $DataSet->GetDataDescription(), SCALE_NORMAL, 150, 150, 150, TRUE, 0, 2);
    $Test->drawGrid(4, TRUE, 230, 230, 230, 255);

// Draw the line graph
    $Test->drawLineGraph($DataSet->GetData(), $DataSet->GetDataDescription());
    $Test->drawPlotGraph($DataSet->GetData(), $DataSet->GetDataDescription(), 3, 2, 255, 255, 255);

// Finish the graph
    $Test->setFontProperties("../pChart/Fonts/tahoma.ttf", 8);
    $Test->drawLegend(45, 35, $DataSet->GetDataDescription(), 255, 255, 255);
    $Test->setFontProperties("../pChart/Fonts/tahoma.ttf", 10);
    $Test->drawTitle(60, 22, "Product '$name' results", 50, 50, 50, 585);  //set up title for figure
    $Test->Render("data.png"); // The code creates a png file named example.png. in order to display the figure you will need to display this image.
}

?>


