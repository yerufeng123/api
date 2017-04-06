<?php $this->beginContent('@app/views/layouts/basic_header.php'); ?>
<div class="vernav2 iconmenu">
    <ul>
        <li><a href="#formsub" class="editor">表单提交</a>
            <span class="arrow"></span>
            <ul id="formsub">
                <li><a href="forms.html">基础表单</a></li>
                <li><a href="wizard.html">表单验证</a></li>
                <li><a href="editor.html">编辑器</a></li>
            </ul>
        </li>
        <!--<li><a href="filemanager.html" class="gallery">文件管理</a></li>-->
        <li><a href="elements.html" class="elements">网页元素</a></li>
        <li><a href="widgets.html" class="widgets">网页插件</a></li>
        <li><a href="calendar.html" class="calendar">日历事件</a></li>
        <li><a href="support.html" class="support">客户支持</a></li>
        <li><a href="typography.html" class="typo">文字排版</a></li>
        <li><a href="tables.html" class="tables">数据表格</a></li>
        <li><a href="buttons.html" class="buttons">按钮 &amp; 图标</a></li>
        <li><a href="#error" class="error">错误页面</a>
            <span class="arrow"></span>
            <ul id="error">
                <li><a href="notfound.html">404错误页面</a></li>
                <li><a href="forbidden.html">403错误页面</a></li>
                <li><a href="internal.html">500错误页面</a></li>
                <li><a href="offline.html">503错误页面</a></li>
            </ul>
        </li>
        <li><a href="#addons" class="addons">其他页面</a>
            <span class="arrow"></span>
            <ul id="addons">
                <li><a href="newsfeed.html">新闻订阅</a></li>
                <li><a href="profile.html">资料页面</a></li>
                <li><a href="productlist.html">产品列表</a></li>
                <li><a href="photo.html">图片视频分享</a></li>
                <li><a href="gallery.html">相册</a></li>
                <li><a href="invoice.html">购物车</a></li>
            </ul>
        </li>
    </ul>
    <a class="togglemenu"></a>
    <br /><br />
</div><!--leftmenu-->
<?= $content ?>
<?php $this->endContent(); ?>