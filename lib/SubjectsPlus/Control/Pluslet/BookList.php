<?php

namespace SubjectsPlus\Control;
require_once("Pluslet.php");

/**
 *   @file sp_Guide
 *   @brief manage guide metadata
 *
 *   @author agdarby, rgilmour, dgonzalez, Lazaro
 *   @date Jan 2011
 *   @todo better blunDer interaction, better message, maybe hide the blunder errors until the end
 */
class Pluslet_BookList extends Pluslet {

    //books properties
    private $_bookTitle;
    private $_ISBN;
    private $_description;
    private $_action;

    public function __construct($pluslet_id, $flag="", $subject_id, $isclone=0) {
        parent::__construct($pluslet_id, $flag, $subject_id, $isclone);

        $this->_type = "BookList";


    }


    //rendered for Admins
    protected function onEditOutput()
    {

        $db = new Querier ();

        if($this->_pluslet_id != '') {
            $query = "
            select t.body
            from pluslet t
            where t.pluslet_id = " . $this->_pluslet_id;

            $result = $db->query($query);
//        $result = implode(' ', $result[0]['body']);
            if (!$result)
                $result = '';
        }



        $output = "

        <div class=\"pure-u-1 pure-u-md-1-1\">
            <form onsubmit=\"return checkID()\" action=\"" ."\" method=\"post\" id=\"edit_record\" accept-charset=\"UTF-8\" class=\"pure-form pure-form-stacked\">

                <div class=\" \">
                    <input type=\"submit\" name=\"insert_ISBN\" class=\"pure-button pure-button-primary\" value=\"" . _("Add List") . "\" />
                </div>

                <input type=\"text\" hidden name=\"pluslet_id\" id=\"pluslet_id\" value=\"" . $this->_pluslet_id . " \" />

                <label for=\"book_decription\" >  " . _("Description") . "</label>
                <textarea class=\"pluslet_body\" name=\"list_ISBN\" id=\"list_ISBN\" rows=\"10\" cols=\"270\">" . $result[0][0] .  "</textarea>

            </form>
        </div>

        <script type='text/javascript'>
            function checkID(){

                if(" . json_encode($this->_pluslet_id) . " == ''){
                    alert('Please, save your Pluslet first before adding info to the system.');
                    return false;
                }

                return true;

            }
        </script>

        ";

        $this->_body = $output;

    }

    //rendered for Public users
    protected function onViewOutput()
    {
        $style = "

        /*border: 1px solid #ccc;*/
        border-radius: 3px;
        font-size: 1em;
        padding: 0em;
        vertical-align: top;

        ";

        $imgStyle = "

        background: #efefef;
        border: 1px solid #333;
        padding: 3px;
        margin-right: 15px;
        width: 70px !important;

        ";

        $result = $this->selectRecord();
        if($result) {
            $output = "<table style='". $style ."' class='table table-striped borderless table-hover footable foo3 default footable-loaded evenrow' width='100%'>


            <thead style='background: #ccc;'>
                <tr class='staff-heading'>
                    <th class='footable-visible footable-first-column'></th>
                    <th class='footable-visible footable-sortable'>
                        <strong>Book</strong>
                    </th>
                    <th>
                        <strong>Related Subjects</strong>
                    </th>

                </tr>
            </thead>
            <tbody class='table-striped' >
        ";

            $i = 0;
            foreach ($result as $item) {
                $output .= "

                <tr class='";

                $output .= (($i++) % 2 == 0) ? "oddrow" : "evenrow";

                $output .= "'>
                    <td style='border: none; width:15%;' >";

                if($item['location'] != '') {
                    $starts = strpos($item['location'], 'src="');
//                $ends = strpos($item['location'], '"', $starts);
                    $str = "<img class='img-thumbnail' style='width: 80px; height: 100px;' ";
                    $str .= substr($item['location'], $starts);
                }else{
                    $str = "<img class='img-thumbnail' style='width: 80px; height: 100px;'
                src='http://localhost:8888/subjectsplus/assets/images/public/default_cover2.png'
                />";
                }

//            $output .= 'Here: ' . $starts . ' - ' . $ends . ' - ' .$str;

                $output .= "<a href=\"#\">" . $str . "</a></td>
                    <td style='border: none; width:65%;'>" .
                    "<a style='color: #0088cc !important;' href=\"#\">" . "<span class='footable-visible'><strong>" . $item['title'] . "</strong></span></a><br>" .
                    "<span>ISBN: " . $item['alternate_title'] . "</span><br>" .
                    "<span>" . $item['description'] . "</span><br>" .

                    "</td>

                    <td style='border: none; width:20%;' >
                    ";
                $tags = explode('|', $item['ctags']);
                foreach($tags as $it)
                    $output .= $it . "<br>";

                $output .= "

                ";
            }
        }else{
            $output = '<table>';
        }

        $this->_body = $output . "</table>";

    }


    public function selectRecord($notrack = 0) {

        $db  = new Querier;

        $query = "
            select t.*
            from pluslet t
            where t.pluslet_id = " . $this->_pluslet_id . "
        ";

        $result = $db->query($query);

        if($result[0]['body']) {

            $query = "
            select t.*, l.*
            from title t, location_title j, location l
            where t.pre = 'book' AND t.alternate_title IN (" . $result[0]['body'] . ")
            AND t.title_id = j.title_id
            AND j.location_id = l.location_id
        ";


            $result = $db->query($query);

            if (!$result) {
                echo blunDer("We have a problem with the subject_discipline query: $query");
            }
        return $result;
        }
        else
            return 0;
    }



    static function getMenuName()
    {
        return _('List Books');
    }

    static function getMenuIcon()
    {
        $icon="<i class=\"fa fa-bars\" title=\"" . _("List Book") . "\" ></i><span class=\"icon-text\">"  . _("List Books") . "</span>";
        return $icon;
    }

}
