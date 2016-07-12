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
class Pluslet_Book extends Pluslet {

    //books properties
    private $_bookTitle;
    private $_ISBN;
    private $_description;
    private $_action;
    private $_mode;// 0 create, 1 edit

    public function __construct($pluslet_id, $flag="", $subject_id, $isclone=0) {
        parent::__construct($pluslet_id, $flag, $subject_id, $isclone);

        $this->_type = "Book";
        $this->_pluslet_bonus_classes = "type-book";
        $this->db = new Querier ();

    }

    //rendered for Admins
    protected function onEditOutput()
    {

        global $CKPath;
        global $CKBasePath;

        include ($CKPath);
        global $BaseURL;


        $oCKeditor = new CKEditor($CKBasePath);
        $oCKeditor->returnOutput = true;

        $oCKeditor->timestamp = time();
        $config['toolbar'] = 'ImageOnly';
        $config['height'] = '150';
        $config['width'] = '150';

        $config['filebrowserUploadUrl'] = $BaseURL . "ckeditor/php/uploader.php";
        $this_instance = "book_Thumbnail";

        $oCKcontent = "";

        $editor = $oCKeditor->editor($this_instance, $oCKcontent, $config);
        $this_instance = "edit_Thumbnail";
        $editor2 = $oCKeditor->editor($this_instance, $oCKcontent, $config);


        $result = $this->selectRecord();
        $select = "<select id=\"select_Id\" name=\"select_Id\" onchange=\"ShowBook(this)\">
        <option value=\"-1\" selected >Select Item</option>";

        $ctags_result = array();
        foreach($result as $item) {
            $select .= "<option value='" . $item['title_id'] . "'>" . $item['title'] . "</option>";
            $ctags_result[$item['title_id']] = $item['ctags'];
        }

        $select .= "</select>";
        global $all_ctags;

        $output = "
        <script type='text/javascript'>
            var jsonResp = ". json_encode($result) .";
            console.log(jsonResp);

            function ShowBook(){

                var val = $('#select_Id option:selected').val();

                if(val != -1){
                    for(it in jsonResp){
//                        console.log(jsonResp[it][0]);
                        if(jsonResp[it][0] == val){
                            $('#edit_record .edit_block').css('display', 'block');
                            $('#edit_record #book_title').val(jsonResp[it][1]);
                            $('#edit_record #book_ISBN').val(jsonResp[it][2]);
                            $('#edit_record #book_decription').val(jsonResp[it][3]);
                            $('#edit_record .cke_editable.cke.editable.themed.cke_contents_ltr').val(jsonResp[it][10]);
//                            $('iframe').src = ( 'data:text/html;charset=utf-8,' + escape('text') );
                            CKEDITOR.instances['edit_Thumbnail'].setData(jsonResp[it][10]);

                            var allTags = (". json_encode($all_ctags) .");
                            var currenttags = jsonResp[it]['ctags'].split('|');
//                            console.log(allTags);
                            for(it2 in allTags){
//                                if(jQuery.inArray(it2, currenttags) != -1)
//                                    $('#ctags ').push('<span class=\"ctag-on\">' + it2 + '</span>';
//                                else
//                                    $('<span class=\"ctag-off\">' + it2 + '</span>').insertAfter$($('#ctags'));

                            }
//                            $('#ctags ').val(jsonResp[it][16]);



                        }
                    }

                }
                else
                    $('.edit_block').css('display', 'none');
//                console.log(val);
            }

        </script>";
//        $current_ctags = explode("|", $tags);
//        $tag_count = 0; // added because if you have a lot of ctags, it just stretches the div forever
//
//        foreach ($all_ctags as $value) {
//            if ($tag_count == 5) {
//                $output .= "<br />";
//                $tag_count = 0;
//            }
//
//            if (in_array($value, $current_ctags)) {
//                $output .= "<span class=\"ctag-on\">$value</span>";
//            } else {
//                $output .= "<span class=\"ctag-off\">$value</span>";
//            }
//            $tag_count++;
//        }

        $output .= "
        Select Pluslet Mode<br>
        Create: <input type='radio' name='mode'><nbsp><nbsp>
        Edit:   <input type='radio' name='mode'>

        ";




        $output .= "
<div id='tabs' class='hide-tabs-fouc ui-tabs ui-widget ui-widget-content ui-corner-all'>
    <div id='main-content'>


        <input type=\"hidden\" name=\"title_id\" value=\"" . $this->_record_id . "\" />

            <div class=\"\">
                <div class=\"pluslet\" data-layout='4-6-2'>

                    <div class=\"titlebar\">
                      <div class=\"titlebar_text\">" . _("Book Editor") . "</div>
                      <div class=\"titlebar_options\"></div>
                    </div>

                    <div class=\"pluslet_body pure-u-1 pure-u-md-1-3\">
                        <form action=\"" ."\" method=\"post\" id=\"new_record\" accept-charset=\"UTF-8\" class=\"pure-form pure-form-stacked\">

                            <div class=\"\">
                                <input type=\"submit\" name=\"insert_record\" class=\"pure-button pure-button-primary\" value=\"" . _("Add Book Now") . "\" />
                            </div>

                            <label for=\"title\">" . _("Title") . "</label>
                            <input type=\"text\" name=\"book_title\" id=\"book_title\" class=\"pure-input-1-4\" value=\""  . "\" />

                            <label for=\"ISBN\">" . _("Book ISBN") . "</label>
                            <input type=\"text\" name=\"book_ISBN\" id=\"book_ISBN\" class=\"pure-input-1 required_field\" value=\""  . "\" />

                            <label for=\"book_decription\">" . _("Description") . "</label>
                            <textarea name=\"book_decription\" id=\"book_decription\" rows=\"4\" cols=\"70\">" . "</textarea>

                            <label for=\"book_Thumbnail\">" . _("Thumbnail") . "</label>

                            " . $editor;


//        $tags = "tag1|tag2|tag3";
        $output .= "<input type=\"hidden\" name=\"ctags[]\" value=\"" . "" . "\" />
                <label for=\"ctags[]\">ctags:</label> ";

        $current_ctags = explode("|", $ctags_result);
        $tag_count = 0; // added because if you have a lot of ctags, it just stretches the div forever

        foreach ($all_ctags as $value) {
            if ($tag_count == 5) {
                $output .= "<br />";
                $tag_count = 0;
            }

            if (in_array($value, $current_ctags)) {
                $output .= "<span class=\"ctag-on\">$value</span>";
            } else {
                $output .= "<span class=\"ctag-off\">$value</span>";
            }
            $tag_count++;
        }


$output .= "

                        </form>
                    </div>

";


        $output .= "                  <div class=\"pluslet_body pure-u-1 pure-u-md-1-3\">
                        <form action=\"" ."\" method=\"post\" id=\"edit_record\" accept-charset=\"UTF-8\" class=\"pure-form pure-form-stacked\">

                            <div class=\"\">
                                <input type=\"submit\" name=\"edit_record\" class=\"pure-button pure-button-primary\" value=\"" . _("Edit Book Now") . "\" />
                                <br><input type=\"submit\" name=\"delete_record\" class=\"pure-button pure-button-primary\" value=\"" . _("Delete Book Now") . "\" />

                            </div>

                            <label for=\"id\">" . _("Edit Book") . "</label>
                            " . $select . "

                            <div class=\"edit_block\" style=\"display:none;\">

                            <label for=\"title\">" . _("Title") . "</label>
                            <input type=\"text\" name=\"book_title\" id=\"book_title\" class=\"pure-input-1-4\" value=\""  . "\" />

                            <label for=\"ISBN\">" . _("Book ISBN") . "</label>
                            <input type=\"text\" name=\"book_ISBN\" id=\"book_ISBN\" class=\"pure-input-1 required_field\" value=\""  . "\" />

                            <label for=\"book_decription\">" . _("Description") . "</label>
                            <textarea name=\"book_decription\" id=\"book_decription\" rows=\"4\" cols=\"70\">" . "</textarea>

                            <label for=\"book_Thumbnail\">" . _("Thumbnail") . "</label>

                            " . $editor2;

        global $all_ctags;

//        $tags = "tag1|tag2|tag3";
        $output .= "<input id='ctags' type=\"hidden\" name=\"ctags[]\" value=\"" . "" . "\" />
                <label for=\"ctags[]\">ctags:</label> ";

//        $current_ctags = explode("|", $tags);
//        $tag_count = 0; // added because if you have a lot of ctags, it just stretches the div forever
//
//        foreach ($all_ctags as $value) {
//            if ($tag_count == 5) {
//                $output .= "<br />";
//                $tag_count = 0;
//            }
//
//            if (in_array($value, $current_ctags)) {
//                $output .= "<span class=\"ctag-on\">$value</span>";
//            } else {
//                $output .= "<span class=\"ctag-off\">$value</span>";
//            }
//            $tag_count++;
//        }


        $output .= "</div>

                        </form>
                    </div>

            </div>
            </div>
    </div>
</div>



<script type='text/javascript'>

    ///////////////
    /* ctags     */
    ///////////////

    $('span[class*=ctag-]').livequery('click', function() {

        var all_tags = '';

        // change to other class
        if ($(this).attr('class') == 'ctag-off') {
            $(this).attr('class', 'ctag-on');
        } else {
            $(this).attr('class', 'ctag-off');
        }

        // determine the new selected items
        $(this).parent().find('.ctag-on').each(function(i) {
            var this_ctag = $(this).text();
            all_tags = all_tags + this_ctag + '|';

        });
        // strip off final pipe (|)
        all_tags = all_tags.replace( /[|]$/, '' );
        // set new value to hidden form field
        $(this).parent().find('input[name*=ctags]').val(all_tags);


    });

    //////////////////
    /* A-Z List tag */
    //////////////////

    $('span[class*=aztag-]').livequery('click', function() {

        // change to other class, update hidden input field
        if ($(this).attr('class') == 'aztag-off') {
            $(this).attr('class', 'aztag-on');
            $(this).parent().find('input[name*=eres_display]').val('Y');
        } else {
            $(this).attr('class', 'aztag-off');
            $(this).parent().find('input[name*=eres_display]').val('N');
        }

    });

</script>



";

        $this->_body = $output;

    }

