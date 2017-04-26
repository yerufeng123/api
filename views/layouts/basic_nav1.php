<?php $this->beginContent('@app/views/layouts/basic_header.php'); ?>
<div class="vernav2 iconmenu">
    <ul>
    <?php if(isset($this->params['menulist'])){?>
    <?php foreach($this->params['menulist'] as $k1 => $row1){?>
    <?php if($row1['self']->type == 1){?>
        <li>
            <a href="<?php 
                    if($row1['self']->style == 1){
                        echo '#'.$k1;
                    }else{
                        echo $row1['self']->url;
                    }
                ?>" class="<?= $row1['self']->pic ?>"><?= $row1['self']->description ?></a>
            <?php if(isset($row1['childlist']) && $row1['self']->style == 1){?>
            <span class="arrow"></span>
            <ul id="<?= $k1 ?>">
                <?php foreach($row1['childlist'] as $row2){?>
                <?php if($row2['self']->type == 1){?>
                <li><a href="<?= $row2['self']->url ?>"><?= $row2['self']->description ?></a></li>
                <?php } ?>
                <?php } ?>
            </ul>
            <?php } ?>
        </li>
    <?php } ?>
    <?php } ?>
    <?php } ?>
    </ul>
    <a class="togglemenu"></a>
    <br /><br />
</div><!--leftmenu-->
<?= $content ?>
<?php $this->endContent(); ?>