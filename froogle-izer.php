<?php
/*
Plugin Name: The Froogle-izer
Plugin URI: http://www.psp-tubes.net/wordpress
Description: Data mines Froogle and Google Suggest for keyword ideas for new niches.
Version: 0.4
Date: October 12th, 2008
Author: Alan Lewis
Author URI: http://www.psp-tubes.net

v0.4    12 Aug, 2008 - Added Google Suggest keyword source
v0.3.1  10 Aug, 2008 - Added submenu for better compatibilty with 3rd party admin menu plugins
v0.3    09 Aug, 2008 - Initial release
*/

#########################################################################
#                                                                       #
#                  Frooglizer Wordpress Plugin                          #
#                                                                       #
#########################################################################
# Copyright 2008 psp-tubes.net                                          #
#                                                                       #
# psp-tubes.net is in no way associated with Google Inc.                #
#                                                                       #
#                                                                       #
# This program is free software: you can redistribute it and/or modify  #
# it under the terms of the GNU General Public License as published by  #
# the Free Software Foundation, either version 3 of the License, or     #
# (at your option) any later version.                                   #
#                                                                       #
# This program is distributed in the hope that it will be useful,       #
# but WITHOUT ANY WARRANTY; without even the implied warranty of        #
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the         #
# GNU General Public License for more details.                          #
#                                                                       #
# You should have received a copy of the GNU General Public License     #
# along with this program.  If not, see <http://www.gnu.org/licenses/>. #
#                                                                       #
#########################################################################

#########################################################################
#                                                                       #
# Note: While this is free software, I respectfully request that you do #
#       not remove my small banner linking to my other plugins.         #
#                                                                       #
# Many thanks - Alan                                                    #
#                                                                       #
#########################################################################



// ------- Configuration for GoDaddy Hosting only --------------

// Set this value to true if you host your website with GoDaddy
$is_godaddy = false;

// ------- End of GoDaddy configuration -----------


// ------- NO NEED to edit anything below here ----------------


// Prevent this plugin from being called from outside of Wordpress Admin Panel
if (preg_match('#'.basename(__FILE__) .'#', $_SERVER['PHP_SELF'])) { 
	die('You cannot call this page directly.');
}

// Hook into the Wordpress admin panel menus
function Froogleizer_Add_Pages() {
  add_menu_page('Froogle Data Miner', 'Froogle-izer', 'switch_themes', __FILE__, 'Froogleizer_Display_Menu');
  add_submenu_page(__FILE__, __('Google Suggest Keywords','Google-izer'), __('Google Suggest Keywords','Google-izer'), 'switch_themes', __FILE__ . '&froogleizer_action=googlesuggest', 'GoogleSuggest_Display_Stuff');
//  add_submenu_page(__FILE__, __('Show Froogle Keywords','Froogle-izer'), __('Show Froogle Keywords','Froogle-izer'), 'switch_themes', __FILE__, 'Froogleizer_Display_Stuff');
}

function Froogleizer_Display_Menu() {
  if ($_GET['froogleizer_action'] == 'googlesuggest') {
    GoogleSuggest_Display_Stuff();
  } else {
    Froogleizer_Display_Stuff();
  }
}

