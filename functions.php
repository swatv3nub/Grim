<?php
//functions for Grim-Info-Fetcher
function getTitle($url) {
  $data = readcontents($url);
  $title = preg_match('/<title[^>]*>(.*?)<\/title>/ims', $data, $matches) ? $matches[1] : null;
  return $title;
  }
  function userinput($message){
    global $white, $bold, $greenbg, $redbg, $bluebg, $cln, $lblue, $fgreen;
    $yellowbg = "\e[100m";
    $inputstyle = $cln . $bold . $lblue . "[#] " . $message . ": " . $fgreen ;
  echo $inputstyle;
  }
function WEBserver($urlws){
  stream_context_set_default( [
    'ssl' => [
        'verify_peer' => false,
        'verify_peer_name' => false,
    ],
]);
  $wsheaders = get_headers($urlws, 1);
  if (is_array($wsheaders['Server'])) { $ws = $wsheaders['Server'][0];}else{
    $ws = $wsheaders['Server'];
  }
  if ($ws == "")
    {
      echo "\e[91mCould Not Detect\e[0m";
    }
  else
    {
      echo "\e[92m$ws \e[0m";
    }
}


function cloudflaredetect($reallink){

  $urlhh    = "http://api.hackertarget.com/httpheaders/?q=" . $reallink;
  $resulthh = file_get_contents($urlhh);
  if (strpos($resulthh, 'cloudflare') !== false)
    {
      echo "\e[91mDetected\n\e[0m";
    }
  else
    {
      echo "\e[92mNot Detected\n\e[0m";
    }
}


