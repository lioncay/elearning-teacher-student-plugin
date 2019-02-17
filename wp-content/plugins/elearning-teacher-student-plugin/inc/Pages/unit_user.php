<?php
global $wpdb;
$coursedetails = array();
$counter= 0;
if (!isset($_POST['previous'])){
    $query = $wpdb->prepare(
        'SELECT * FROM chapters WHERE unit_id = %d',
        $id = $_GET['unitid']
    );
    $wpdb->query($query);
    $items = $wpdb->last_result;
    $listofcoursdetails = "<ol>";
    $first=true;
    foreach ($items as $item) {
        if ($first){
            $listofcoursdetails .= "<li><span class='blackstate'>" . $item->name . "</span>";
            $first=false;
        }else{
            $listofcoursdetails .= "<li><span class='graystate'>" . $item->name . "</span>";
        }
        array_push($coursedetails,array($item->name,$item->id));
        $query = $wpdb->prepare(
            'SELECT * FROM chapterentries WHERE chapter_id = %d',
            $id = $item->id
        );
        $wpdb->query($query);
        if($wpdb->num_rows){
            $listofcoursdetails .= "<ol>";
            foreach ($wpdb->last_result as $entry) {
                array_push($coursedetails,array($entry->title,$entry->id,$item->id));
                $listofcoursdetails .= "<li class='graystate'>" . $entry->title . "</li>";
            }
            $listofcoursdetails .= "</ol>";
        }
        $listofcoursdetails .= "</li>";
    }
    $listofcoursdetails .= "</ol>";
    echo "<script>window.onload = function what(){document.getElementById('right-sidebar').innerHTML = \"" . $listofcoursdetails . "\"};</script>";
}else if (isset($_POST['previous'])){
    $counter = $_POST['counter'];
    $coursedetails = $_POST['coursedetails'];
    $listofcoursdetails = "<ol>";
    $state=0;
    $lastcoursedetail=false;
    foreach ($coursedetails as $item) {
        if (sizeof($item)<3){
            if ($lastcoursedetail){
                $listofcoursdetails .= "</ol></li>";
            }
            if ($counter>=$state){
                $listofcoursdetails .= "<li><span class='blackstate'>" . $item[0] . "</span>";
            }else{
                $listofcoursdetails .= "<li><span class='graystate'>" . $item[0] . "</span>";
            }
        }else{
            $listofcoursdetails .= "<ol>";
            $listofcoursdetails .= "<li>" . $item[0] . "</li>";
        }
        $lastcoursedetail=true;
        $state++;
    }
    $listofcoursdetails .= "</ol>";
    echo "<script>window.onload = function what(){document.getElementById('right-sidebar').innerHTML = \"" . $listofcoursdetails . "\"};</script>";
}


?>