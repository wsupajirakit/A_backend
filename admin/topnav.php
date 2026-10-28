<header class="header">
  <div class="container-fluid">
    <div class="row">
      <div class="bg-white border-bottom col-12 d-flex">
        <ul class="nav-left no-bullets">
          <li class="nav-LR-list">
            <label class="nav-toggle sidebar-toggle">
              <input type="checkbox" name="sidebar-toggle" id="sidebar-toggle" class="hidden" />
              <span class="marker"><i class="fas fa-bars"></i></span>
            </label>
          </li>
          <?php if(isset($enableSearch) && $enableSearch===true){?>
          <li class="nav-LR-list">
            <label class="nav-toggle search-toggle">
              <input type="checkbox" name="search-toggle" id="search-toggle" class="hidden" />
              <span class="marker is-active"><i class="fas fa-times"></i></span>
              <span class="marker in-active"><i class="fas fa-search"></i></span>
            </label>
          </li>
          <li class="nav-LR-list">
            <form action="" id="search-form" class="search-form">
              <input type="text" name="search" id="search" placeholder="Search..." />
            </form>
          </li>
          <?php }?>
        </ul>
        <ul class="nav-right no-bullets">
          <!-- <li class="nav-LR-list">
            <div class="nav-toggle user-nav-toggle">
              <i class="fas fa-bell"></i>
              <div class="notice-count text-white bg-danger">10</div>  
              <ul class="nav-dropdown bg-white notification border">
                <li class="nav-dropdown-head b border-bottom text-left py-3 px-4">
                  <i class="fas fa-bell mx-2"></i><span>Notifications</span>
                </li>
                <li class="nav-dropdown-body text-left custom-scbar">
                  <ul>
                    <?php for($i=0; $i<5; $i++){ ?>
                    <li class="nav-dropdown-list py-2 px-3 border-bottom <?php if($i%2==0){?>_new<?php }?>">
                      <div class="nvdd d-flex">
                        <figure class="nvdd-img rounded-circle ">
                          <img src="img/profile/user.png" alt="Profile" class="img-fluid" />
                        </figure>
                         <div class="nvdd-content">
                          <div class="_msg">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                          </div>
                          <div class="_time text-muted mt-2">
                            10/10/2018 15:50
                          </div>
                         </div>
                      </div>
                    </li>
                    <?php }?>
                  </ul>
                </li>
                <li class="nav-dropdown-footer border-top">
                  <a class="viewall-link text-center py-3">
                    View all notifications
                  </a>
                </li>
              </ul>
            </div>
          </li> -->
<!--           <li class="nav-LR-list">
            <div class="nav-toggle user-nav-toggle">
              <i class="fas fa-envelope"></i>
              <div class="notice-count text-white bg-danger">10</div>  
              <ul class="nav-dropdown bg-white message border">
                <li class="nav-dropdown-head b border-bottom text-left py-3 px-4">
                  <i class="fas fa-envelope mx-2"></i><span>Messages</span>
                </li>
                <li class="nav-dropdown-body text-left custom-scbar">
                  <ul>
                    <?php for($i=0; $i<5; $i++){ ?>
                    <li class="nav-dropdown-list py-2 px-3 border-bottom <?php if($i%2==0){?>_new<?php }?>">
                      <div class="nvdd d-flex">
                        <figure class="nvdd-img rounded-circle ">
                          <img src="img/profile/user.png" alt="Profile" class="img-fluid" />
                        </figure>
                         <div class="nvdd-content">
                          <div class="msg_head d-flex mb-2">
                            <div class="_name b ellipsis flex-peer text-drak">John Doe</div>
                            <div class="_time text-muted">
                              10/10/2018 15:50
                            </div>
                          </div>
                          <div class="_msg">
                            Lorem ipsum dolor sit amet, consectetur adipisicing elit.
                          </div>
                         </div>
                      </div>
                    </li>
                    <?php }?>
                  </ul>
                </li>
                <li class="nav-dropdown-footer border-top">
                  <a class="viewall-link text-center py-3">
                    View all messages
                  </a>
                </li>
              </ul>
            </div>
          </li> -->
          <li class="nav-LR-list d-flex justify-content-center">
            <div class="nav-toggle user-nav-toggle user-menu">
              <div class="user-info">
                <div class="pf-img mx-2 rounded-circle">
                  <img src="img/profile/<?= e($userData['profile_image']) ?>" alt="<?= e($userData['fullname']) ?>" />
                </div>
                <div class="pf-name flex-peer ellipsis"><?= e($userData['fullname']) ?></div>
              </div>
               <ul class="nav-dropdown user border no-bullets">
                <li class="nav-dropdown-body text-left">
                  <ul class="no-bullets">
                    <li class="nav-dropdown-list p-2">
                      <a href="edit-profile.php" class="d-block text-dark">
                        <div class="nvdd d-flex">
                          <div class="_icon mx-3"><i class="fas fa-user"></i></div>
                          <div class="nvdd-content">
                            <div class="_msg"><?=$txt_var['edit_profile'];?></div>
                           </div>
                        </div>
                      </a>
                    </li>
                    <li class="nav-dropdown-list p-2">
                      <a href="change-password.php" class="d-block text-dark">
                        <div class="nvdd d-flex">
                          <div class="_icon mx-3"><i class="fas fa-unlock"></i></div>
                          <div class="nvdd-content">
                            <div class="_msg"><?=$txt_var['change_password'];?></div>
                           </div>
                        </div>
                      </a>
                    </li>
                   <!--  <li class="nav-dropdown-list p-2">
                      <a href="change-password.php" class="d-block text-dark">
                        <div class="nvdd d-flex">
                          <div class="_icon mx-3"><i class="fas fa-cog"></i></div>
                          <div class="nvdd-content">
                            <div class="_msg"><?=$txt_var['setting'];?></div>
                           </div>
                        </div>
                      </a>
                    </li> -->
                    <li class="nav-dropdown-list p-2 border-top">
                      <a href="logout.php" class="d-block j-confirm-link text-dark" data-title="<?=$txt_var['signout'];?>" data-content="<?=$txt_var['signout_confirm'];?>">
                        <div class="nvdd d-flex">
                          <div class="_icon mx-3"><i class="fas fa-sign-out-alt"></i></div>
                          <div class="nvdd-content">
                            <div class="_msg"><?=$txt_var['signout'];?></div>
                           </div>
                        </div>
                      </a>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</header>