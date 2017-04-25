<?php $this->beginContent('@app/views/layouts/basic.php'); ?>
<body class="withvernav">
    <div class="bodywrapper">
        <div class="topheader">
            <div class="left">
                <h1 class="logo">Ama.<span>Admin</span></h1>
                <span class="slogan">后台管理系统</span>
                
                <div class="search">
                    <form action="" method="post">
                        <input type="text" name="keyword" id="keyword" value="请输入" />
                        <button class="submitbutton"></button>
                    </form>
                </div><!--search-->
                
                <br clear="all" />
                
            </div><!--left-->
            
            <div class="right">
                <!--<div class="notification">
                    <a class="count" href="ajax/notifications.html"><span>9</span></a>
                </div>-->
                <div class="userinfo">
                    <img src="<?= Yii::getAlias('@basic') ?>/images/thumbs/avatar.png" alt="" />
                    <span>管理员</span>
                </div><!--userinfo-->
                
                <div class="userinfodrop">
                    <div class="avatar">
                        <a href=""><img src="<?= Yii::getAlias('@basic') ?>/images/thumbs/avatarbig.png" alt="" /></a>
                        <div class="changetheme">
                            切换主题: <br />
                            <a class="default"></a>
                            <a class="blueline"></a>
                            <a class="greenline"></a>
                            <a class="contrast"></a>
                            <!-- <a class="custombg"></a> -->
                        </div>
                    </div><!--avatar-->
                    <div class="userdata">
                        <h4>Juan</h4>
                        <span class="email">youremail@yourdomain.com</span>
                        <ul>
                            <li><a href="editprofile.html">编辑资料</a></li>
                            <li><a href="accountsettings.html">账号设置</a></li>
                            <li><a href="help.html">帮助</a></li>
                            <li><a href="index.html">退出</a></li>
                        </ul>
                    </div><!--userdata-->
                </div><!--userinfodrop-->
            </div><!--right-->
        </div><!--topheader-->
        
        
        <div class="header">
            <ul class="headermenu">
                <?php foreach ($this->params['menulist'] as $key => $value) {?>
                    <li class=""><a href="<?= $value['self']->url?>"><span class="icon icon-flatscreen"></span><?= $value['self']->description ?></a></li>
                <?php } ?>
            </ul>
            
           <div class="headerwidget">
                <div class="earnings">
                    <div class="one_half">
                        <h4>Today's Earnings</h4>
                        <h2>$640.01</h2>
                    </div><!--one_half-->
                    <div class="one_half last alignright">
                        <h4>Current Rate</h4>
                        <h2>53%</h2>
                    </div><!--one_half last-->
                </div><!--earnings-->
            </div><!--headerwidget-->
            
        </div><!--header-->
		<?= $content ?>      
    </div><!--bodywrapper-->
</body>
<?php $this->endContent(); ?>