function CMSdetect($reallink){
  $cmssc  = readcontents($reallink);
  if (strpos($cmssc, '/wp-content/') !== false)
    {
      $tcms = "WordPress";

    }
  else
    {
      if (strpos($cmssc, 'Joomla') !== false)
        {
          $tcms = "Joomla";
        }
      else
        {
          $drpurl = $reallink . "/misc/drupal.js";
          $drpsc  = readcontents("$drpurl");
          if (strpos($drpsc, 'Drupal') !== false)
            {
              $tcms = "Drupal";
            }
          else
            {
              if (strpos($cmssc, '/skin/frontend/') !== false)
                {
                  $tcms = "Magento";
                }
              else
                {
                  if (strpos($cmssc, 'content="WordPress')!== false) {
                    $tcms = "WordPress";
                  }
                  else {


                  $tcms = "\e[91mCould Not Detect";
                }
                }
            }
        }
    }
    return $tcms;
}
function robotsdottxt($reallink){
  $rbturl    = $reallink . "/robots.txt";
  $rbthandle = curl_init($rbturl);
  curl_setopt($rbthandle, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($rbthandle, CURLOPT_RETURNTRANSFER, TRUE);
  $rbtresponse = curl_exec($rbthandle);
  $rbthttpCode = curl_getinfo($rbthandle, CURLINFO_HTTP_CODE);
  if ($rbthttpCode == 200)
    {
      $rbtcontent = readcontents($rbturl);
      if ($rbtcontent == "")
        {
          echo "Found But Empty!";
        }
      else
        {
          echo "\e[92mFound \e[0m\n";
          echo "\e[36m\n-------------[ *_* ]----------------  \e[0m\n";
          echo $rbtcontent;
          echo "\e[36m\n-----------[*_*]-------------\e[0m";
        }
    }
  else
    {
      echo "\e[91mCould NOT Find robots.txt! \e[0m\n";
    }
}
function gethttpheader($reallink){
  $hdr = get_headers($reallink);
  foreach ($hdr as $shdr) {
    echo "\n\e[92m\e[1m[i]\e[0m  $shdr";
  }
  echo "\n";

}
function extract_social_links($sourcecode){
  global $bold, $lblue, $fgreen, $red, $blue, $magenta, $orange, $white, $green, $grey, $cyan;
  $fb_link_count = 0;
  $twitter_link_count = 0;
  $insta_link_count = 0;
  $yt_link_count = 0;
  $gp_link_count = 0;
  $pint_link_count = 0;
  $github_link_count = 0;
  $total_social_link_count = 0;

  $social_links_array = array (
    'facebook' => array(),
    'twitter' => array(),
    'instagram' => array(),
    'youtube' => array(),
    'google_p' => array(),
    'pinterest' => array(),
    'github' => array()
  );

  $fb_links = $social_links_array['facebook'];
  $twitter_links = $social_links_array['twitter'];
  $insta_links = $social_links_array['instagram'];
  $youtube_links = $social_links_array['youtube'];
  $googlep_links = $social_links_array['google_p'];
  $pinterest_links = $social_links_array['pinterest'];
  $github_links = $social_links_array['github'];

  $sm_dom = new DOMDocument;
  @$sm_dom->loadHTML($sourcecode);
  $links = $sm_dom->getElementsByTagName('a');
  foreach ($links as $link) {
    $link = $link->getAttribute('href');
    if (strpos ($link, "facebook.com/") !== false){
      $total_social_link_count++;
      $fb_link_count++;
      array_push($social_links_array['facebook'], $link);
    }
    elseif (strpos ($link, "twitter.com/") !== false) {
      $total_social_link_count++;
      $twitter_link_count++;
      array_push($social_links_array['twitter'], $link);
    }
    elseif (strpos ($link, "instagram.com/") !== false) {
      $total_social_link_count++;
      $insta_link_count++;
      array_push($social_links_array['instagram'], $link);
    }
    elseif (strpos ($link, "youtube.com/") !== false) {
      $total_social_link_count++;
      $yt_link_count++;
      array_push($social_links_array['youtube'], $link);
    }
    elseif (strpos ($link, "plus.google.com/") !== false) {
      $total_social_link_count++;
      $gp_link_count++;
      array_push($social_links_array['google_p'], $link);
    }
    elseif (strpos ($link, "github.com/") !== false) {
      $total_social_link_count++;
      $github_link_count++;
      array_push($social_links_array['github'], $link);
    }
    elseif (strpos ($link, "pinterest.com/") !== false) {
      $total_social_link_count++;
      $pint_link_count++;
      array_push($social_links_array['pinterest'], $link);
    }
    else {
      
    }
  }
  if ($total_social_link_count == 0){
    echo $bold . $red . "[!] No Social Link Found In Source Code. \n\e[0m";
  }
  elseif ($total_social_link_count == "1") {
    // Read the readme file
    echo $bold . $lblue . "[i] " . $fgreen . $total_social_link_count . $lblue . " Social Link Was Gathered From Source Code \n\n";
    foreach ($social_links_array['facebook'] as $link) {
      echo $bold . $blue . "[ facebook  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['twitter'] as $link) {
      echo $bold . $cyan . "[  twitter  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['instagram'] as $link) {
      echo $bold . $magenta . "[ instagram ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['youtube'] as $link) {
      echo $bold . $red . "[  youtube  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['google_p'] as $link) {
      echo $bold . $orange . "[  google+  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['pinterest'] as $link) {
      echo $bold . $red . "[ pinterest ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['github'] as $link) {
      echo $bold . $grey . "[  github   ] " . $white . $link . "\n";
    }
    echo "\n";
  } else {
    echo $bold . $lblue . "[i] " . $fgreen . $total_social_link_count . $lblue . " Social Links Were Gathered From Source Code \n\n";
    foreach ($social_links_array['facebook'] as $link) {
      echo $bold . $blue . "[ facebook  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['twitter'] as $link) {
      echo $bold . $cyan . "[  twitter  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['instagram'] as $link) {
      echo $bold . $magenta . "[ instagram ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['youtube'] as $link) {
      echo $bold . $red . "[  youtube  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['google_p'] as $link) {
      echo $bold . $orange . "[  google+  ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['pinterest'] as $link) {
      echo $bold . $red . "[ pinterest ] " . $white . $link . "\n";
    }
    foreach ($social_links_array['github'] as $link) {
      echo $bold . $grey . "[  github   ] " . $white . $link . "\n";
    }
    echo "\n";
  }
}
function extractLINKS($reallink){
  global $bold, $lblue, $fgreen;
  $arrContextOptions=array(
      "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
  $ip = str_replace("https://","",$reallink);
  $lwwww = str_replace("www.","",$ip);
  $elsc = file_get_contents($reallink, false, stream_context_create($arrContextOptions));
  $eldom     = new DOMDocument;
  @$eldom->loadHTML($elsc);
  $elinks = $eldom->getElementsByTagName('a');
  $elinks_count = 0;
  foreach ($elinks as $ec) {
    $elinks_count++;
  }
  echo $bold . $lblue . "[i] Number Of Links Found In Source Code : " . $fgreen . $elinks_count . "\n";
  userinput("Display Links ? (Y/N) ");
  $bv_show_links = trim(fgets(STDIN, 1024));
  if ($bv_show_links == "y" or $bv_show_links =="Y"){
    foreach ($elinks as $elink) {
      $elhref = $elink->getAttribute('href');
      if (strpos($elhref, $lwwww) !== false ) {
        echo "\n\e[92m\e[1m*\e[0m\e[1m $elhref";

      }
      else {
        echo "\n\e[38;5;208m\e[1m*\e[0m\e[1m $elhref";
      }
    }
    echo "\n";
  }

else {
  // not showing links.
}
}
function readcontents($urltoread){
  $arrContextOptions=array(
      "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    );
    $filecntns = file_get_contents($urltoread, false, stream_context_create($arrContextOptions));
    return $filecntns;
}

function MXlookup ($site){
  $Mxlkp = dns_get_record($site, DNS_MX);
	$mxrcrd = $Mxlkp[0]['target'];
	$mxip = gethostbyname($mxrcrd);
	$mx = gethostbyaddr($mxip);
  $mxresult = "\e[1m\e[36mIP      :\e[32m " . $mxip ."\n\e[36mHOSTNAME:\e[32m " . $mx ;
  return $mxresult;
}

function fetch_url($url) {
  $ch = curl_init($url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0");
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}

function saveScanResults($results, $filename = 'scan_results.txt') {
    $timestamp = date('Y-m-d H:i:s');
    $header = "Scan Results - $timestamp\n";
    $header .= "========================\n\n";
    
    try {
        file_put_contents($filename, $header . $results . "\n\n", FILE_APPEND);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

function get_similarweb_rank($url) {
  $domain = parse_url($url, PHP_URL_HOST) ?: $url;
  $sw_url = "https://www.similarweb.com/website/$domain/";

  // Initialize cURL
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $sw_url);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
  curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64)");
  
  $html = curl_exec($ch);
  curl_close($ch);

  if (!$html) {
      return "N/A (No Data Available)";
  }

  // Extract rank using regex (as SimilarWeb uses JavaScript rendering)
  preg_match('/"global":[ ]*([0-9]+)/', $html, $matches);

  return isset($matches[1]) ? "Global Rank: #" . $matches[1] : "N/A (Rank not found)";
}
function bv_moz_info($url, $saveResults = false){
  global $bold, $red, $fgreen, $lblue, $blue;
  require ("config.php");
  if (strpos($accessID, " ") !== false OR strpos($secretKey, " ") !== false){
    echo $bold . $red . "\n[!] Some Results Will Be Omited (Please Put Valid MOZ API Keys in config.php file)\n\n";
  }
  else {
  	$expires = time() + 300;
  	$SignInStr = $accessID. "\n" .$expires;
  	$binarySignature = hash_hmac('sha1', $SignInStr, $secretKey, true);
  	$SafeSignature = urlencode(base64_encode($binarySignature));
  	$objURL = $url;
    $flags = "103079231492";
  	$reqUrl = "http://lsapi.seomoz.com/linkscape/url-metrics/".urlencode($objURL)."?Cols=".$flags."&AccessID=".$accessID."&Expires=".$expires."&Signature=".$SafeSignature;
    $opts = array(
  	    CURLOPT_RETURNTRANSFER => true
  	    );
   $curlhandle = curl_init($reqUrl);
  	curl_setopt_array($curlhandle, $opts);
  	$content = curl_exec($curlhandle);
  	curl_close($curlhandle);
  	$resObj = json_decode($content);
  $results = $bold . $lblue . "[i] Moz Rank : " . $fgreen . $resObj->{'umrp'} . "\n";
  $results .= $bold . $lblue . "[i] Domain Authority : " . $fgreen . $resObj->{'pda'} . "\n";
  $results .= $bold . $lblue . "[i] Page Authority : " . $fgreen . $resObj->{'upa'} . "\n";
  
  echo $results;
  
  if ($saveResults) {
      saveScanResults($results);
  }

  }
}
?>
