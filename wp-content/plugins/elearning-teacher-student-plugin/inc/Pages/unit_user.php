<?php
global $wpdb;
$coursedetails = array();
$counter= 0;
$done=false;
$mcdone=true;
$oqdone=true;
$chapter=false;

function clean($string)
{
    $string = str_replace(' ', '-', $string);
    $string = preg_replace('/[^A-Za-z0-9\-]/', '', $string);
    return strtolower($string);
}

if (isset($_GET['unitname'])){
    echo "<script>document.getElementsByTagName('h1')[0].innerHTML = ' Unit - " . $_GET['unitname'] . "'</script>";
}
if (!isset($_POST['counter'])){
    $query = $wpdb->prepare(
        'SELECT * FROM chapters WHERE unit_id = %d',
        $id = $_GET['unitid']
    );
    $wpdb->query($query);
    if (!$wpdb->num_rows){
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
    }else{
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
    }
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
                echo "<p>" . $wpdb->last_result[0]->summary . "</p>";
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
        if (!$done) {
            ?>
            <form method="post">
                <input type="hidden" name="coursedetails" value="<?php echo $string; ?>">
                <input type="hidden" name="counter" value="<?php echo $counter + 1; ?>">
                <button type="submit" name="submit">Kapitel starten</button>
            </form>
            <?php
        }
        $chapter = true;
    }else{
        $query = $wpdb->prepare(
            'SELECT * FROM chapterentries WHERE id = %d',
            $id = $coursedetails[$counter][1]
        );
        $wpdb->query($query);
        if($wpdb->num_rows){
            if($wpdb->last_result[0]->entry_type=="IN"){
                echo "<p>" . $wpdb->last_result[0]->input . "</p>";
            }elseif ($wpdb->last_result[0]->entry_type=="MC"){
                $multiplechoicedetails = explode("&",$wpdb->last_result[0]->input);
                $answers = explode("|", $multiplechoicedetails[2]);
                if (isset($_POST['mc'])){
                    $mcdone=true;
                    echo "<h5>" . $multiplechoicedetails[0] . "</h5>";
                    $correctanswers = 0;
                    $incorrectanswers = 0;
                    foreach ($answers as $answeritem){
                        if (explode("#",$answeritem)[1]){
                            if (isset($_POST[clean(explode("#",$answeritem)[0])])){
                                $correctanswers++;
                                echo "<div id='mcbox' class='correctmc'><input type='checkbox' checked='true' name='" . clean(explode("#",$answeritem)[0]) . "' value='" . explode("#",$answeritem)[0] . "'/>" . explode("#",$answeritem)[0] . "</div>";
                            }else{
                                echo "<div id='mcbox' class='correctmc'><input type='checkbox' name='" . clean(explode("#",$answeritem)[0]) . "' value='" . explode("#",$answeritem)[0] . "'/>" . explode("#",$answeritem)[0] . "</div>";
                            }
                        }else{
                            if (isset($_POST[clean(explode("#",$answeritem)[0])])){
                                $incorrectanswers++;
                                echo "<div id='mcbox' class='incorrectmc'><input type='checkbox' checked='true' name='" . clean(explode("#",$answeritem)[0]) . "' value='" . explode("#",$answeritem)[0] . "'/>" . explode("#",$answeritem)[0] . "</div>";
                            }else{
                                echo "<div id='mcbox' class='incorrectmc'><input type='checkbox' name='" . clean(explode("#",$answeritem)[0]) . "' value='" . explode("#",$answeritem)[0] . "'/>" . explode("#",$answeritem)[0] . "</div>";
                            }
                        }
                    }
                    if ($correctanswers>0){
                        echo "<p>Du hast " . $correctanswers . " Antworten richtig angekreuzt.</p>";
                    }
                    if ($incorrectanswers>0){
                        echo "<p>Du hast " . $incorrectanswers . " Antworten falsch angekreuzt.</p>";
                    }
                    echo "<p>Erklärung:</br>" . $multiplechoicedetails[1] . "</p>";
                }else{
                    $mcdone=false;
                    echo "<form method='post'><h5>" . $multiplechoicedetails[0] . "</h5>";
                    foreach ($answers as $answeritem){
                        echo "<div id='mcbox'><input type='checkbox' name='" . clean(explode("#",$answeritem)[0]) . "' value='" . explode("#",$answeritem)[0] . "'/>" . explode("#",$answeritem)[0] . "</div>";
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
                    echo "<input type='hidden' name='mc' value='" . $wpdb->last_result[0]->input . "'/>";
                    echo "<input type='hidden' name='coursedetails' value='". $string ."'/>";
                    echo "<input type='hidden' name='counter' value='" . $counter . "'> <br/>";
                    echo "<button type='submit' name='submit'>Check</button>";
                    echo "</form>";
                }
            }elseif ($wpdb->last_result[0]->entry_type=="OQ"){
                $openquestiondetails = explode("&",$wpdb->last_result[0]->input);
                if (isset($_POST['oq']) && $_POST['oq']!=""){
                    $oqdone=true;
                    echo "<h5>" . $openquestiondetails[0] . "</h5>";

                    $quinput=strtolower(str_replace(' ', '-', $_POST['oq']));
                    if (
                            $quinput==strtolower(str_replace(' ', '-', $openquestiondetails[1])) ||
                            $quinput==strtolower(str_replace(' ', '-', $openquestiondetails[2])) ||
                            $quinput==strtolower(str_replace(' ', '-', $openquestiondetails[3]))
                    ){
                        echo "<p>Deine Antwort war korrekt!</p>";
                        echo "<div id='mcbox' class='correctmc'>" . $_POST['oq'] . "</div>";
                    }else{
                        echo "<p>Deine Antwort war nicht korrekt!</p>";
                        echo "<div id='mcbox' class='incorrectmc'>" . $_POST['oq'] . "</div>";
                    }
                    if ($openquestiondetails[2]==""&&$openquestiondetails[3]==""){
                        echo "<p>Korrekte Antwortmöglichkeit:</br>" . $openquestiondetails[1] . "</p>";
                    }else{
                        echo "<p>Korrekte Antwortmöglichkeiten:</br>" . $openquestiondetails[1] . "</br>" . $openquestiondetails[2] . "</br>" . $openquestiondetails[3] . "</p>";
                    }
                }else{
                    $oqdone=false;
                    echo "<form method='post'><h5>" . $openquestiondetails[0] . "</h5>";
                    echo "<input type='text' name='oq' placeholder='Gib deine Antwort hier ein.' required/>";
                    $string = "";
                    foreach ($coursedetails as $item){
                        foreach ($item as $inneritem){
                            $string .= $inneritem . "-";
                        }
                        $string = substr($string, 0, -1);
                        $string .= "&";
                    }
                    $string = substr($string, 0, -1);
                    echo "<input type='hidden' name='mc' value='" . $wpdb->last_result[0]->input . "'/>";
                    echo "<input type='hidden' name='coursedetails' value='". $string ."'/>";
                    echo "<input type='hidden' name='counter' value='" . $counter . "'> <br/>";
                    echo "<button type='submit' name='submit'>Check</button>";
                    echo "</form>";
                }
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
    if (!$done && $mcdone && $oqdone && !$chapter) {
        ?>
        <form method="post">
            <input type="hidden" name="coursedetails" value="<?php echo $string; ?>">
            <input type="hidden" name="counter" value="<?php echo $counter + 1; ?>">
            <button type="submit" name="submit">Weiter</button>
        </form>
        <?php
    }elseif ($done && $mcdone && $oqdone){
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