// Function to show the Google Suggest keyword section
function GoogleSuggest_Display_Stuff() {
  ?>
  <div class='wrap'>
    <h2><?php _e('Google Suggest Keywords','Froogle-izer'); ?></h2>
    <table width="100%"><tr>
    <?php
    $randomnumber = rand(0,29);
    if ($randomnumber < 10) {
      ?>
      <td align="center" bgcolor="#ffffcc"><strong>Get more useful phpBay and EPN plugins <a href="http://www.psp-tubes.net/wordpress/" target="_blank"><font color="#ff0000">here.</font></a></strong></td>
      <?php
    }
    elseif (($randomnumber >= 10) && ($randomnumber < 20)) {
      ?>
      <td align="center" bgcolor="#ffffcc"><strong>Auto-generate new content from words entered into your phpBay sites search box. <a href="http://www.psp-tubes.net/wordpress/auto-content-generator-using-the-search-function-of-your-phpbay-site/" target="_blank"><font color="#ff0000">Get plugin here.</font></a></strong></td>
      <?php
    }
    elseif (($randomnumber >= 20) && ($randomnumber < 30)) {
      ?>
      <td align="center" bgcolor="#ffffcc"><strong>Display your eBay Partner Network (EPN) stats in your Wordpress admin panel <a href="http://www.psp-tubes.net/wordpress/display-ebay-partner-network-epn-stats-in-your-wordpress-administration-panel/" target="_blank"><font color="#ff0000">Get plugin here.</font></a></strong></td>
      <?php
    }
    ?>
    </tr></table>
    <table><tr><td><br /><br />
    <form method="post">
    <input type="text" name="thekeyword" />
    <input type="submit" value="Search" /> Enter a root keyword/s, or partial keyword, or keyword and partial second keyword. For example, "green widget" or "green w". Keywords are listed in order of most searched for on Google. Right hand column indicates approximate number of competing pages on Google.<br /><br />
    </form>
    </td></tr></table>
    <?php

    // grab the keyword that was entered and sanitise it    
    if ($_POST) {
      $thekeyword = strip_tags(stripslashes(htmlspecialchars($_POST['thekeyword'])));
    }
    else {
      $thekeyword = 'keyword';
    }
    // make the keyword url friendly
    $thekeyword = urlencode(trim($thekeyword));

    $rawurl = "http://www.google.com/complete/search?hl=en&js=true&qu=" . $thekeyword;
     
    // open a curl session
    $curl = curl_init();
    // set up the curl options   
    curl_setopt($curl, CURLOPT_URL, $rawurl);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_HEADER, false);

    if ($is_godaddy) {
      curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
      curl_setopt($curl, CURLOPT_PROXY,"http://proxy.shr.secureserver.net:3128");
    }

    // execute and return string of html
    $stringdump = curl_exec($curl);

    // close the curl session
    curl_close($curl);
  
    if (!$stringdump) {        // the curl failed to return anything
      echo '<td align="center">There was a problem retrieving data from Google. Please try again in a few minutes !!<td>';
    }
    else {                     // curl success
      // Now process the long page string we get back. Look for our code snippet and dump everything before it
      $needlestring = 'new Array(';
      $startofstring = strpos($stringdump, $needlestring);
      // throw away everything up to our needle + the needle .. our string now starts with (
      $stringdump = substr($stringdump,$startofstring + 10);
      // now lets get rid of everything after the )
      $stringdump = substr($stringdump,0,strpos($stringdump,')') );
      // we now have our string isolated between the ( and )
      // lets get all our string delimters organised now
      $stringdump = str_replace(', ','|',$stringdump);   // replace all the comma+spacebars with |
      $stringdump = str_replace('"','',$stringdump);     // get rid of all the double quotes around everything
      // explode the string into an array.
      $rawarray = explode('|',$stringdump);
      // we dont want the first row in the array ... so zap it out
      $dumpthiskey = array_shift($rawarray);
      // now we have an array ... the even keys is the keyword .. the odd keys is the competition
    }  // end if
    
    // now lay up the table for display
    echo '<table width="100%">';
    $counterindex = count($rawarray);
    $arrayindex = 0;

    if ($counterindex < 1) {             // no keywords found
        echo '<tr>';
          echo '<td><strong>No keyword suggestions found !!</strong></td>';
        echo '</tr>';
    }
    else {                               // keywords found
      while ($arrayindex < $counterindex) {
        echo '<tr>';
          echo '<td>' . $rawarray[$arrayindex] . '</td>';
          $arrayindex++;      
          echo '<td>' . $rawarray[$arrayindex] . '</td>';
          $arrayindex++;      
        echo '</tr>';
      }
    }
    echo '</table>';
    ?>
  </div>
  <?php
}


