<?php
  require_once __DIR__.'/initialize.php';
  require_once __DIR__.'/../admin/assets/php-function/function.php';

  $active = "terms-of-use";
  $siteMode = "normal";
  $htmlBodyClass = "";
  $htmlAppearance = $appearanceData["default"];
  $headerClass = "main-gradient shadow-sm";
  $showCover = false;
  $showAnnounceMessage = false;
  $showSponsor = false;
  $showSidebar = false;
  $pageTitle = "ข้อตกลงในการใช้งาน" . " : " . $appData['about']['app_title'];

  $meta_lang = '';
  $meta_description = 'ข้อตกลงในการใช้งาน '. $appData['about']['app_name'] . ' ' . $appData['about']['organization_name'];
  $meta_keywords = $meta_keywords_default . ',' . 'นโยบาย,ข้อตกลงในการใช้งาน';
  $meta_revisit_after = '1 month';
  $meta_robots = '';
  $meta_googlebot = '';
  $meta_author = '';

  $og_url = 'https://www.example.com/terms-of-use.php';
  $og_type = 'website';
  $og_title = "ข้อตกลงในการใช้งาน - " . $appData['about']['app_name'];
  $og_description = '';
  $og_image = '';
  $og_image_secure = '';
  $og_image_type = '';
  $og_image_width = '';
  $og_image_height = '';

  include 'header.php';
?>

<div class="mb-3">
  <h1 class="text-center">Terms of use</h1>
  <br />
  <article class="col-md-10 mx-auto">

<h4>1. Terms</h4>
<p>By accessing the website at <a href="https://example.com">https://example.com</a>, you are agreeing to be bound by these terms of service, all applicable laws and regulations, and agree that you are responsible for compliance with any applicable local laws. If you do not agree with any of these terms, you are prohibited from using or accessing this site. The materials contained in this website are protected by applicable copyright and trademark law.</p>
<h4>2. Use License</h4>
<ol type="a">
   <li>Permission is granted to temporarily download one copy of the materials (information or software) on A Radio Ltd.'s website for personal, non-commercial transitory viewing only. This is the grant of a license, not a transfer of title, and under this license you may not:
   <ol type="i">
       <li>modify or copy the materials;</li>
       <li>use the materials for any commercial purpose, or for any public display (commercial or non-commercial);</li>
       <li>attempt to decompile or reverse engineer any software contained on A Radio Ltd.'s website;</li>
       <li>remove any copyright or other proprietary notations from the materials; or</li>
       <li>transfer the materials to another person or "mirror" the materials on any other server.</li>
   </ol>
    </li>
   <li>This license shall automatically terminate if you violate any of these restrictions and may be terminated by A Radio Ltd. at any time. Upon terminating your viewing of these materials or upon the termination of this license, you must destroy any downloaded materials in your possession whether in electronic or printed format.</li>
</ol>

<h4>3. Disclaimer</h4>
<ol type="a">
   <li>The materials on A Radio Ltd.'s website are provided on an 'as is' basis. A Radio Ltd. makes no warranties, expressed or implied, and hereby disclaims and negates all other warranties including, without limitation, implied warranties or conditions of merchantability, fitness for a particular purpose, or non-infringement of intellectual property or other violation of rights.</li>
   <li>Further, A Radio Ltd. does not warrant or make any representations concerning the accuracy, likely results, or reliability of the use of the materials on its website or otherwise relating to such materials or on any sites linked to this site.</li>
</ol>

<h4>4. Limitations</h4>
<p>In no event shall A Radio Ltd. or its suppliers be liable for any damages (including, without limitation, damages for loss of data or profit, or due to business interruption) arising out of the use or inability to use the materials on A Radio Ltd.'s website, even if A Radio Ltd. or a A Radio Ltd. authorized representative has been notified orally or in writing of the possibility of such damage. Because some jurisdictions do not allow limitations on implied warranties, or limitations of liability for consequential or incidental damages, these limitations may not apply to you.</p>
<h4>5. Accuracy of materials</h4>
<p>The materials appearing on A Radio Ltd.'s website could include technical, typographical, or photographic errors. A Radio Ltd. does not warrant that any of the materials on its website are accurate, complete or current. A Radio Ltd. may make changes to the materials contained on its website at any time without notice. However A Radio Ltd. does not make any commitment to update the materials.</p>

<h4>6. Links</h4>
<p>A Radio Ltd. has not reviewed all of the sites linked to its website and is not responsible for the contents of any such linked site. The inclusion of any link does not imply endorsement by A Radio Ltd. of the site. Use of any such linked website is at the user's own risk.</p>

<h4>7. Modifications</h4>
<p>A Radio Ltd. may revise these terms of service for its website at any time without notice. By using this website you are agreeing to be bound by the then current version of these terms of service.</p>

<h4>8. Governing Law</h4>
<p>These terms and conditions are governed by and construed in accordance with the laws of Thailand and you irrevocably submit to the exclusive jurisdiction of the courts in that State or location.</p>

<br />
  </article>
</div>

<?php
  include 'footer.php';
?>