<?php
/**
 * Created by PhpStorm.
 * User: lazaroalvarez
 * Date: 6/18/16
 * Time: 4:07 AM
 */



echo "View";
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



//$action = htmlentities($_SERVER['PHP_SELF']) . "?record_id=" . $this->_record_id;
//
//
//$output = "
//  	<form action=\"" . $action . "\" method=\"post\" id=\"new_record\" accept-charset=\"UTF-8\" class=\"pure-form pure-form-stacked\">
//  	<input type=\"hidden\" name=\"title_id\" value=\"" . $this->_record_id . "\" />
//
//  	    <div class=\"pure-u-1-1\">
//  	        <div class=\"pluslet pure-u-1-2\">
//
//                <div class=\"titlebar\">
//                  <div class=\"titlebar_text\">" . _("Enter New Book") . "</div>
//                  <div class=\"titlebar_options\"></div>
//                </div>
//
//                <div class=\"pluslet_body\">
//                    <label for=\"title\">" . _("Title") . "</label>
//                    <input type=\"text\" name=\"book_title\" id=\"book_title\" class=\"pure-input-1-4\" value=\"" . $this->_title . "\" />
//
//                    <label for=\"ISBN\">" . _("Book ISBN") . "</label>
//                    <input type=\"text\" name=\"ISBN\" id=\"book_ISBN\" class=\"pure-input-1 required_field\" value=\"" . $_ISBN . "\" />
//
//                    <label for=\"book_decription\">" . _("Description") . "</label>
//                    <textarea name=\"book_decription\" id=\"book_decription\" rows=\"4\" cols=\"70\">" . stripslashes($description) . "</textarea>
//
//                </div>
//            </div>
//        </div>
//
//
//
//    </form>
//
//  	";
////Pluslet body is closed by _body on Pluslet parent class
//echo $output;
