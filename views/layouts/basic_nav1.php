<?php $this->beginContent('@app/views/layouts/basic_header.php'); ?>
<div class="vernav2 iconmenu">
    <ul>
        <?php if(isset($this->params['menulist'][$this->params['act']]['childlist'])){?>
            <?php foreach($this->params['menulist'][$this->params['act']]['childlist'] as $row1){?>
                <li><a href="#formsub" class="editor"><?= $row1['self']->description ?></a>
                    <span class="arrow"></span>
                    <ul id="formsub">
                    <?php if(isset($row1['childlist'])){?>
                        <?php foreach($row1['childlist'] as $row2){?>
                            <li><a href="<?= $row2['self']->url ?>"><?= $row2['self']->description ?></a></li>
                        <?php } ?>
                    <?php } ?>
                    </ul>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
    <a class="togglemenu"></a>
    <br /><br />
</div><!--leftmenu-->
<?= $content ?>
<?php $this->endContent(); ?>