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

    public function __construct($pluslet_id, $flag="", $subject_id, $isclone=0) {
        parent::__construct($pluslet_id, $flag, $subject_id, $isclone);

        $this->_type = "BookList";
        $this->_pluslet_bonus_classes = "type-booklist";
        $this->_bookTitle = 'BookList';
        $this->_title = 'BookList';
        $this->_ISBN = 987654;
        $this->_description = "Description";

        $this->db = new Querier ();

    }

    //rendered for Admins
    protected function onEditOutput()
    {

//        $output = $this->outputGuides();
//        $output = "On Edit";



        $this->_body = $this->loadHtml(__DIR__ . '/views/BookEdit.php');



//        $this->_body = $output;


    }

    //rendered for public users
    protected function onViewOutput()
    {

//        $output = $this->outputGuides();


        $querier = new Querier();
        $query = "select title_id, title, alternate_title, description, pre, last_modified_by, last_modified from title where pre = 'book'";
        $result = $querier->query($query);

        $output = "";

        foreach($result as $item)
            $output .= "<li>" . $item['title'] . "</li>";

//        $this->_body = "<ul>" . $output . "</ul>";
        $this->_body;

    }

    //difference: Record uses makePluslet to render content and Plustets output function
    public function output($action="", $view="public") {

        global $title_input_size;

        // Public vs. Private
        parent::establishView($view);

        if ($action == "edit") {

//            if ($this->_pluslet_id) {
//                $this->_pluslet_id_field = "pluslet-" . $this->_pluslet_id;
//                $this->_pluslet_name_field = "";
//                $this->_title = "<input type=\"text\" class=\"required_field edit-input\" id=\"pluslet-update-title-$this->_pluslet_id\" value=\"$this->_title\" size=\"$title_input_size\" />";
//            } else {
//                $new_id = rand(10000, 100000);
//                $this->_pluslet_bonus_classes = "unsortable";
//                $this->_pluslet_id_field = $new_id;
//                $this->_pluslet_name_field = "new-pluslet-Heading";
//                $this->_title = "<input type=\"text\" class=\"required_field edit-input\" id=\"pluslet-new-title-$new_id\" name=\"new_pluslet_title\" value=\"$this->_title\" size=\"$title_input_size\" />";
//            }
//
//            global $title_input_size; // alter size based on column

            onEditOutput();
//            $this->_body = "On edit";
//            parent::startPluslet();
//            print $this->_body;
//            parent::finishPluslet();

            return;
        } else {

            //parent::assemblePluslet();

//            return $this->_pluslet;
            $this->_body = "On View";

            print $this->_body;

        }
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

    public function outputForm($wintype="") {

        global $wysiwyg_desc;
        global $CKPath;
        global $CKBasePath;
        global $IconPath;

        $action = htmlentities($_SERVER['PHP_SELF']) . "?record_id=" . $this->_record_id;

        if ($wintype != "") {
            $action .= "&wintype=pop";
        }

        // set up
        print "<div class=\"pure-g\">";

        echo "
  	<form action=\"" . $action . "\" method=\"post\" id=\"new_record\" accept-charset=\"UTF-8\" class=\"pure-form pure-form-stacked\">
  	<input type=\"hidden\" name=\"title_id\" value=\"" . $this->_record_id . "\" />
  	<div class=\"pure-u-1-3\">
  	<div class=\"pluslet\">
    <div class=\"titlebar\">
      <div class=\"titlebar_text\">" . _("Record") . "</div>
      <div class=\"titlebar_options\"></div>
    </div>
    <div class=\"pluslet_body\">
        <label for=\"prefix\">" . _("Prefix") . "</label>
      	<input type=\"text\" name=\"prefix\" id=\"prefix\" class=\"pure-input-1-4\" value=\"" . $this->_prefix . "\" />

        <label for=\"record_title\">" . _("Record Title") . "</label>
        <input type=\"text\" name=\"title\" id=\"record_title\" class=\"pure-input-1 required_field\" value=\"" . $this->_title . "\" />

  	<label for=\"alternate_record_title\">" . _("Alternate Title") . "</label>
  	<input type=\"text\" name=\"alternate_title\" id=\"alternate_record_title\" class=\"pure-input-1\" value=\"" . $this->_alternate_title . "\" />

  	<label for=\"description\">" . _("Description") . "</label>

  	";

        if ($wysiwyg_desc == 1) {
            include($CKPath);
            global $BaseURL;

            // Create and output object
            $oCKeditor = new CKEditor($CKBasePath);
            $oCKeditor->timestamp = time();
            $config['toolbar'] = 'Basic';// Default shows a much larger set of toolbar options
            $config['filebrowserUploadUrl'] = $BaseURL . "ckeditor/php/uploader.php";

            $oCKeditor->editor('description', $this->_description, $config);
            echo "<br />";
        } else {
            echo "<textarea name=\"description\" id=\"description\" rows=\"4\" cols=\"70\">" . stripslashes($this->_description) . "</textarea>";
        }

        echo "</div></div>"; // end pluslet_body, end pluslet
        print "</div>"; // end 1/3 grid
        print "<div class=\"pure-u-1-3\">";

        // Loop through locations
        self::buildLocation();


        $add_loc = "<div class=\"add_location\"><button alt=\"add new location\"  class=\"pure-button pure-button-success\" border=\"0\" /> Add another location</div>";

        print $add_loc;

        echo "</div>
	<!-- right hand column -->";
        print "<div class=\"pure-u-1-3\">";


        $content = "
	<input type=\"submit\" name=\"submit_record\" class=\"pure-button pure-button-primary\" value=\"" . _("Save Record Now") . "\" />";
        // if it's not a new record, and we're authorized, show delete button
        if ($this->_record_id != "") {
            if (isset($_SESSION["eresource_mgr"]) && $_SESSION["eresource_mgr"] == "1") {
                $content .= " <input type=\"submit\" name=\"delete_record\" class=\"pure-button delete_button pure-button-warning\" value=\"" . _("Delete Forever!") . "\" />";
            } else {
                $content .= " <input type=\"submit\" name=\"recommend_delete\" class=\"pure-button pure-button-warning\" value=\"" . _("Recommend Delete") . "\" />";
            }
        }
        // get edit history
        $last_mod = _("Last modified: ") . lastModded("record", $this->_record_id);
        $title = "<div id=\"last_edited\">$last_mod</div>";

        makePluslet($title, $content, "no_overflow");

        /////////////////
        // Default Source
        /////////////////

        $querierSource = new Querier();
        $qSource = "select source_id, source from source order by source";
        $defsourceArray = $querierSource->query($qSource);
        // let's not have an undefined offset
        if (!isset($this->_def_source[0][0])) {
            $this->_def_source[0][0] = "";
        }

        $sourceMe = new Dropdown("default_source_id", $defsourceArray, $this->_def_source[0][0]);
        $source_string = $sourceMe->display();

        echo "<div class=\"pluslet\">
    <div class=\"titlebar\">
      <div class=\"titlebar_text\"><i class=\"fa fa-book\"></i> " . _("Default Source Type") . "</div>
      <div class=\"titlebar_options\"></div>
    </div>
    <div class=\"pluslet_body\">

	$source_string
	</div></div>"; // end pluslet_body, end pluslet

        /////////////////
        // Subjects
        /////////////////

        $subject_list = "";

        if ($this->_subjects == FALSE) {
            // No results
            $subject_list = "";
        } else {
            // loop through results
            foreach ($this->_subjects as $value) {

                $subject_list .= self::outputSubject($value);
            }
        }

        if (isset($_SESSION["eresource_mgr"]) && $_SESSION["eresource_mgr"] == "1") {
            $subject_string = getSubBoxes('', 50, 1);
        } else {
            $subject_string = getSubBoxes('', 50);
        }

        echo "
  <div class=\"pluslet no_overflow\">
    <div class=\"titlebar\">
      <div class=\"titlebar_text\">" . _("Subjects") . "</div>
      <div class=\"titlebar_options\"></div>
    </div>
    <div class=\"pluslet_body\">

	<select name=\"subject_id[]\"><option value=\"\">" . _("-- Select --") . "</option>
	$subject_string
	</select>
	<div id=\"subject_list\">$subject_list</div> <!-- subjects inserted here -->
	</div>


	</div>";

        $this->outputRelatedPluslets();
        print "</div></form>";
    }

    public function outputRelatedPluslets()
    {
        global $BaseURL;

        $db = new Querier();

        $q = "SELECT sub.subject_id, p.pluslet_id, t.tab_index, p.title, sub.subject
			FROM pluslet p
			INNER JOIN pluslet_section ps
			ON p.pluslet_id = ps.pluslet_id
			INNER JOIN section s
			ON ps.section_id = s.section_id
			INNER JOIN tab t
			ON s.tab_id = t.tab_id
			INNER JOIN subject sub
			ON t.subject_id = sub.subject_id
			WHERE p.body LIKE '%{{dab},{{$this->_record_id}}%'";

        $lobjRows = $db->query($q);

        $lstrBody = "";

        foreach( $lobjRows as $lobjRow )
        {
            $lstrBody .= "<div><a href=\"{$BaseURL}control/guides/guide.php?subject_id={$lobjRow['subject_id']}#box-{$lobjRow['tab_index']}-{$lobjRow['pluslet_id']}\">
						{$lobjRow['subject']} <span class=\"small_extra\">{$lobjRow['title']}</span>
						</a></div>";
        }

        makePluslet( 'Referenced in Pluslets', $lstrBody, 'no-overflow' );
    }

    public function deleteRecord() {

        // make sure they're allowed to delete
        if (!isset($_SESSION["eresource_mgr"]) || $_SESSION["eresource_mgr"] != "1") {
            $this->_debug = _("Permission denied to delete.");
            return FALSE;
        }

        $db = new Querier;

        // Delete the location, location_title and title records
        $q = "DELETE location , location_title, title
 	FROM location,location_title, title
 	WHERE location.location_id = location_title.location_id
 	AND title.title_id = location_title.title_id
 	AND title.title_id = '" . $this->_record_id . "'";

        $delete_result = $db->exec($q);

        $this->_debug = "<p>Del query: $q";

        if (isset($delete_result)) {
            $q2 = "DELETE FROM rank WHERE title_id = '" . $this->_record_id . "'";

            $delete_result2 = $db->exec($q2);

            $this->_debug .= "<p>Del query 2: $q2";
        } else {
            // message
            $this->_message = _("There was a problem with your delete (stage 1 of 2).");
            return FALSE;
        }

        if (isset($delete_result2)) {
            // message
            $this->_message = _("Thy will be done.  Offending record deleted.");

            // /////////////////////
            // Alter chchchanges table
            // table, flag, item_id, title, staff_id
            ////////////////////

            $updateChangeTable = changeMe("record", "delete", $this->_record_id, $this->_title, $_SESSION['staff_id']);

            return TRUE;
        } else {
            // message
            $this->_message = _("There was a problem with your delete (stage 2 of 2).");
            return FALSE;
        }
    }

    public function insertRecord($notrack = 0) {

        // dupe check
        ////////////////
        // Insert title table
        ////////////////
        $db = new Querier;
        $our_title = $db->quote(scrubData($this->_title));
        $our_alternate_title = $db->quote(scrubData($this->_alternate_title));
        $our_prefix = $db->quote(scrubData($this->_prefix));

        $qInsertTitle = "INSERT INTO title (title, alternate_title, description, pre) VALUES (
 		" . $our_title . ",
 		" . $our_alternate_title . ",
 		" . $db->quote(scrubData($this->_description, "richtext")) . ",
 		" . $our_prefix . "
 		)";

        $rInsertTitle = $db->exec($qInsertTitle);

        $this->_debug .= "<p>1. insert title: $qInsertTitle</p>";
        if (!$rInsertTitle) {
            echo blunDer("We have a problem with the insert title query: $qInsertTitle");
        }

        $this->_record_id = $db->last_id();
        $this->_title_id = $this->_record_id;

        /////////////////////
        // insert into rank
        ////////////////////

        self::modifyRank();

        /////////////////////
        // insert/update locations
        ////////////////////

        self::modifyLocation();

        // /////////////////////
        // Alter chchchanges table
        // table, flag, item_id, title, staff_id
        ////////////////////
        if ($notrack != 1) {
            $updateChangeTable = changeMe("record", "insert", $this->_record_id, $our_title, $_SESSION['staff_id']);
        }


        // message
        $this->_message = _("Thy Will Be Done.  Record added.");
    }

    public function updateRecord($notrack = 0) {

        $db  = new Querier;

        // dupe check
        /////////////////////
        // update title table
        /////////////////////

        $db = new Querier();

        $our_title = $db->quote(scrubData($this->_title));
        $our_alternate_title = $db->quote(scrubData($this->_alternate_title));
        $our_prefix = $db->quote(scrubData($this->_prefix));

        $qUpTitle = "UPDATE title SET title = " . $our_title . ", alternate_title = " . $our_alternate_title . ", description = " . $db->quote(scrubData($this->_description, "richtext")) . ", pre = " . $our_prefix . " WHERE title_id = " . scrubData($this->_title_id, "integer");

        $rUpTitle = $db->exec($qUpTitle);

        /////////////////////
        // clear rank
        /////////////////////

        $qClearRank = "DELETE FROM rank WHERE title_id = " . $this->_title_id;

        $rClearRank = $db->exec($qClearRank);

        $this->_debug .= "<p>2. clear rank: $qClearRank</p>";

        if ($rClearRank === FALSE) {
            echo blunDer("We have a problem with the clear rank query: $qClearRank");
        }

        /////////////////////
        // insert into rank
        ////////////////////

        self::modifyRank();

        // wipe entry from intervening table, location_title
        $qClearLoc = "DELETE FROM location_title WHERE title_id = " . scrubData($this->_title_id, "integer");
        $rClearLoc = $db->exec($qClearLoc);

        $this->_debug .= "<p>4. wipe location_title: $qClearLoc</p>";
        if ($rClearLoc === FALSE) {
            echo blunDer("We have a problem with the clear locations query: $qClearLoc");
        }

        /////////////////////
        // insert/update locations
        ////////////////////

        self::modifyLocation();

        // /////////////////////
        // Alter chchchanges table
        // table, flag, item_id, title, staff_id
        ////////////////////

        if ($notrack != 1) {
            $updateChangeTable = changeMe("record", "update", $this->_title_id, $our_title, $_SESSION['staff_id']);
        }

        // message
        $this->_message = _("Thy Will Be Done.  Record updated.");
    }


}