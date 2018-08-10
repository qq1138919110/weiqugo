<a  <?php if($GLOBALS['foot_active']==1){ echo  'class="active"'; } ?> href="index.php?s=/Home/Selected<?php echo $GLOBALS['puid_url'];   ?>">
        <?php if($GLOBALS['foot_active']==1){ ?><img src="images/jinping.png" alt=""><?php }else{ ?><img src="images/jinping1.png" alt=""><?php  } ?>
        <span>精品</span>
      </a>
      <a <?php if($GLOBALS['foot_active']==2){ echo  'class="active"'; } ?> href="index.php?s=/Home/Selected/type_detail<?php echo $GLOBALS['puid_url'];   ?>">
        <?php if($GLOBALS['foot_active']==2){ ?><img src="images/faxian.png" alt=""><?php }else{ ?><img src="images/faxian1.png" alt=""><?php  } ?>
        <span>发现</span>
      </a>
      <a <?php if($GLOBALS['foot_active']==3){ echo  'class="active"'; } ?> href="index.php?s=/Home/Order/cart<?php echo $GLOBALS['puid_url'];   ?>">
        <?php if($GLOBALS['foot_active']==3){ ?><img src="images/gouwuche.png" alt=""><?php }else{ ?><img src="images/gouwuche1.png" alt=""><?php  } ?>
        <span>购物车</span>
      </a>
      <a <?php if($GLOBALS['foot_active']==4){ echo  'class="active"'; } ?> href="index.php?s=/Home/User/info<?php echo $GLOBALS['puid_url'];   ?>">
        <?php if($GLOBALS['foot_active']==4){ ?><img src="images/my1.png" alt=""><?php }else{ ?><img src="images/my.png" alt=""><?php  } ?>
        <span>我的</span>
      </a>