    //rendered for Public users
    protected function onViewOutput()
    {

        $style = "

        border: none;
        border-radius: 3px;
        font-size: 1em;
        padding: 1em;
        vertical-align: top;

        ";

        $imgStyle = "

        background: #efefef;
        border: 1px solid #333;
        padding: 3px;
        margin-right: 15px;
        width: 70px !important;

        ";

//        $output = $this->outputGuides();
        $result = $this->selectRecord();
        $output = "<table style='". $style ."' class='table table-striped borderless table-hover footable foo3 default footable-loaded evenrow' width='100%'>
            <thead style='background: #ccc;'>
                <tr class='staff-heading'>
                    <th class='footable-visible footable-first-column'></th>
                    <th class='footable-visible footable-sortable'>
                        <strong>Book</strong>
                    </th>
                    <th>
                        <strong>Tags</strong>
                    </th>

                </tr>
            </thead>
            <tbody class='table-striped'>
        ";

        $i = 0;
        foreach($result as $item){
            $output .= "

                <tr class='";

            $output .= ( ($i++)%2 == 0)? "oddrow": "evenrow" ;

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

                    </td>

                </tr>

                ";
        }

        $this->_body = $output . "</table>


       ";

    }


    //difference: Record uses makePluslet to render content and Plustets output function
    function makePluslet ($title = "", $body = "", $bonus_styles = "", $printout = TRUE) {
        $pluslet = "
  <div class=\"pluslet $bonus_styles\">
    <div class=\"titlebar\">
      <div class=\"titlebar_text\">$title</div>
      <div class=\"titlebar_options\"></div>
    </div>
    <div class=\"pluslet_body\">$body
    </div>
  </div>";

        if ($printout == TRUE) {
            print $pluslet;
        } else {
            return $pluslet;
        }
    }

    public function outputForm() {


        $action = htmlentities($_SERVER['PHP_SELF']) . "?record_id=" . $this->_record_id;

        // set up
        print "<div class=\"pure-g\">";

        $this->_body = $this->loadHtml(__DIR__ . '/views/BookListView.php');

    }

    public function selectRecord($notrack = 0) {

        $db  = new Querier;

        $query = "
            select t.*, l.*
            from title t, location_title j, location l
            where t.pre = 'book'
            AND t.title_id = j.title_id
            AND j.location_id = l.location_id
        ";

        $result = $db->query($query);

        return $result;
    }

    static function getMenuName()
    {
        return _('Books');
    }

    static function getMenuIcon()
    {
        $icon="<i class=\"fa fa-book\" title=\"" . _("Books") . "\" ></i><span class=\"icon-text\">"  . _("Book") . "</span>";
        return $icon;
    }


}
