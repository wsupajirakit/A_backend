<section class="sidebar border-right bg-white">
  <div class="sidebar-inner">
    <div class="sidebar-head d-flex border-bottom pddX-15 d-flex">
      <div class="title-sidebar d-flex flex-peer align-items-center flex-grow-1">
        <div class="logo-link d-flex align-items-center">
          <figure class="logo">
            <img src="<?= e($mainLogo) ?>" alt="" />
          </figure>
          <div class="text b-500 ellipsis">
            <div><?=$txt_var['administrator'];?></div>
            <a href="../index.php" class="text-sm"><i class="fas fa-home mr-2"></i><span><?=$txt_var['visit_site'];?></span></a>
          </div>
        </div>
      </div>
      <button id="close-sidebar" class="close-sidebar text-muted bg-white">
        <div class=""><i class="fas fa-times"></i></div>
      </button>
    </div>
    <div class="sidebar-body">
      <ul class="sidebar-menu custom-scbar no-bullets">
        <!-- #dashboard -->
        <li class="menu-item">
          <a href="index.php" class="item-link <?php if($active=='home'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-tachometer-alt"></i></span>
            <span class="text"><?=$txt_var['dashboard'];?></span>
          </a>
        </li>
        <!-- #public-relations -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='public-relations'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-bullhorn"></i></span>
            <span class="text"><?=$txt_var['public_relations'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="pr-cover.php" class="item-link <?php if($active_sm=='page-cover'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['page_cover_image'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="pr-sponsor.php" class="item-link <?php if($active_sm=='sponsor'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['sponsor_image'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="pr-announce-message.php" class="item-link <?php if($active_sm=='announce-message'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['announce_message'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="pr-app-popup.php" class="item-link <?php if($active_sm=='app-popup'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['app_popup'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #artist -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='artist'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-microphone"></i></span>
            <span class="text"><?=$txt_var['artist'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="artist.php" class="item-link <?php if($active_sm=='all-artist'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['all'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="artist-form.php?action=new" class="item-link <?php if($active_sm=='add-artist'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['add_new'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #song -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='song'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-music"></i></span>
            <span class="text"><?=$txt_var['song'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="song.php" class="item-link <?php if($active_sm=='all-song'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['all'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="song-form.php?action=new" class="item-link <?php if($active_sm=='add-song'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['add_new'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #top-chart -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='top-chart'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-chart-line"></i></span>
            <span class="text"><?=$txt_var['top_chart'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="top-chart.php" class="item-link <?php if($active_sm=='all-top-chart'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['all'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="top-chart-form.php?action=new" class="item-link <?php if($active_sm=='add-top-chart'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['add_new'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #radio-presenter -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='radio-presenter'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-headset"></i></span>
            <span class="text"><?=$txt_var['radio_presenter'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="radio-presenter.php" class="item-link <?php if($active_sm=='all-radio-presenter'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['all'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="radio-presenter-form.php?action=new" class="item-link <?php if($active_sm=='add-radio-presenter'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['add_new'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #radio-program -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='radio-program'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-calendar-alt"></i></span>
            <span class="text"><?=$txt_var['radio_program'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="radio-program.php" class="item-link <?php if($active_sm=='all-radio-program'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['all'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="radio-program-form.php?action=new" class="item-link <?php if($active_sm=='add-radio-program'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['add_new'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #post -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='post'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-newspaper"></i></span>
            <span class="text"><?=$txt_var['post'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="post.php" class="item-link <?php if($active_sm=='all-post'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['post_all'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="post-editor.php?action=new" class="item-link <?php if($active_sm=='add-post'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['add_new'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #media -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='media-file'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-images"></i></span>
            <span class="text"><?=$txt_var['media_file'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="media.php" class="item-link <?php if($active_sm=='media-image'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['image'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #profile -->
        <li class="menu-item dropdown">
          <label class="item-link <?php if($active=='myprofile'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-user"></i></span>
            <span class="text"><?=$txt_var['profile'];?></span>
            <input type="checkbox" name="sidebar-menu-dropdown" />
            <span class="dropdown-pointer"><i class="fas fa-chevron-right"></i></span>
          </label>
          <ul class="sidebar-menu no-bullets">
            <li class="menu-item">
              <a href="edit-profile.php" class="item-link <?php if($active_sm=='edit-profile'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['edit_profile'];?></span>
              </a>
            </li>
            <li class="menu-item">
              <a href="change-password.php" class="item-link <?php if($active_sm=='change-password'){?>-active<?php }?>">
                <span class="icon"></span>
                <span class="text"><?=$txt_var['change_password'];?></span>
              </a>
            </li>
          </ul>
        </li>
        <!-- #appearance -->
        <li class="menu-item">
          <a href="appearance.php" class="item-link <?php if($active=='appearance'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-palette"></i></span>
            <span class="text"><?=$txt_var['appearance'];?></span>
          </a>
        </li>
        <!-- <li class="menu-item">
          <a href="setting.php" class="item-link <?php if($active=='setting'){?>-active<?php }?>">
            <span class="icon"><i class="fas fa-cog"></i></span>
            <span class="text"><?=$txt_var['setting'];?></span>
          </a>
        </li> -->
        <!-- #signout -->
        <li class="menu-item">
          <a href="logout.php" class="item-link <?php if($active=='sign-out'){?>-active<?php }?> j-confirm-link" data-title="<?=$txt_var['signout'];?>" data-content="<?=$txt_var['signout_confirm'];?>">
            <span class="icon"><i class="fas fa-sign-out-alt"></i></span>
            <span class="text"><?=$txt_var['signout'];?></span>
          </a>
        </li>
      </ul>
    </div>
  </div> 
</section>