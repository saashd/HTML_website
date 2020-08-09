<html lang="en-us">
<meta charset="utf-8"/>
<link rel="stylesheet" href="style.css" type="text/css">
<body>
<div class="bg-image" style="text-align:center"></div>
<div class="bg-text" style="padding: 200px" >

    <h1>Welcome to ALL FOR SKI Store</h1>

    <h4>Your online ski and snowboard shop is right here! We focus on the passionate skiers and snowboarders spending their time searching for powder,<br>
        pulling tricks in the park and climbing high mountains far away from the slopes. We love what we sell and we want you to love it as much as we do.<br>
        That's why every person who is employed here is involved in the sports we sell. This way we can offer you the best product knowledge along with the best products.<br>
        We wouldn't have it any other way! </h4>

    <hr>
    <img src="ski.jpg" alt="ski"  class="img"/>
    <a href="LoadFile/load_file.php" target="mainFrame"> Load Data</a><br>
    <br>
    <a href="AddReview/add_review.php" target="mainFrame"> Add Review </a><br>
    <br>
    <a href="DisplayVisualData/display_visual_data.php" target="mainFrame"> Display Visual Data </a><br>
    <br>
    <?php
    require_once('connect.php');

    //-- reviewerID with max number of reviews.(if more then one-then by reversed alphabetic order)
    $sql1 = "select Top 1 reviewerID
from aux_Reviews
where num=(select max(num)
           from aux_Reviews)
order by reviewerID ";

    //--reviewerID with max sum of people whom helped the reviewer's posts.(if more then one-then by alphabetic order)
    $sql2 = "select reviewerID
from aux_Reviews
where helped=(select max(helped)
              from aux_Reviews)
order by reviewerID ";

    //--reviewerID with max ratio(if more then one-then by alphabetic order)
    $sql3 = "select reviewerID
from aux_Reviews2
where relation=(select max(relation)
              from aux_Reviews2)
order by reviewerID ";

    //-- product ID with max number of relations
    $sql4 = "select asinID
from aux_Reviews3
where num_relations=(select max(num_relations)
                from aux_Reviews3)
order by asinID ";
    $array1 = execute_sql_query($sql1);
    $array2 = execute_sql_query($sql2);
    $array3 = execute_sql_query($sql3);
    $array4 = execute_sql_query($sql4);

    echo "<table class='center'>
 <tr>
  <th colspan='4' style='text-align:center'>General Data </th>
    <tr>
    <th>Highest amount of reviews</th>
    <th>Highest amount of helpful ratings</th>
    <th>Highest average ratio</th>
    <th>Highest number of relations</th>
  </tr>
 </tr>
 <tr>
  <td>" . $array1[0]['reviewerID'] . "</td>
  <td>" . $array2[0]['reviewerID'] . "</td>
  <td>" . $array3[0]['reviewerID'] . "</td>
  <td>" . $array4[0]['asinID'] . "</td>
</table>"

    ?>
</div>
</body>
</html>