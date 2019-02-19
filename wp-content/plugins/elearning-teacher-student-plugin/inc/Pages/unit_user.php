<?php
global $wpdb;
$coursedetails = array();
$counter= 0;
$done=false;
$mcdone=true;
if (isset($_GET['unitname'])){
    echo "<script>document.getElementsByTagName('h1')[0].innerHTML = ' Unit - " . $_GET['unitname'] . "'</script>";
}
if (!isset($_POST['counter'])){
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
}else if (isset($_POST['counter'])){
    $counter = $_POST['counter'];
    $tmp = explode("&",$_POST['coursedetails']);
    $coursedetails = array();
    foreach($tmp as $item){
        array_push($coursedetails,explode("-",$item));
    }
    $listofcoursdetails = "<ol>";
    $state=0;
    $lastcoursedetail=false;
    $innerorderedliststared=false;
    if (sizeof($coursedetails)==$counter+1){
        $done=true;
    }
    foreach ($coursedetails as $item) {
        if (sizeof($item)<3){
            if ($lastcoursedetail){
                $innerorderedliststared=false;
                $listofcoursdetails .= "</ol></li>";
            }
            if ($counter>=$state){
                $listofcoursdetails .= "<li><span class='blackstate'>" . $item[0] . "</span>";
            }else{
                $listofcoursdetails .= "<li><span class='graystate'>" . $item[0] . "</span>";
            }
        }else{
            if (!$innerorderedliststared){
                $listofcoursdetails .= "<ol>";
                $innerorderedliststared=true;
            }
            if ($counter>=$state){
                $listofcoursdetails .= "<li class='blackstate'>" . $item[0] . "</li>";
            }else{
                $listofcoursdetails .= "<li class='graystate'>" . $item[0] . "</li>";
            }
        }
        $lastcoursedetail=true;
        $state++;
    }
    $listofcoursdetails .= "</ol>";
    echo "<script>window.onload = function what(){document.getElementById('right-sidebar').innerHTML = \"" . $listofcoursdetails . "\"};</script>";
}
if (isset($coursedetails[$counter])){
    echo "<h2>" . $coursedetails[$counter][0] . "</h2>";
    if (sizeof($coursedetails[$counter])<3){
        $query = $wpdb->prepare(
            'SELECT summary FROM chapters WHERE id = %d',
            $id = $coursedetails[$counter][1]
        );
        $wpdb->query($query);
        if($wpdb->num_rows){
            if($wpdb->last_result[0]->summary!="Hier wird das Summary für das Kapitel hinzugefügt!"){
                echo $wpdb->last_result[0]->summary;
            }
        }
    }else{
        $query = $wpdb->prepare(
            'SELECT * FROM chapterentries WHERE id = %d',
            $id = $coursedetails[$counter][1]
        );
        $wpdb->query($query);
        if($wpdb->num_rows){
            if($wpdb->last_result[0]->entry_type=="IN"){
                echo $wpdb->last_result[0]->input;
            }elseif ($wpdb->last_result[0]->entry_type=="MC"){
                if (isset($_POST['mc'])){
                    $mcdone=true;
                    echo "done";
                }else{
                    // TODO mc check
                    // $mcdone=false
                    $mcdone=true;
                    $multiplechoicedetails = explode("&",$wpdb->last_result[0]->input);
                    $answers = explode("|", $multiplechoicedetails[2]);
                    echo /*"<from methode='post' action=''>*/"<h5>" . $multiplechoicedetails[0] . "</h5>";
                    foreach ($answers as $answeritem){
                        echo "<input type='checkbox' name='" . explode("#",$answeritem)[0] . "' value='" . explode("#",$answeritem)[0] . "'>" . explode("#",$answeritem)[0] . "<br>";
                    }
                    $string = "";
                    foreach ($coursedetails as $item){
                        foreach ($item as $inneritem){
                            $string .= $inneritem . "-";
                        }
                        $string = substr($string, 0, -1);
                        $string .= "&";
                    }
                    $string = substr($string, 0, -1);
                    /*
                    echo "<input type='hidden' name='mc' value='" . $wpdb->last_result[0]->input . "'>";
                    echo "<input type='hidden' name='coursedetails' value='<?php echo $string; ?>'>";
                    echo "<input type='hidden' name='counter' value='<?php echo $counter; ?>'> <br>";
                    echo "<button type='submit' name='submit'>Check</button>";
                    echo "</form>";*/
                }
            }elseif ($wpdb->last_result[0]->entry_type=="OQ"){
                echo "Open Question";
            }
        }
    }
    $string = "";
    foreach ($coursedetails as $item){
        foreach ($item as $inneritem){
            $string .= $inneritem . "-";
        }
        $string = substr($string, 0, -1);
        $string .= "&";
    }
    $string = substr($string, 0, -1);
    if (!$done && $mcdone) {
        ?>
        <form method="post">
            <input type="hidden" name="coursedetails" value="<?php echo $string; ?>">
            <input type="hidden" name="counter" value="<?php echo $counter + 1; ?>">
            <button type="submit" name="submit">Weiter</button>
        </form>
        <?php
    }elseif ($done && $mcdone){
        $query = $wpdb->prepare(
            'SELECT * FROM user_course_attempts WHERE userid = %d AND courseid = %d',
            $user=get_current_user_id(),
            $courseid = $_GET['courseid']
        );
        $wpdb->query($query);
        if($wpdb->num_rows){
            if ($wpdb->last_result[0]->done_units!=""){
                $newdone_units = $wpdb->last_result[0]->done_units . "," . $_GET['unitid'];
            }else{
                $newdone_units = $_GET['unitid'];
            }
            $query = $wpdb->prepare(
                'UPDATE `user_course_attempts` SET `done_units`=%s WHERE userid=%d AND courseid=%d',
                $one= $newdone_units,
                $two=get_current_user_id(),
                $three=$_GET['courseid']
            );
            $wpdb->query( $query );
        }
        $query = $wpdb->prepare(
            'SELECT pageid FROM courses WHERE id = %d',
            $courseid = $_GET['courseid']
        );
        $wpdb->query($query);
        ?>
        <form method="post" action="<?php echo get_home_url(); ?>/course/?courseid=<?php echo $wpdb->last_result[0]->pageid; ?>">
            <input type="hidden" value="Ja" name="confirm">
            <button type="submit">Beenden</button>
        </form>
        <?php
    }
}
?>