// Funtion to display the Froogle keywords section
function Froogleizer_Display_Stuff() {

  $numberofcurls = 5;      // set the number of iterations - default is 5
  
  ?>
  <div class='wrap'>
    <h2><?php _e('Show Keywords','Froogle-izer'); ?></h2>
    <table width="100%"><tr>
    <?php
    $randomnumber = rand(0,29);
    if ($randomnumber < 10) {
      ?>
      <td align="center" bgcolor="#ffffcc"><strong>Get more useful phpBay and EPN plugins <a href="http://www.psp-tubes.net/wordpress/" target="_blank"><font color="#ff0000">here.</font></a></strong></td>
      <?php
    }
    elseif (($randomnumber >= 10) && ($randomnumber < 20)) {
      ?>
      <td align="center" bgcolor="#ffffcc"><strong>Auto-generate new content from words entered into your phpBay sites search box. <a href="http://www.psp-tubes.net/wordpress/auto-content-generator-using-the-search-function-of-your-phpbay-site/" target="_blank"><font color="#ff0000">Get plugin here.</font></a></strong></td>
      <?php
    }
    elseif (($randomnumber >= 20) && ($randomnumber < 30)) {
      ?>
      <td align="center" bgcolor="#ffffcc"><strong>Display your eBay Partner Network (EPN) stats in your Wordpress admin panel <a href="http://www.psp-tubes.net/wordpress/display-ebay-partner-network-epn-stats-in-your-wordpress-administration-panel/" target="_blank"><font color="#ff0000">Get plugin here.</font></a></strong></td>
      <?php
    }
    ?>
    </tr></table>
    <?php

    $countervar = 0;
    while ($countervar < $numberofcurls) {
    
      $rawurl = "http://www.google.com/products";
      // open a curl session
      $curl = curl_init();
      // set up the curl options   
      curl_setopt($curl, CURLOPT_URL, $rawurl);
      curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($curl, CURLOPT_HEADER, false);

      if ($is_godaddy) {
        curl_setopt($curl, CURLOPT_PROXYTYPE, CURLPROXY_HTTP);
        curl_setopt($curl, CURLOPT_PROXY,"http://proxy.shr.secureserver.net:3128");
      }

      // execute and return string of html
      $stringdump = curl_exec($curl);

      // close the curl session
      curl_close($curl);
  
      if (!$stringdump) {        // the curl failed to return anything
        echo '<td align="center">There was a problem retrieving data from Froogle. Please try again in a few minutes !!<td>';
      }
      else {                     // curl success
        // Now process the long page string we get back. Look for our code snippet and dump everything before it
        $needlestring = '<td width=20 nowrap style="text-align:left;"><nobr><font size="-1">';
        $startofstring = strpos($stringdump, $needlestring);
        $secondindex = 0;
        while ($startofstring !== false) {    // we got a find
    
          // throw away everything up to our needle + the needle
          $stringdump = substr($stringdump,$startofstring + 67);
          // now we have an http statement in front that we have to get rid of .. so look for its end then substr it
          $endofhtml = strpos($stringdump, '>');
          $stringdump = substr($stringdump,$endofhtml+1);
          // now we are the start of what we want .. look for the postion of the end of what we want and grab it.
          $theitemwewant = substr($stringdump,0,strpos($stringdump,'</a>'));
          // we have what we want so write it to the array
          $arrayofitems[$countervar][$secondindex] = $theitemwewant;
          // do another string search for our key needle in preparation for looping back the WHILE
          $startofstring = strpos($stringdump, $needlestring);
          $secondindex = $secondindex + 1;
        }  // end while
      }  // end if
    
      $countervar = $countervar + 1;   // increment the outer loop counter

    } // end while
    
    // now lay up the table for display
    echo '<table width="100%">';
    $innerindex = 0;
    while ($innerindex < 25) {
      $outerindex = 0;
      echo '<tr>';
      while ($outerindex < $numberofcurls) {
        echo '<td>' . $arrayofitems[$outerindex][$innerindex] . '</td>';      
        $outerindex = $outerindex + 1;
      }
      echo '</tr>';
      $innerindex = $innerindex + 1;    
    }
    echo '</table>';
    ?>
  </div>
  <?php
}

// Hook into WP
add_action('admin_menu', 'Froogleizer_Add_Pages');
?>